<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cashier') {
    header("Location: ../../auth/Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Methods - Cashier</title>
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

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 30px;
            animation: fadeIn 0.5s ease-out;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .method-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            text-align: center;
            border: 1px solid #f1f5f9;
            transition: transform 0.2s;
        }

        .method-card:hover {
            transform: translateY(-5px);
        }

        .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .analysis-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .chart-box {
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
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
            .cards-container {
                grid-template-columns: 1fr 1fr;
            }

            .analysis-grid {
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
            <h1 class="page-title">Payment Method Analysis</h1>

            <div class="cards-container">
                <!-- Cash -->
                <div class="method-card">
                    <div class="icon-circle" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3 style="margin-bottom: 5px;">Cash</h3>
                    <h2 style="color: #1e293b;">42%</h2>
                    <p style="color: #64748b; font-size: 0.8rem;">315 Transactions</p>
                </div>

                <!-- Online -->
                <div class="method-card">
                    <div class="icon-circle" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3 style="margin-bottom: 5px;">Online/App</h3>
                    <h2 style="color: #1e293b;">35%</h2>
                    <p style="color: #64748b; font-size: 0.8rem;">262 Transactions</p>
                </div>

                <!-- Bank -->
                <div class="method-card">
                    <div class="icon-circle" style="background: #fff7ed; color: #ea580c;">
                        <i class="fas fa-university"></i>
                    </div>
                    <h3 style="margin-bottom: 5px;">Bank Transfer</h3>
                    <h2 style="color: #1e293b;">18%</h2>
                    <p style="color: #64748b; font-size: 0.8rem;">135 Transactions</p>
                </div>

                <!-- Cheque -->
                <div class="method-card">
                    <div class="icon-circle" style="background: #faf5ff; color: #9333ea;">
                        <i class="fas fa-money-check"></i>
                    </div>
                    <h3 style="margin-bottom: 5px;">Cheque</h3>
                    <h2 style="color: #1e293b;">5%</h2>
                    <p style="color: #64748b; font-size: 0.8rem;">37 Transactions</p>
                </div>
            </div>

            <div class="analysis-grid">
                <div class="chart-box">
                    <h3 style="margin-bottom: 25px;">Preference Trends (Last 6 Months)</h3>
                    <canvas id="trendsChart"></canvas>
                </div>
                <div class="chart-box">
                    <h3 style="margin-bottom: 25px;">Digital vs Physical</h3>
                    <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                        <canvas id="digitalChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preference Trends
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'],
                datasets: [
                    {
                        label: 'Cash',
                        data: [50, 48, 45, 42, 40, 42],
                        borderColor: '#0284c7',
                        tension: 0.4
                    },
                    {
                        label: 'Digital (Online + Bank)',
                        data: [40, 45, 48, 52, 54, 53],
                        borderColor: '#16a34a',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
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

        // Digital vs Physical Pie
        const digitalCtx = document.getElementById('digitalChart').getContext('2d');
        new Chart(digitalCtx, {
            type: 'pie',
            data: {
                labels: ['Physical Cash/Cheque', 'Digital Channels'],
                datasets: [{
                    data: [47, 53],
                    backgroundColor: ['#64748b', '#1648bc'],
                    hoverOffset: 4
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
</body>

</html>