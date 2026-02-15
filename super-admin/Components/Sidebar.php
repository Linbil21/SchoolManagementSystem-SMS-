<?php
// Super Admin Sidebar Component
$current_page = basename($_SERVER['PHP_SELF']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'superadmin';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@sms.com';

// Robust absolute-relative path logic (case-insensitive)
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/super-admin/', '/admin/', '/cashier/', '/admission/', '/auth/', '/student/', '/modules/'];
$project_base = '';

foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        $project_base = rtrim(substr($script_name, 0, $pos), '/');
        break;
    }
}

// Use lowercase for cross-platform compatibility
$base = $project_base . '/super-admin/';
$root = $project_base . '/';
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <a href="<?php echo $base; ?>Dashboard.php" class="brand-wrapper">
            <div class="logo-box">
                <img src="<?php echo $root; ?>Assets/image/logo.png" alt="Logo" class="sidebar-logo">
            </div>
            <span class="brand-name">Super Admin</span>
        </a>
    </div>

    <div class="sidebar-menu">
        <p class="menu-label">MAIN</p>
        <ul>
            <li class="<?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>">
                <a href="<?php echo $base; ?>Dashboard.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="has-dropdown <?php echo (stripos($_SERVER['PHP_SELF'], '/Cashier/') !== false) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Finance Hub</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a href="<?php echo $root; ?>Cashier/Dashboard.php">
                            <i class="fas fa-chart-pie"></i>
                            <span>Revenue Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $root; ?>Cashier/Submodules/Daily-Collection.php">
                            <i class="fas fa-receipt"></i>
                            <span>Collections</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        <p class="menu-label">ADMISSION & STUDENTS</p>
        <ul>
            <li class="has-dropdown <?php echo (stripos($_SERVER['PHP_SELF'], '/super-admin/submodules/') !== false) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-user-graduate"></i>
                    <span>Admissions</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a href="<?php echo $base; ?>Submodules/Admission-Dashboard.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Admission Stats</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base; ?>Submodules/Applications-Manager.php">
                            <i class="fas fa-tasks"></i>
                            <span>App Manager</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base; ?>Submodules/Student-ID-Center.php">
                            <i class="fas fa-id-card"></i>
                            <span>ID Center</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        <p class="menu-label">ACADEMIC & SYSTEM</p>
        <ul class="main-menu">
            <li class="<?php echo ($current_page == 'User-Management.php') ? 'active' : ''; ?>">
                <a href="<?php echo $base; ?>Modules/User-Management.php">
                    <i class="fas fa-users-cog"></i>
                    <span>User Management</span>
                </a>
            </li>
            <li class="has-dropdown <?php echo (stripos($_SERVER['PHP_SELF'], '/Admin/') !== false) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-microchip"></i>
                    <span>System Admin</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a href="<?php echo $root; ?>Admin/Dashboard.php">
                            <i class="fas fa-desktop"></i>
                            <span>Admin Console</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $root; ?>Admin/Modules/Enrollment.php">
                            <i class="fas fa-user-edit"></i>
                            <span>Enrollment Control</span>
                        </a>
                    </li>
                    <li class="<?php echo ($current_page == 'Courses.php') ? 'active' : ''; ?>">
                        <a href="<?php echo $root; ?>super-admin/Modules/Courses.php">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Course Management</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        <p class="menu-label">ACCOUNT & SETTINGS</p>
        <ul>
            <li>
                <a href="<?php echo $root; ?>modules/Profile.php"
                    class="<?php echo ($current_page == 'Profile.php') ? 'active' : ''; ?>">
                    <i class="fas fa-user-circle"></i>
                    <span>My Profile</span>
                </a>
            </li>

            <li>
                <a href="<?php echo $root; ?>modules/Settings.php"
                    class="<?php echo ($current_page == 'Settings.php') ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>

            <li>
                <a href="javascript:void(0)" onclick="openLogoutModal()" class="logout-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>End Session</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <div class="user-peek">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($email); ?>&background=1648bc&color=fff"
                alt="User">
            <div class="user-peek-info">
                <h4><?php echo $role === 'superadmin' ? 'Super Admin' : 'Admin'; ?></h4>
                <p><?php echo $email; ?></p>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.dropdown-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const parent = button.parentElement;
            parent.classList.toggle('open');
        });
    });

    function openLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.style.display = 'flex'; // Override inline display:none
        setTimeout(() => { modal.classList.add('show'); }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.remove('show');
        setTimeout(() => { modal.style.display = 'none'; }, 300); // Wait for transition
        document.body.style.overflow = 'auto';
    }

    window.addEventListener('click', function(e) {
        if (e.target.id === 'logoutModal') closeLogoutModal();
    });
</script>

<!-- Logout Modal -->
<div id="logoutModal" class="logout-modal-overlay" style="display: none;">
    <div class="logout-modal-content">
        <div class="logout-modal-icon">
            <i class="fas fa-power-off"></i>
        </div>
        <h2>End Session?</h2>
        <p>Your Super Admin session will be terminated. For your security, an automatic session timeout occurs after periods of inactivity.</p>
        <div class="logout-modal-buttons">
            <button onclick="closeLogoutModal()" class="btn-cancel">Stay Here</button>
            <a href="<?php echo $root; ?>auth/logout.php" class="btn-logout">End Session</a>
        </div>
    </div>
</div>

<style>
.logout-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(10px);
    z-index: 200000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.logout-modal-overlay.show {
    display: flex;
    animation: fadeIn 0.3s ease;
}

.logout-modal-content {
    background: var(--surface-color, #ffffff);
    border: 1px solid var(--border-color, #e2e8f0);
    padding: 45px 40px;
    border-radius: 32px;
    max-width: 440px;
    width: 100%;
    text-align: center;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    transform: scale(0.9);
}

.logout-modal-overlay.show .logout-modal-content {
    animation: modalPop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes modalPop { 
    0% { transform: scale(0.9); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

.logout-modal-icon {
    width: 85px;
    height: 85px;
    background: #fef2f2;
    color: #ef4444;
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    font-size: 2.2rem;
    transform: rotate(-10deg);
}

.logout-modal-content h2 {
    color: var(--text-color, #1e293b);
    font-weight: 800;
    font-size: 1.7rem;
    margin-bottom: 12px;
    letter-spacing: -0.5px;
}

.logout-modal-content p {
    color: var(--text-muted, #64748b);
    line-height: 1.6;
    margin-bottom: 35px;
    font-size: 1rem;
}

.logout-modal-buttons {
    display: flex; gap: 15px;
}

.logout-modal-buttons button, .logout-modal-buttons a {
    flex: 1; padding: 15px; border-radius: 16px; font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; font-family: 'Poppins', sans-serif; display: flex; align-items: center; justify-content: center;
}

.btn-cancel {
    background: var(--hover-bg, #f1f5f9); color: var(--text-color, #1e293b); border: 1px solid var(--border-color, #e2e8f0);
}

.btn-cancel:hover {
    background: var(--border-color, #e2e8f0);
}

.btn-logout {
    background: #0f172a; color: white; border: none; box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.3);
}

.btn-logout:hover {
    background: #1e293b; transform: translateY(-2px); box-shadow: 0 15px 25px -5px rgba(15, 23, 42, 0.4);
}
</style>
