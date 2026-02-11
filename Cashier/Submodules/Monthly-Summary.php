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
    <title>Monthly Summary - Cashier</title>
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
            padding: 25px;
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: end;
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease-out;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 5px;
        }

        .month-selector {
            padding: 10px 20px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            color: var(--text-main);
            font-family: inherit;
            cursor: pointer;
            outline: none;
            font-weight: 500;
        }

        .totals-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .total-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .card-details h4 {
            color: var(--text-sub);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 3px;
        }

        .card-details h2 {
            color: var(--text-main);
            font-size: 1.8rem;
            line-height: 1;
            font-weight: 700;
        }

        .analytics-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .graph-card {
            background: white;
            padding: 25px;
            border-radius: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .breakdown-list {
            list-style: none;
        }

        .breakdown-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .breakdown-item:last-child {
            border-bottom: none;
        }

        .cat-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cat-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        @media (max-width: 1024px) {
            .analytics-section {
                grid-template-columns: 1fr;
            }

            .totals-grid {
                grid-template-columns: 1fr;
            }
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
            <div class="header-actions">
                <div>
                    <h1 class="page-title">Monthly Summary</h1>
                    <p style="color: var(--text-sub);">Financial performance overview</p>
                </div>
                <select class="month-selector">
                    <option>January 2026</option>
                    <option>December 2025</option>
                    <option>November 2025</option>
                </select>
            </div>

            <!-- high level totals -->
            <div class="totals-grid">
                <div class="total-card">
                    <div class="icon-box" style="background: #eff6ff; color: #1648bc;">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="card-details">
                        <h4>Total Revenue</h4>
                        <h2>₱4,250,000</h2>
                    </div>
                </div>
                <div class="total-card">
                    <div class="icon-box" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-arrow-trend-up"></i>
                    </div>
                    <div class="card-details">
                        <h4>Growth (MoM)</h4>
                        <h2>+15.4%</h2>
                    </div>
                </div>
                <div class="total-card">
                    <div class="icon-box" style="background: #fff1f2; color: #e11d48;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="card-details">
                        <h4>Outstanding</h4>
                        <h2>₱850,000</h2>
                    </div>
                </div>
            </div>

            <!-- detailed analytics -->
            <div class="analytics-section">
                <!-- Main bar chart -->
                <div class="graph-card">
                    <h3 style="margin-bottom: 20px; font-weight:700;">Daily Income Trend</h3>
                    <div style="flex: 1; width: 100%; position: relative; min-height: 300px;">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>

                <!-- Side breakdown -->
                <div class="graph-card">
                    <h3 style="margin-bottom: 20px; font-weight:700;">Revenue by Category</h3>
                    <div
                        style="position: relative; height: 180px; width: 100%; display: flex; justify-content: center; margin-bottom: 20px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <ul class="breakdown-list">
                        <li class="breakdown-item">
                            <div class="cat-info">
                                <span class="cat-dot" style="background: #1648bc;"></span>
                                <span>Tuition Fees</span>
                            </div>
                            <strong>₱3.2M</strong>
                        </li>
                        <li class="breakdown-item">
                            <div class="cat-info">
                                <span class="cat-dot" style="background: #3b82f6;"></span>
                                <span>Miscellaneous</span>
                            </div>
                            <strong>₱650K</strong>
                        </li>
                        <li class="breakdown-item">
                            <div class="cat-info">
                                <span class="cat-dot" style="background: #93c5fd;"></span>
                                <span>Others</span>
                            </div>
                            <strong>₱400K</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Monthly trend
        const trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], // Simplified for UI demo
                datasets: [{
                    label: 'Income',
                    data: [850000, 1200000, 950000, 1250000],
                    backgroundColor: '#1648bc',
                    borderRadius: 8,
                    barThickness: 40
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
                        grid: { borderDash: [5, 5] }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // Category Doughnut
        const catCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: ['Tuition', 'Misc', 'Others'],
                datasets: [{
                    data: [75, 15, 10],
                    backgroundColor: ['#1648bc', '#3b82f6', '#93c5fd'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: { legend: { display: false } }
            }
        });
    </script>
</body>

</html>