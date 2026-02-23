<?php
require_once __DIR__ . '/../../auth/Security.php';
checkRole(['admission']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications Summary - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-blue: #1648bc;
            --bg-light: #f7fafc;
            --surface: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--bg-light);
            display: flex;
            min-height: 100vh;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .content-area {
            padding: 30px;
        }

        .module-header {
            margin-bottom: 25px;
        }

        .module-header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .module-header p {
            color: var(--text-muted);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--surface);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1;
        }

        .stat-info p {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: var(--surface);
            padding: 25px;
            border-radius: 24px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .chart-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-container {
            height: 350px;
            position: relative;
        }

        /* Table Card */
        .table-card {
            background: var(--surface);
            padding: 25px;
            border-radius: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            background: #f8fafc;
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
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

        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>Applications Summary</h1>
                <p>Real-time analytics for current admission cycle.</p>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #eef2ff; color: #4f46e5;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="stat-info">
                        <h3>1,248</h3>
                        <p>Total Applications</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>856</h3>
                        <p>Approved</p>
                    </div>
                </div>
                <div class="stat-card" style="border-bottom: 4px solid #f59e0b;">
                    <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>312</h3>
                        <p>In Review</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fef2f2; color: #ef4444;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>80</h3>
                        <p>Rejected</p>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-grid">
                <div class="chart-card">
                    <h3><i class="fas fa-chart-line" style="color: #4f46e5;"></i> Monthly Application Volume</h3>
                    <div class="chart-container">
                        <canvas id="timelineChart"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <h3><i class="fas fa-chart-pie" style="color: #10b981;"></i> Dept. Distribution</h3>
                    <div class="chart-container">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Detailed Breakdown -->
            <div class="table-card">
                <h3 style="margin-bottom: 20px; font-weight: 700;">Department Breakdown</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Department / Course</th>
                            <th>Total Applied</th>
                            <th>Passed</th>
                            <th>Waitlisted</th>
                            <th>Rejected</th>
                            <th>Success Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>BS Information Technology</td>
                            <td>450</td>
                            <td>310</td>
                            <td>120</td>
                            <td>20</td>
                            <td><span class="badge" style="background: #ecfdf5; color: #10b981;">68.9%</span></td>
                        </tr>
                        <tr>
                            <td>BS Computer Science</td>
                            <td>380</td>
                            <td>240</td>
                            <td>110</td>
                            <td>30</td>
                            <td><span class="badge" style="background: #ecfdf5; color: #10b981;">63.2%</span></td>
                        </tr>
                        <tr>
                            <td>BS Hospitality Management</td>
                            <td>218</td>
                            <td>180</td>
                            <td>28</td>
                            <td>10</td>
                            <td><span class="badge" style="background: #ecfdf5; color: #10b981;">82.6%</span></td>
                        </tr>
                        <tr>
                            <td>BS Business Administration</td>
                            <td>200</td>
                            <td>126</td>
                            <td>54</td>
                            <td>20</td>
                            <td><span class="badge" style="background: #fff7ed; color: #f59e0b;">63.0%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Timeline Chart
            const timelineCtx = document.getElementById('timelineChart').getContext('2d');
            new Chart(timelineCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        label: 'Applications',
                        data: [150, 230, 480, 610, 520, 840, 1248],
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2
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

            // Dept Distribution Chart
            const deptCtx = document.getElementById('deptChart').getContext('2d');
            new Chart(deptCtx, {
                type: 'doughnut',
                data: {
                    labels: ['BSIT', 'BSCS', 'BSHM', 'BSBA'],
                    datasets: [{
                        data: [450, 380, 218, 200],
                        backgroundColor: [
                            '#4f46e5',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444'
                        ],
                        hoverOffset: 15,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: { size: 12, family: 'Poppins' }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>
