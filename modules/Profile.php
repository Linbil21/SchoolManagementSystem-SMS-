<?php
require_once '../auth/Security.php';
// Allowed roles for Profile page
checkRole(['admin', 'superadmin', 'admission', 'cashier', 'student']);

require_once '../Database/config.php';

// Mock user data if not in DB (for display purposes if session doesn't have all details)
$user_name = $_SESSION['username'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'Role';
$user_email = $_SESSION['email'] ?? 'user@school.edu';

// Determine Sidebar to Include
$role = strtolower($user_role);
$sidebar_path = '';
$css_path = '';

switch ($role) {
    case 'admin':
        $sidebar_path = '../Admin/Components/Side-bar.php';
        $header_path = '../Admin/Components/Head-bar.php';
        $css_path = '../Admin/Assets/style.css';
        break;
    case 'superadmin':
    case 'super-admin':
        $sidebar_path = '../Super-admin/Components/Sidebar.php';
        $header_path = '../Super-admin/Components/header.php';
        $css_path = '../Super-admin/assets/super-admin.css';
        break;
    case 'admission':
        $sidebar_path = '../Admission/Components/Sidebar.php';
        $header_path = '../Admission/Components/header.php';
        $css_path = '../Admin/Assets/style.css';
        break;
    case 'cashier':
        $sidebar_path = '../Cashier/Components/Sidebar.php';
        $header_path = '../Cashier/Components/header.php';
        $css_path = '../Admin/Assets/style.css';
        break;
    default:
        $sidebar_path = '../Admin/Components/Side-bar.php';
        $header_path = '../Admin/Components/Head-bar.php';
        $css_path = '../Admin/Assets/style.css';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - SMS</title>
    <link rel="icon" type="image/x-icon" href="../Assets/image/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="<?php echo htmlspecialchars($css_path); ?>">
    <link rel="stylesheet" href="../Assets/css/theme.css">

    <style>
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .profile-header-premium {
            background: linear-gradient(135deg, var(--accent-color) 0%, #4f46e5 100%);
            padding: 60px 40px;
            border-radius: 40px;
            color: white;
            display: flex;
            align-items: center;
            gap: 40px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.2);
        }

        .profile-header-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 35c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm60-21c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM70 88c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM31 13c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3z' fill='rgba(255,255,255,0.05)' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.4;
        }

        .profile-avatar-wrapper {
            position: relative;
            z-index: 1;
        }

        .profile-img-lg {
            width: 140px;
            height: 140px;
            border-radius: 40px;
            object-fit: cover;
            border: 6px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .profile-header-info {
            z-index: 1;
        }

        .profile-header-info h1 {
            font-size: 2.2rem;
            font-weight: 850;
            letter-spacing: -1.5px;
            margin-bottom: 8px;
            color: white;
        }

        .profile-header-info p {
            font-size: 1.1rem;
            opacity: 0.9;
            color: white;
            margin-bottom: 20px;
        }

        .role-badge-premium {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 8px 20px;
            border-radius: 14px;
            font-size: 0.85rem;
            font-weight: 700;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .info-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        @media (max-width: 992px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .profile-header-premium {
                flex-direction: column;
                text-align: center;
                padding: 40px 20px;
            }
        }

        .field-group {
            margin-bottom: 25px;
        }

        .field-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .input-premium {
            width: 100%;
            padding: 16px 20px;
            background: var(--hover-bg);
            border: 2px solid transparent;
            border-radius: 18px;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .input-premium:focus {
            background: var(--surface-color);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .input-premium:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .stat-item {
            padding: 20px 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-label {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .stat-value {
            font-weight: 700;
            color: var(--text-color);
        }
    </style>
    </style>
</head>

<body>
    <?php
    if (file_exists($sidebar_path)) {
        include $sidebar_path;
    } else {
        echo "<!-- Sidebar Path Error: $sidebar_path not found -->";
    }
    ?>

    <div class="main-wrapper">
        <?php
        if (file_exists($header_path)) {
            include $header_path;
        } else {
            // Minimal internal header if component missing
            echo '<div style="background: var(--header-bg); padding: 15px 30px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: flex-end; align-items: center; height: 70px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-weight: 600; color: var(--text-color);">'.htmlspecialchars($user_name).'</span>
                </div>
            </div>';
        }
        ?>

        <div class="content-area">
            <div class="profile-header-premium">
                <div class="profile-avatar-wrapper">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=1648bc&color=fff&size=200"
                        class="profile-img-lg" alt="Profile">
                </div>
                <div class="profile-header-info">
                    <h1><?php echo htmlspecialchars($user_name); ?></h1>
                    <p><?php echo htmlspecialchars($user_email); ?></p>
                    <div class="role-badge-premium">
                        <i class="fas fa-shield-alt"></i>
                        <span><?php echo htmlspecialchars($user_role); ?></span>
                    </div>
                </div>
            </div>

            <div class="info-grid">
                <div class="card-premium">
                    <h3 style="font-size: 1.3rem; font-weight: 850; letter-spacing: -0.5px; margin-bottom: 30px; display: flex; align-items: center; gap: 15px;">
                        <i class="fas fa-id-card-alt" style="color: var(--accent-color);"></i>
                        Personal Details
                    </h3>
                    <form>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                            <div class="field-group">
                                <label class="field-label">Full Name</label>
                                <input type="text" class="input-premium" value="<?php echo htmlspecialchars($user_name); ?>">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Email Address</label>
                                <input type="email" class="input-premium" value="<?php echo htmlspecialchars($user_email); ?>">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Primary Contact</label>
                                <input type="text" class="input-premium" placeholder="+63 900 000 0000">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Official Designation</label>
                                <input type="text" class="input-premium" value="<?php echo ucfirst($user_role); ?>" disabled>
                            </div>
                        </div>
                        <div class="field-group" style="margin-top: 10px;">
                            <label class="field-label">Professional Bio</label>
                            <textarea class="input-premium" rows="4" placeholder="Briefly describe your responsibilities..."></textarea>
                        </div>
                        <div style="margin-top: 20px; text-align: right;">
                            <button type="button" class="btn-premium">
                                <i class="fas fa-save"></i>
                                Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-premium">
                    <h3 style="font-size: 1.2rem; font-weight: 850; letter-spacing: -0.5px; margin-bottom: 25px; display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-chart-pie" style="color: #f59e0b;"></i>
                        Account Summary
                    </h3>
                    <div class="stat-item">
                        <span class="stat-label">Member Since</span>
                        <span class="stat-value">Jan 2024</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Last Session</span>
                        <span class="stat-value"><?php echo date('M d, Y'); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Account Health</span>
                        <span class="stat-value" style="color: #10b981;">Excellent</span>
                    </div>
                    
                    <div style="margin-top: 40px; text-align: center;">
                        <div style="width: 80px; height: 80px; background: var(--hover-bg); border-radius: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--accent-color); margin-bottom: 15px;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h4 style="font-weight: 800; margin-bottom: 5px;">Secure Account</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Your account is protected by enterprise-grade security.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function initTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        }
        initTheme();
    </script>
</body>

</html>