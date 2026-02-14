<?php
session_start();
require_once '../../Database/config.php';

$email = $_GET['email'] ?? '';
$error = '';

if (empty($email)) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = implode('', $_POST['otp']);
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ? AND verification_code = ?");
        $stmt->execute([$email, $otp]);
        $student = $stmt->fetch();

        if ($student) {
            // OTP is correct, Store email in session for the password change page
            $_SESSION['reset_email'] = $email;
            $_SESSION['otp_verified'] = true;
            
            header("Location: reset_password.php");
            exit();
        } else {
            $error = "Invalid verification code. Please check your email.";
        }
    } catch (PDOException $e) {
        $error = "Critical Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code | Student Portal</title>
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

        .verify-card {
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
            background: #f0f9ff;
            color: #0284c7;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2rem;
        }

        h1 { font-size: 1.75rem; color: #1e293b; font-weight: 800; margin-bottom: 10px; }
        p { color: #64748b; font-size: 0.9rem; margin-bottom: 30px; line-height: 1.6; }

        .otp-container {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 30px;
        }

        .otp-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 800;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
            color: var(--primary);
            transition: all 0.3s;
        }

        .otp-input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1);
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
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 64, 175, 0.3);
        }

        .error-msg {
            color: #dc2626;
            margin-bottom: 20px;
            font-size: 0.85rem;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="icon-box"><i class="fas fa-shield-halved"></i></div>
        <h1>Verify Code</h1>
        <p>We've sent a 6-digit verification code to <br><b style="color: var(--primary);"><?php echo htmlspecialchars($email); ?></b></p>
        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <div class="otp-container">
                <input type="text" name="otp[]" maxlength="1" class="otp-input" autofocus required>
                <input type="text" name="otp[]" maxlength="1" class="otp-input" required>
                <input type="text" name="otp[]" maxlength="1" class="otp-input" required>
                <input type="text" name="otp[]" maxlength="1" class="otp-input" required>
                <input type="text" name="otp[]" maxlength="1" class="otp-input" required>
                <input type="text" name="otp[]" maxlength="1" class="otp-input" required>
            </div>
            <button type="submit" class="submit-btn">Verify & Continue</button>
        </form>

        <p style="margin-top: 25px; font-size: 0.85rem;">
            Didn't get the code? <a href="forgot_password.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">Resend Email</a>
        </p>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
</body>
</html>
