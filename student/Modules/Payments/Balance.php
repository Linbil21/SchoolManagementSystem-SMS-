<?php
session_start();

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

// Security check
require_once '../../../auth/Security.php';
checkRole(['student']);

// Admission Approval Check
$enrollment_status = $_SESSION['enrollment_status'] ?? 'Pending';
$allowed_payment_statuses = ['Pending', 'Pending Payment', 'Validation', 'Enrolled'];
if (!in_array($enrollment_status, $allowed_payment_statuses)) {
    header("Location: " . $root . "student/Modules/Admission/Result.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Balance</title>
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

        .balance-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 40px;
            border-radius: 24px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.1);
        }
        
        .balance-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .balance-label {
            font-size: 1rem;
            color: #94a3b8;
            font-weight: 500;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .balance-amount {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: baseline;
            gap: 10px;
        }
        
        .currency {
             font-size: 2rem;
             color: #94a3b8;
             font-weight: 500;
        }

        .pay-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }
        
        .pay-btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .breakdown-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .tag-projected {
            background: #fff7ed;
            color: #ea580c;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-left: 10px;
            border: 1px solid #ffedd5;
        }

        .fee-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }
        
        .fee-label {
            color: #64748b;
        }
        
        .fee-val {
            font-weight: 600;
            color: #1e293b;
        }
        
        .fee-row.total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px dashed #e2e8f0;
            font-size: 1.1rem;
            font-weight: 700;
        }
        
        .fee-row.total .fee-val {
            color: var(--primary);
        }

        .info-banner {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            padding: 15px 20px;
            border-radius: 12px;
            color: #0369a1;
            font-size: 0.85rem;
            margin-bottom: 25px;
            display: flex;
            gap: 12px;
            align-items: center;
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <?php
            require_once '../../../Database/config.php';
            $student_email = $_SESSION['email'] ?? 'student@sms.com';

            $balance = 0;
            $total_fee = 0;
            $is_projected = true;
            $tuition = 0;
            $misc = 0;
            $lab = 0;
            $course_name = "N/A";
            $enrolled = false;

            try {
                // 1. Check for official enrollment record
                $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE email = ? ORDER BY created_at DESC LIMIT 1");
                $stmt->execute([$student_email]);
                $enrollment = $stmt->fetch();

                if ($enrollment) {
                    $balance = $enrollment->balance;
                    $total_fee = $enrollment->total_fee;
                    $tuition = $enrollment->tuition_fee ?? 4975.00;
                    $misc = $enrollment->misc_fee ?? 0;
                    $lab = $enrollment->lab_fee ?? 0;
                    $is_projected = ($enrollment->status === 'Pending Review' || $enrollment->status === 'Pending' || $enrollment->status === 'Validation');
                    $enrolled = true;
                } else {
                    // 2. No enrollment yet: check admission application for course choice
                    $app_stmt = $pdo->prepare("SELECT a.*, c.course_name FROM admission_applications a 
                                               LEFT JOIN courses c ON (a.preferred_course_1 = CAST(c.courseId AS CHAR) OR a.preferred_course_1 = c.course_name)
                                               WHERE a.email = ? LIMIT 1");
                    $app_stmt->execute([$student_email]);
                    $app = $app_stmt->fetch();

                    if ($app) {
                        $course_name = $app->course_name ?: $app->preferred_course_1;
                        // Mock assessment based on standard rates
                        $tuition = 4975; 
                        $misc = 0;
                        $lab = 0;
                        $total_fee = $tuition + $misc + $lab;
                        
                        // Calculate balance by checking payments already made via email fallback
                        $pay_stmt = $pdo->prepare("SELECT SUM(amount) FROM payments WHERE (description LIKE ? OR enrollment_id IS NULL) AND status IN ('Completed', 'Verified')");
                        $pay_stmt->execute(["%$student_email%"]);
                        $payments_made = $pay_stmt->fetchColumn() ?: 0;
                        
                        $balance = $total_fee - $payments_made;
                        $is_projected = true;
                        $enrolled = true; // Set true to show the breakdown even if projected
                    }
                }
            } catch (PDOException $e) {
                // error fetching
            }

            $paid = $total_fee - $balance;
            ?>
            
            <div class="page-header">
                <h1 class="page-title">Accounts Balance</h1>
                <p style="color: var(--secondary); font-size: 0.9rem;">View your current financial standing and fee breakdowns.</p>
            </div>

            <?php if ($is_projected): ?>
            <div class="info-banner">
                <i class="fas fa-magic" style="font-size: 1.2rem;"></i>
                <div>
                    <strong>Projected Assessment</strong><br>
                    This is a preliminary estimation based on your preferred course choice. Final charges will be updated upon official enrollment approval.
                </div>
            </div>
            <?php endif; ?>
            
            <div class="balance-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                    <div class="balance-label">Current Outstanding Balance</div>
                    <?php if ($is_projected): ?>
                        <span class="tag-projected" style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2);">Pending Approval</span>
                    <?php endif; ?>
                </div>
                <div class="balance-amount">
                    <span class="currency">₱</span>
                    <?php echo number_format($balance, 2); ?>
                </div>
                <div style="margin-bottom: 25px; font-size: 0.9rem; color: rgba(255,255,255,0.7);">
                    <i class="fas fa-clock"></i> Last updated: <?php echo date('F d, Y • h:i A'); ?>
                </div>
                <div style="display: flex; gap: 12px;">
                    <a href="<?php echo $root; ?>student/Modules/Payments/History.php" class="pay-btn" style="background: white; color: #1e293b;">
                        <i class="fas fa-wallet"></i> My Receipts
                    </a>
                    <a href="<?php echo $root; ?>student/Dashboard.php" class="pay-btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3);">
                        Make Payment
                    </a>
                </div>
            </div>

            <div class="breakdown-card">
                <h3 class="section-title">
                    <i class="fas fa-file-invoice-dollar" style="color: var(--primary); margin-right: 8px;"></i>
                    Fee Breakdown (SY <?php echo date('Y'); ?>-<?php echo date('Y') + 1; ?>)
                </h3>
                
                <?php if ($enrolled): ?>
                <div class="fee-row">
                    <span class="fee-label">Tuition Fee (Regular)</span>
                    <span class="fee-val">₱<?php echo number_format($tuition, 2); ?></span>
                </div>
                <div class="fee-row">
                    <span class="fee-label">Miscellaneous Fees</span>
                    <span class="fee-val">₱<?php echo number_format($misc, 2); ?></span>
                </div>
                <div class="fee-row">
                    <span class="fee-label">Laboratory & Other Fees</span>
                    <span class="fee-val">₱<?php echo number_format($lab, 2); ?></span>
                </div>
                
                <div style="margin: 15px 0; border-bottom: 1px solid #f1f5f9;"></div>

                <div class="fee-row">
                    <span class="fee-label" style="color: #1e293b; font-weight: 600;">Total Assessment</span>
                    <span class="fee-val" style="color: #1e293b; font-weight: 700;">₱<?php echo number_format($total_fee, 2); ?></span>
                </div>

                <div class="fee-row">
                    <span class="fee-label">Less: Payments Made</span>
                    <span class="fee-val" style="color: #10b981;">- ₱<?php echo number_format($paid, 2); ?></span>
                </div>
                
                <div class="fee-row total">
                    <span class="fee-label">Current Outstanding Balance</span>
                    <span class="fee-val">₱<?php echo number_format($balance, 2); ?></span>
                </div>
                <?php else: ?>
                <div style="text-align:center; padding: 20px; color: var(--secondary);">
                    No active enrollment record or application found to display breakdown.
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</body>

</html>
