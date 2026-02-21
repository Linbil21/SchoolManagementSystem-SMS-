<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/config.php';

$email = $_GET['email'] ?? '';
$type = $_GET['type'] ?? 'login'; // login or register
$error = $_GET['error'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = implode('', $_POST['otp']);
    $email = $_POST['email'];
    $type = $_POST['type'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ? AND verification_code = ?");
        $stmt->execute([$email, $otp]);
        $student = $stmt->fetch();

        if ($student) {
            // Verify and clear code
            $updateStmt = $pdo->prepare("UPDATE students SET is_verified = 1, verification_code = NULL, status = 'online' WHERE id = ?");
            $updateStmt->execute([$student->id]);

            // Set Session
            $_SESSION['user_id'] = $student->id;
            $_SESSION['student_id'] = $student->student_id;
            $_SESSION['email'] = $student->email;
            $_SESSION['fullname'] = $student->first_name . ' ' . $student->last_name;
            $_SESSION['role'] = 'student';
            $_SESSION['profile_image'] = $student->profile_image;

            // Notification for successful verification
            $notif_title = $type == 'register' ? 'New Student Verified' : 'Student Logged In';
            $notif_msg = $student->first_name . " " . $student->last_name . ($type == 'register' ? " has completed verification." : " has logged in successfully.");
            $notif_type = $type == 'register' ? 'verification_success' : 'login_success';
            
            $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, profile_image, icon, icon_bg, icon_color) VALUES (NULL, ?, ?, ?, ?, 'fa-check-circle', '#d1fae5', '#059669')");
            $notif_stmt->execute([$notif_type, $notif_title, $notif_msg, $student->profile_image]);

            header("Location: ../student/Dashboard.php");
            exit();
        } else {
            header("Location: Verification.php?email=" . urlencode($email) . "&type=" . $type . "&error=invalid_otp");
            exit();
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - SMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #64748b;
            --success: #059669;
            --danger: #dc2626;
            --background: #f8fafc;
            --glass: rgba(255, 255, 255, 0.9);
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
            overflow: hidden;
        }

        .verification-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 450px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: #dbeafe;
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(37, 99, 235, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
        }

        h2 {
            color: #1e293b;
            margin-bottom: 10px;
            font-weight: 700;
        }

        p {
            color: var(--secondary);
            font-size: 0.95rem;
            margin-bottom: 30px;
        }

        .email-display {
            color: var(--primary);
            font-weight: 600;
        }

        .otp-inputs {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 30px;
        }

        .otp-field {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            transition: all 0.3s ease;
            color: #1e293b;
        }

        .otp-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            outline: none;
            transform: translateY(-2px);
        }

        .verify-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }

        .verify-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        .verify-btn:active {
            transform: translateY(0);
        }

        .resend-link {
            display: block;
            margin-top: 25px;
            color: var(--secondary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .resend-link b {
            color: var(--primary);
            cursor: pointer;
        }

        .error-msg {
            background: #fee2e2;
            color: var(--danger);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            display: <?php echo $error === 'invalid_otp' ? 'block' : 'none'; ?>;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="icon-box">
            <i class="fas fa-shield-halved"></i>
        </div>
        <h2>Verification Code</h2>
        <p>We've sent a 6-digit code to <br><span class="email-display"><?php echo htmlspecialchars($email); ?></span></p>

        <div class="error-msg">
            <i class="fas fa-exclamation-circle"></i> Invalid verification code. Please try again.
        </div>

        <form action="Verification.php" method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
            
            <div class="otp-inputs">
                <input type="text" name="otp[]" maxlength="1" class="otp-field" required>
                <input type="text" name="otp[]" maxlength="1" class="otp-field" required>
                <input type="text" name="otp[]" maxlength="1" class="otp-field" required>
                <input type="text" name="otp[]" maxlength="1" class="otp-field" required>
                <input type="text" name="otp[]" maxlength="1" class="otp-field" required>
                <input type="text" name="otp[]" maxlength="1" class="otp-field" required>
            </div>

            <button type="submit" class="verify-btn">Verify Account</button>
        </form>

        <a href="#" class="resend-link">Didn't receive code? <b>Resend Code</b></a>
        <a href="Login.php" style="margin-top: 15px; display: block; color: var(--secondary); font-size: 0.85rem; text-decoration: none;">
            <i class="fas fa-arrow-left"></i> Back to Login
        </a>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-field');
        
        inputs.forEach((input, index) => {
            input.addEventListener('keyup', (e) => {
                if (e.key >= 0 && e.key <= 9) {
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                } else if (e.key === 'Backspace') {
                    if (index > 0) {
                        inputs[index - 1].focus();
                    }
                }
            });

            // Handle paste
            input.addEventListener('paste', (e) => {
                const data = e.clipboardData.getData('text');
                if (data.length === inputs.length) {
                    for (let i = 0; i < inputs.length; i++) {
                        inputs[i].value = data[i];
                    }
                }
            });
        });
    </script>
</body>
</html>
