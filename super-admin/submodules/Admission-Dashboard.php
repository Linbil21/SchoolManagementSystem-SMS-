<?php
session_start();
require_once '../../auth/Security.php';
require_once '../../Database/config.php';
checkRole(['superadmin']);

// Fetch Stats
$total_applicants = 0;
$approved_month = 0;
$pending_eval = 0;

try {
    $total_applicants = $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
    $approved_month = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status = 'Enrolled' AND MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn();
    $pending_eval = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status = 'Pending Review'")->fetchColumn();
} catch (PDOException $e) {
    // Table might be different or missing in some environments, fallback to 0
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel='icon' type='image/png' href='../../Assets/image/logo.png'>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Dashboard - Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/super-admin.css">
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 30px; letter-spacing: -1px;">Admission Dashboard</h1>
            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="stat-card" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <p style="color: #64748b; font-weight: 600; font-size: 0.9rem;">Total Applicants</p>
                    <h2 style="color: var(--primary); font-size: 2.5rem; font-weight: 800;"><?php echo number_format($total_applicants); ?></h2>
                </div>
                <div class="stat-card" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <p style="color: #64748b; font-weight: 600; font-size: 0.9rem;">Approved This Month</p>
                    <h2 style="color: #10b981; font-size: 2.5rem; font-weight: 800;"><?php echo number_format($approved_month); ?></h2>
                </div>
                <div class="stat-card" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <p style="color: #64748b; font-weight: 600; font-size: 0.9rem;">Pending Evaluation</p>
                    <h2 style="color: #f59e0b; font-size: 2.5rem; font-weight: 800;"><?php echo number_format($pending_eval); ?></h2>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
