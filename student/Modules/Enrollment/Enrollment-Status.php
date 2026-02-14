<?php
session_start();
// Security check
require_once '../../../auth/Security.php';
checkRole(['student']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Status</title>
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

        .status-tracker {
            background: white;
            border-radius: 24px;
            padding: 40px 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 40px;
            overflow-x: auto;
        }

        .steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px 0;
        }

        .steps::before {
            content: '';
            position: absolute;
            top: 45px;
            left: 0;
            width: 100%;
            height: 4px;
            background: #e2e8f0;
            z-index: 0;
        }

        .step-item {
            position: relative;
            z-index: 1;
            text-align: center;
            width: 120px;
        }

        .step-circle {
            width: 50px;
            height: 50px;
            background: white;
            border: 3px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 700;
            color: #94a3b8;
            transition: all 0.3s;
        }

        .step-item.completed .step-circle {
            border-color: #22c55e;
            background: #22c55e;
            color: white;
        }

        .step-item.active .step-circle {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.1);
        }

        .step-label {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }

        .step-item.active .step-label {
            color: var(--primary);
            font-weight: 700;
        }

        .info-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }

        .status-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #ea580c;
        }
        
        .status-icon.success { color: #10b981; }
        .status-icon.pending { color: #f59e0b; }
        .status-icon.error { color: #ef4444; }

        .info-card h2 {
            margin-bottom: 10px;
            color: #1e293b;
        }

        .info-card p {
            color: #64748b;
            line-height: 1.6;
        }
    </style>
</head>

<?php
// Determine status
$status_steps = [
    'Pending Review' => 1,
    'Assessment' => 2,
    'Pending Payment' => 3,
    'Validation' => 4,
    'Enrolled' => 5
];

$current_status = 'Validation'; // Default / Fallback
$status_message = '';
$icon_class = 'fa-search-dollar status-icon pending';
$title_text = 'Payment Under Validation';
$desc_text = 'We have received your payment proof and it is currently being verified by the Cashier\'s Office. This process usually takes 24-48 hours.';

require_once '../../Database/config.php';
if (isset($_SESSION['student_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT status FROM enrollments WHERE student_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$_SESSION['student_id']]);
        $enrollment = $stmt->fetch();
        if ($enrollment) {
            $current_status = $enrollment['status'];
        }
    } catch (PDOException $e) {
        // Fallback to default
    }
}

// Logic for messages
if ($current_status == 'Enrolled') {
    $icon_class = 'fa-check-circle status-icon success';
    $title_text = 'Officially Enrolled!';
    $desc_text = 'Congratulations! You are now officially enrolled for the upcoming semester. You can now view your schedule and grades.';
} elseif ($current_status == 'Pending Payment') {
    $icon_class = 'fa-credit-card status-icon pending';
    $title_text = 'Pending Payment';
    $desc_text = 'Please settle your tuition fee balance or upload your proof of payment to proceed with enrollment.';
} elseif ($current_status == 'Assessment') {
    $icon_class = 'fa-file-invoice status-icon';
    $title_text = 'Under Assessment';
    $desc_text = 'Your subjects are currently being assessed by the registrar. Please wait for the assessment fee breakdown.';
}

$current_step_index = $status_steps[$current_status] ?? 4;
?>
<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <h1 class="page-title" style="margin-bottom: 30px; font-weight: 800; color: #1e293b;">Enrollment Status</h1>

            <div class="status-tracker">
                <div class="steps">
                    <?php 
                    $steps = [
                        1 => 'Subjects', 
                        2 => 'Assessment', 
                        3 => 'Payment', 
                        4 => 'Validation', 
                        5 => 'Enrolled'
                    ]; 
                    
                    foreach($steps as $idx => $label): 
                        $class = '';
                        if ($idx < $current_step_index) $class = 'completed';
                        elseif ($idx == $current_step_index) $class = 'active';
                        
                        // Icon or Number logic
                        $content = ($idx < $current_step_index) ? '<i class="fas fa-check"></i>' : $idx;
                    ?>
                        <div class="step-item <?php echo $class; ?>">
                            <div class="step-circle"><?php echo $content; ?></div>
                            <span class="step-label"><?php echo $label; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="info-card">
                <i class="fas <?php echo $icon_class; ?>"></i>
                <h2><?php echo $title_text; ?></h2>
                <p><?php echo $desc_text; ?></p>
                <div style="margin-top: 25px;">
                    <button onclick="window.location.reload()"
                        style="background: #f1f5f9; color: #475569; border: none; padding: 12px 25px; border-radius: 10px; cursor: pointer; font-weight: 600;">Check
                        Status Update</button>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
