<?php
// Admission Sidebar Component
$current_page = basename($_SERVER['PHP_SELF']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'admission';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admission@sms.com';
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <a href="/Admission/Dashboard.php" class="brand-wrapper">
            <img src="/Assets/image/logo.png" alt="Logo" class="sidebar-logo">
            <h2>Admission</h2>
        </a>
    </div>

    <div class="sidebar-menu">
        <!-- Dashboard Section -->
        <p class="menu-label">DASHBOARD</p>
        <ul class="main-menu">
            <li class="has-dropdown <?php echo ($current_page == 'Dashboard.php') ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <div class="icon-box">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <span>Dashboard</span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo (isset($_GET['view']) && $_GET['view'] == 'summary') ? 'active' : ''; ?>">
                        <a href="/Admission/Dashboard.php?view=summary">Application Summary</a>
                    </li>
                    <li class="<?php echo (isset($_GET['view']) && $_GET['view'] == 'pending') ? 'active' : ''; ?>">
                        <a href="/Admission/Dashboard.php?view=pending">Pending Evaluation</a>
                    </li>
                    <li class="<?php echo (isset($_GET['view']) && $_GET['view'] == 'notifications') ? 'active' : ''; ?>">
                        <a href="/Admission/Dashboard.php?view=notifications">Notifications</a>
                    </li>
                </ul>
            </li>
        </ul>

        <!-- Applications Section -->
        <p class="menu-label">ADMISSION PROCESS</p>
        <ul class="main-menu">
            <!-- Applications Dropdown -->
            <li class="has-dropdown <?php echo (in_array($current_page, ['New-Applications.php', 'For-Evaluation.php', 'Approved.php', 'Rejected.php', 'Archived.php'])) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <div class="icon-box">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <span>Applications</span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo ($current_page == 'New-Applications.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/New-Applications.php">New Applications</a></li>
                    <li class="<?php echo ($current_page == 'For-Evaluation.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/For-Evaluation.php">For Evaluation</a></li>
                    <li class="<?php echo ($current_page == 'Approved.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Approved.php">Approved</a></li>
                    <li class="<?php echo ($current_page == 'Rejected.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Rejected.php">Rejected</a></li>
                    <li class="<?php echo ($current_page == 'Archived.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Archived.php">Archived</a></li>
                </ul>
            </li>

            <!-- Application Evaluation Dropdown -->
            <li class="has-dropdown <?php echo (in_array($current_page, ['Document-Review.php', 'Exam-Results.php', 'Interview-Assessment.php', 'Evaluation-Summary.php'])) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <div class="icon-box">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <span>Application Evaluation</span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo ($current_page == 'Document-Review.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Document-Review.php">Document Review</a></li>
                    <li class="<?php echo ($current_page == 'Exam-Results.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Exam-Results.php">Entrance Exam Results</a></li>
                    <li class="<?php echo ($current_page == 'Interview-Assessment.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Interview-Assessment.php">Interview Assessment</a></li>
                    <li class="<?php echo ($current_page == 'Evaluation-Summary.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Evaluation-Summary.php">Evaluation Summary</a></li>
                </ul>
            </li>

            <!-- Student Management -->
            <li class="has-dropdown <?php echo (in_array($current_page, ['Generate-ID.php', 'ID-Verification.php', 'Print-Export-ID.php', 'Lost-Replacement-IDs.php', 'Student-Grades.php', 'Student-Attendance.php'])) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <div class="icon-box">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <span>Student Management</span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo ($current_page == 'Generate-ID.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Generate-ID.php">Generate Student ID</a></li>
                    <li class="<?php echo ($current_page == 'ID-Verification.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/ID-Verification.php">ID Verification</a></li>
                    <li class="<?php echo ($current_page == 'Print-Export-ID.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Print-Export-ID.php">Print / Export ID</a></li>
                    <li class="<?php echo ($current_page == 'Lost-Replacement-IDs.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Lost-Replacement-IDs.php">Lost / Replacement IDs</a></li>
                    <li class="<?php echo ($current_page == 'Student-Grades.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Student-Grades.php">My Grades</a></li>
                    <li class="<?php echo ($current_page == 'Student-Attendance.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Student-Attendance.php">Attendance</a></li>
                    <li class="<?php echo ($current_page == 'Fetch-Table.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Fetch-Table.php"><i class="fas fa-sync-alt" style="font-size: 0.75rem; color: #10b981;"></i> Current Subject Fetcher</a></li>
                </ul>
            </li>
        </ul>

        <!-- Requirements & Results -->
        <p class="menu-label">MANAGEMENT</p>
        <ul class="main-menu">
            <!-- Admission Requirements -->
            <li class="has-dropdown <?php echo (in_array($current_page, ['Requirement-List.php', 'Submission-Status.php', 'Validation-Rules.php'])) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <div class="icon-box">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <span>Admission Requirements</span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo ($current_page == 'Requirement-List.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Requirement-List.php">Requirement List</a></li>
                    <li class="<?php echo ($current_page == 'Submission-Status.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Submission-Status.php">Submission Status</a></li>
                    <li class="<?php echo ($current_page == 'Validation-Rules.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Validation-Rules.php">Validation Rules</a></li>
                </ul>
            </li>

            <!-- Admission Results -->
            <li class="has-dropdown <?php echo (in_array($current_page, ['Passers-List.php', 'Waitlisted.php', 'Result-Notifications.php'])) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <div class="icon-box">
                        <i class="fas fa-poll"></i>
                    </div>
                    <span>Admission Results</span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo ($current_page == 'Passers-List.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Passers-List.php">Passers List</a></li>
                    <li class="<?php echo ($current_page == 'Waitlisted.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Waitlisted.php">Waitlisted</a></li>
                    <li class="<?php echo ($current_page == 'Result-Notifications.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Result-Notifications.php">Notifications</a></li>
                </ul>
            </li>
        </ul>

        <!-- Reports & Analytics -->
        <p class="menu-label">REPORTS & ANALYTICS</p>
        <ul class="main-menu">
            <li class="has-dropdown <?php echo (in_array($current_page, ['Applications-Summary.php', 'Evaluation-Statistics.php', 'ID-Reports.php'])) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <div class="icon-box">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <span>Reports</span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo ($current_page == 'Applications-Summary.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Applications-Summary.php">Applications Summary</a></li>
                    <li class="<?php echo ($current_page == 'Evaluation-Statistics.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Evaluation-Statistics.php">Evaluation Statistics</a></li>
                    <li class="<?php echo ($current_page == 'ID-Reports.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/ID-Reports.php">Student ID Reports</a></li>
                </ul>
            </li>
        </ul>

        <!-- Settings Section -->
        <p class="menu-label">SYSTEM CONFIG</p>
        <ul class="main-menu">
            <li class="has-dropdown <?php echo (in_array($current_page, ['Admission-Year.php', 'Cut-off-Score.php', 'Notification-Templates.php'])) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <div class="icon-box">
                        <i class="fas fa-cog"></i>
                    </div>
                    <span>Settings</span>
                    <i class="fas fa-chevron-down arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo ($current_page == 'Admission-Year.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Admission-Year.php">Admission Year</a></li>
                    <li class="<?php echo ($current_page == 'Cut-off-Score.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Cut-off-Score.php">Cut-off Score</a></li>
                    <li class="<?php echo ($current_page == 'Notification-Templates.php') ? 'active' : ''; ?>"><a href="/Admission/Modules/Notification-Templates.php">Notification Templates</a></li>
                </ul>
            </li>
            <li class="<?php echo ($current_page == 'Profile.php') ? 'active' : ''; ?>">
                <a href="/modules/Profile.php">
                    <i class="fas fa-user-circle"></i>
                    <span>My Profile</span>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'Settings.php') ? 'active' : ''; ?>">
                <a href="/modules/Settings.php">
                    <i class="fas fa-cog"></i>
                    <span>Account Settings</span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" onclick="openLogoutModal()" style="color: #ef4444;">
                    <i class="fas fa-power-off"></i>
                    <span>Log Out</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-profile">
        <div class="profile-card">
            <?php 
            $initials = "AD"; // Standard for Admission
            if(isset($_SESSION['fullname'])) {
                $names = explode(' ', $_SESSION['fullname']);
                $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
            }
            ?>
            <div class="avatar-circle">
                <?php echo $initials; ?>
            </div>
            <div class="profile-info">
                <h4>Admission Staff</h4>
                <p>Admission Office</p>
            </div>
            <span class="status-dot"></span>
        </div>
    </div>
</div>

<style>
    .sidebar {
        width: 260px;
        height: 100vh;
        background: var(--sidebar-bg);
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--border-color);
        position: sticky;
        top: 0;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .sidebar-brand {
        padding: 25px 20px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .brand-wrapper {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .sidebar-logo {
        width: 45px;
        height: auto;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.05));
    }

    .sidebar-brand h2 {
        color: var(--accent-color);
        font-size: 1.2rem;
        font-weight: 800;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .sidebar-menu {
        flex: 1;
        padding: 0 20px;
        overflow-y: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .sidebar-menu::-webkit-scrollbar {
        display: none;
    }

    .menu-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--text-muted);
        margin: 20px 0 10px 10px;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .sidebar-menu ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-menu ul li {
        margin-bottom: 4px;
    }

    .sidebar-menu ul li a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 15px;
        text-decoration: none;
        color: var(--text-muted);
        font-size: 0.9rem;
        font-weight: 500;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .sidebar-menu ul li.active>a {
        background: var(--hover-bg);
        color: var(--accent-color);
        font-weight: 700;
    }

    .sidebar-menu ul li a:hover {
        background: var(--hover-bg);
        color: var(--accent-color);
    }

    .icon-box {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: 0.3s;
        color: var(--text-muted);
    }

    .has-dropdown.active.open > a {
        background: var(--hover-bg);
        color: var(--accent-color);
    }

    .has-dropdown.active.open .icon-box {
        background: var(--accent-color);
        color: white;
    }

    .sub-menu {
        list-style: none;
        padding-left: 15px;
        margin-top: 2px;
        margin-bottom: 5px;
        border-left: 1px dashed var(--border-color);
        margin-left: 24px;
        display: none;
    }

    .has-dropdown.open .sub-menu {
        display: block;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .arrow-icon {
        margin-left: auto;
        font-size: 0.8rem !important;
        transition: transform 0.3s ease;
        color: var(--text-muted);
    }

    .has-dropdown.open .arrow-icon {
        transform: rotate(180deg);
        color: var(--accent-color);
    }

    .sub-menu li a {
        padding: 8px 15px !important;
        font-size: 0.8rem !important;
        color: var(--text-muted) !important;
    }

    .sub-menu li a:hover, .sub-menu li.active a {
        color: var(--accent-color) !important;
        background: transparent !important;
    }

    /* Profile Section */
    .sidebar-profile {
        padding: 20px;
        border-top: 1px solid var(--border-color);
    }

    .profile-card {
        background: var(--hover-bg);
        padding: 12px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        border: 1px solid var(--border-color);
    }

    .profile-info h4 {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-color);
        margin: 0;
    }

    .profile-info p {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin: 0;
    }

    .status-dot {
        position: absolute;
        bottom: 12px;
        left: 42px;
        width: 10px;
        height: 10px;
        background: #22c55e;
        border: 2px solid var(--surface-color);
        border-radius: 50%;
        box-shadow: 0 0 10px rgba(34, 197, 94, 0.4);
    }

    /* Collapsed State */
    .sidebar.collapsed {
        width: 85px;
    }

    .sidebar.collapsed .sidebar-brand h2,
    .sidebar.collapsed .menu-label,
    .sidebar.collapsed .sidebar-menu ul li a span,
    .sidebar.collapsed .arrow-icon,
    .sidebar.collapsed .profile-info,
    .sidebar.collapsed .status-dot,
    .sidebar.collapsed .sub-menu {
        display: none !important;
    }
    .sidebar.collapsed .sidebar-brand {
        padding: 20px 10px;
    }

    .sidebar.collapsed .sidebar-menu {
        padding: 0 15px;
    }

    .sidebar.collapsed .sidebar-menu ul li a {
        justify-content: center;
        padding: 12px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Dropdown toggle
        const dropdowns = document.querySelectorAll('.dropdown-toggle');
        dropdowns.forEach(toggle => {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const parent = this.parentElement;

                // Toggle current dropdown
                const isOpen = parent.classList.contains('open');

                // Close other dropdowns
                document.querySelectorAll('.has-dropdown').forEach(item => {
                    item.classList.remove('open');
                });

                if (!isOpen) {
                    parent.classList.add('open');
                }
            });
        });

        // Sidebar toggle persistence (optional)
        const toggleBtn = document.getElementById('sidebar-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                document.querySelector('.sidebar').classList.toggle('collapsed');
            });
        }
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
<div id="logoutModal" class="modal"
    style="display:none; z-index: 9999; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); position: fixed; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden;">
    <div class="modal-content"
        style="max-width: 400px; text-align: center; border-radius: 24px; padding: 40px; margin: 15% auto; position: relative; background-color: var(--surface-color); color: var(--text-color); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid var(--border-color); font-family: 'Poppins', sans-serif;">
        <div
            style="width: 70px; height: 70px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 1.8rem;">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <h2 style="margin-bottom: 12px; color: var(--text-color); font-weight: 700; font-size: 1.5rem;">Confirm Logout</h2>
        <p style="color: var(--text-muted); margin-bottom: 32px; line-height: 1.6; font-size: 0.95rem;">Are you sure you want to
            end your session? Any unsaved changes might be lost.</p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button onclick="closeLogoutModal()"
                style="flex: 1; padding: 12px; border-radius: 12px; border: 1px solid var(--border-color); background: var(--hover-bg); color: var(--text-color); font-weight: 600; cursor: pointer; transition: 0.3s; font-family: inherit;">Stay
                Here</button>
            <a href="/auth/logout.php"
                style="flex: 1; padding: 12px; border-radius: 12px; background: #ef4444; color: white; font-weight: 600; text-decoration: none; display: inline-block; transition: 0.3s; font-family: inherit; font-size: 0.95rem; border: none; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);">Sign
                Out</a>
        </div>
    </div>
</div>