<?php
session_start();
require_once '../../Database/config.php';
require_once '../../auth/mail_helper.php';

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

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Please enter your registered email address.";
    } else {
        try {
            // Check if student exists
            $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
            $stmt->execute([$email]);
            $student = $stmt->fetch();

            if ($student) {
                // Generate 6-digit OTP
                $otp = rand(100000, 999999);
                
                // Save OTP to student record
                $update = $pdo->prepare("UPDATE students SET verification_code = ? WHERE id = ?");
                $update->execute([$otp, $student->id]);
                
                // Send OTP via email
                if (sendOTP($email, $otp, 'Student Account Password Reset')) {
                    header("Location: " . $root . "student/auth/reset_verification.php?email=" . urlencode($email));
                    exit();
                } else {
                    $error = "System failed to send the reset code. Please try again.";
                }
            } else {
                $error = "This email is not registered in the student portal.";
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
    <title>Forgot Password | Student Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary: #1e40af;
            --primary-light: #3b82f6;
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
            background: var(--bg-gradient), url('<?php echo $root; ?>Assets/image/background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 20px;
        }

        .forgot-container {
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .forgot-card {
            background: var(--card-bg);
            backdrop-filter: blur(25px);
            padding: 50px 40px;
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.3);
            text-align: center;
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: #eff6ff;
            color: var(--primary);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2rem;
            box-shadow: 0 10px 25px rgba(30, 64, 175, 0.1);
        }

        h1 {
            color: #1e293b;
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.025em;
        }

        .subtitle {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 35px;
            font-weight: 500;
            line-height: 1.6;
        }

        .form-group {
            text-align: left;
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 800;
            color: #64748b;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 20px;
            color: #94a3b8;
            font-size: 1.1rem;
        }

        .form-input {
            width: 100%;
            background: #f8fafc;
            border: 2px solid #f1f5f9;
            padding: 16px 20px 16px 58px;
            border-radius: 20px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #1e293b;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-input:focus {
            outline: none;
            background: white;
            border-color: var(--primary);
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
            transition: all 0.4s;
            box-shadow: 0 10px 25px -5px rgba(30, 64, 175, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 35px -10px rgba(30, 64, 175, 0.4);
        }

        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 15px;
            border-radius: 16px;
            margin-bottom: 25px;
            font-size: 0.85rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .back-link {
            margin-top: 30px;
            display: inline-block;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 700;
            transition: all 0.3s;
        }

        .back-link:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="forgot-card">
            <div class="icon-box">
                <i class="fas fa-key"></i>
            </div>
            <h1>Forgot Password?</h1>
            <p class="subtitle">Enter your email address and we'll send you a 6-digit code to reset your password.</p>

            <?php if ($error): ?>
                <div class="alert-error">
                    <i class="fas fa-circle-exclamation"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-input" placeholder="student@example.com" required>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    Get Reset Code
                </button>
            </form>

            <a href="<?php echo $root; ?>student/auth/Login.php" class="back-link">
                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back to Login
            </a>

            <div style="margin-top: 35px; border-top: 1px solid #f1f5f9; padding-top: 25px;">
                <p style="font-size: 0.85rem; color: #64748b; font-weight: 600;">
                    Need help? <a href="javascript:void(0)" id="contactSupport" style="color: var(--primary); text-decoration: none; font-weight: 800; margin-left: 5px;">Contact support</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Support Modal Resources -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('contactSupport')?.addEventListener('click', function() {
            Swal.fire({
                title: 'System Support',
                html: `
                    <div style="text-align: left; padding: 10px;">
                        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">
                            If you are experiencing issues with your account or the enrollment process, please contact the <b>Administrative Office</b>:
                        </p>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <div style="display: flex; align-items: center; gap: 12px; background: #f8fafc; padding: 12px; border-radius: 12px;">
                                <div style="width: 40px; height: 40px; background: #dbeafe; color: #1e40af; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <p style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; font-weight: 800; margin: 0;">Email Support</p>
                                    <p style="font-size: 0.9rem; color: #1e293b; font-weight: 700; margin: 0;">admin@jampzdev.com</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px; background: #f8fafc; padding: 12px; border-radius: 12px;">
                                <div style="width: 40px; height: 40px; background: #fcf6e5; color: #da9100; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <p style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; font-weight: 800; margin: 0;">Hotline</p>
                                    <p style="font-size: 0.9rem; color: #1e293b; font-weight: 700; margin: 0;">+63 912 345 6789</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px; background: #f8fafc; padding: 12px; border-radius: 12px;">
                                <div style="width: 40px; height: 40px; background: #ecfdf5; color: #059669; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-building-columns"></i>
                                </div>
                                <div>
                                    <p style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; font-weight: 800; margin: 0;">Office Hours</p>
                                    <p style="font-size: 0.9rem; color: #1e293b; font-weight: 700; margin: 0;">Mon - Fri, 8:00 AM - 5:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'Got it',
                confirmButtonColor: '#1e40af',
                width: '420px',
                padding: '2em'
            });
        });
    </script>
</body>
</html>
