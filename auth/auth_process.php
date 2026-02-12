<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/config.php';

// PHPMailer Setup
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

function sendEnrolmentEmail($recipientEmail)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP Settings - USER: Please update these with your actual credentials
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'linbilcelestre3@gmail.com'; // Your official SMS email
        $mail->Password = 'wovw wjac wzlf pzev'; // Your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('linbilcelestre3@gmail.com', 'SMS Official');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Official Enrolled - SMS';

        // Dynamic absolute URL detection for email compatibility
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $login_url = $protocol . $host . "/student/auth/Login.php?registered=true";

        // Email Body with Button
        $mail->Body = "
<div style='font-family: Poppins, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px;'>
<h2 style='color: #2563eb; text-align: center;'>OFFICIAL ENROLLED SMS</h2>
<p>Welcome to the School Management System! You have been successfully validated and officially enrolled.</p>
<div style='text-align: center; margin: 30px 0;'>
<a href='$login_url' 
style='background-color: #2563eb; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 1rem;'>
STUDENT LOGIN
</a>
</div>
<p style='font-size: 0.8rem; color: #64748b; text-align: center;'>If you did not register for this account, please ignore this email.</p>
</div>
";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error if needed: $e->getMessage()
        return false;
    }
}

// Check which form was submitted (Login or Register)
// In Login.php:
// Login form submits 'email' and 'password'
// Registration form submits 'reg_email', 'reg_password', etc.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'Security.php';
    // Validate CSRF Token
    verifyCsrfToken($_POST['csrf_token'] ?? '');

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
                // Already registered - Proceed to log them in automatically
                process_login($email, $password, $pdo);
                exit();
            }

            // New Registration Logic
            // Collecting all form data
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
            
            // Guardian Info
            $guardian_first = $_POST['guardian_first'] ?? '';
            $guardian_middle = $_POST['guardian_middle'] ?? '';
            $guardian_last = $_POST['guardian_last'] ?? '';
            $guardian_email = $_POST['guardian_email'] ?? '';
            $guardian_contact = $_POST['guardian_contact'] ?? '';
            $relationship = $_POST['relationship'] ?? '';
            $guardian_address = $_POST['guardian_address'] ?? '';

            // Education Info
            $primary_school = $_POST['primary_school'] ?? '';
            $primary_year = $_POST['primary_year'] ?? '';
            $secondary_school = $_POST['secondary_school'] ?? '';
            $secondary_year = $_POST['secondary_year'] ?? '';

            // Secondary Docs (Optional but good to track)
            $form_137 = null; // Add logic if needed
            $good_moral = null;
            $barangay_clearance = null;

            // Generate Student ID (e.g., 2026-0004)
            $year = date('Y');
            $stmt = $pdo->query("SELECT MAX(id) as last_id FROM students");
            $last_id = $stmt->fetch()->last_id ?? 0;
            $new_id_num = str_pad($last_id + 1, 4, '0', STR_PAD_LEFT);
            $student_id = "$year-$new_id_num";

            // Generate Reference Code (e.g., ENR26000006)
            $ref_year = date('y');
            $ref_num = str_pad($last_id + 1, 7, '0', STR_PAD_LEFT);
            $reference_code = "ENR$ref_year$ref_num";

            // Map course names to IDs (Assuming 1=BSIT, 2=BSCS, 3=BSBA etc based on screenshots)
            $course_map = [
                'BSIT' => 1,
                'BSCS' => 2,
                'BSBA' => 3
            ];
            $course_id = $course_map[$course] ?? 1;

            // Map course short names to full names for 'students' table
            $full_course_map = [
                'BSIT' => 'BS Information Technology',
                'BSCS' => 'BS Computer Science',
                'BSBA' => 'BS Business Administration'
            ];
            $full_course_name = $full_course_map[$course] ?? $course;

            // File Upload Logic for Student ID Picture
            $profile_image_path = null;
            if (isset($_FILES['id_picture']) && $_FILES['id_picture']['error'] == 0) {
                $target_dir = "../Assets/image/uploads/students/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $file_extension = pathinfo($_FILES["id_picture"]["name"], PATHINFO_EXTENSION);
                $new_filename = $student_id . "_" . time() . "." . $file_extension;
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($_FILES["id_picture"]["tmp_name"], $target_file)) {
                    $profile_image_path = "Assets/image/uploads/students/" . $new_filename;
                }
            }

            // Start Transaction to ensure both inserts succeed
            $pdo->beginTransaction();

            try {
                // Insert into students table
                // Password hash (matching student portal login logic)
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                try {
                     $student_stmt = $pdo->prepare("INSERT INTO students (student_id, first_name, mid_name, last_name, email, password, course, year_level, status, profile_image) 
                                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Regular', ?)");
                     $student_stmt->execute([$student_id, $first_name, $middle_name, $last_name, $email, $hashed_password, $full_course_name, $year_level, $profile_image_path]);
                } catch (PDOException $e) {
                     throw new Exception("ERROR IN STUDENTS TABLE INSERT: " . $e->getMessage());
                }

                // Insert into enrollments table
                try {
                     $enroll_stmt = $pdo->prepare("INSERT INTO enrollments (
                        reference_code, admission_type, course_id, year_level, 
                        first_name, mid_name, last_name, gender, birthdate, contact_number, email, address,
                        id_picture, guardian_first, guardian_middle, guardian_last, guardian_email, guardian_contact, relationship, guardian_address,
                        primary_school, primary_year, secondary_school, secondary_year
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    
                    $enroll_stmt->execute([
                        $reference_code, $admission_type, $course_id, $year_level, 
                        $first_name, $middle_name, $last_name, $gender, $birthdate, $contact_number, $email, $address,
                        $profile_image_path, $guardian_first, $guardian_middle, $guardian_last, $guardian_email, $guardian_contact, $relationship, $guardian_address,
                        $primary_school, $primary_year, $secondary_school, $secondary_year
                    ]);
                } catch (PDOException $e) {
                     throw new Exception("ERROR IN ENROLLMENTS TABLE INSERT: " . $e->getMessage());
                }

                $pdo->commit();

                // Create notification for admins about new student registration
                try {
                    $notif_title = "New Student Registration";
                    $notif_message = htmlspecialchars($first_name . " " . $last_name) . " has registered successfully.";
                    $notif_stmt = $pdo->prepare("
                        INSERT INTO notifications (user_id, type, title, message, profile_image, icon, icon_bg, icon_color, link) 
                        VALUES (NULL, 'student_registration', ?, ?, ?, 'fa-user-plus', '#d1fae5', '#059669', '/Admin/Submodules/Student-Accounts.php')
                    ");
                    $notif_stmt->execute([$notif_title, $notif_message, $profile_image_path]);
                } catch (PDOException $e) {
                    // Log error but don't block registration
                    error_log("Failed to create notification: " . $e->getMessage());
                }


                // Send Enrolment Email
                sendEnrolmentEmail($email);

                // Redirect to Student Portal login as success
                header("Location: ../student/auth/Login.php?registered=true");
                exit();

            } catch (Exception $e) {
                $pdo->rollBack();
                // Direct Debug Output
                header("Location: Login.php?error=system_error&msg=" . urlencode($e->getMessage()));
                exit();
            }

        } catch (PDOException $e) {
            // Direct Debug Output
            echo "<div style='background:white; color:darkred; padding:20px; font-family:monospace;'>";
            echo "<h1>DATABASE ERROR (Outer Catch)</h1>";
            echo "<h3>" . htmlspecialchars($e->getMessage()) . "</h3>";
            echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
            echo "</div>";
            exit();
        }
    }

    // 2. Check for LOGIN (Either from Login form or auto-login after "already registered" check)
    elseif (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        process_login($email, $password, $pdo);
        exit();
    }
} else {
    header("Location: Login.php");
    exit();
}

/**
 * Handle Login UI Logic
 */
function process_login($email, $password, $pdo)
{
    if (empty($email) || empty($password)) {
        header("Location: Login.php?error=empty_fields");
        exit();
    }

    try {
        // First check 'users' table (Staff/Admin)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Plain text check or password_verify (Adjust based on your Auth requirements)
            if ($password === $user->password || (isset($user->password_hash) && password_verify($password, $user->password_hash))) {
                $_SESSION['userId'] = $user->userId;
                $_SESSION['email'] = $user->email;
                $_SESSION['role'] = $user->role;

                // Update status to online
                $updateStmt = $pdo->prepare("UPDATE users SET status = 'online', last_login = CURRENT_TIMESTAMP WHERE userId = ?");
                $updateStmt->execute([$user->userId]);

                // Role-based redirects
                $redirects = [
                    'admin' => '../Admin/Dashboard.php',
                    'superadmin' => '../Super-admin/Dashboard.php',
                    'admission' => '../Admission/Dashboard.php',
                    'cashier' => '../Cashier/Dashboard.php',
                    'student' => '../student/Dashboard.php'
                ];

                $location = isset($redirects[$user->role]) ? $redirects[$user->role] : '../auth/Login.php?error=unauthorized';
                header("Location: $location");
                exit();
            } else {
                header("Location: Login.php?error=invalid_password");
                exit();
            }
        }

        // Fallback: Check 'students' table
        else {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
            $stmt->execute([$email]);
            $student = $stmt->fetch();

            if ($student) {
                // Students usually use password_verify
                if (password_verify($password, $student->password) || $password === $student->password) {
                    $_SESSION['user_id'] = $student->id;
                    $_SESSION['student_id'] = $student->student_id;
                    $_SESSION['email'] = $student->email;
                    $_SESSION['fullname'] = $student->first_name . ' ' . $student->last_name;
                    $_SESSION['role'] = 'student';
                    $_SESSION['profile_image'] = $student->profile_image;

                    header("Location: ../student/Dashboard.php");
                    exit();
                } else {
                    header("Location: Login.php?error=invalid_password");
                    exit();
                }
            } else {
                header("Location: Login.php?error=user_not_found");
                exit();
            }
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>
