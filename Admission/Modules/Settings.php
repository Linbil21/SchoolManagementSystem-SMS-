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
    <title>Settings - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1648bc;
            --bg-light: #f7fafc;
            --text-dark: #2d3748;
            --text-gray: #718096;
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
            overflow-x: hidden;
        }

        .content-area {
            padding: 30px;
            flex: 1;
        }

        .settings-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .settings-header {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #edf2f7;
        }

        .settings-header h2 {
            color: var(--text-dark);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .setting-item {
            padding: 20px;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            transition: 0.3s;
        }

        .setting-item:hover {
            border-color: var(--primary-blue);
            background: #f8fafc;
        }

        .setting-icon {
            width: 45px;
            height: 45px;
            background: #eef2ff;
            color: var(--primary-blue);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="settings-card">
                <div class="settings-header">
                    <h2>General Settings</h2>
                    <p>Manage your admission module preferences and system configuration.</p>
                </div>

                <div class="settings-grid">
                    <div class="setting-item">
                        <div class="setting-icon"><i class="fas fa-bell"></i></div>
                        <h3>Notifications</h3>
                        <p>Configure how you receive alerts for new applications.</p>
                    </div>
                    <div class="setting-item">
                        <div class="setting-icon"><i class="fas fa-shield-alt"></i></div>
                        <h3>Security</h3>
                        <p>Update your password and manage session security.</p>
                    </div>
                    <div class="setting-item">
                        <div class="setting-icon"><i class="fas fa-users-cog"></i></div>
                        <h3>Admission Setup</h3>
                        <p>Configure admission cycles, dates, and requirements.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>