<?php
session_start();
require_once '../auth/Security.php';
checkRole(['admission']);
$role = $_SESSION['role'];
$view = isset($_GET['view']) ? $_GET['view'] : 'default';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Dashboard - SMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            transition: background 0.3s;
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
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .banner {
            background: linear-gradient(135deg, var(--accent-color) 0%, #3b82f6 100%);
            padding: 40px;
            border-radius: 24px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(22, 72, 188, 0.2);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .banner h1 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .view-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .card {
            background: var(--surface-color);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: var(--surface-color);
            padding: 25px;
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border-color);
            border-left: 5px solid var(--accent-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
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
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-color);
            margin-top: 8px;
        }

        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 15px;
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
            transform: scale(1.1);
        }

        /* Notifications Styling */
        .notification-item {
            display: flex;
            gap: 20px;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            transition: 0.3s;
        }

        .notification-item:last-child { border-bottom: none; }
        .notification-item:hover { background: var(--hover-bg); border-radius: 12px; }

        .notif-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.2rem;
            background: var(--hover-bg);
            color: var(--accent-color);
        }

        .notif-content h4 { font-size: 1rem; font-weight: 700; color: var(--text-color); margin-bottom: 5px; }
        .notif-content p { font-size: 0.9rem; color: var(--text-muted); line-height: 1.6; }
        .notif-time { font-size: 0.8rem; color: var(--text-muted); margin-top: 8px; font-weight: 500; }

        /* Summary Table */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .summary-table th {
            text-align: left;
            padding: 15px 20px;
            background: var(--hover-bg);
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            border-bottom: 2px solid var(--border-color);
        }

        .summary-table td {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.95rem;
            color: var(--text-color);
        }

        .summary-table tr:last-child td { border-bottom: none; }
        .summary-table tr:hover td { background: var(--hover-bg); }

        .badge {
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-block;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .chart-header {
            margin-bottom: 20px;
        }

        .chart-header h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-color);
        }

        .chart-header p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .chart-container {
            height: 320px;
            display: flex;
            flex-direction: column;
        }

        .chart-wrapper {
            flex: 1;
            position: relative;
            min-height: 0;
        }

        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include 'Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include 'Components/header.php'; ?>
        <div class="content-area">
            
            <?php if ($view == 'default'): ?>
                <!-- Default Dashboard View -->
                <div class="banner">
                    <h1>Welcome Back, Admission!</h1>
                    <p>You have 24 new applications to review today. Keep up the great work!</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card" onclick="location.href='?view=summary'">
                        <div class="stat-info">
                            <span>New Applications</span>
                            <h2>128</h2>
                        </div>
                        <div class="stat-icon"><i class="fas fa-user-plus"></i></div>
                    </div>
                    <div class="stat-card" onclick="location.href='?view=pending'">
                        <div class="stat-info">
                            <span>Pending Evaluation</span>
                            <h2>45</h2>
                        </div>
                        <div class="stat-icon" style="background: #fff7ed; color: #f59e0b;"><i class="fas fa-hourglass-half"></i></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <span>Total Enrolled</span>
                            <h2>1,240</h2>
                        </div>
                        <div class="stat-icon" style="background: #f0fdf4; color: #22c55e;"><i class="fas fa-user-graduate"></i></div>
                    </div>
                </div>

                <div class="charts-grid">
                    <div class="card chart-container">
                        <div class="chart-header">
                            <h3>Application Trends</h3>
                            <p>Monthly submission overview</p>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="trendsChart"></canvas>
                        </div>
                    </div>
                    <div class="card chart-container">
                        <div class="chart-header">
                            <h3>Course Distribution</h3>
                            <p>Applications per top department</p>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="courseChart"></canvas>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($view == 'summary'): ?>
                <div class="view-title">
                    <i class="fas fa-chart-pie" style="color: var(--primary-blue);"></i>
                    Application Summary
                </div>
                <div class="card">
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Count</th>
                                <th>Percentage</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Freshman Applications</td>
                                <td>85</td>
                                <td>66%</td>
                                <td><span class="badge" style="background: #eef2ff; color: #4f46e5;">Stable</span></td>
                            </tr>
                            <tr>
                                <td>Transferee Applications</td>
                                <td>43</td>
                                <td>34%</td>
                                <td><span class="badge" style="background: #f0fdf4; color: #16a34a;">Increasing</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ($view == 'pending'): ?>
                <div class="view-title">
                    <i class="fas fa-clock" style="color: #f59e0b;"></i>
                    Pending Evaluatons
                </div>
                <div class="card">
                    <p style="color: #64748b; margin-bottom: 20px;">The following applications are waiting for your final decision.</p>
                    <div class="notification-item">
                        <div class="notif-icon" style="background: #e0f2fe; color: #0ea5e9;"><i class="fas fa-file-invoice"></i></div>
                        <div class="notif-content">
                            <h4>Miguel Enrique Uy</h4>
                            <p>Document verification completed. Ready for entrance exam scheduling.</p>
                            <div class="notif-time">Applied 2 days ago</div>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notif-icon" style="background: #e0f2fe; color: #0ea5e9;"><i class="fas fa-file-invoice"></i></div>
                        <div class="notif-content">
                            <h4>Alice Johnson</h4>
                            <p>Transcript of Records received. Pending GPA calculation.</p>
                            <div class="notif-time">Applied 3 days ago</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($view == 'notifications'): ?>
                <div class="view-title">
                    <i class="fas fa-bell" style="color: #ef4444;"></i>
                    Recent Notifications
                </div>
                <div class="card">
                    <div class="notification-item">
                        <div class="notif-icon" style="background: #fef2f2; color: #ef4444;"><i class="fas fa-exclamation-circle"></i></div>
                        <div class="notif-content">
                            <h4>System Update</h4>
                            <p>Admission portal will undergo maintenance tonight at 10:00 PM GMT+8.</p>
                            <div class="notif-time">10 minutes ago</div>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notif-icon" style="background: #ecfdf5; color: #10b981;"><i class="fas fa-check-circle"></i></div>
                        <div class="notif-content">
                            <h4>Evaluation Success</h4>
                            <p>Batch #2024-A results have been successfully sent to student emails.</p>
                            <div class="notif-time">2 hours ago</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Trends Chart
            const trendsCtx = document.getElementById('trendsChart').getContext('2d');
            new Chart(trendsCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Applications',
                        data: [45, 59, 80, 81, 56, 128],
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: '#3b82f6',
                        borderWidth: 2,
                        borderRadius: 5,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // Course Distribution
            const courseCtx = document.getElementById('courseChart').getContext('2d');
            new Chart(courseCtx, {
                type: 'doughnut',
                data: {
                    labels: ['BSIT', 'BSCS', 'BSHM', 'BSBA'],
                    datasets: [{
                        data: [40, 25, 20, 15],
                        backgroundColor: [
                            '#3b82f6',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444'
                        ],
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
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: { size: 12, family: 'Poppins' }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
</body>

</html>