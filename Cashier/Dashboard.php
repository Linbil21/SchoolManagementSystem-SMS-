<?php
session_start();
require_once '../auth/Security.php';
require_once '../Database/config.php';
checkRole(['cashier', 'superadmin']);
$role = $_SESSION['role'];

// Fetch Statistics
$total_collections = $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'Completed'")->fetchColumn() ?: 0;
$pending_verification = $pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'Pending'")->fetchColumn() ?: 0;
$new_assessments = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE DATE(created_at) = CURDATE()")->fetchColumn() ?: 0;

// New Financial Estimates
$total_revenue_estimate = $pdo->query("SELECT SUM(total_fee) FROM enrollments")->fetchColumn() ?: 0;
$total_outstanding = $pdo->query("SELECT SUM(balance) FROM enrollments")->fetchColumn() ?: 0;

// Fetch Recent Transactions
$stmt = $pdo->query("SELECT p.*, e.first_name, e.last_name 
                     FROM payments p 
                     JOIN enrollments e ON p.enrollment_id = e.enrollmentId 
                     ORDER BY p.created_at DESC LIMIT 5");
$recent_transactions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard - SMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $root_path; ?>Assets/css/theme.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

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
            background: var(--bg-color);
        }

        .content-area {
            padding: 30px;
            flex: 1;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .banner {
            background: linear-gradient(135deg, var(--accent-color) 0%, #3b82f6 50%, #06b6d4 100%);
            padding: 40px;
            border-radius: 24px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(22, 72, 188, 0.2);
            position: relative;
            overflow: hidden;
        }

        .banner::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .banner h1 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .banner p {
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: var(--surface-color);
            padding: 25px;
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border-color: var(--accent-color);
        }

        .stat-info span {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-info h2 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-color);
            margin-top: 5px;
        }

        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.4rem;
            background: var(--hover-bg);
            color: var(--accent-color);
            transition: 0.3s;
        }

        .stat-card:hover .stat-icon {
            background: var(--accent-color);
            color: white;
        }

        .dashboard-grid {
            margin-top: 30px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }

        @media (max-width: 1200px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }

        .data-card {
            background: var(--surface-color);
            padding: 25px;
            border-radius: 24px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .data-card h3 {
            margin-bottom: 20px;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .transaction-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .transaction-table th {
            text-align: left;
            color: var(--text-muted);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 0 15px 10px;
        }

        .transaction-table tr:not(:first-child) {
            background: var(--hover-bg);
            transition: 0.2s;
        }

        .transaction-table td {
            padding: 15px;
            color: var(--text-color);
            font-size: 0.9rem;
        }

        .transaction-table tr td:first-child { border-radius: 12px 0 0 12px; }
        .transaction-table tr td:last-child { border-radius: 0 12px 12px 0; }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .status-success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }

        .action-card {
            background: linear-gradient(135deg, var(--accent-color) 0%, #0a2e7a 100%);
            padding: 25px;
            border-radius: 24px;
            color: white;
            height: fit-content;
        }

        .quick-actions-list {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
            color: white;
            cursor: pointer;
            text-align: left;
            font-size: 0.9rem;
            font-weight: 500;
            transition: 0.3s;
            text-decoration: none;
        }

        .action-btn:hover {
            background: rgba(255,255,255,0.15);
            transform: translateX(5px);
        }

        .action-btn i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include 'Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include 'Components/header.php'; ?>
        <div class="content-area">
            <div class="banner">
                <h1>Finance Overview</h1>
                <p>Welcome back! Monitor and manage institutional financial records with precision.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <span>Projected Revenue</span>
                        <h2 style="color: #1648bc;">₱<?php echo number_format($total_revenue_estimate, 2); ?></h2>
                    </div>
                    <div class="stat-icon" style="background: rgba(22, 72, 188, 0.1); color: #1648bc;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <span>Total Collections</span>
                        <h2 style="color: #059669;">₱<?php echo number_format($total_collections, 2); ?></h2>
                    </div>
                    <div class="stat-icon" style="background: rgba(5, 150, 105, 0.1); color: #059669;">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <span>Outstanding Bal</span>
                        <h2 style="color: #dc2626;">₱<?php echo number_format($total_outstanding, 2); ?></h2>
                    </div>
                    <div class="stat-icon" style="background: rgba(220, 38, 38, 0.1); color: #dc2626;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <span>Pending Verification</span>
                        <h2 style="color: #f97316;"><?php echo $pending_verification; ?></h2>
                    </div>
                    <div class="stat-icon" style="background: rgba(249, 115, 22, 0.1); color: #f97316;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="data-card">
                    <h3><i class="fas fa-history"></i> Recent Transactions</h3>
                    <table class="transaction-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_transactions): ?>
                                <?php foreach ($recent_transactions as $tx): ?>
                                <tr>
                                    <td style="font-weight: 600;"><?php echo htmlspecialchars($tx->first_name . ' ' . $tx->last_name); ?></td>
                                    <td><?php echo htmlspecialchars($tx->payment_method); ?></td>
                                    <td style="font-weight: 700; color: var(--accent-color);">₱<?php echo number_format($tx->amount, 2); ?></td>
                                    <td><span class="status-badge status-success"><?php echo $tx->status; ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #64748b;">No recent transactions found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($role === 'cashier'): ?>
                <div class="action-card">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="quick-actions-list">
                        <a href="<?php echo $root_path; ?>Cashier/Modules/Walk-in-Payments.php" class="action-btn">
                            <i class="fas fa-plus-circle"></i> Record Walk-in
                        </a>
                        <a href="<?php echo $root_path; ?>Cashier/Submodules/Payment-Status.php" class="action-btn">
                            <i class="fas fa-search"></i> Find Student Account
                        </a>
                        <a href="<?php echo $root_path; ?>Cashier/Modules/Issue-Receipt.php" class="action-btn">
                            <i class="fas fa-file-invoice-dollar"></i> Generate Billing
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <div class="data-card" style="background: #eff6ff; border: 1px dashed #3b82f6;">
                    <h3 style="color: #1e40af;"><i class="fas fa-info-circle"></i> View Only Mode</h3>
                    <p style="color: #1e40af; font-size: 0.9rem;">You are viewing the finance dashboard with read-only permissions. Actions are reserved for the Cashier Office.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>