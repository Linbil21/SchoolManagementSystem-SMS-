<?php
// Admin Side-bar Component
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <a href="/Admin/Dashboard.php" class="brand-wrapper">
            <img src="/Assets/image/logo.png" alt="Logo" class="sidebar-logo">
            <h2>Admin</h2>
        </a>
    </div>

    <div class="sidebar-menu">
        <p class="menu-label">MAIN</p>
        <ul>
            <li class="<?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>">
                <a href="/Admin/Dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
            </li>
        </ul>

        <p class="menu-label">MANAGEMENT</p>
        <ul>
            <li class="has-dropdown <?php echo ($current_page == 'Enrollment.php') ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-user-graduate"></i>
                    <span>Enrollment</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/Admin/Modules/Enrollment-Queue.php"><i class="fas fa-list-ol"></i> <span>Enrollment
                                Queue</span></a></li>
                    <li><a href="/Admin/Modules/Enrollment.php"><i class="fas fa-clipboard-list"></i>
                            <span>Enrollment List</span></a></li>
                    <li><a href="/Admin/Modules/Subject-Enrollment.php"><i class="fas fa-book"></i> <span>Subject
                                Enrollment</span></a></li>
                    <li><a href="/Admin/Modules/Section-Assignment.php"><i class="fas fa-users-viewfinder"></i>
                            <span>Section Assignment</span></a></li>
                    <li><a href="/Admin/Modules/Payments-Fees.php"><i class="fas fa-file-invoice-dollar"></i>
                            <span>Payments & Fees</span></a></li>
                    <li><a href="/Admin/Modules/Enrollment-History.php"><i class="fas fa-history"></i>
                            <span>Enrollment History</span></a></li>
                    <li><a href="/Admin/Modules/Reports.php"><i class="fas fa-chart-line"></i>
                            <span>Reports</span></a></li>
                </ul>
            </li>

            <li class="has-dropdown <?php echo ($current_page == 'User-Management.php') ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-users-cog"></i>
                    <span>User Management</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/Admin/Submodules/Admin-Users.php"><i class="fas fa-user-shield"></i> <span>Admin
                                Users</span></a></li>
                    <li><a href="/Admin/Submodules/Staff-Registration.php"><i class="fas fa-id-card-alt"></i>
                            <span>Staff / Registration</span></a></li>
                    <li><a href="/Admin/Submodules/Student-Accounts.php"><i class="fas fa-user-graduate"></i>
                            <span>Student Accounts</span></a></li>
                    <li><a href="/Admin/Submodules/Roles-Permissions.php"><i class="fas fa-user-tag"></i>
                            <span>Roles & Permissions</span></a></li>
                    <li><a href="/Admin/Submodules/Account-Status.php"><i class="fas fa-user-check"></i>
                            <span>Account Status</span></a></li>
                </ul>
            </li>


            <!-- New Faculty External Link -->
            <li class="<?php echo ($current_page == 'Faculty-Masterlist.php') ? 'active' : ''; ?>">
                <a href="/Admin/Modules/Faculty-Masterlist.php">
                    <i class="fas fa-chalkboard-teacher"></i> 
                    <span>External Faculty</span>
                </a>
            </li>
        </ul>

        <!-- <p class="menu-label">OPERATIONS</p>
        <ul>
            <li><a href="#"><i class="fas fa-money-check-alt"></i> <span>Payments</span></a></li>
            <li><a href="#"><i class="fas fa-file-invoice-dollar"></i> <span>Payment Queue</span></a></li>
        </ul> -->

        <p class="menu-label">ACCOUNT</p>
        <ul>
            <li class="<?php echo ($current_page == 'Profile.php') ? 'active' : ''; ?>">
                <a href="/modules/Profile.php"><i class="fas fa-user-circle"></i> <span>Profile</span></a>
            </li>
            <li class="<?php echo ($current_page == 'Settings.php') ? 'active' : ''; ?>">
                <a href="/modules/Settings.php"><i class="fas fa-sliders-h"></i> <span>Settings</span></a>
            </li>
            <li>
                <a href="javascript:void(0)" onclick="openLogoutModal()" style="color: #ef4444;"><i
                        class="fas fa-power-off"></i> <span>Log Out</span></a>
            </li>
        </ul>
    </div>

    <div class="sidebar-profile">
        <?php
        // Robust absolute-relative path logic
        $script_name = $_SERVER['SCRIPT_NAME'];
        $check_paths = ['/Super-admin/', '/modules/', '/Admin/', '/submodules/', '/Cashier/', '/Admission/', '/auth/', '/student/'];
        $project_base = '';
        foreach ($check_paths as $path) {
            if (($pos = stripos($script_name, $path)) !== false) {
                $project_base = rtrim(substr($script_name, 0, $pos), '/');
                break;
            }
        }
        $root = $project_base . '/';
        if ($root === '/') $root = '/sms/'; // Common XAMPP fallback

        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
            if (!headers_sent()) {
                header("Location: " . $root . "auth/Login.php");
            } else {
                echo '<script>window.location.href = "' . $root . 'auth/Login.php";</script>';
            }
            exit();
        }
        $profile_email = $_SESSION['email'] ?? 'admin@sms.com';
        ?>
        <div class="profile-card">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($profile_email); ?>&background=1648bc&color=fff"
                alt="Profile">
            <div class="profile-info">
                <h4>Sample Admin</h4>
                <p>
                    <?php echo ucfirst($_SESSION['role']); ?>
                </p>
            </div>
            <span class="status-dot"></span>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sidebar toggle
        const toggleBtn = document.getElementById('sidebar-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                document.querySelector('.sidebar').classList.toggle('collapsed');
            });
        }

        // Dropdown toggle
        const dropdowns = document.querySelectorAll('.dropdown-toggle');
        dropdowns.forEach(toggle => {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const parent = this.parentElement;

                // Toggle current dropdown
                parent.classList.toggle('open');

                // Optional: Close other dropdowns
                document.querySelectorAll('.has-dropdown').forEach(item => {
                    if (item !== parent) {
                        item.classList.remove('open');
                    }
                });
            });
        });
    });

    function openLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    window.onclick = function (event) {
        const modal = document.getElementById('logoutModal');
        if (event.target == modal) {
            closeLogoutModal();
        }
    }
</script>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="logout-modal-overlay">
    <div class="logout-modal-content">
        <div class="logout-modal-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <h2>Ready to Leave?</h2>
        <p>Your current session will be ended. Make sure you've saved any changes before logging out.</p>
        <div class="logout-modal-buttons">
            <button onclick="closeLogoutModal()" class="btn-cancel">Stay Here</button>
            <a href="<?php echo $root; ?>auth/logout.php" class="btn-logout">Sign Out</a>
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
    display: flex;
    gap: 15px;
}

.logout-modal-buttons button, .logout-modal-buttons a {
    flex: 1;
    padding: 15px;
    border-radius: 16px;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    font-family: 'Poppins', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-cancel {
    background: var(--hover-bg, #f1f5f9);
    color: var(--text-color, #1e293b);
    border: 1px solid var(--border-color, #e2e8f0);
}

.btn-cancel:hover {
    background: var(--border-color, #e2e8f0);
}

.btn-logout {
    background: #ef4444;
    color: white;
    border: none;
    box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3);
}

.btn-logout:hover {
    background: #dc2626;
    transform: translateY(-2px);
    box-shadow: 0 15px 25px -5px rgba(239, 68, 68, 0.4);
}
</style>