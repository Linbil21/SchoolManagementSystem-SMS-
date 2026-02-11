<?php
session_start();

// Check if user is logged in
require_once '../auth/Security.php';
checkRole(['admin']);

$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Enrollment System</title>
    <link rel="icon" type="image/x-icon" href="../Assets/image/logo.png">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./Assets/Dashboard.css">
</head>

<body>

    <!-- Sidebar -->
    <?php include 'Components/Side-bar.php'; ?>

    <div class="main-wrapper">
        <!-- Head-bar -->
        <?php include 'Components/Head-bar.php'; ?>

        <div class="content-area">


            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card card-total">
                    <div class="stat-info">
                        <span>Total Students</span>
                        <h2>1,280</h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-card card-pending-review">
                    <div class="stat-info">
                        <span>Pending Review</span>
                        <h2>45</h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-card card-pending-payment">
                    <div class="stat-info">
                        <span>Pending Payment</span>
                        <h2>12</h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="stat-card card-enrolled">
                    <div class="stat-info">
                        <span>Enrolled</span>
                        <h2>1,223</h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section">
                <div class="chart-container main-chart">
                    <div class="chart-header">
                        <h3>Enrollment Trends</h3>
                        <div class="chart-actions">
                            <select class="chart-filter">
                                <option>Last 6 Months</option>
                                <option>Last Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="enrollmentTrendChart"></canvas>
                    </div>
                </div>
                <div class="chart-container side-chart">
                    <div class="chart-header">
                        <h3>Course Distribution</h3>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="courseDistChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Use system themes
        const isDarkMode = document.documentElement.getAttribute('data-theme') === 'dark';
        const primaryColor = '#1648bc';
        const lightBlue = '#3b82f6';
        const textColor = isDarkMode ? '#94a3b8' : '#64748b';
        const titleColor = isDarkMode ? '#f8fafc' : '#0f172a';
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

        // Enrollment Trend Chart (Line)
        const trendCtx = document.getElementById('enrollmentTrendChart').getContext('2d');
        const trendGradient = trendCtx.createLinearGradient(0, 0, 0, 400);
        trendGradient.addColorStop(0, 'rgba(22, 72, 188, 0.2)');
        trendGradient.addColorStop(1, 'rgba(22, 72, 188, 0)');

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'New Enrollments',
                    data: [65, 59, 80, 81, 56, 95],
                    fill: true,
                    backgroundColor: trendGradient,
                    borderColor: primaryColor,
                    tension: 0.4,
                    borderWidth: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: primaryColor,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: primaryColor,
                    pointHoverBorderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { left: 10, right: 10, top: 10, bottom: 10 } },
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor, drawBorder: false },
                        ticks: { color: textColor, font: { size: 10, weight: '500' }, padding: 8 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor, font: { size: 10, weight: '500' }, padding: 8 }
                    }
                }
            }
        });

        // Course Distribution Chart (Bar)
        const distCtx = document.getElementById('courseDistChart').getContext('2d');
        const barGradient = distCtx.createLinearGradient(0, 0, 0, 400);
        barGradient.addColorStop(0, '#1648bc');
        barGradient.addColorStop(1, '#3b82f6');

        new Chart(distCtx, {
            type: 'bar',
            data: {
                labels: ['BSIT', 'BSCS', 'BSBA', 'BSED', 'BSCrim'],
                datasets: [{
                    label: 'Students',
                    data: [350, 280, 220, 180, 150],
                    backgroundColor: barGradient,
                    hoverBackgroundColor: '#0a2e7a',
                    borderRadius: 8,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { left: 10, right: 10, top: 10, bottom: 10 } },
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor, drawBorder: false },
                        ticks: { color: textColor, font: { size: 10, weight: '500' }, padding: 8 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor, font: { size: 10, weight: '500' }, padding: 8 }
                    }
                }
            }
        });
    </script>
</body>

</html>