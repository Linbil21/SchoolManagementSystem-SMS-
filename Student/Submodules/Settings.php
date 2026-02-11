<?php
session_start();
require_once '../../Database/config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/Login.php");
    exit();
}

$student_name = $_SESSION['fullname'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings | Student Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --success: #10b981;
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
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .settings-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .settings-card {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 35px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-title i {
            color: var(--primary);
        }

        /* PIN Input Styles */
        .pin-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin: 30px 0;
        }

        .pin-input {
            width: 60px;
            height: 70px;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            transition: all 0.3s;
            background: #f8fafc;
        }

        .pin-input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }

        .settings-info {
            color: var(--secondary);
            font-size: 0.9rem;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .btn-save {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
            margin-top: 20px;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.3);
        }

        /* Toggle Switch */
        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .setting-item:last-child {
            border-bottom: none;
        }

        .setting-text h5 {
            font-weight: 600;
            color: var(--text-main);
            font-size: 0.95rem;
        }

        .setting-text p {
            font-size: 0.8rem;
            color: var(--secondary);
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
            background-color: #e2e8f0;
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

        input:checked + .slider {
            background-color: var(--primary);
        }

        input:checked + .slider:before {
            transform: translateX(24px);
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Account Settings</h1>
                <p style="color: #64748b;">Manage your security preferences and notifications.</p>
            </div>

            <div class="settings-grid">
                <!-- Security PIN Section -->
                <div class="settings-card">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt"></i>
                        Security PIN Setup
                    </h3>
                    <p class="settings-info">
                        Set a 4-digit security PIN for sensitive transactions like enrollment and payment verifications.
                    </p>
                    
                    <form id="pinForm">
                        <div class="pin-container">
                            <input type="text" class="pin-input" maxlength="1" pattern="\d*" inputmode="numeric">
                            <input type="text" class="pin-input" maxlength="1" pattern="\d*" inputmode="numeric">
                            <input type="text" class="pin-input" maxlength="1" pattern="\d*" inputmode="numeric">
                            <input type="text" class="pin-input" maxlength="1" pattern="\d*" inputmode="numeric">
                        </div>
                        <button type="submit" class="btn-save">Update Security PIN</button>
                    </form>
                </div>

                <!-- Notifications Section -->
                <div class="settings-card">
                    <h3 class="card-title">
                        <i class="fas fa-bell"></i>
                        Notifications
                    </h3>
                    
                    <div class="setting-item">
                        <div class="setting-text">
                            <h5>Email Notifications</h5>
                            <p>Receive enrollment and payment updates via email.</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="setting-item">
                        <div class="setting-text">
                            <h5>Two-Factor Authentication</h5>
                            <p>Enable extra security for your account logins.</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="setting-item">
                        <div class="setting-text">
                            <h5>Login Alerts</h5>
                            <p>Get notified when someone logs into your account.</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // PIN Input logic: auto-focus next box
        const inputs = document.querySelectorAll('.pin-input');
        
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        document.getElementById('pinForm').addEventListener('submit', (e) => {
            e.preventDefault();
            let pin = "";
            inputs.forEach(input => pin += input.value);
            
            if(pin.length === 4) {
                alert("Security PIN updated successfully: " + pin);
            } else {
                alert("Please enter a 4-digit PIN.");
            }
        });
    </script>
</body>

</html>
