<?php
session_start();
header("Location: Result.php");
exit();
?>

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $student_type = $_POST['student_type'];
        $course1 = $_POST['course1'];
        $course2 = $_POST['course2'];
        $last_school = $_POST['last_school'];
        
        // Basic Validation
        if (!preg_match('/^[0-9]{11}$/', $phone)) {
            throw new Exception("Phone number must be exactly 11 digits.");
        }
        
        // Generate Application Number
        $year = date('Y');
        $stmt = $pdo->query("SELECT MAX(applicationId) FROM admission_applications");
        $next_id = ($stmt->fetchColumn() ?: 0) + 1;
        $app_no = "APP-" . $year . "-" . str_pad($next_id, 3, '0', STR_PAD_LEFT);

        $sql = "INSERT INTO admission_applications (application_no, first_name, last_name, date_of_birth, gender, email, phone_number, student_type, preferred_course_1, preferred_course_2, last_school_attended) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$app_no, $first_name, $last_name, $dob, $gender, $email, $phone, $student_type, $course1, $course2, $last_school]);

        header("Location: Result.php?status=success&app_no=" . urlencode($app_no));
        exit();
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $status = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--bg);
            display: flex;
            min-height: 100vh;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .content-area {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .page-subtitle {
            color: var(--secondary);
            font-size: 0.95rem;
        }

        /* Form Card */
        .form-card {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            color: #1e293b;
            transition: all 0.2s;
            font-family: inherit;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .submit-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover {
            background: #1d4ed8;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Application for Admission</h1>
                <p class="page-subtitle">Fill out the form below to start your journey with us.</p>
            </div>

            <form action="" method="POST">
                <div class="form-card">
                    <h3 class="section-title">
                        <i class="fas fa-user" style="color: var(--primary);"></i> Personal Information
                    </h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-input" placeholder="e.g. John" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-input" placeholder="e.g. Doe" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="">Select Gender</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-input" placeholder="e.g. john@example.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" id="phoneInput" class="form-input" placeholder="e.g. 09123456789" 
                                pattern="[0-9]{11}" maxlength="11" minlength="11" 
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <h3 class="section-title">
                        <i class="fas fa-graduation-cap" style="color: var(--primary);"></i> Program Preference
                    </h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Student Type</label>
                            <select name="student_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option>Incoming Freshman</option>
                                <option>Transferee</option>
                                <option>Second Courser</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Preferred Course (1st Choice)</label>
                            <select name="course1" class="form-select" required>
                                <option value="">Select Course</option>
                                <option>BS Information Technology</option>
                                <option>BS Computer Science</option>
                                <option>BS Accountancy</option>
                                <option>BS Civil Engineering</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Preferred Course (2nd Choice)</label>
                            <select name="course2" class="form-select">
                                <option value="">Select Course</option>
                                <option>BS Information Technology</option>
                                <option>BS Computer Science</option>
                                <option>BS Accountancy</option>
                                <option>BS Civil Engineering</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last School Attended</label>
                            <input type="text" name="last_school" class="form-input" placeholder="School Name">
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    Submit Application <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('phoneInput').addEventListener('keypress', function(e) {
            if (e.which < 48 || e.which > 57) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>
