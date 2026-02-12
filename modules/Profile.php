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
    <title>My Profile - SMS</title>
    <link rel="icon" type="image/x-icon" href="../Assets/image/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="<?php echo htmlspecialchars($css_path); ?>">
    <link rel="stylesheet" href="/Assets/css/theme.css">

    <style>
        body {
            background: var(--bg-color);
            color: var(--text-color);
            transition: background 0.3s;
        }

        .main-wrapper {
            background: var(--bg-color);
            min-height: 100vh;
        }

        .profile-header {
            background: var(--surface-color);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 30px;
            transition: 0.3s;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #f1f5f9;
        }

        .profile-info h1 {
            font-size: 1.5rem;
            color: var(--text-color);
            margin-bottom: 5px;
        }

        .profile-info p {
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .badge-role {
            background: rgba(37, 99, 235, 0.1);
            color: var(--accent-color);
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-super {
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            color: white;
            box-shadow: 0 4px 10px rgba(217, 119, 6, 0.2);
        }

        .grid-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .card {
            background: var(--surface-color);
            padding: 25px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-color);
            color: var(--text-color);
            font-family: inherit;
            transition: 0.2s;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-save {
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-save:hover {
            background: #1d4ed8;
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
            <div class="profile-header">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random&size=200"
                    class="profile-img" alt="Profile">
                <div class="profile-info">
                    <h1>
                        <?php echo htmlspecialchars($user_name); ?>
                    </h1>
                    <p>
                        <?php echo htmlspecialchars($user_email); ?>
                    </p>
                    <span class="badge-role <?php echo (strpos(strtolower($user_role), 'super') !== false) ? 'badge-super' : ''; ?>">
                        <?php echo htmlspecialchars($user_role); ?>
                    </span>
                </div>
            </div>

            <div class="grid-layout">
                <div class="col-left">
                    <div class="card">
                        <h3 class="card-title">Personal Information</h3>
                        <form>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control"
                                        value="<?php echo htmlspecialchars($user_name); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control"
                                        value="<?php echo htmlspecialchars($user_email); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" placeholder="+63 900 000 0000">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Designation</label>
                                    <input type="text" class="form-control" value="<?php echo ucfirst($user_role); ?>"
                                        disabled style="background: #f8fafc;">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Bio</label>
                                <textarea class="form-control" rows="4" placeholder="Brief description..."></textarea>
                            </div>
                            <button type="button" class="btn-save">Save Changes</button>
                        </form>
                    </div>
                </div>

                <div class="col-right">
                    <div class="card">
                        <h3 class="card-title">Account Statistics</h3>
                        <div style="margin-bottom: 15px; display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Member Since</span>
                            <span style="font-weight: 600;">Jan 2024</span>
                        </div>
                        <div style="margin-bottom: 15px; display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Last Login</span>
                            <span style="font-weight: 600;">
                                <?php echo date('M d, Y'); ?>
                            </span>
                        </div>
                        <div style="margin-bottom: 15px; display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Status</span>
                            <span style="color: #10b981; font-weight: 600;">Active</span>
                        </div>
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