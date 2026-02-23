<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/config.php';

$email = $_GET['email'] ?? '';
$type = $_GET['type'] ?? 'login';
$error = $_GET['error'] ?? '';
$resend_status = $_GET['resend_status'] ?? '';

// Handle Resend OTP
if (isset($_GET['resend']) && $_GET['resend'] == '1' && !empty($email)) {
    try {
        // Generate new 6-digit OTP
        $new_otp = sprintf("%06d", mt_rand(1, 999999));
        
        // Update verification_code in database
        $stmt = $pdo->prepare("UPDATE students SET verification_code = ? WHERE email = ?");
        $stmt->execute([$new_otp, $email]);
        
        if ($stmt->rowCount() > 0) {
            // Send email with new OTP (i-configure ang mail settings)
            $to = $email;
            $subject = "Your New OTP Code";
            $message = "Your new verification code is: $new_otp";
            $headers = "From: no-reply@yourdomain.com\r\n" .
                       "Reply-To: no-reply@yourdomain.com\r\n" .
                       "X-Mailer: PHP/" . phpversion();
            
            if (mail($to, $subject, $message, $headers)) {
                $resend_status = 'success';
            } else {
                $resend_status = 'mail_failed';
            }
        } else {
            $resend_status = 'email_not_found';
        }
    } catch (PDOException $e) {
        $resend_status = 'db_error';
    }
    
    // Redirect back to verification page with status
    header("Location: Verification.php?email=" . urlencode($email) . "&type=" . urlencode($type) . "&resend_status=" . $resend_status);
    exit();
}

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

            // Fetch Admission & Enrollment Progress
            $app_stmt = $pdo->prepare("SELECT status FROM admission_applications WHERE email = ? ORDER BY submission_date DESC LIMIT 1");
            $app_stmt->execute([$student->email]);
            $_SESSION['admission_status'] = $app_stmt->fetchColumn() ?: 'Pending';

            $enr_stmt = $pdo->prepare("SELECT status FROM enrollments WHERE email = ? ORDER BY created_at DESC LIMIT 1");
            $enr_stmt->execute([$student->email]);
            $_SESSION['enrollment_status'] = $enr_stmt->fetchColumn() ?: 'Pending';

            // Notification for successful verification
            $notif_title = $type == 'register' ? 'New Student Verified' : 'Student Logged In';
            $notif_msg = $student->first_name . " " . $student->last_name . ($type == 'register' ? " has completed verification." : " has logged in successfully.");
            $notif_type = $type == 'register' ? 'verification_success' : 'login_success';
            
            $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, profile_image, icon, icon_bg, icon_color) VALUES (NULL, ?, ?, ?, ?, 'fa-check-circle', '#d1fae5', '#059669')");
            $notif_stmt->execute([$notif_type, $notif_title, $notif_msg, $student->profile_image]);

            // Ipakita ang success message na may delay bago mag-redirect
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="refresh" content="3;url=../student/Dashboard.php">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Verification Successful</title>
                <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
                <style>
                    body {
                        font-family: 'Poppins', sans-serif;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        height: 100vh;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0;
                        color: #fff;
                        text-align: center;
                    }
                    .message-box {
                        background: rgba(255,255,255,0.2);
                        backdrop-filter: blur(10px);
                        padding: 40px;
                        border-radius: 20px;
                        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                    }
                    .spinner {
                        border: 4px solid rgba(255,255,255,0.3);
                        border-top: 4px solid #fff;
                        border-radius: 50%;
                        width: 50px;
                        height: 50px;
                        animation: spin 1s linear infinite;
                        margin: 20px auto;
                    }
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                </style>
            </head>
            <body>
                <div class="message-box">
                    <i class="fas fa-check-circle" style="font-size: 4rem; margin-bottom: 20px;"></i>
                    <h2>Verification Successful!</h2>
                    <p>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?>! You will be redirected to your dashboard in 3 seconds.</p>
                    <div class="spinner"></div>
                    <p>If you are not redirected, <a href="../student/Dashboard.php" style="color: #fff; font-weight: 600;">click here</a>.</p>
                </div>
                <script src="https://kit.fontawesome.com/your-kit.js" crossorigin="anonymous"></script>
            </body>
            </html>
            <?php
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
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
            word-break: break-all;
        }

        .otp-inputs {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
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
            transition: opacity 0.3s;
        }

        .resend-link.disabled b {
            pointer-events: none;
            opacity: 0.5;
        }

        #timer {
            color: var(--primary);
            font-weight: 600;
        }

        .error-msg, .success-msg {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .error-msg {
            background: #fee2e2;
            color: var(--danger);
        }

        .success-msg {
            background: #d1fae5;
            color: var(--success);
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

        <?php if ($error === 'invalid_otp'): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i> Invalid verification code. Please try again.
            </div>
        <?php endif; ?>

        <?php if ($resend_status === 'success'): ?>
            <div class="success-msg">
                <i class="fas fa-check-circle"></i> A new OTP has been sent to your email.
            </div>
        <?php elseif ($resend_status === 'mail_failed'): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i> Failed to send email. Please try again later.
            </div>
        <?php elseif ($resend_status === 'email_not_found'): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i> Email not found. Please register again.
            </div>
        <?php elseif ($resend_status === 'db_error'): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i> Database error. Please contact support.
            </div>
        <?php endif; ?>

        <form action="Verification.php" method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
            
            <div class="otp-inputs">
                <input type="text" name="otp[]" maxlength="1" class="otp-field" required autofocus>
                <input type="text" name="otp[]" maxlength="1" class="otp-field" required>
                <input type="text" name="otp[]" maxlength="1" class="otp-field" required>
                <input type="text" name="otp[]" maxlength="1" class="otp-field" required>
                <input type="text" name="otp[]" maxlength="1" class="otp-field" required>
                <input type="text" name="otp[]" maxlength="1" class="otp-field" required>
            </div>

            <button type="submit" class="verify-btn">Verify Account</button>
        </form>

        <div class="resend-link" id="resendSection">
            <span id="resendText">Didn't receive code? <b id="resendBtn">Resend Code</b></span>
            <span id="timer" style="display: none;">Resend in <span id="countdown">60</span>s</span>
        </div>
        <a href="Login.php" style="margin-top: 15px; display: block; color: var(--secondary); font-size: 0.85rem; text-decoration: none;">
            <i class="fas fa-arrow-left"></i> Back to Login
        </a>
    </div>

    <script>
        // OTP input navigation
        const inputs = document.querySelectorAll('.otp-field');
        inputs.forEach((input, index) => {
            input.addEventListener('keyup', (e) => {
                if (e.key >= '0' && e.key <= '9') {
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                } else if (e.key === 'Backspace') {
                    if (index > 0) {
                        inputs[index - 1].focus();
                    }
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const data = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, inputs.length);
                for (let i = 0; i < data.length; i++) {
                    inputs[i].value = data[i];
                }
                if (data.length === inputs.length) {
                    inputs[inputs.length - 1].focus();
                }
            });
        });

        // Resend OTP with countdown timer
        const resendBtn = document.getElementById('resendBtn');
        const resendText = document.getElementById('resendText');
        const timerSpan = document.getElementById('timer');
        const countdownSpan = document.getElementById('countdown');
        const resendSection = document.getElementById('resendSection');
        let countdown = 60;
        let timerInterval;

        resendBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Disable resend button and start timer
            resendBtn.style.pointerEvents = 'none';
            resendBtn.style.opacity = '0.5';
            resendText.style.display = 'none';
            timerSpan.style.display = 'inline';

            // Redirect to resend endpoint
            window.location.href = 'Verification.php?resend=1&email=<?php echo urlencode($email); ?>&type=<?php echo urlencode($type); ?>';

            // Start countdown
            timerInterval = setInterval(function() {
                countdown--;
                countdownSpan.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(timerInterval);
                    resendBtn.style.pointerEvents = 'auto';
                    resendBtn.style.opacity = '1';
                    resendText.style.display = 'inline';
                    timerSpan.style.display = 'none';
                    countdown = 60;
                }
            }, 1000);
        });
    </script>
</body>
</html>