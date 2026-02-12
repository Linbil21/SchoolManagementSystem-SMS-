<?php
require_once '../../auth/Security.php';
$csrf_token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | SMS</title>
    <link rel="icon" type="image/x-icon" href="../../Assets/image/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary: #1e40af;
            --primary-light: #3b82f6;
            --primary-dark: #1e3a8a;
            --secondary: #64748b;
            --accent: #0ea5e9;
            --bg-gradient: linear-gradient(135deg, rgba(30, 64, 175, 0.8) 0%, rgba(37, 99, 235, 0.8) 100%);
            --card-bg: rgba(255, 255, 255, 0.95);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        * {
            scrollbar-width: none !important; /* Firefox */
            -ms-overflow-style: none !important; /* IE/Edge */
        }

        *::-webkit-scrollbar {
            display: none !important; /* Chrome/Safari/Opera */
            width: 0 !important;
            height: 0 !important;
        }

        html, body {
            overflow-x: hidden;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(30, 58, 138, 0.4), rgba(30, 58, 138, 0.4)), url('../../Assets/image/background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
            overflow-y: auto;
            padding: 40px 0;
        }

        /* Animated Particles Background */
        .bg-blobs {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(30, 64, 175, 0.3) 100%);
            filter: blur(100px);
            border-radius: 50%;
            animation: move 25s infinite alternate;
        }

        .blob-1 { top: -100px; left: -100px; }
        .blob-2 { bottom: -150px; right: -100px; animation-delay: -5s; }
        .blob-3 { top: 30%; left: 40%; width: 400px; height: 400px; background: rgba(14, 165, 233, 0.2); }

        @keyframes move {
            from { transform: translate(0, 0) scale(1) rotate(0deg); }
            to { transform: translate(150px, 150px) scale(1.3) rotate(30deg); }
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 20px;
            animation: containerAppear 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes containerAppear {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            background: var(--card-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            padding: 50px 40px;
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }

        .logo-box {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            position: relative;
        }

        .logo-box img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .logo-box::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 26px;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            z-index: -1;
            opacity: 0.5;
        }

        h1 {
            color: #1e293b;
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .subtitle {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 35px;
            font-weight: 500;
        }

        .success-banner {
            background: #ecfdf5;
            border: 1px solid #10b981;
            color: #065f46;
            padding: 16px;
            border-radius: 16px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 0.85rem;
            animation: pulseSuccess 2s infinite;
        }

        @keyframes pulseSuccess {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
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
            margin-left: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group i:not(.toggle-password) {
            position: absolute;
            left: 20px;
            color: #94a3b8;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            z-index: 5;
        }

        .form-input {
            width: 100%;
            background: #f8fafc;
            border: 2px solid #f1f5f9;
            padding: 18px 20px 18px 58px;
            border-radius: 20px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #1e293b;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            line-height: 1.2;
        }

        .form-input::placeholder {
            color: #cbd5e1;
            font-weight: 500;
        }

        .form-input:hover {
            background: #f1f5f9;
            border-color: #e2e8f0;
        }

        .form-input:focus {
            outline: none;
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.15);
        }

        .form-input:focus + i {
            color: var(--primary);
            transform: scale(1.1);
        }

        .toggle-password {
            position: absolute;
            right: 20px;
            color: #94a3b8;
            cursor: pointer;
            padding: 5px;
            transition: all 0.3s;
            z-index: 5;
            font-size: 1rem;
        }

        .toggle-password:hover { 
            color: var(--primary);
            transform: scale(1.1);
        }

        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, #4f46e5 100%);
            color: white;
            border: none;
            padding: 18px;
            border-radius: 20px;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .login-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 35px -10px rgba(99, 102, 241, 0.5);
            background: linear-gradient(135deg, #4f46e5 0%, var(--primary) 100%);
        }

        .login-btn:active { transform: scale(0.98); }

        .form-links {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .form-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.3s;
            padding: 5px 10px;
            border-radius: 8px;
        }

        .form-links a:hover { 
            color: var(--primary);
            background: rgba(99, 102, 241, 0.08);
        }

        .footer-copyright {
            margin-top: 40px;
            color: rgba(30, 58, 138, 0.7);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .back-to-home {
            position: absolute;
            top: 30px;
            left: 30px;
            color: #1e3a8a;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: all 0.3s;
            z-index: 100;
        }

        .back-to-home:hover { 
            background: white;
            transform: translateX(-5px);
            color: var(--primary);
        }
    </style>
</head>

<body>
    <div class="bg-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <a href="../../auth/Login.php" class="back-to-home">
        <i class="fas fa-arrow-left"></i> Selection Screen
    </a>

    <div class="login-container">
        <div class="login-card">
            <div class="logo-box">
                <img src="../../Assets/image/logo.png" alt="SMS Logo">
            </div>

            <?php if (isset($_GET['registered']) && $_GET['registered'] == 'true'): ?>
                <div class="success-banner">
                    <div style="width: 30px; height: 30px; border-radius: 50%; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-check"></i>
                    </div>
                    <span>ENROLLMENT COMPLETE! You can now access your portal.</span>
                </div>
            <?php endif; ?>

            <h1>Student Portal</h1>
            <p class="subtitle">Enter your credentials to manage your studies</p>

            <form action="Login_process.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label>Student ID or Email</label>
                    <div class="input-group">
                        <input type="text" name="student_identifier" class="form-input" placeholder="20XX-XXXX or email@example.com" required>
                        <i class="fas fa-id-card"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Account Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="student_password" class="form-input" placeholder="••••••••" required>
                        <i class="fas fa-shield-lock"></i>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    SIGN IN NOW <i class="fas fa-chevron-right"></i>
                </button>

                <div class="form-links">
                    <a href="forgot_password.php">Forgot password?</a>
                    <a href="../../auth/Login.php?action=register">New? Enroll here</a>
                </div>
            </form>
        </div>
        <p class="footer-copyright">© 2026 SMS Student Portal. Powered by SMS Intelligence.</p>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelector("form").addEventListener("submit", async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const btn = document.querySelector(".login-btn");
            const originalBtnText = btn.innerHTML;
            
            // UI Loading state
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Authenticating...';
            
            try {
                const response = await fetch('Login_process.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    window.location.href = result.redirect;
                } 
                else if (result.status === 'otp_required') {
                    // SHOW OTP MODAL
                    showOTPModal(result.email, result.masked_email);
                    btn.disabled = false;
                    btn.innerHTML = originalBtnText;
                }
                else {
                    Swal.fire({
                        title: 'Login Failed',
                        text: result.message,
                        icon: 'error',
                        confirmButtonColor: '#1e40af'
                    });
                    btn.disabled = false;
                    btn.innerHTML = originalBtnText;
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    title: 'System Error',
                    text: 'An unexpected error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#1e40af'
                });
                btn.disabled = false;
                btn.innerHTML = originalBtnText;
            }
        });

        function showOTPModal(email, maskedEmail) {
            Swal.fire({
                title: 'Verification Code',
                html: `
                    <div style="text-align: center;">
                        <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 25px;">
                            We've sent a 6-digit code to <br>
                            <b style="color: #1e40af;">${maskedEmail}</b>
                        </p>
                        <div style="display: flex; gap: 8px; justify-content: center; margin-bottom: 25px;">
                            <input type="text" maxlength="1" class="otp-box" style="width: 45px; height: 55px; text-align: center; font-size: 1.5rem; font-weight: 700; border: 2px solid #e2e8f0; border-radius: 10px; outline: none; transition: all 0.3s;" autofocus>
                            <input type="text" maxlength="1" class="otp-box" style="width: 45px; height: 55px; text-align: center; font-size: 1.5rem; font-weight: 700; border: 2px solid #e2e8f0; border-radius: 10px; outline: none; transition: all 0.3s;">
                            <input type="text" maxlength="1" class="otp-box" style="width: 45px; height: 55px; text-align: center; font-size: 1.5rem; font-weight: 700; border: 2px solid #e2e8f0; border-radius: 10px; outline: none; transition: all 0.3s;">
                            <input type="text" maxlength="1" class="otp-box" style="width: 45px; height: 55px; text-align: center; font-size: 1.5rem; font-weight: 700; border: 2px solid #e2e8f0; border-radius: 10px; outline: none; transition: all 0.3s;">
                            <input type="text" maxlength="1" class="otp-box" style="width: 45px; height: 55px; text-align: center; font-size: 1.5rem; font-weight: 700; border: 2px solid #e2e8f0; border-radius: 10px; outline: none; transition: all 0.3s;">
                            <input type="text" maxlength="1" class="otp-box" style="width: 45px; height: 55px; text-align: center; font-size: 1.5rem; font-weight: 700; border: 2px solid #e2e8f0; border-radius: 10px; outline: none; transition: all 0.3s;">
                        </div>
                        <p style="font-size: 0.8rem; color: #94a3b8;">Please enter the code sent to your email to continue.</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Verify Now',
                confirmButtonColor: '#1e40af',
                didOpen: () => {
                    const inputs = document.querySelectorAll('.otp-box');
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
                        // Add blue border on focus
                        input.addEventListener('focus', () => {
                            input.style.borderColor = '#3b82f6';
                            input.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.1)';
                        });
                        input.addEventListener('blur', () => {
                            input.style.borderColor = '#e2e8f0';
                            input.style.boxShadow = 'none';
                        });
                    });
                },
                preConfirm: () => {
                    const inputs = document.querySelectorAll('.otp-box');
                    let otp = '';
                    inputs.forEach(input => otp += input.value);
                    if (otp.length < 6) {
                        Swal.showValidationMessage('Please enter all 6 digits');
                        return false;
                    }
                    return otp;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    verifyOTP(email, result.value);
                }
            });
        }

        async function verifyOTP(email, otp) {
            // Show loading overlay
            Swal.fire({
                title: 'Verifying...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const formData = new FormData();
            formData.append('email', email);
            formData.append('otp[]', otp[0]); // Compatibility with Verification.php format if needed
            formData.append('otp[]', otp[1]);
            formData.append('otp[]', otp[2]);
            formData.append('otp[]', otp[3]);
            formData.append('otp[]', otp[4]);
            formData.append('otp[]', otp[5]);
            formData.append('type', 'login');

            try {
                const response = await fetch('../../auth/Verification.php', {
                    method: 'POST',
                    body: formData
                });
                
                // If the redirect happens, window.location will change. 
                // But Verification.php might return a raw redirect header which fetch doesn't follow automatically for navigation
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    const text = await response.text();
                    if (text.includes('invalid_otp')) {
                         Swal.fire({
                            title: 'Invalid Code',
                            text: 'The code you entered is incorrect. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#1e40af'
                        }).then(() => {
                             // Show modal again
                             // showOTPModal(email, maskedEmail); 
                             // For now just allow retry
                        });
                    } else {
                        // Assuming success if it contains dashboard or similar
                         window.location.href = '../Dashboard.php';
                    }
                }
            } catch (err) {
                Swal.fire('Error', 'Verification failed.', 'error');
            }
        }

        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#student_password");

        togglePassword.addEventListener("click", function () {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    </script>
</body>

</html>
