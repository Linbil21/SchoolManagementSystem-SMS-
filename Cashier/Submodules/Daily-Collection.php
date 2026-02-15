<?php
session_start();
require_once '../../auth/Security.php';
checkRole(['cashier', 'superadmin']);
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Collection - Cashier</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #1648bc;
            --secondary: #475569;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-sub: #64748b;
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
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            margin-bottom: 30px;
            animation: fadeIn 0.5s ease-out;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: var(--text-sub);
            font-size: 0.95rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2.5rem;
            opacity: 0.1;
            color: var(--primary);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 10px 0 5px;
        }

        .stat-label {
            color: var(--text-sub);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .chart-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .table-card {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid #f1f5f9;
            color: var(--secondary);
            font-size: 0.85rem;
            font-weight: 600;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--text-main);
            font-size: 0.9rem;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .bg-success {
            background: #dcfce7;
            color: #166534;
        }

        .bg-warning {
            background: #fef9c3;
            color: #854d0e;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1024px) {
            .chart-section {
                grid-template-columns: 1fr;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 5px;
            border: 2px solid #f1f5f9;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Daily Collection</h1>
                <p class="page-subtitle">
                    <?php echo date('l, F j, Y'); ?> - Overview of today's financial activity
                </p>
            </div>

            <?php
            require_once '../../Database/config.php';
            
            // Metrics for TODAY
            $today = date('Y-m-d');
            $total_today = $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'Completed' AND DATE(created_at) = '$today'")->fetchColumn() ?: 0;
            $count_today = $pdo->query("SELECT COUNT(*) FROM payments WHERE DATE(created_at) = '$today'")->fetchColumn() ?: 0;
            $cash_today = $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'Completed' AND payment_method = 'Cash' AND DATE(created_at) = '$today'")->fetchColumn() ?: 0;
            $digital_today = $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'Completed' AND payment_method != 'Cash' AND DATE(created_at) = '$today'")->fetchColumn() ?: 0;
            
            // Previous day for comparison
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $total_yesterday = $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'Completed' AND DATE(created_at) = '$yesterday'")->fetchColumn() ?: 1; // Prevent div by zero
            $change_percent = (($total_today - $total_yesterday) / $total_yesterday) * 100;
            ?>
            <!-- Key Metrics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-coins stat-icon"></i>
                    <p class="stat-label">Total Collected Today</p>
                    <h3 class="stat-value">₱<?php echo number_format($total_today, 2); ?></h3>
                    <span style="color: <?php echo $change_percent >= 0 ? '#22c55e' : '#ef4444'; ?>; font-size: 0.85rem;">
                        <i class="fas fa-arrow-<?php echo $change_percent >= 0 ? 'up' : 'down'; ?>"></i> 
                        <?php echo abs(round($change_percent, 1)); ?>% vs yesterday
                    </span>
                </div>
                <div class="stat-card">
                    <i class="fas fa-receipt stat-icon"></i>
                    <p class="stat-label">Transactions Processed</p>
                    <h3 class="stat-value"><?php echo $count_today; ?></h3>
                    <span style="color: #22c55e; font-size: 0.85rem;">Activity logged today</span>
                </div>
                <div class="stat-card">
                    <i class="fas fa-wallet stat-icon"></i>
                    <p class="stat-label">Cash on Hand</p>
                    <h3 class="stat-value">₱<?php echo number_format($cash_today, 2); ?></h3>
                    <span style="color: #64748b; font-size: 0.85rem;">From walk-in payments</span>
                </div>
                <div class="stat-card">
                    <i class="fas fa-credit-card stat-icon"></i>
                    <p class="stat-label">Digital Payments</p>
                    <h3 class="stat-value">₱<?php echo number_format($digital_today, 2); ?></h3>
                    <span style="color: #64748b; font-size: 0.85rem;">Bank & E-wallets</span>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="chart-section">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Hourly Collection Trend</h3>
                    </div>
                    <canvas id="hourlyChart" height="120"></canvas>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Payment Methods</h3>
                    </div>
                    <canvas id="methodChart" height="200"></canvas>
                </div>
            </div>

            <!-- Recent Transactions Table -->
            <div class="table-card">
                <div class="chart-header">
                    <h3 class="chart-title">Today's Transactions</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Receipt No.</th>
                            <th>Student</th>
                            <th>Description</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $stmt = $pdo->query("
                                SELECT p.*, e.first_name, e.last_name 
                                FROM payments p 
                                JOIN enrollments e ON p.enrollment_id = e.enrollmentId 
                                WHERE DATE(p.created_at) = '$today'
                                ORDER BY p.created_at DESC
                            ");
                            $today_txns = $stmt->fetchAll();
                            
                            if (empty($today_txns)) {
                                echo '<tr><td colspan="6" style="text-align: center; padding: 20px;">No transactions found for today.</td></tr>';
                            } else {
                                foreach ($today_txns as $row) {
                                    $name = htmlspecialchars($row->first_name . " " . $row->last_name);
                                    $ref = htmlspecialchars($row->transaction_id);
                                    $desc = htmlspecialchars($row->description ?? "Fee Payment");
                                    $method = htmlspecialchars($row->payment_method);
                                    $amount = number_format($row->amount, 2);
                                    $status = $row->status;
                                    $badge = ($status === 'Completed' || $status === 'Verified') ? 'bg-success' : 'bg-warning';

                                    echo "<tr>
                                            <td>$ref</td>
                                            <td>$name</td>
                                            <td>$desc</td>
                                            <td>$method</td>
                                            <td style='font-weight: 600;'>₱$amount</td>
                                            <td><span class='badge $badge'>$status</span></td>
                                        </tr>";
                                }
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='6'>Error loading transactions</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Hourly Collection Trend Chart
        const ctxHourly = document.getElementById('hourlyChart').getContext('2d');
        new Chart(ctxHourly, {
            type: 'line',
            data: {
                labels: ['8AM', '9AM', '10AM', '11AM', '12PM', '1PM', '2PM', '3PM', '4PM'],
                datasets: [{
                    label: 'Collection Amount (₱)',
                    data: [5000, 15000, 22000, 18000, 8000, 25000, 30000, 20000, 10000],
                    borderColor: '#1648bc',
                    backgroundColor: 'rgba(22, 72, 188, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5] }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // Payment Method Pie Chart
        const ctxMethod = document.getElementById('methodChart').getContext('2d');
        new Chart(ctxMethod, {
            type: 'doughnut',
            data: {
                labels: ['Cash', 'GCash', 'Bank Transfer', 'Cheque'],
                datasets: [{
                    data: [35, 25, 30, 10],
                    backgroundColor: [
                        '#1648bc',
                        '#3b82f6',
                        '#60a5fa',
                        '#93c5fd'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                },
                cutout: '70%'
            }
        });
    </script>
</body>

</html>