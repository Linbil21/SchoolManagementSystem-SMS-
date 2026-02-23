<?php
require_once __DIR__ . '/../../auth/Security.php';
checkRole(['admission']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Statistics - Admission</title>
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

        /* Charts Section */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
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
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        /* Criteria List */
        .criteria-list {
            margin-top: 20px;
        }

        .criteria-item {
            margin-bottom: 15px;
        }

        .criteria-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .progress-bar {
            height: 10px;
            background: #f1f5f9;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>Evaluation Statistics</h1>
                <p>Detailed performance analytics of entrance evaluations.</p>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div class="stat-info">
                        <h3>942</h3>
                        <p>Total Evaluated</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #f0fdf4; color: #22c55e;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>710</h3>
                        <p>Passed Evaluation</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fff1f2; color: #e11d48;">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="stat-info">
                        <h3>232</h3>
                        <p>Below Threshold</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #faf5ff; color: #9333ea;">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                        <h3>84.5%</h3>
                        <p>Average Score</p>
                    </div>
                </div>
            </div>

            <div class="charts-grid">
                <!-- Evaluation Results -->
                <div class="chart-card">
                    <h3>Monthly Pass vs Fail Rate</h3>
                    <div class="chart-container">
                        <canvas id="passFailChart"></canvas>
                    </div>
                </div>

                <!-- Criteria Performance -->
                <div class="chart-card">
                    <h3>Average Criteria Performance</h3>
                    <div class="criteria-list">
                        <div class="criteria-item">
                            <div class="criteria-label">
                                <span>Entrance Exam</span>
                                <span>88%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 88%; background: #2563eb;"></div>
                            </div>
                        </div>
                        <div class="criteria-item">
                            <div class="criteria-label">
                                <span>Interview Assessment</span>
                                <span>75%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 75%; background: #9333ea;"></div>
                            </div>
                        </div>
                        <div class="criteria-item">
                            <div class="criteria-label">
                                <span>Document Review</span>
                                <span>92%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 92%; background: #22c55e;"></div>
                            </div>
                        </div>
                        <div class="criteria-item">
                            <div class="criteria-label">
                                <span>Background Check</span>
                                <span>82%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 82%; background: #f59e0b;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Score Distribution -->
            <div class="chart-card">
                <h3>Applicant Score Distribution</h3>
                <div class="chart-container" style="height: 250px;">
                    <canvas id="scoreDistChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Pass/Fail Bar Chart
            const pfCtx = document.getElementById('passFailChart').getContext('2d');
            new Chart(pfCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        {
                            label: 'Passed',
                            data: [65, 80, 120, 150, 140, 155],
                            backgroundColor: '#22c55e',
                            borderRadius: 6
                        },
                        {
                            label: 'Failed',
                            data: [15, 20, 35, 45, 38, 79],
                            backgroundColor: '#e11d48',
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        x: { stacked: true, grid: { display: false } },
                        y: { stacked: true, grid: { color: 'rgba(0,0,0,0.05)' } }
                    }
                }
            });

            // Score Distribution
            const sdCtx = document.getElementById('scoreDistChart').getContext('2d');
            new Chart(sdCtx, {
                type: 'bar',
                data: {
                    labels: ['0-50', '51-60', '61-70', '71-80', '81-90', '91-100'],
                    datasets: [{
                        label: 'Applicants',
                        data: [20, 45, 120, 310, 350, 97],
                        backgroundColor: '#2563eb',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { grid: { color: 'rgba(0,0,0,0.05)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
</body>

</html>


