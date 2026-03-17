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
require_once '../../auth/Security.php';
checkRole(['student']);

require_once '../../Database/config.php';

$student_name = $_SESSION['fullname'] ?? 'Student';
$student_email = $_SESSION['email'] ?? '';
$enrollment_status = $_SESSION['enrollment_status'] ?? '';

// --- Fetch student financial data ---
$balance        = 0;
$total_fee      = 0;
$total_paid     = 0;
$pending_payments = 0;
$recent_payments  = [];

try {
    // Enrollment record
    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE email = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$student_email]);
    $enrollment = $stmt->fetch(PDO::FETCH_OBJ);

    $enrollment_id = 0;
    if ($enrollment && is_object($enrollment)) {
        // Force update to 4,975 if it's an old dummy record (approximate detection)
        if ($enrollment->total_fee > 6000 || $enrollment->total_fee <= 0) {
            $total_fee = 4975.00;
            
            // Calculate total paid from payments table for accurate balance
            $pay_sum_stmt = $pdo->prepare("SELECT SUM(amount) FROM payments WHERE (enrollment_id = ? OR description LIKE ?) AND (status = 'Completed' OR status = 'Verified' OR status = 'Verified (Online)')");
            $pay_sum_stmt->execute([$enrollment->enrollmentId ?? 0, "%$student_email%"]);
            $actual_paid = $pay_sum_stmt->fetchColumn() ?: 0;
            
            $balance = $total_fee - $actual_paid;
            
            // Sync this to the database silently for consistency
            $upd = $pdo->prepare("UPDATE enrollments SET tuition_fee = ?, misc_fee = 0, lab_fee = 0, total_fee = ?, balance = ? WHERE enrollmentId = ?");
            $upd->execute([$total_fee, $total_fee, $balance, $enrollment->enrollmentId]);
            $total_paid = $actual_paid;
        } else {
            $total_fee   = $enrollment->total_fee ?? 0;
            $balance     = $enrollment->balance ?? 0;
            $total_paid  = $total_fee - $balance;
        }
        $enrollment_id = $enrollment->enrollmentId ?? 0;
    }

    // Pending uploaded receipts
    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE (description LIKE ? OR enrollment_id = ?) AND status = 'Pending'");
    $stmt2->execute(["%{$student_email}%", $enrollment_id]);
    $pending_payments = $stmt2->fetchColumn() ?: 0;

    // Recent payments
    $stmt3 = $pdo->prepare(
        "SELECT p.*, e.first_name, e.last_name 
         FROM payments p 
         LEFT JOIN enrollments e ON p.enrollment_id = e.enrollmentId 
         WHERE e.email = ? OR p.description LIKE ?
         ORDER BY p.created_at DESC LIMIT 5"
    );
    $stmt3->execute([$student_email, "%{$student_email}%"]);
    $recent_payments = $stmt3->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    // silently fail
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cashier Dashboard - Student Portal</title>
    <link rel="icon" type="image/png" href="<?php echo $root; ?>Assets/image/logo.png">
    <link rel="shortcut icon" href="<?php echo $root; ?>Assets/image/logo.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $root; ?>Assets/css/theme.css">
</head>
<body>
    <?php include __DIR__ . '/Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/Components/Header.php'; ?>
        <div class="content-area">

            <!-- Banner -->
            <div class="banner">
                <h1><i class="fas fa-cash-register" style="margin-right:10px;"></i> My Financial Overview</h1>
                <p>Welcome, <?php echo htmlspecialchars($student_name); ?>! Here's a summary of your tuition fees, payments, and account balance.</p>
            </div>

            <!-- Enrollment Progress Step-by-Step Guidance -->
            <?php 
            // Show roadmap if not yet fully Enrolled
            if ($enrollment_status !== 'Enrolled'): 
            ?>
            <div style="background: linear-gradient(135deg, #1e40af, #3b82f6); color: white; padding: 30px; border-radius: 24px; margin-bottom: 30px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
                <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px;">
                    <div style="background: rgba(255,255,255,0.2); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fas fa-route"></i>
                    </div>
                    <div>
                        <h2 style="font-weight: 800; margin: 0;">Enrollment Roadmap 🎓</h2>
                        <p style="opacity: 0.9; font-size: 0.9rem;">Complete these steps to officially enroll in this institution.</p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px;">
                    <!-- Step 1 -->
                    <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 18px; border: 1px solid rgba(255,255,255,0.1); position: relative;">
                        <span style="position: absolute; top: 10px; right: 15px; font-weight: 800; font-size: 1.2rem; opacity: 0.3;">01</span>
                        <h4 style="margin: 0 0 5px 0; font-size: 0.95rem;">Pay & Seal</h4>
                        <p style="font-size: 0.75rem; line-height: 1.4; opacity: 0.8; margin-bottom: 12px;">Settle the per-sem fee (₱4,975). Secure your official e-receipt.</p>
                        <a href="<?php echo $root; ?>student/Modules/Payments/Make-Payment.php" style="display: block; text-align: center; padding: 8px; background: white; color: #2563eb; border-radius: 8px; font-weight: 700; font-size: 0.75rem; text-decoration: none;">
                            <i class="fas fa-credit-card" style="margin-right: 4px;"></i> Pay Now
                        </a>
                    </div>
                    
                    <!-- Step 2 -->
                    <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 18px; border: 1px solid rgba(255,255,255,0.1); position: relative;">
                        <span style="position: absolute; top: 10px; right: 15px; font-weight: 800; font-size: 1.2rem; opacity: 0.3;">02</span>
                        <h4 style="margin: 0 0 5px 0; font-size: 0.95rem;">Approval</h4>
                        <p style="font-size: 0.75rem; line-height: 1.4; opacity: 0.8; margin-bottom: 12px;">Wait for the admission team to verify your payment and details.</p>
                        <div style="display: block; text-align: center; padding: 8px; background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.6); border-radius: 8px; font-weight: 600; font-size: 0.75rem;">
                            <i class="fas fa-hourglass" style="margin-right: 4px;"></i> Wait Verification
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 18px; border: 1px solid rgba(255,255,255,0.1); position: relative;">
                        <span style="position: absolute; top: 10px; right: 15px; font-weight: 800; font-size: 1.2rem; opacity: 0.3;">03</span>
                        <h4 style="margin: 0 0 5px 0; font-size: 0.95rem;">Requirements</h4>
                        <p style="font-size: 0.75rem; line-height: 1.4; opacity: 0.8; margin-bottom: 12px;">Ensure all documents (PSA, Form 138) are correctly uploaded.</p>
                        <a href="<?php echo $root; ?>student/Modules/Admission/Requirements.php" style="display: block; text-align: center; padding: 8px; background: white; color: #2563eb; border-radius: 8px; font-weight: 700; font-size: 0.75rem; text-decoration: none;">
                            <i class="fas fa-upload" style="margin-right: 4px;"></i> Upload Files
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="stats-grid">
                <!-- Total Assessment -->
                <div class="stat-card">
                    <div class="stat-info">
                        <span>Total Assessment</span>
                        <h2 style="color: #1648bc;">₱<?php echo number_format($total_fee, 2); ?></h2>
                    </div>
                    <div class="stat-icon" style="background: rgba(22,72,188,0.1); color: #1648bc;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>

                <!-- Total Paid -->
                <div class="stat-card">
                    <div class="stat-info">
                        <span>Total Paid</span>
                        <h2 style="color: #059669;">₱<?php echo number_format($total_paid, 2); ?></h2>
                    </div>
                    <div class="stat-icon" style="background: rgba(5, 150, 105, 0.1); color: #059669;">
                        <i class="fas fa-check-double"></i>
                    </div>
                </div>

                <!-- Outstanding Balance -->
                <div class="stat-card">
                    <div class="stat-info">
                        <span>Outstanding Balance</span>
                        <h2 style="color: #dc2626;">₱<?php echo number_format($balance, 2); ?></h2>
                    </div>
                    <div class="stat-icon" style="background: rgba(220,38,38,0.1); color: #dc2626;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>

                <!-- Pending Receipts -->
                <div class="stat-card">
                    <div class="stat-info">
                        <span>Pending Verification</span>
                        <h2 style="color: #f97316;"><?php echo $pending_payments; ?></h2>
                    </div>
                    <div class="stat-icon" style="background: rgba(249, 115, 22, 0.1); color: #f97316;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Recent Transactions -->
                <div class="data-card">
                    <h3><i class="fas fa-history" style="color: #2563eb;"></i> Recent Payments</h3>
                    <?php if (!empty($recent_payments)): ?>
                    <table class="tx-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_payments as $tx): ?>
                            <tr>
                                <td style="color: var(--text-muted); font-size: 0.82rem;">
                                    <?php echo date('M d, Y', strtotime($tx->created_at)); ?>
                                </td>
                                <td><?php echo htmlspecialchars($tx->payment_method ?? 'N/A'); ?></td>
                                <td style="font-weight: 700; color: #2563eb;">₱<?php echo number_format($tx->amount, 2); ?></td>
                                <td>
                                    <?php
                                    $s = $tx->status ?? 'Pending';
                                    $cls = ($s === 'Completed' || $s === 'Verified') ? 'badge-success'
                                         : (($s === 'Pending') ? 'badge-pending' : 'badge-failed');
                                    ?>
                                    <span class="status-badge <?php echo $cls; ?>"><?php echo htmlspecialchars($s); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-receipt"></i>
                        <p>No payment records found yet.</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Actions -->
                <div class="action-card">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <a href="<?php echo $root; ?>student/Modules/Payments/Make-Payment.php" class="dashboard-action-item">
                        <i class="fas fa-credit-card"></i> <span>Make a Payment</span>
                    </a>
                    <a href="<?php echo $root; ?>student/Modules/Payments/Upload-Receipt.php" class="dashboard-action-item">
                        <i class="fas fa-upload"></i> <span>Upload Payment Receipt</span>
                    </a>
                    <a href="<?php echo $root; ?>student/Modules/Payments/Balance.php" class="dashboard-action-item">
                        <i class="fas fa-coins"></i> <span>View My Balance</span>
                    </a>
                    <a href="<?php echo $root; ?>student/Modules/Payments/History.php" class="dashboard-action-item">
                        <i class="fas fa-list-alt"></i> <span>Payment History</span>
                    </a>
                    <a href="<?php echo $root; ?>student/Modules/Payments/Print-Receipt.php" class="dashboard-action-item">
                        <i class="fas fa-print"></i> <span>Print Receipt</span>
                    </a>
                </div>
            </div>

        </div><!-- /content-area -->
    </div><!-- /main-wrapper -->

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body {
            background: var(--bg-color);
            color: var(--text-color);
            display: flex;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .content-area {
            padding: 35px;
            flex: 1;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ---- Banner ---- */
        .banner {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 60%, #06b6d4 100%);
            padding: 38px 40px;
            border-radius: 24px;
            color: white;
            margin-bottom: 28px;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.25);
            position: relative;
            overflow: hidden;
        }

        .banner::after {
            content: '';
            position: absolute;
            top: -60px;
            right: -40px;
            width: 280px;
            height: 280px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }

        .banner::before {
            content: '';
            position: absolute;
            bottom: -80px;
            right: 80px;
            width: 180px;
            height: 180px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .banner h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .banner p {
            font-size: 0.95rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
            max-width: 540px;
        }

        /* ---- Stats Grid ---- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--surface-color);
            padding: 24px;
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.09);
            border-color: var(--accent-color);
        }

        .stat-info span {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-info h2 {
            font-size: 1.7rem;
            font-weight: 800;
            margin-top: 6px;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.35rem;
            transition: 0.3s;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(-5deg);
        }

        /* ---- Dashboard Grid ---- */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        @media (max-width: 1100px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }

        /* ---- Data Card ---- */
        .data-card {
            background: var(--surface-color);
            padding: 28px;
            border-radius: 24px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }

        .data-card h3 {
            margin-bottom: 20px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ---- Transactions Table ---- */
        .tx-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .tx-table th {
            text-align: left;
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 0 14px 10px;
        }

        .tx-table tr:not(:first-child) {
            background: var(--hover-bg);
            transition: 0.2s;
        }

        .tx-table tr:not(:first-child):hover {
            background: rgba(37, 99, 235, 0.06);
        }

        .tx-table td {
            padding: 14px;
            font-size: 0.88rem;
            color: var(--text-color);
        }

        .tx-table tr td:first-child { border-radius: 12px 0 0 12px; }
        .tx-table tr td:last-child  { border-radius: 0 12px 12px 0; }

        .status-badge {
            padding: 5px 13px;
            border-radius: 20px;
            font-size: 0.73rem;
            font-weight: 700;
        }

        .badge-success  { background: rgba(34,197,94,0.12); color: #22c55e; }
        .badge-pending  { background: rgba(249,115,22,0.12); color: #f97316; }
        .badge-failed   { background: rgba(239,68,68,0.12);  color: #ef4444; }

        /* ---- Quick Actions Card ---- */
        .action-card {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            padding: 30px;
            border-radius: 24px;
            color: white;
            box-shadow: 0 12px 24px rgba(30, 64, 175, 0.2);
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .action-card h3 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dashboard-action-item {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: flex-start !important;
            padding: 18px 22px !important;
            border-radius: 16px !important;
            border: 1px solid rgba(255,255,255,0.18) !important;
            background: rgba(255,255,255,0.1) !important;
            color: white !important;
            cursor: pointer;
            text-align: left !important;
            font-size: 0.92rem !important;
            font-weight: 600 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            text-decoration: none !important;
            margin-bottom: 12px !important;
            width: 100% !important;
            min-width: 100% !important;
            height: auto !important;
            box-sizing: border-box !important;
            white-space: nowrap !important;
            overflow: visible !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .dashboard-action-item:last-child { margin-bottom: 0 !important; }

        .dashboard-action-item:hover {
            background: rgba(255,255,255,0.2) !important;
            transform: translateX(8px) !important;
            border-color: rgba(255,255,255,0.4) !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .dashboard-action-item i { 
            font-size: 1.2rem !important; 
            width: 28px !important; 
            text-align: center !important;
            margin-right: 15px !important;
            opacity: 1 !important;
            transition: transform 0.3s ease;
        }

        .dashboard-action-item:hover i {
            transform: scale(1.2);
        }

        /* ---- Empty state ---- */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 2.8rem;
            margin-bottom: 14px;
            opacity: 0.35;
            display: block;
        }

        .empty-state p { font-size: 0.9rem; }
    </style>
</body>
</html>
