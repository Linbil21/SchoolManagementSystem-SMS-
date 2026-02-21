<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/config.php';
require_once 'mail_helper.php';

// Check which form was submitted (Login or Register)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'Security.php';
    
    // Check if it's an AJAX request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || (isset($_POST['ajax']) && $_POST['ajax'] == 1);

    // Validate CSRF Token
    try {
        verifyCsrfToken($_POST['csrf_token'] ?? '');
    } catch (Exception $e) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Security token expired. Please refresh.']);
            exit();
        }
        die("Security Violation");
    }

    // 1. Check for REGISTRATION
    if (isset($_POST['reg_email'])) {
        $email = trim($_POST['reg_email']);
        $password = $_POST['reg_password'];
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        if (empty($email) || empty($password)) {
            header("Location: Login.php?error=empty_fields");
            exit();
        }

        if ($password !== $confirm_password) {
            header("Location: Login.php?error=password_mismatch");
            exit();
        }

        try {
            // Check if student already exists in 'students' table
            $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
            $stmt->execute([$email]);
            $student = $stmt->fetch();

            if ($student) {
                // Already registered - Proceed to log them in (will trigger OTP if student)
                process_login($email, $password, $pdo, $isAjax);
                exit();
            }

            // New Registration Logic
            $admission_type = $_POST['admission_type'] ?? '';
            $course = $_POST['course'] ?? '';
            $year_level = $_POST['year_level'] ?? '';
            $first_name = $_POST['first_name'] ?? '';
            $middle_name = $_POST['middle_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $gender = $_POST['gender'] ?? '';
            $birthdate = $_POST['birthdate'] ?? '';
            $contact_number = $_POST['contact_number'] ?? '';
            $address = $_POST['address'] ?? '';
            
            $guardian_first = $_POST['guardian_first'] ?? '';
            $guardian_middle = $_POST['guardian_middle'] ?? '';
            $guardian_last = $_POST['guardian_last'] ?? '';
            $guardian_email = $_POST['guardian_email'] ?? '';
            $guardian_contact = $_POST['guardian_contact'] ?? '';
            $relationship = $_POST['relationship'] ?? '';
            $guardian_address = $_POST['guardian_address'] ?? '';

            $primary_school = $_POST['primary_school'] ?? '';
            $primary_year = $_POST['primary_year'] ?? '';
            $secondary_school = $_POST['secondary_school'] ?? '';
            $secondary_year = $_POST['secondary_year'] ?? '';

            $stmt = $pdo->query("SELECT MAX(id) as last_id FROM students");
            $last_id = $stmt->fetch()->last_id ?? 0;

            $ref_year = date('y');
            $ref_num = str_pad($last_id + 1, 7, '0', STR_PAD_LEFT);
            $reference_code = "ENR$ref_year$ref_num";
            
            // Use Reference Code as temporary ID until officially enrolled
            $student_id = $reference_code; 

            $course_id = $_POST['course'] ?? 1;

            // Fetch the actual course name from the database based on the selected ID
            $course_stmt = $pdo->prepare("SELECT course_name FROM courses WHERE courseId = ?");
            $course_stmt->execute([$course_id]);
            $course_row = $course_stmt->fetch(PDO::FETCH_ASSOC);
            $full_course_name = $course_row ? $course_row['course_name'] : 'Unknown Course';

            // File Uploads Handling
            $upload_fields = [
                'id_picture' => '../Assets/image/uploads/students/',
                'birth_cert' => '../Assets/image/uploads/documents/psa/',
                'form_138' => '../Assets/image/uploads/documents/grades/',
                'form_137' => '../Assets/image/uploads/documents/grades/',
                'good_moral' => '../Assets/image/uploads/documents/certificates/',
                'barangay_clearance' => '../Assets/image/uploads/documents/certificates/'
            ];

            $uploaded_paths = [];
            foreach ($upload_fields as $field => $dir) {
                $uploaded_paths[$field] = null;
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
                    if (!is_dir($dir)) mkdir($dir, 0777, true);
                    $file_extension = pathinfo($_FILES[$field]["name"], PATHINFO_EXTENSION);
                    $new_filename = $student_id . "_" . $field . "_" . time() . "." . $file_extension;
                    if (move_uploaded_file($_FILES[$field]["tmp_name"], $dir . $new_filename)) {
                        $uploaded_paths[$field] = str_replace('../', '', $dir) . $new_filename;
                    }
                }
            }
            
            $profile_image_path = $uploaded_paths['id_picture'];

            // Generate 6-digit OTP
            $otp = rand(100000, 999999);

            $pdo->beginTransaction();
            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $student_stmt = $pdo->prepare("INSERT INTO students (student_id, first_name, mid_name, last_name, email, password, course, year_level, status, profile_image, verification_code, is_verified) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Regular', ?, ?, 0)");
                $student_stmt->execute([$student_id, $first_name, $middle_name, $last_name, $email, $hashed_password, $full_course_name, $year_level, $profile_image_path, $otp]);

                $enroll_stmt = $pdo->prepare("INSERT INTO enrollments (reference_code, admission_type, course_id, year_level, first_name, mid_name, last_name, gender, birthdate, contact_number, email, address, id_picture, birth_cert, form_138, form_137, good_moral, barangay_clearance, guardian_first, guardian_middle, guardian_last, guardian_email, guardian_contact, relationship, guardian_address, primary_school, primary_year, secondary_school, secondary_year, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending Review')");
                $enroll_stmt->execute([
                    $reference_code, $admission_type, $course_id, $year_level, 
                    $first_name, $middle_name, $last_name, $gender, $birthdate, 
                    $contact_number, $email, $address, 
                    $uploaded_paths['id_picture'], 
                    $uploaded_paths['birth_cert'], 
                    $uploaded_paths['form_138'], 
                    $uploaded_paths['form_137'], 
                    $uploaded_paths['good_moral'], 
                    $uploaded_paths['barangay_clearance'],
                    $guardian_first, $guardian_middle, $guardian_last, $guardian_email, $guardian_contact, $relationship, $guardian_address, 
                    $primary_school, $primary_year, $secondary_school, $secondary_year
                ]);

                // Create Admission Application automatically
                $app_stmt = $pdo->prepare("INSERT INTO admission_applications (application_no, first_name, last_name, date_of_birth, gender, email, phone_number, student_type, preferred_course_1, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
                $app_stmt->execute([
                    'APP-' . time(), 
                    $first_name, 
                    $last_name, 
                    $birthdate, 
                    $gender, 
                    $email, 
                    $contact_number, 
                    $admission_type, 
                    $full_course_name
                ]);

                $pdo->commit();

                // Notification
                $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, profile_image, icon, icon_bg, icon_color, link) VALUES (NULL, 'student_registration', 'New Student Registration', ?, ?, 'fa-user-plus', '#d1fae5', '#059669', '/Admission/Modules/Evaluation.php')");
                $notif_stmt->execute([$first_name . " " . $last_name . " has registered and applied.", $profile_image_path]);

                // Prepare detail for Unified Email
                $details = [
                    'first_name' => $first_name,
                    'middle_name' => $middle_name,
                    'last_name' => $last_name,
                    'student_id' => 'PENDING',
                    'course' => $full_course_name,
                    'year_level' => $year_level,
                    'contact_number' => $contact_number,
                    'address' => $address,
                    'reference_code' => $reference_code,
                    'profile_image' => $profile_image_path
                ];

                // Send Unified Email (Summary + OTP) 
                if (sendOTP($email, $otp, 'Registration Verification', $details)) {
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'otp_required', 'email' => $email, 'type' => 'register', 'masked_email' => maskEmail($email)]);
                        exit();
                    }
                    header("Location: Verification.php?email=" . urlencode($email) . "&type=register");
                } else {
                    $error_msg = get_last_mail_error();
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP. ' . $error_msg]);
                        exit();
                    }
                    header("Location: Login.php?error=mail_error&details=" . urlencode($error_msg));
                }
                exit();
            } catch (Exception $e) {
                $pdo->rollBack();
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                    exit();
                }
                header("Location: Login.php?error=system_error&msg=" . urlencode($e->getMessage()));
                exit();
            }
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }

    // 2. Check for LOGIN
    elseif (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        process_login($email, $password, $pdo, $isAjax);
        exit();
    }
} else {
    header("Location: Login.php");
    exit();
}

/**
 * Handle Login UI Logic
 */
function process_login($email, $password, $pdo, $isAjax = false)
{
    if (empty($email) || empty($password)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Empty fields.']);
            exit();
        }
        header("Location: Login.php?error=empty_fields");
        exit();
    }

    try {
        // First check 'users' table (Staff/Admin)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $isValid = ($password === $user->password || (isset($user->password_hash) && password_verify($password, $user->password_hash)));
            if ($isValid) {
                // Automatic verification for admins
                $_SESSION['userId'] = $user->userId;
                $_SESSION['email'] = $user->email;
                $_SESSION['role'] = strtolower($user->role);

                $updateStmt = $pdo->prepare("UPDATE users SET status = 'online', last_login = CURRENT_TIMESTAMP WHERE userId = ?");
                $updateStmt->execute([$user->userId]);

                // Notification for Admin Login
                $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, icon, icon_bg, icon_color) VALUES (?, 'login', 'Admin Login', 'System administrator logged in.', 'fa-shield-alt', '#dbeafe', '#2563eb')");
                $notif_stmt->execute([$user->userId]);

                $redirects = [
                    'admin' => '../Admin/Dashboard.php',
                    'superadmin' => '../super-admin/Dashboard.php',
                    'admission' => '../Admission/Dashboard.php',
                    'cashier' => '../Cashier/Dashboard.php',
                    'student' => '../student/Dashboard.php'
                ];
                
                $redirect = $redirects[$_SESSION['role']] ?? '../auth/Login.php?error=unauthorized';
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success', 'redirect' => $redirect]);
                    exit();
                }
                header("Location: " . $redirect);
                exit();
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
                    exit();
                }
                header("Location: Login.php?error=invalid_password");
                exit();
            }
        }

        // Fallback: Check 'students' table
        else {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
            $stmt->execute([$email]);
            $student = $stmt->fetch();

            if ($student && (password_verify($password, $student->password) || $password === $student->password)) {
                // Generate 6-digit OTP
                $otp = rand(100000, 999999);
                $updateStmt = $pdo->prepare("UPDATE students SET verification_code = ? WHERE id = ?");
                $updateStmt->execute([$otp, $student->id]);

                if (sendOTP($student->email, $otp, 'Login Verification')) {
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'otp_required', 'email' => $student->email, 'type' => 'login', 'masked_email' => maskEmail($student->email)]);
                        exit();
                    }
                    header("Location: Verification.php?email=" . urlencode($student->email) . "&type=login");
                } else {
                    $error_msg = get_last_mail_error();
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP. ' . $error_msg]);
                        exit();
                    }
                    header("Location: Login.php?error=mail_error&details=" . urlencode($error_msg));
                }
                exit();
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'User not found or invalid password.']);
                    exit();
                }
                header("Location: Login.php?error=" . ($student ? "invalid_password" : "user_not_found"));
                exit();
            }
        }
    } catch (PDOException $e) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Database error.']);
            exit();
        }
        die("Database error: " . $e->getMessage());
    }
}
?>
