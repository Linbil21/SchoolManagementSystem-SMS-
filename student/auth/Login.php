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
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #94a3b8;
            --accent: #10b981;
            --bg-gradient: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            --card-bg: rgba(255, 255, 255, 0.85);
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
            background: #0f172a;
            position: relative;
            overflow: hidden;
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
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(168, 85, 247, 0.15) 100%);
            filter: blur(80px);
            border-radius: 50%;
            animation: move 20s infinite alternate;
        }

        .blob-1 { top: -100px; left: -100px; }
        .blob-2 { bottom: -150px; right: -100px; animation-delay: -5s; }
        .blob-3 { top: 40%; left: 50%; width: 300px; height: 300px; background: rgba(16, 185, 129, 0.1); }

        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(100px, 100px) scale(1.2); }
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
            width: 50px;
            height: 50px;
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
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 10px;
            margin-left: 4px;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group i:not(.toggle-password) {
            position: absolute;
            left: 18px;
            color: var(--secondary);
            font-size: 1.1rem;
            transition: color 0.3s;
        }

        .form-input {
            width: 100%;
            background: #f1f5f9;
            border: 2px solid transparent;
            padding: 16px 16px 16px 52px;
            border-radius: 16px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #1e293b;
            transition: all 0.3s;
        }

        .form-input:focus {
            outline: none;
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.1);
        }

        .form-input:focus + i {
            color: var(--primary);
        }

        .toggle-password {
            position: absolute;
            right: 18px;
            color: var(--secondary);
            cursor: pointer;
            padding: 5px;
            transition: color 0.3s;
        }

        .toggle-password:hover { color: var(--primary); }

        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 16px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 35px -10px rgba(79, 70, 229, 0.5);
        }

        .login-btn:active { transform: scale(0.98); }

        .form-links {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .form-links a {
            color: var(--secondary);
            text-decoration: none;
            transition: color 0.3s;
        }

        .form-links a:hover { color: var(--primary); }

        .footer-copyright {
            margin-top: 40px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .back-to-home {
            position: absolute;
            top: 30px;
            left: 30px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .back-to-home:hover { opacity: 1; }
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
                    <a href="../../auth/Login.php">New? Enroll here</a>
                </div>
            </form>
        </div>
        <p class="footer-copyright">© 2026 SMS Student Portal. Powered by SMS Intelligence.</p>
    </div>

    <script>
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
