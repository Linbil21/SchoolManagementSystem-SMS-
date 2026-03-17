// Check if user is logged in
require_once '../auth/Security.php';
require_once '../Database/config.php';
checkRole(['admin', 'superadmin']);

$role = $_SESSION['role'];

// Fetch Real Statistics
try {
    $total_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn() ?: 0;
    $pending_review = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status = 'Pending Review'")->fetchColumn() ?: 0;
    $pending_payment = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status = 'Pending Payment'")->fetchColumn() ?: 0;
    $enrolled = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status = 'Enrolled'")->fetchColumn() ?: 0;
    
    // Recent Payments
    $stmt = $pdo->query("SELECT p.*, e.first_name, e.last_name 
                         FROM payments p 
                         LEFT JOIN enrollments e ON p.enrollment_id = e.enrollmentId 
                         ORDER BY p.created_at DESC LIMIT 5");
    $recent_payments = $stmt->fetchAll();
} catch (PDOException $e) {
    $total_students = $pending_review = $pending_payment = $enrolled = 0;
    $recent_payments = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Enrollment System</title>
    <link rel="icon" type="image/x-icon" href="../Assets/image/logo.png">
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="Assets/Dashboard.css">
    <style>
        .recent-box {
            background: white;
            padding: 25px;
            border-radius: 24px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            margin-top: 30px;
        }
        .recent-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .recent-table th {
            text-align: left;
            padding: 12px;
            color: #718096;
            font-size: 0.85rem;
            text-transform: uppercase;
            border-bottom: 2px solid #f1f5f9;
        }
        .recent-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
        }
        .status-pill {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php include 'Components/Side-bar.php'; ?>

    <div class="main-wrapper">
        <!-- Head-bar -->
        <?php include 'Components/Head-bar.php'; ?>

        <div class="content-area">
            
            <!-- Welcome Banner -->
            <div class="banner">
                <div class="banner-content">
                    <h1 style="font-size: 2.2rem; font-weight: 800; color: white;">Welcome Back, <span style="color: #fbbf24;">Admin</span>!</h1>
                    <p style="font-size: 1.1rem; opacity: 0.9; margin-top: 10px;">Monitor and manage student enrollments, accounts, and reports from your control center.</p>
                </div>
                <div class="banner-img">
                    <i class="fas fa-graduation-cap" style="font-size: 5rem; opacity: 0.3;"></i>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 25px; margin-bottom: 35px;">
                <div class="stat-card card-total" style="background: white; padding: 25px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border-left: 5px solid #1648bc; display: flex; justify-content: space-between; align-items: center;">
                    <div class="stat-info">
                        <span style="font-size: 0.85rem; color: #718096; font-weight: 600;">Total Students</span>
                        <h2 style="font-size: 1.8rem; font-weight: 800; color: #2d3748; margin-top: 5px;"><?php echo number_format($total_students); ?></h2>
                    </div>
                    <div class="stat-icon" style="width: 50px; height: 50px; background: #ebf8ff; color: #4299e1; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                
                <div class="stat-card card-pending-review" style="background: white; padding: 25px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border-left: 5px solid #ecc94b; display: flex; justify-content: space-between; align-items: center;">
                    <div class="stat-info">
                        <span style="font-size: 0.85rem; color: #718096; font-weight: 600;">Pending Review</span>
                        <h2 style="font-size: 1.8rem; font-weight: 800; color: #2d3748; margin-top: 5px;"><?php echo number_format($pending_review); ?></h2>
                    </div>
                    <div class="stat-icon" style="width: 50px; height: 50px; background: #fffaf0; color: #ecc94b; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>

                <div class="stat-card card-pending-payment" style="background: white; padding: 25px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border-left: 5px solid #ed8936; display: flex; justify-content: space-between; align-items: center;">
                    <div class="stat-info">
                        <span style="font-size: 0.85rem; color: #718096; font-weight: 600;">Pending Payment</span>
                        <h2 style="font-size: 1.8rem; font-weight: 800; color: #2d3748; margin-top: 5px;"><?php echo number_format($pending_payment); ?></h2>
                    </div>
                    <div class="stat-icon" style="width: 50px; height: 50px; background: #fff5eb; color: #ed8936; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>

                <div class="stat-card card-enrolled" style="background: white; padding: 25px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border-left: 5px solid #48bb78; display: flex; justify-content: space-between; align-items: center;">
                    <div class="stat-info">
                        <span style="font-size: 0.85rem; color: #718096; font-weight: 600;">Enrolled</span>
                        <h2 style="font-size: 1.8rem; font-weight: 800; color: #2d3748; margin-top: 5px;"><?php echo number_format($enrolled); ?></h2>
                    </div>
                    <div class="stat-icon" style="width: 50px; height: 50px; background: #f0fff4; color: #48bb78; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section" style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px; min-height: 400px;">
                <div class="chart-container" style="background: white; padding: 25px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                    <div class="chart-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">Enrollment Trends</h3>
                        <select class="chart-filter" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.85rem; outline: none;">
                            <option>Last 6 Months</option>
                            <option>Last Year</option>
                        </select>
                    </div>
                    <div class="chart-wrapper" style="flex: 1; min-height: 300px; position: relative;">
                        <canvas id="enrollmentTrendChart"></canvas>
                    </div>
                </div>

                <div class="chart-container" style="background: white; padding: 25px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                    <div class="chart-header" style="margin-bottom: 20px;">
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">Course Distribution</h3>
                    </div>
                    <div class="chart-wrapper" style="flex: 1; min-height: 300px; position: relative;">
                        <canvas id="courseDistChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Payments Section -->
            <div class="recent-box">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b;"><i class="fas fa-receipt" style="color: #4c51bf; margin-right: 10px;"></i> Recent Student Payments</h3>
                <table class="recent-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_payments): ?>
                            <?php foreach ($recent_payments as $pay): ?>
                                <tr>
                                    <td style="font-weight: 600;"><?php echo htmlspecialchars($pay->first_name . ' ' . $pay->last_name); ?></td>
                                    <td style="font-weight: 700; color: #2f855a;">₱<?php echo number_format($pay->amount, 2); ?></td>
                                    <td><?php echo htmlspecialchars($pay->payment_method); ?></td>
                                    <td>
                                        <?php 
                                            $pill_style = "background: #ebf8ff; color: #3182ce;";
                                            if($pay->status == 'Completed' || $pay->status == 'Verified') $pill_style = "background: #f0fff4; color: #38a169;";
                                            if($pay->status == 'Rejected') $pill_style = "background: #fff5f5; color: #e53e3e;";
                                        ?>
                                        <span class="status-pill" style="<?php echo $pill_style; ?>"><?php echo $pay->status; ?></span>
                                    </td>
                                    <td style="color: #a0aec0; font-size: 0.8rem;"><?php echo date('M d, Y', strtotime($pay->created_at)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center; padding: 30px; color: #a0aec0;">No recent payments found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div style="margin-top: 15px; text-align: right;">
                    <a href="Modules/Payments-Fees.php" style="color: #4a5568; font-size: 0.85rem; font-weight: 600; text-decoration: none;">View All Payments <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
                    <div class="chart-wrapper" style="flex: 1; min-height: 300px; position: relative;">
                        <canvas id="enrollmentTrendChart"></canvas>
                    </div>
                </div>

                <div class="chart-container" style="background: white; padding: 25px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                    <div class="chart-header" style="margin-bottom: 20px;">
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">Course Distribution</h3>
                    </div>
                    <div class="chart-wrapper" style="flex: 1; min-height: 300px; position: relative;">
                        <canvas id="courseDistChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Trend Chart
            const trendCtx = document.getElementById('enrollmentTrendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'New Enrollments',
                        data: [65, 59, 80, 81, 56, 95],
                        borderColor: '#1648bc',
                        backgroundColor: 'rgba(22, 72, 188, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#1648bc',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Distribution Chart
            const distCtx = document.getElementById('courseDistChart').getContext('2d');
            new Chart(distCtx, {
                type: 'doughnut',
                data: {
                    labels: ['BSIT', 'BSCS', 'BSBA', 'BSED'],
                    datasets: [{
                        data: [350, 280, 220, 180],
                        backgroundColor: ['#1648bc', '#3b82f6', '#10b981', '#f59e0b'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { 
                            position: 'bottom',
                            labels: { usePointStyle: true, padding: 20, font: { family: 'Poppins' } }
                        } 
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
</body>

</html>