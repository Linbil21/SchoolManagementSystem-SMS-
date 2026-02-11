<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cashier') {
    header("Location: ../../auth/Login.php");
    exit();
}
// Settings Page for Cashier
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Cashier</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1648bc;
            --bg: #f8fafc;
            --white: #ffffff;
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
            background: var(--bg);
            display: flex;
            min-height: 100vh;
            color: var(--text-main);
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .content-area {
            padding: 40px;
        }

        .module-header {
            margin-bottom: 40px;
        }

        .module-header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
        }

        .settings-card {
            background: var(--white);
            border-radius: 24px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .icon-box {
            width: 50px;
            height: 50px;
            background: #eef2ff;
            color: var(--primary);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 14px 18px;
            border-radius: 14px;
            border: 1.5px solid #edf2f7;
            background: #f8fafc;
            outline: none;
        }

        .btn-save {
            background: var(--primary);
            color: var(--white);
            padding: 14px 30px;
            border-radius: 14px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
        }

        .switch-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: var(--primary);
        }

        input:checked+.slider:before {
            transform: translateX(24px);
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>Settings</h1>
                <p>Global configuration for Cashier modules.</p>
            </div>
            <div class="settings-grid">
                <div class="settings-card">
                    <div class="card-header">
                        <div class="icon-box"><i class="fas fa-lock"></i></div>
                        <h3>Account Security</h3>
                    </div>
                    <div class="form-group"><label>Change Local PIN</label><input type="password"
                            placeholder="Enter 6-digit PIN"></div>
                    <div class="switch-row">
                        <div>
                            <p style="font-weight: 600;">Two-Factor Auth</p>
                        </div><label class="switch"><input type="checkbox"><span class="slider"></span></label>
                    </div>
                    <button class="btn-save">Update Security</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>