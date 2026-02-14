<?php
session_start();
require_once '../../Database/config.php';

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['otp_verified'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];
$error = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        try {
            // Hash password
            $hashed = $password; // Keeping it simple as per original if needed, but normally use password_hash
            // Let's check how the original login handles it. Usually it's raw for some legacy systems or hashed.
            // Based on previous explorations, the system uses password_hash in login_process usually.
            // Let's use password_hash for security.
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Update student password and clear the OTP
            $stmt = $pdo->prepare("UPDATE students SET password = ?, verification_code = NULL WHERE email = ?");
            if ($stmt->execute([$hashed, $email])) {
                $success = true;
                // Clear reset session
                unset($_SESSION['reset_email']);
                unset($_SESSION['otp_verified']);
            } else {
                $error = "Failed to update password. Please try again.";
            }
        } catch (PDOException $e) {
            $error = "Critical Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password | Student Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary: #1e40af;
            --bg-gradient: linear-gradient(rgba(30, 58, 138, 0.4), rgba(30, 58, 138, 0.4));
            --card-bg: rgba(255, 255, 255, 0.95);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-gradient), url('../../Assets/image/background.jpg');
            background-size: cover;
            background-position: center;
            padding: 20px;
        }

        .reset-card {
            background: var(--card-bg);
            backdrop-filter: blur(25px);
            padding: 50px 40px;
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            max-width: 440px;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: #f0fdf4;
            color: #16a34a;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2rem;
        }

        h1 { font-size: 1.75rem; color: #1e293b; font-weight: 800; margin-bottom: 10px; }
        p { color: #64748b; font-size: 0.9rem; margin-bottom: 30px; }

        .form-group { text-align: left; margin-bottom: 25px; }
        .form-group label { display: block; font-size: 0.8rem; font-weight: 800; color: #64748b; margin-bottom: 10px; text-transform: uppercase; }
        
        .input-wrapper { position: relative; display: flex; align-items: center; }
        .input-wrapper i { position: absolute; left: 20px; color: #94a3b8; }
        
        .form-input {
            width: 100%;
            background: #f8fafc;
            border: 2px solid #f1f5f9;
            padding: 16px 20px 16px 58px;
            border-radius: 20px;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 5px rgba(30, 64, 175, 0.1);
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%);
            color: white;
            border: none;
            padding: 18px;
            border-radius: 20px;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
        }

        .alert-success { background: #dcfce7; color: #166534; padding: 20px; border-radius: 20px; margin-bottom: 25px; font-weight: 700; }
        .error-msg { color: #dc2626; margin-bottom: 20px; font-size: 0.85rem; font-weight: 700; }
    </style>
</head>
<body>
    <div class="reset-card">
        <div class="icon-box"><i class="fas fa-lock"></i></div>
        <h1>New Password</h1>
        <p>Your identity has been verified. <br>Please set a secure new password.</p>

        <?php if ($success): ?>
            <div class="alert-success">
                <i class="fas fa-circle-check" style="margin-right: 10px;"></i>
                Password Reset Successfully!
            </div>
            <a href="Login.php" class="submit-btn" style="display: block; text-decoration: none; text-align: center;">Go to Login</a>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>New Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-shield-lock"></i>
                        <input type="password" name="password" class="form-input" placeholder="••••••••" required minlength="8">
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-shield-lock"></i>
                        <input type="password" name="confirm_password" class="form-input" placeholder="••••••••" required minlength="8">
                    </div>
                </div>

                <button type="submit" class="submit-btn">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
