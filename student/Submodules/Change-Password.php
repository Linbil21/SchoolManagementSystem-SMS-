<?php
session_start();
require_once '../../Database/config.php';

// Robust absolute-relative path logic
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/student/', '/Super-admin/', '/Admin/', '/Cashier/', '/Admission/', '/auth/', '/modules/'];
$project_base = '';
foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        $project_base = rtrim(substr($script_name, 0, $pos), '/');
        break;
    }
}
$root = $project_base . '/';

if (!isset($_SESSION['student_id'])) {
    header("Location: " . $root . "student/auth/Login.php");
    exit();
}

$student_name = $_SESSION['fullname'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | Student Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --border: #e2e8f0;
            --error: #ef4444;
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
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            margin-bottom: 35px;
        }

        .page-title h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .card {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 10px;
        }

        .input-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-container i.field-icon {
            position: absolute;
            left: 18px;
            color: var(--secondary);
            font-size: 1rem;
        }

        .input-container input {
            width: 100%;
            padding: 14px 50px 14px 50px;
            background: #f8fafc;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-main);
            transition: all 0.3s;
        }

        .input-container input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 18px;
            color: var(--secondary);
            cursor: pointer;
            transition: 0.3s;
        }

        .toggle-password:hover {
            color: var(--primary);
        }

        .password-requirements {
            background: #f1f5f9;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .requirements-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .requirements-list {
            list-style: none;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .requirement-item {
            font-size: 0.75rem;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .requirement-item i {
            font-size: 0.7rem;
        }

        .requirement-item.valid {
            color: #10b981;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.3);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            display: none;
        }

        .alert-error {
            background: #fee2e2;
            color: #ef4444;
            border: 1px solid #fecaca;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <div class="page-title">
                    <h1>Security Management</h1>
                    <p style="color: #64748b;">Keep your account secure by updating your password regularly.</p>
                </div>
            </div>

            <div class="card">
                <div id="errorAlert" class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="errorMessage"></span>
                </div>

                <form id="changePasswordForm">
                    <div class="form-group">
                        <label>Current Password</label>
                        <div class="input-container">
                            <i class="fas fa-lock field-icon"></i>
                            <input type="password" id="currentPassword" placeholder="Enter current password" required>
                            <i class="fas fa-eye toggle-password"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>New Password</label>
                        <div class="input-container">
                            <i class="fas fa-key field-icon"></i>
                            <input type="password" id="newPassword" placeholder="Enter new password" required>
                            <i class="fas fa-eye toggle-password"></i>
                        </div>
                    </div>

                    <div class="password-requirements">
                        <div class="requirements-title">
                            <i class="fas fa-info-circle"></i>
                            Password Requirements
                        </div>
                        <ul class="requirements-list">
                            <li class="requirement-item"><i class="fas fa-circle"></i> At least 8 characters</li>
                            <li class="requirement-item"><i class="fas fa-circle"></i> One uppercase letter</li>
                            <li class="requirement-item"><i class="fas fa-circle"></i> One lowercase letter</li>
                            <li class="requirement-item"><i class="fas fa-circle"></i> One number (0-9)</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <div class="input-container">
                            <i class="fas fa-check-circle field-icon"></i>
                            <input type="password" id="confirmPassword" placeholder="Confirm new password" required>
                            <i class="fas fa-eye toggle-password"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i>
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function () {
                const input = this.parentElement.querySelector('input');
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });

        // Form Submission
        document.getElementById('changePasswordForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const current = document.getElementById('currentPassword').value;
            const newPass = document.getElementById('newPassword').value;
            const confirm = document.getElementById('confirmPassword').value;
            const errorAlert = document.getElementById('errorAlert');
            const errorMsg = document.getElementById('errorMessage');

            if (newPass !== confirm) {
                errorMsg.innerText = "New passwords do not match!";
                errorAlert.style.display = 'block';
                return;
            }

            if (newPass.length < 8) {
                errorMsg.innerText = "Password must be at least 8 characters long.";
                errorAlert.style.display = 'block';
                return;
            }

            // Success Simulation
            alert("Password updated successfully!");
            location.reload();
        });
    </script>
</body>

</html>
