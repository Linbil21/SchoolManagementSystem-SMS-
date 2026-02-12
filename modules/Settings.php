<?php
require_once '../auth/Security.php';
// Allowed roles for Settings page
checkRole(['admin', 'superadmin', 'admission', 'cashier']);

require_once '../Database/config.php';

// Mock user data
$user_role = $_SESSION['role'] ?? 'Role';
$user_name = $_SESSION['username'] ?? 'User';

// Determine Sidebar to Include (Same logic as Profile.php)
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
        $css_path = '../Super-admin/Assets/super-admin.css';
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
    <title>Account Settings - SMS</title>
    <link rel="icon" type="image/x-icon" href="../Assets/image/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="<?php echo htmlspecialchars($css_path); ?>">
    <link rel="stylesheet" href="/Assets/css/theme.css">

    <style>
        .settings-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 35px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .settings-section-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .settings-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            background: var(--hover-bg);
            color: var(--accent-color);
        }

        .settings-section-title h2 {
            font-size: 1.25rem;
            font-weight: 850;
            letter-spacing: -0.5px;
            margin: 0;
            color: var(--text-color);
        }

        .settings-section-title p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin: 0;
        }

        .toggle-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background: var(--hover-bg);
            border-radius: 20px;
            margin-bottom: 15px;
            transition: 0.3s;
        }

        .toggle-card:hover {
            background: var(--surface-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transform: translateX(5px);
        }

        .toggle-label-info h4 {
            font-size: 0.95rem;
            font-weight: 750;
            margin-bottom: 4px;
        }

        .toggle-label-info p {
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        .input-premium {
            width: 100%;
            padding: 15px 20px;
            background: var(--hover-bg);
            border: 2px solid transparent;
            border-radius: 16px;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-color);
            transition: 0.3s;
        }

        .input-premium:focus {
            background: var(--surface-color);
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .pin-input-group {
            display: flex;
            gap: 12px;
        }

        .pin-box-premium {
            width: 55px;
            height: 55px;
            text-align: center;
            font-size: 1.4rem;
            font-weight: 800;
            background: var(--hover-bg);
            border: 2px solid transparent;
            border-radius: 16px;
            color: var(--accent-color);
            transition: 0.3s;
        }

        .pin-box-premium:focus {
            background: var(--surface-color);
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        /* Modern Toggle Switch */
        .switch-premium {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 28px;
        }

        .switch-premium input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider-premium {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--border-color);
            transition: .4s;
            border-radius: 34px;
        }

        .slider-premium:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        input:checked+.slider-premium {
            background-color: var(--accent-color);
        }

        input:checked+.slider-premium:before {
            transform: translateX(22px);
        }
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
            <h1 style="font-size: 2.2rem; font-weight: 850; color: var(--text-color); margin-bottom: 40px; letter-spacing: -1.5px;">Account Settings</h1>

            <div class="settings-grid">
                <!-- Security Settings -->
                <div class="card-premium">
                    <div class="settings-section-header">
                        <div class="settings-icon-box" style="background: #fee2e2; color: #ef4444;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="settings-section-title">
                            <h2>Security Credentials</h2>
                            <p>Manage your password and authentication methods.</p>
                        </div>
                    </div>

                    <form>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                            <div class="field-group">
                                <label class="field-label" style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 10px;">Current Password</label>
                                <input type="password" class="input-premium" placeholder="••••••••">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                            <div class="field-group">
                                <label class="field-label" style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 10px;">New Password</label>
                                <input type="password" class="input-premium" placeholder="••••••••">
                            </div>
                            <div class="field-group">
                                <label class="field-label" style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 10px;">Confirm New Password</label>
                                <input type="password" class="input-premium" placeholder="••••••••">
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <button type="button" class="btn-premium">
                                <i class="fas fa-key"></i>
                                Update Credentials
                            </button>
                        </div>
                    </form>

                    <div style="margin-top: 40px; border-top: 1px solid var(--border-color); padding-top: 30px;">
                        <div class="toggle-card">
                            <div class="toggle-label-info">
                                <h4>Two-Factor Authentication (2FA)</h4>
                                <p>Add an extra layer of security to your Super Admin account.</p>
                            </div>
                            <label class="switch-premium">
                                <input type="checkbox">
                                <span class="slider-premium"></span>
                            </label>
                        </div>

                        <div class="toggle-card" style="background: rgba(239, 68, 68, 0.05);">
                            <div class="toggle-label-info">
                                <h4 style="color: #ef4444;">Critical Login Alerts</h4>
                                <p>Get notified immediately on unauthorized access attempts.</p>
                            </div>
                            <label class="switch-premium">
                                <input type="checkbox" checked>
                                <span class="slider-premium"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- PIN Security -->
                <div class="card-premium">
                    <div class="settings-section-header">
                        <div class="settings-icon-box" style="background: #fff7ed; color: #ea580c;">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="settings-section-title">
                            <h2>Security PIN</h2>
                            <p>Required for high-level system modifications.</p>
                        </div>
                    </div>

                    <form id="pinForm">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin-bottom: 30px;">
                            <div>
                                <label class="field-label" style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 12px;">Current PIN</label>
                                <div class="pin-input-group" id="current-pin">
                                    <input type="password" class="pin-box-premium" maxlength="1" oninput="moveToNext(this, 'current-pin-2')" id="current-pin-1">
                                    <input type="password" class="pin-box-premium" maxlength="1" oninput="moveToNext(this, 'current-pin-3')" id="current-pin-2">
                                    <input type="password" class="pin-box-premium" maxlength="1" oninput="moveToNext(this, 'current-pin-4')" id="current-pin-3">
                                    <input type="password" class="pin-box-premium" maxlength="1" oninput="moveToNext(this, null)" id="current-pin-4">
                                </div>
                            </div>
                            <div>
                                <label class="field-label" style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 12px;">New Security PIN</label>
                                <div class="pin-input-group" id="new-pin">
                                    <input type="password" class="pin-box-premium" maxlength="1" oninput="moveToNext(this, 'new-pin-2')" id="new-pin-1">
                                    <input type="password" class="pin-box-premium" maxlength="1" oninput="moveToNext(this, 'new-pin-3')" id="new-pin-2">
                                    <input type="password" class="pin-box-premium" maxlength="1" oninput="moveToNext(this, 'new-pin-4')" id="new-pin-3">
                                    <input type="password" class="pin-box-premium" maxlength="1" oninput="moveToNext(this, null)" id="new-pin-4">
                                </div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <button type="button" class="btn-premium" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">
                                <i class="fas fa-shield-alt"></i>
                                Save Security PIN
                            </button>
                        </div>
                    </form>
                </div>

                <script>
                    function moveToNext(current, nextFieldId) {
                        if (current.value.length >= 1) {
                            if (nextFieldId) {
                                document.getElementById(nextFieldId).focus();
                            }
                        }
                    }
                </script>

                <!-- Preference Settings -->
                <div class="card-premium">
                    <div class="settings-section-header">
                        <div class="settings-icon-box" style="background: #eff6ff; color: #2563eb;">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div class="settings-section-title">
                            <h2>System Preferences</h2>
                            <p>Customize your dashboard experience.</p>
                        </div>
                    </div>

                    <div class="toggle-card">
                        <div class="toggle-label-info">
                            <h4>Real-time Notifications</h4>
                            <p>Get instant browser alerts for system events.</p>
                        </div>
                        <label class="switch-premium">
                            <input type="checkbox" checked>
                            <span class="slider-premium"></span>
                        </label>
                    </div>

                    <div class="toggle-card">
                        <div class="toggle-label-info">
                            <h4>Compact Sidebar</h4>
                            <p>Maximize workspace by collapsing the side navigation.</p>
                        </div>
                        <label class="switch-premium">
                            <input type="checkbox">
                            <span class="slider-premium"></span>
                        </label>
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