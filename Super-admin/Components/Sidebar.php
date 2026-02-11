<?php
// Super Admin Sidebar Component
$current_page = basename($_SERVER['PHP_SELF']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'superadmin';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@sms.com';
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <a href="/sms/Super-admin/Dashboard.php" class="brand-wrapper">
            <img src="/sms/Assets/image/logo.png" alt="Logo" class="sidebar-logo">
            <h2>Super Admin</h2>
        </a>
    </div>

    <div class="sidebar-menu">
        <p class="menu-label">MAIN</p>
        <ul>
            <li class="<?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>">
                <a href="/sms/Super-admin/Dashboard.php">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>

        <p class="menu-label">SYSTEM MANAGEMENT</p>
        <ul class="main-menu">
            <!-- User Management Dropdown -->
            <li
                class="has-dropdown <?php echo (strpos($_SERVER['PHP_SELF'], 'Modules/User-Management') !== false) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-user-shield"></i>
                    <span>User Accounts</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/sms/Super-admin/Modules/User-Management.php">Staff Accounts</a></li>
                    <li><a href="/sms/Super-admin/Modules/Roles.php">Roles & Permissions</a></li>
                </ul>
            </li>
        </ul>

        <p class="menu-label">ADMISSION CONTROL</p>
        <ul class="main-menu">
            <!-- Admission Summary Dropdown -->
            <li class="has-dropdown <?php echo (strpos($_SERVER['PHP_SELF'], 'Submodules/') !== false) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-user-graduate"></i>
                    <span>Admission Hub</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo ($current_page == 'Admission-Dashboard.php') ? 'active' : ''; ?>"><a href="/sms/Super-admin/Submodules/Admission-Dashboard.php">Admission Dashboard</a></li>
                    <li class="<?php echo ($current_page == 'Applications-Manager.php') ? 'active' : ''; ?>"><a href="/sms/Super-admin/Submodules/Applications-Manager.php">Applications Manager</a></li>
                    <li class="<?php echo ($current_page == 'Evaluation-Desk.php') ? 'active' : ''; ?>"><a href="/sms/Super-admin/Submodules/Evaluation-Desk.php">Evaluation Desk</a></li>
                    <li class="<?php echo ($current_page == 'Student-ID-Center.php') ? 'active' : ''; ?>"><a href="/sms/Super-admin/Submodules/Student-ID-Center.php">Student ID Center</a></li>
                    <li class="<?php echo ($current_page == 'Requirements-Config.php') ? 'active' : ''; ?>"><a href="/sms/Super-admin/Submodules/Requirements-Config.php">Requirements Config</a></li>
                </ul>
            </li>
        </ul>

        <p class="menu-label">ACCOUNT & SETTINGS</p>
        <ul>
            <li>
                <a href="/sms/modules/Profile.php"
                    class="<?php echo ($current_page == 'Profile.php') ? 'active' : ''; ?>">
                    <i class="fas fa-user-circle"></i>
                    <span>My Profile</span>
                </a>
            </li>

            <li>
                <a href="/sms/modules/Settings.php"
                    class="<?php echo ($current_page == 'Settings.php') ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>

            <li>
                <a href="javascript:void(0)" onclick="openLogoutModal()" style="color: #ef4444;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <div class="user-peek">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($email); ?>&background=1648bc&color=fff"
                alt="User">
            <div class="user-peek-info">
                <h4>Super Admin</h4>
                <p><?php echo $email; ?></p>
            </div>
        </div>
    </div>
</div>


<style>
    .sidebar {
        width: 280px;
        height: 100vh;
        background: var(--sidebar-bg);
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--border-color);
        position: sticky;
        top: 0;
        z-index: 1000;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-brand {
        padding: 30px 25px;
    }

    .brand-wrapper {
        display: flex;
        align-items: center;
        gap: 15px;
        text-decoration: none;
    }

    .sidebar-logo {
        width: 40px;
        height: 40px;
        object-fit: contain;
        filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
    }

    .sidebar-brand h2 {
        color: var(--accent-color);
        font-size: 1.3rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        text-transform: uppercase;
    }

    .sidebar-menu {
        flex: 1;
        padding: 0 15px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--border-color) transparent;
    }

    .menu-label {
        font-size: 0.72rem;
        font-weight: 800;
        color: var(--text-muted);
        margin: 25px 0 10px 15px;
        letter-spacing: 1.2px;
        text-transform: uppercase;
    }

    .sidebar-menu ul { list-style: none; padding: 0; }
    
    .sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        text-decoration: none;
        color: var(--text-color);
        font-size: 0.92rem;
        font-weight: 500;
        border-radius: 12px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-menu a:hover,
    .sidebar-menu li.active > a {
        background: var(--hover-bg);
        color: var(--accent-color);
        font-weight: 600;
    }

    .sidebar-menu li.active > a {
        border-left: 4px solid var(--accent-color);
        border-radius: 0 12px 12px 0;
    }

    .has-dropdown .sub-menu {
        max-height: 0;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        padding-left: 20px;
    }

    .has-dropdown.open .sub-menu {
        max-height: 500px;
        margin-top: 5px;
        margin-bottom: 10px;
    }

    .sub-menu a {
        padding: 8px 15px;
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .arrow-icon {
        margin-left: auto;
        font-size: 0.7rem;
        transition: 0.3s;
    }

    .open > a .arrow-icon {
        transform: rotate(90deg);
    }

    .sidebar-footer {
        padding: 20px;
        border-top: 1px solid var(--border-color);
    }

    .user-peek {
        background: var(--hover-bg);
        padding: 12px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        transition: 0.3s;
    }

    .user-peek img {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        border: 2px solid white;
    }

    .user-peek-info h4 {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-color);
        margin: 0;
    }

    .user-peek-info p {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin: 0;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-content {
        background: var(--surface-color);
        border-radius: 24px;
        width: 100%;
        max-width: 400px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: modalSlide 0.3s ease-out;
    }

    @keyframes modalSlide {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    document.querySelectorAll('.dropdown-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const parent = button.parentElement;
            parent.classList.toggle('open');
        });
    });

    function openLogoutModal() {
        document.getElementById('logoutModal').style.display = 'flex';
    }

    function closeLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }

    window.onclick = function(e) {
        if (e.target.id === 'logoutModal') closeLogoutModal();
    }
</script>

<!-- Logout Modal -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <div style="text-align: center; padding: 40px;">
            <div style="width: 70px; height: 70px; background: #fee2e2; color: #ef4444; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 1.8rem; transform: rotate(-10deg);">
                <i class="fas fa-power-off"></i>
            </div>
            <h2 style="font-weight: 800; color: var(--text-color); margin-bottom: 12px; font-size: 1.5rem;">End Session?</h2>
            <p style="color: var(--text-muted); margin-bottom: 32px; line-height: 1.6; font-size: 0.95rem;">Are you sure you want to log out of the Super Admin panel?</p>
            <div style="display: flex; gap: 12px;">
                <button onclick="closeLogoutModal()" style="flex: 1; padding: 14px; border-radius: 12px; border: 1px solid var(--border-color); background: var(--surface-color); color: var(--text-color); font-weight: 700; cursor: pointer; transition: 0.3s;">Cancel</button>
                <a href="/sms/auth/logout.php" style="flex: 1; padding: 14px; border-radius: 12px; background: #ef4444; color: white; font-weight: 700; text-decoration: none; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2); transition: 0.3s;">Logout</a>
            </div>
        </div>
    </div>
</div>