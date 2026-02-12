<?php
// Super Admin Sidebar Component
$current_page = basename($_SERVER['PHP_SELF']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'superadmin';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@sms.com';
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <a href="/Super-admin/Dashboard.php" class="brand-wrapper">
            <img src="/Assets/image/logo.png" alt="Logo" class="sidebar-logo">
            <h2>Super Admin</h2>
        </a>
    </div>

    <div class="sidebar-menu">
        <p class="menu-label">MAIN</p>
        <ul>
            <li class="<?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>">
                <a href="/Super-admin/Dashboard.php">
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
                    <li><a href="/Super-admin/Modules/User-Management.php">Staff Accounts</a></li>
                    <li><a href="/Super-admin/Modules/Roles.php">Roles & Permissions</a></li>
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
                    <li class="<?php echo ($current_page == 'Admission-Dashboard.php') ? 'active' : ''; ?>"><a href="/Super-admin/Submodules/Admission-Dashboard.php">Admission Dashboard</a></li>
                    <li class="<?php echo ($current_page == 'Applications-Manager.php') ? 'active' : ''; ?>"><a href="/Super-admin/Submodules/Applications-Manager.php">Applications Manager</a></li>
                    <li class="<?php echo ($current_page == 'Evaluation-Desk.php') ? 'active' : ''; ?>"><a href="/Super-admin/Submodules/Evaluation-Desk.php">Evaluation Desk</a></li>
                    <li class="<?php echo ($current_page == 'Student-ID-Center.php') ? 'active' : ''; ?>"><a href="/Super-admin/Submodules/Student-ID-Center.php">Student ID Center</a></li>
                    <li class="<?php echo ($current_page == 'Requirements-Config.php') ? 'active' : ''; ?>"><a href="/Super-admin/Submodules/Requirements-Config.php">Requirements Config</a></li>
                </ul>
            </li>
        </ul>

        <p class="menu-label">ACCOUNT & SETTINGS</p>
        <ul>
            <li>
                <a href="/modules/Profile.php"
                    class="<?php echo ($current_page == 'Profile.php') ? 'active' : ''; ?>">
                    <i class="fas fa-user-circle"></i>
                    <span>My Profile</span>
                </a>
            </li>

            <li>
                <a href="/modules/Settings.php"
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
<div id="logoutModal" class="modal centered">
    <div class="modal-content">
        <div style="text-align: center; padding: 45px;">
            <div style="width: 80px; height: 80px; background: #fee2e2; color: #ef4444; border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; font-size: 2rem; transform: rotate(-5deg); box-shadow: 0 10px 20px rgba(239, 68, 68, 0.1);">
                <i class="fas fa-power-off"></i>
            </div>
            <h2 style="font-weight: 800; color: var(--text-color); margin-bottom: 15px; font-size: 1.6rem; letter-spacing: -1px;">End Session?</h2>
            <p style="color: var(--text-muted); margin-bottom: 35px; line-height: 1.6; font-size: 0.95rem;">Are you sure you want to exit the Super Admin panel?</p>
            <div style="display: flex; gap: 15px;">
                <button onclick="closeLogoutModal()" style="flex: 1; padding: 15px; border-radius: 12px; border: 1px solid var(--border-color); background: var(--surface-color); color: var(--text-color); font-weight: 700; cursor: pointer; transition: 0.3s;">Stay Here</button>
                <a href="/auth/logout.php" style="flex: 1; padding: 15px; border-radius: 12px; background: #ef4444; color: white; font-weight: 700; text-decoration: none; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 15px rgba(239, 68, 68, 0.2); transition: 0.3s;">Exit System</a>
            </div>
        </div>
    </div>
</div>