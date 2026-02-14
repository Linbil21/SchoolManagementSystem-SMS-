<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Reports - Admission</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .module-header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .module-header p {
            color: var(--text-muted);
        }

        .btn-export {
            background: var(--primary-blue);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }

        .btn-export:hover {
            background: #0d3596;
            transform: translateY(-2px);
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
        }

        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 5px;
        }

        .stat-info p {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Chart Card */
        .chart-card {
            background: var(--surface);
            padding: 25px;
            border-radius: 24px;
            margin-bottom: 30px;
            border: 1px solid #edf2f7;
        }

        .chart-container {
            height: 300px;
        }

        /* Table Card */
        .table-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            border: 1px solid #edf2f7;
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
            font-weight: 700;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #edf2f7;
            color: #2d3748;
            font-size: 0.9rem;
        }

        .type-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <div>
                    <h1>Student ID Reports</h1>
                    <p>Logs and analytics for ID card generation and issuance.</p>
                </div>
                <a href="#" class="btn-export">
                    <i class="fas fa-file-download"></i> Export Full Report
                </a>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>1,850</h3>
                        <p>Total IDs Printed</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>1,420</h3>
                        <p>First Issuance</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>430</h3>
                        <p>Replacements</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>12</h3>
                        <p>Pending Printing</p>
                    </div>
                </div>
            </div>

            <!-- Trend Chart -->
            <div class="chart-card">
                <h3 style="margin-bottom: 20px; font-weight: 700;">ID Issuance Trend (Last 7 Days)</h3>
                <div class="chart-container">
                    <canvas id="idTrendChart"></canvas>
                </div>
            </div>

            <!-- Detailed Logs -->
            <div class="table-card">
                <h3 style="margin-bottom: 20px; font-weight: 700;">Recent Issuance Logs</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Type</th>
                            <th>Date Issued</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#TR-8921</td>
                            <td>Dela Cruz, Juan A.</td>
                            <td>2024-0001</td>
                            <td><span class="type-badge" style="background: #eef2ff; color: #4f46e5;">NEW</span></td>
                            <td>Oct 24, 2024</td>
                            <td><span style="color: #10b981; font-weight: 600;">Success</span></td>
                        </tr>
                        <tr>
                            <td>#TR-8922</td>
                            <td>Santos, Maria B.</td>
                            <td>2024-0002</td>
                            <td><span class="type-badge" style="background: #fffbeb; color: #f59e0b;">REPLACEMENT</span></td>
                            <td>Oct 24, 2024</td>
                            <td><span style="color: #10b981; font-weight: 600;">Success</span></td>
                        </tr>
                        <tr>
                            <td>#TR-8923</td>
                            <td>Aquino, Benigno C.</td>
                            <td>2024-0003</td>
                            <td><span class="type-badge" style="background: #eef2ff; color: #4f46e5;">NEW</span></td>
                            <td>Oct 23, 2024</td>
                            <td><span style="color: #10b981; font-weight: 600;">Success</span></td>
                        </tr>
                        <tr>
                            <td>#TR-8924</td>
                            <td>Marcos, Ferdinand D.</td>
                            <td>2024-0004</td>
                            <td><span class="type-badge" style="background: #eef2ff; color: #4f46e5;">NEW</span></td>
                            <td>Oct 22, 2024</td>
                            <td><span style="color: #10b981; font-weight: 600;">Success</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('idTrendChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Oct 18', 'Oct 19', 'Oct 20', 'Oct 21', 'Oct 22', 'Oct 23', 'Oct 24'],
                    datasets: [
                        {
                            label: 'New IDs',
                            data: [45, 32, 10, 58, 62, 48, 55],
                            backgroundColor: '#1648bc',
                            borderRadius: 5
                        },
                        {
                            label: 'Replacements',
                            data: [12, 8, 2, 15, 18, 10, 14],
                            backgroundColor: '#f59e0b',
                            borderRadius: 5
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
                        x: { grid: { display: false } },
                        y: { 
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' } 
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>
