<?php
session_start();
require_once '../../Database/config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: ../../Auth/log-reg.php");
    exit();
}

// Fetch Statistics
try {
    $stats = [
        'total_enroll_req' => $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn(),
        'enrolled' => $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status = 'Enrolled'")->fetchColumn(),
        'pending' => $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status = 'Pending Review'")->fetchColumn(),
        'rejected' => $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status = 'Rejected'")->fetchColumn(),
        'total_revenue' => $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'Completed'")->fetchColumn() ?? 0
    ];
    
    // Recent Payments for Report Table
    $stmt = $pdo->query("SELECT p.*, e.first_name, e.last_name FROM payments p JOIN enrollments e ON p.enrollment_id = e.enrollmentId ORDER BY p.created_at DESC LIMIT 10");
    $recent_reports = $stmt->fetchAll();
} catch (PDOException $e) {
    $stats = ['total_enroll_req' => 0, 'enrolled' => 0, 'pending' => 0, 'rejected' => 0, 'total_revenue' => 0];
    $recent_reports = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Stats - SMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/admin.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            border: 1px solid var(--border-color);
            transition: 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card i { font-size: 2rem; color: var(--primary-blue); opacity: 0.2; float: right; }
        .stat-label { color: var(--text-gray); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; }
        .stat-value { font-size: 1.8rem; font-weight: 700; color: var(--text-dark); margin-top: 10px; }
    </style>
</head>

<body>
    <?php include '../Components/Side-bar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/Head-bar.php'; ?>
        <div class="content-area">
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-label">Total Applications</div>
                    <div class="stat-value"><?php echo $stats['total_enroll_req']; ?></div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-check-double"></i>
                    <div class="stat-label">Total Enrolled</div>
                    <div class="stat-value" style="color:#10b981;"><?php echo $stats['enrolled']; ?></div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <div class="stat-label">Pending Review</div>
                    <div class="stat-value" style="color:#f59e0b;"><?php echo $stats['pending']; ?></div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-coins"></i>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value" style="color:var(--primary-blue);">₱<?php echo number_format($stats['total_revenue'], 2); ?></div>
                </div>
            </div>

            <div class="table-container">
                <div class="table-header">
                    <h2>Financial Reports Summary</h2>
                    <button class="btn-view" onclick="window.print()"><i class="fas fa-print"></i> Print Report</button>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Transaction</th>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recent_reports) > 0): ?>
                                <?php foreach ($recent_reports as $r): ?>
                                    <tr>
                                        <td class="ref-code"><?php echo $r->transaction_id; ?></td>
                                        <td class="student-name"><?php echo $r->last_name . ", " . $r->first_name; ?></td>
                                        <td style="font-weight:700;">₱<?php echo number_format($r->amount, 2); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($r->created_at)); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" style="text-align:center; padding:50px;">No records data.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>