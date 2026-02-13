<?php
session_start();
require_once '../Database/config.php';
require_once 'mail_helper.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        try {
            // 1. Check Students
            $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
            $stmt->execute([$email]);
            $student = $stmt->fetch();

            if ($student) {
                // Generate OTP
                $otp = rand(100000, 999999);
                // Update Student Record
                $update = $pdo->prepare("UPDATE students SET verification_code = ? WHERE id = ?");
                $update->execute([$otp, $student->id]);
                
                // Send OTP
                if (sendOTP($email, $otp, 'Password Reset')) {
                    header("Location: reset_verification.php?email=" . urlencode($email) . "&type=student");
                    exit();
                } else {
                    $error = "Failed to send OTP. Please try again later.";
                }
            } else {
                // 2. Check Users (Staff/Admin)
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user) {
                     // Generate OTP
                    $otp = rand(100000, 999999);
                    $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                    
                    // Use password_resets table
                    // Clear old tokens
                    $del = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
                    $del->execute([$email]);
                    
                    // Insert new
                    $ins = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
                    $ins->execute([$email, $otp, $expires]);
                    
                    if (sendOTP($email, $otp, 'Password Reset')) {
                        header("Location: reset_verification.php?email=" . urlencode($email) . "&type=staff");
                        exit();
                    } else {
                        $error = "Failed to send OTP. Please try again later.";
                    }
                } else {
                    // Security: Don't reveal if email exists or not? 
                    // For this environment, user-friendly error is okay.
                    $error = "Email address not found in our records.";
                }
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - SMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #64748b;
            --bg: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(rgba(30, 58, 138, 0.6), rgba(30, 58, 138, 0.6)), url('../Assets/image/background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 450px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-box {
            width: 70px;
            height: 70px;
            background: #dbeafe;
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.8rem;
        }

        h2 {
            color: #1e293b;
            margin-bottom: 10px;
            font-weight: 700;
        }

        p {
            color: var(--secondary);
            font-size: 0.9rem;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .input-group {
            margin-bottom: 25px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #475569;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .input-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        .error-alert {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .back-link {
            display: block;
            margin-top: 25px;
            color: var(--secondary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .back-link:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-box">
            <i class="fas fa-lock-open"></i>
        </div>
        <h2>Forgot Password?</h2>
        <p>No worries! Enter your email address below and we'll send you a verification code to reset your password.</p>

        <?php if ($error): ?>
            <div class="error-alert">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your registered email" required>
            </div>
            <button type="submit" class="btn">Send Verification Code</button>
        </form>

        <a href="Login.php" class="back-link">
            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back to Login
        </a>
    </div>
</body>
</html>
