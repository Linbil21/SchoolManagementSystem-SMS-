<?php
session_start();
// Security check
require_once '../../../auth/Security.php';
checkRole(['student']);

// Robust absolute-relative path logic
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/student/', '/Super-admin/', '/Admin/', '/Cashier/', '/Admission/', '/auth/', '/modules/'];
$project_base = '';
foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        $project_base = rtrim(substr($script_name, 0, $pos), '/');
        break;
    }
}
$root = $project_base . '/';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Guide - Student Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #64748b;
            --bg: #f8fafc;
            --glass: rgba(255, 255, 255, 0.9);
            --border: #e2e8f0;
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
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }

        .header-section {
            text-align: center;
            margin-bottom: 50px;
        }

        .header-section h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 15px;
            background: linear-gradient(to right, #2563eb, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-section p {
            color: var(--secondary);
            font-size: 1.1rem;
        }

        /* Steps System */
        .steps-container {
            position: relative;
            margin-top: 40px;
        }

        .step-item {
            display: flex;
            gap: 30px;
            margin-bottom: 50px;
            position: relative;
        }

        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 25px;
            top: 60px;
            width: 2px;
            height: calc(100% - 10px);
            background: #e2e8f0;
        }

        .step-number {
            width: 50px;
            height: 50px;
            background: white;
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.2rem;
            flex-shrink: 0;
            z-index: 1;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.1);
        }

        .step-content {
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            flex: 1;
            transition: transform 0.3s;
            border: 1px solid var(--border);
        }

        .step-content:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .step-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .step-title i {
            color: var(--primary);
        }

        .step-desc {
            color: var(--secondary);
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .action-box {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-step {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #eff6ff;
            color: var(--primary);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.85rem;
            transition: 0.3s;
        }

        .btn-step:hover {
            background: var(--primary);
            color: white;
        }

        .notice-card {
            background: #fffbeb;
            border: 1px solid #fde68a;
            padding: 20px;
            border-radius: 16px;
            margin-top: 20px;
            color: #92400e;
            font-size: 0.85rem;
            display: flex;
            gap: 12px;
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="header-section">
                <h1>Enrollment Journey</h1>
                <p>Your step-by-step roadmap to becoming an officially enrolled student.</p>
            </div>

            <div class="steps-container">
                <!-- Step 1 -->
                <div class="step-item">
                    <div class="step-number">01</div>
                    <div class="step-content">
                        <div class="step-title">
                            <i class="fas fa-file-signature"></i>
                            Admission Assessment
                        </div>
                        <p class="step-desc">
                            The admission team reviews your application. Once verified, you will receive an evaluation showing your total fees (Standard: ₱4,975 per semester).
                        </p>
                        <div class="action-box">
                            <a href="<?php echo $root; ?>student/Modules/Admission/Result.php" class="btn-step">
                                <i class="fas fa-search"></i> Check My Status
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="step-item">
                    <div class="step-number">02</div>
                    <div class="step-content">
                        <div class="step-title">
                            <i class="fas fa-cash-register"></i>
                            Fee Payment (Settlement)
                        </div>
                        <p class="step-desc">
                            Pay your Downpayment, Half-payment, or Full-payment (₱4,975) through the school cashier or via our online payment gateway. Secure your proof of payment for verification.
                        </p>
                        <div class="action-box">
                            <a href="<?php echo $root; ?>student/Modules/Payments/Make-Payment.php" class="btn-step">
                                <i class="fas fa-credit-card"></i> Pay Online
                            </a>
                            <a href="<?php echo $root; ?>student/Modules/Payments/Balance.php" class="btn-step">
                                <i class="fas fa-calculator"></i> View My Balance
                            </a>
                        </div>
                        <div class="notice-card">
                            <i class="fas fa-info-circle" style="margin-top: 3px;"></i>
                            <div>
                                <strong>Important:</strong> Keep a clear photo or screenshot of your receipt. You will need to upload this in the next step if paying online.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="step-item">
                    <div class="step-number">03</div>
                    <div class="step-content">
                        <div class="step-title">
                            <i class="fas fa-upload"></i>
                            Document & Receipt Submission
                        </div>
                        <p class="step-desc">
                            Upload your proof of payment along with other requirements (PSA, Form 138, etc.). The admission team will verify these documents to unlock subject selection.
                        </p>
                        <div class="action-box">
                            <a href="<?php echo $root; ?>student/Modules/Admission/Requirements.php" class="btn-step">
                                <i class="fas fa-folder-plus"></i> Upload Documents
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="step-item">
                    <div class="step-number">04</div>
                    <div class="step-content">
                        <div class="step-title">
                            <i class="fas fa-book-open"></i>
                            Subject Selection & Enrollment
                        </div>
                        <p class="step-desc">
                            Once payment is verified, the "Enrollment" module becomes active. Select your subjects for the semester based on your course curriculum.
                        </p>
                        <div class="action-box">
                            <a href="<?php echo $root; ?>student/Modules/Enrollment/Subject-Selection.php" class="btn-step">
                                <i class="fas fa-plus-circle"></i> Selecting Subjects
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="step-item">
                    <div class="step-number">05</div>
                    <div class="step-content">
                        <div class="step-title">
                            <i class="fas fa-user-check"></i>
                            Official Verification
                        </div>
                        <p class="step-desc">
                            The Registrar/Dean's office will give the final approval. You are now officially enrolled! You can view your Class Schedule and download your Certificate of Enrollment.
                        </p>
                        <div class="action-box">
                            <a href="<?php echo $root; ?>student/Modules/Academic/Schedule.php" class="btn-step">
                                <i class="fas fa-calendar-alt"></i> View Schedule
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
