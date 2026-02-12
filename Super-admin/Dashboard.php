<?php
session_start();
require_once '../auth/Security.php';
checkRole(['superadmin']);
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel='icon' type='image/png' href='/Assets/image/logo.png'>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - SMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/Super-admin/assets/super-admin.css">
</head>

<body>
    <?php include 'Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include 'Components/header.php'; ?>
        <div class="content-area">
            <div class="banner">
                <h1>Super Admin Dashboard</h1>
                <p>Full system control and user management overview.</p>
            </div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info"><span>Total Users</span>
                        <h2>150</h2>
                    </div>
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info"><span>System Health</span>
                        <h2>99%</h2>
                    </div>
                    <div class="stat-icon"><i class="fas fa-heartbeat"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info"><span>Last Backup</span>
                        <h2>2h ago</h2>
                    </div>
                    <div class="stat-icon"><i class="fas fa-database"></i></div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

