<?php
// Super Admin Sidebar Component
$current_page = basename($_SERVER['PHP_SELF']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'superadmin';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@sms.com';

// Path logic to handle different directory depths
$is_sub = (strpos($_SERVER['PHP_SELF'], '/Modules/') !== false || strpos($_SERVER['PHP_SELF'], '/Submodules/') !== false);
$base = $is_sub ? '../' : ''; // Relative to Super-admin root
$root = $is_sub ? '../../' : '../'; // Relative to project root
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
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>

        <p class="menu-label">SYSTEM MANAGEMENT</p>
        <ul class="main-menu">
            <!-- User Management Dropdown -->
            <li
                class="has-dropdown <?php echo (strpos($_SERVER['PHP_SELF'], '/Modules/') !== false) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-user-shield"></i>
                    <span>User Accounts</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo ($current_page == 'User-Management.php') ? 'active' : ''; ?>">
                        <a href="<?php echo $base; ?>Modules/User-Management.php">Staff Accounts</a>
                    </li>
                    <li class="<?php echo ($current_page == 'Roles.php') ? 'active' : ''; ?>">
                        <a href="<?php echo $base; ?>Modules/Roles.php">Roles & Permissions</a>
                    </li>
                </ul>
            </li>
        </ul>

        <p class="menu-label">ADMISSION CONTROL</p>
        <ul class="main-menu">
            <!-- Admission Summary Dropdown -->
            <li class="has-dropdown <?php echo (strpos($_SERVER['PHP_SELF'], '/Submodules/') !== false) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-user-graduate"></i>
                    <span>Admission Hub</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo ($current_page == 'Admission-Dashboard.php') ? 'active' : ''; ?>">
                        <a href="<?php echo $base; ?>Submodules/Admission-Dashboard.php">Admission Dashboard</a>
                    </li>
                    <li class="<?php echo ($current_page == 'Applications-Manager.php') ? 'active' : ''; ?>">
                        <a href="<?php echo $base; ?>Submodules/Applications-Manager.php">Applications Manager</a>
                    </li>
                    <li class="<?php echo ($current_page == 'Evaluation-Desk.php') ? 'active' : ''; ?>">
                        <a href="<?php echo $base; ?>Submodules/Evaluation-Desk.php">Evaluation Desk</a>
                    </li>
                    <li class="<?php echo ($current_page == 'Student-ID-Center.php') ? 'active' : ''; ?>">
                        <a href="<?php echo $base; ?>Submodules/Student-ID-Center.php">Student ID Center</a>
                    </li>
                    <li class="<?php echo ($current_page == 'Requirements-Config.php') ? 'active' : ''; ?>">
                        <a href="<?php echo $base; ?>Submodules/Requirements-Config.php">Requirements Config</a>
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
<div id="logoutModal" class="modal centered" style="display: none;">
    <div class="modal-content premium" style="width: 440px; border: none; overflow: visible;">
        <div style="text-align: center; padding: 50px 35px; position: relative;">
            <!-- Premium Icon Header -->
            <div style="position: absolute; top: -35px; left: 50%; transform: translateX(-50%); width: 85px; height: 85px; background: var(--surface-color); border-radius: 28px; display: flex; align-items: center; justify-content: center; box-shadow: 0 15px 30px rgba(244, 63, 94, 0.2); border: 1px solid var(--border-color);">
                <div style="width: 65px; height: 65px; background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); color: white; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                    <i class="fas fa-power-off"></i>
                </div>
            </div>
            
            <div style="margin-top: 35px;">
                <h2 style="font-weight: 800; color: var(--text-color); margin-bottom: 15px; font-size: 1.7rem; letter-spacing: -1px;">End Session?</h2>
                <p style="color: var(--text-muted); margin-bottom: 35px; line-height: 1.6; font-size: 1.05rem; font-weight: 500;">Are you sure you want to exit the Super Admin panel?</p>
                
                <div style="display: flex; gap: 12px;">
                    <button onclick="closeLogoutModal()" style="flex: 1; padding: 15px; border-radius: 16px; border: 1.5px solid var(--border-color); background: var(--surface-color); color: var(--text-color); font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 0.95rem;" onmouseover="this.style.background='var(--hover-bg)'" onmouseout="this.style.background='var(--surface-color)'">No, Stay</button>
                    <a href="/auth/logout.php" style="flex: 1; padding: 15px; border-radius: 16px; background: #0f172a; color: white; font-weight: 700; text-decoration: none; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 15px rgba(15, 23, 42, 0.2); transition: 0.3s; font-size: 0.95rem;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 25px rgba(15, 23, 42, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 15px rgba(15, 23, 42, 0.2)'">Yes, Log Out</a>
                </div>
            </div>
        </div>
    </div>
</div>
