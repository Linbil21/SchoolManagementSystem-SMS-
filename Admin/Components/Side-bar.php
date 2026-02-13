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
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
            if (!headers_sent()) {
                header("Location: /auth/Login.php");
            } else {
                echo '<script>window.location.href = "/auth/Login.php";</script>';
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
        document.getElementById('logoutModal').style.display = 'block';
    }

    function closeLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }

    window.onclick = function (event) {
        const modal = document.getElementById('logoutModal');
        if (event.target == modal) {
            closeLogoutModal();
        }
    }
</script>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal-overlay">
    <div class="modal-content confirm-modal">
        <div class="modal-icon warning">
            <i class="fas fa-power-off"></i>
        </div>
        <h2>Confirm Logout</h2>
        <p>Are you sure you want to log out? Your current session will be ended.</p>
        <div class="modal-actions">
            <button onclick="closeLogoutModal()" class="btn-cancel">Cancel</button>
            <a href="/auth/logout.php" class="btn-confirm-delete">Logout</a>
        </div>
    </div>
</div>