<?php
session_start();
require_once '../../../Database/config.php';

$email = $_SESSION['email'] ?? '';
$app = null;

if ($email) {
    $stmt = $pdo->prepare("SELECT a.*, COALESCE(c.course_name, a.preferred_course_1) as course_display_name 
                           FROM admission_applications a 
                           LEFT JOIN courses c ON (a.preferred_course_1 = CAST(c.courseId AS CHAR) OR a.preferred_course_1 = c.course_name)
                           WHERE a.email = ? 
                           ORDER BY a.submission_date DESC 
                           LIMIT 1");
    $stmt->execute([$email]);
    $app = $stmt->fetch();
}

$status = $app ? $app->status : 'Pending';
$app_no = $app ? $app->application_no : 'N/A';
$course = $app ? $app->course_display_name : 'N/A';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Result</title>
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

        .result-card {
            background: white;
            border-radius: 24px;
            padding: 50px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 40px auto;
            position: relative;
            overflow: hidden;
        }
        
        .confetti-decoration {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 10px;
            background: linear-gradient(90deg, #ff0000, #ffa500, #ffff00, #008000, #0000ff, #4b0082, #ee82ee);
        }

        .result-icon {
            width: 100px;
            height: 100px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 30px;
        }
        
        .result-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 10px;
        }
        
        .result-message {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .score-box {
            background: #f8fafc;
            padding: 20px;
            border-radius: 16px;
            display: inline-flex;
            gap: 40px;
            margin-bottom: 30px;
        }
        
        .score-item h4 {
            font-size: 2rem;
            font-weight: 800;
            color: #2563eb;
            margin-bottom: 0;
        }
        
        .score-item span {
            font-size: 0.85rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .result-action-btn {
            background: #2563eb;
            color: white;
            padding: 16px 36px;
            border-radius: 50px;
            border: none;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
            min-width: 220px;
            letter-spacing: 0.3px;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.35);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .result-action-btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: rgba(255,255,255,0.15);
            transition: left 0.3s ease;
        }

        .result-action-btn:hover::before {
            left: 0;
        }

        .result-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 30px rgba(37, 99, 235, 0.4);
        }

        .result-action-btn:active {
            transform: translateY(-1px);
        }

        .btn-area {
            margin-top: 10px;
            display: flex;
            justify-content: center;
        }

    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <?php 
            $enrollment_status = $_SESSION['enrollment_status'] ?? 'Pending Review';
            if ($enrollment_status === 'Pending Review' || $enrollment_status === 'Pending'): 
            ?>
                <div class="result-card">
                    <div class="result-icon" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h1 class="result-title">Application Under Review</h1>
                    <p class="result-message">
                        Your application is currently being evaluated by the Admission Office. Please check back later for updates.
                    </p>

                    <div class="score-box">
                        <div class="score-item">
                            <h4 style="color: #0284c7;">Pending</h4>
                            <span>Evaluation Status</span>
                        </div>
                    </div>

                    <div class="btn-area">
                        <a href="/student/Dashboard.php" class="result-action-btn" style="background: linear-gradient(135deg, #0369a1, #0284c7);">
                            <i class="fas fa-home"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            <?php elseif ($enrollment_status === 'Pending Payment'): ?>
                <div class="result-card">
                    <div class="result-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h1 class="result-title">Admission Approved!</h1>
                    <p class="result-message">
                        Congratulations! Your application has been <strong>APPROVED</strong>. You may now proceed to payment to finalize your enrollment.
                    </p>

                    <div class="score-box">
                        <div class="score-item">
                            <h4 style="color: #d97706;">Approved</h4>
                            <span>Status</span>
                        </div>
                    </div>

                    <div class="btn-area">
                        <a href="/student/Modules/Enrollment/Enrollment-Status.php" class="result-action-btn" style="background: linear-gradient(135deg, #b45309, #d97706);">
                            <i class="fas fa-wallet"></i> Select Payment Method
                        </a>
                    </div>
                </div>
            <?php elseif ($enrollment_status === 'Enrolled'): ?>
                <div class="result-card">
                    <div class="confetti-decoration"></div>
                    <div class="result-icon" style="background: #dcfce7; color: #16a34a;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h1 class="result-title">Officially Enrolled!</h1>
                    <p class="result-message">
                        Welcome to the institution! Your enrollment is complete. You can now access your class schedule and other services.
                    </p>

                    <div class="score-box">
                        <div class="score-item">
                            <h4 style="color: #16a34a;">Active</h4>
                            <span>Enrollment</span>
                        </div>
                        <div class="score-item">
                            <h4 style="color: #16a34a;">Verified</h4>
                            <span>Payment</span>
                        </div>
                    </div>

                    <div class="btn-area">
                        <a href="/student/Modules/Academic/Schedule.php" class="result-action-btn" style="background: linear-gradient(135deg, #15803d, #16a34a);">
                            <i class="fas fa-calendar-alt"></i> View Class Schedule
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Default or Rejected -->
                <div class="result-card">
                    <div class="result-icon" style="background: #fee2e2; color: #ef4444;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h1 class="result-title">Result Pending</h1>
                    <p class="result-message">
                        Your application status is currently: <strong><?php echo htmlspecialchars($enrollment_status); ?></strong>.
                    </p>
                    <div class="btn-area">
                        <a href="/student/Dashboard.php" class="result-action-btn" style="background: linear-gradient(135deg, #dc2626, #ef4444);">
                            <i class="fas fa-home"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
