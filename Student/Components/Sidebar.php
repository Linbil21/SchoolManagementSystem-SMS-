<?php
// Student Sidebar Component
$current_page = basename($_SERVER['PHP_SELF']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'student';
$student_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Student';

// Helper to check if a dropdown should be open
function isDropdownOpen($searchStrings)
{
    if (!is_array($searchStrings)) {
        $searchStrings = [$searchStrings];
    }
    $current = basename($_SERVER['PHP_SELF']);
    foreach ($searchStrings as $str) {
        if ($str === $current || strpos($current, $str) !== false) {
            return 'active open';
        }
    }
    return '';
}
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <a href="/Student/Dashboard.php" class="brand-wrapper">
            <i class="fas fa-graduation-cap" style="font-size: 1.8rem; color: #2563eb;"></i>
            <h2>Student<span style="color: #64748b; font-weight: 400; font-size: 1rem; margin-left: 5px;">Portal</span>
            </h2>
        </a>
    </div>

    <div class="sidebar-menu">
        <p class="menu-label">MAIN</p>
        <ul class="main-menu">
            <li class="<?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>">
                <a href="/Student/Dashboard.php">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>

        <p class="menu-label">ACADEMIC</p>
        <ul class="main-menu">
            <!-- Admission -->
            <li
                class="has-dropdown <?php echo isDropdownOpen(['Admission-Apply', 'Requirements', 'Exam-Schedule', 'Interview', 'Admission-Result', 'Admission-History']); ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-university"></i>
                    <span>Admission</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/Student/Modules/Admission/Apply.php">Apply for Admission</a></li>
                    <li><a href="/Student/Modules/Admission/Requirements.php">Upload Requirements</a></li>
                    <li><a href="/Student/Modules/Admission/Exam-Schedule.php">Entrance Exam Schedule</a></li>
                    <li><a href="/Student/Modules/Admission/Interview.php">Interview Schedule</a></li>
                    <li><a href="/Student/Modules/Admission/Result.php">View Admission Result</a></li>
                    <li><a href="/Student/Modules/Admission/History.php">Admission History</a></li>
                </ul>
            </li>

            <!-- Enrollment -->
            <li
                class="has-dropdown <?php echo isDropdownOpen(['Subject-Selection', 'Assessment', 'Enrollment-Status', 'Upload-Payment', 'Enrollment-History']); ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-book-open"></i>
                    <span>Enrollment</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/Student/Modules/Enrollment/Subject-Selection.php">Subject Selection</a></li>
                    <li><a href="/Student/Modules/Enrollment/View-Assessment.php">View Assessment</a></li>
                    <li><a href="/Student/Modules/Enrollment/Enrollment-Status.php">Enrollment Status</a></li>
                    <li><a href="/Student/Modules/Enrollment/Upload-Payment.php">Upload Payment</a></li>
                    <li><a href="/Student/Modules/Enrollment/History.php">Enrollment History</a></li>
                </ul>
            </li>

            <!-- My Studies -->
            <li class="has-dropdown <?php echo isDropdownOpen(['Schedule.php', 'Grades.php', 'Attendance.php']); ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-user-graduate"></i>
                    <span>My Studies</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo ($current_page == 'Schedule.php') ? 'active' : ''; ?>">
                        <a href="/Student/Modules/Academic/Schedule.php">Class Schedule</a>
                    </li>
                    <li><a href="javascript:void(0)" style="opacity: 0.5;">My Grades (Soon)</a></li>
                    <li><a href="javascript:void(0)" style="opacity: 0.5;">Attendance (Soon)</a></li>
                </ul>
            </li>
        </ul>

        <p class="menu-label">FINANCIAL</p>
        <ul class="main-menu">
            <!-- Payments -->
            <li class="has-dropdown <?php echo isDropdownOpen(['Balance', 'Payment-History', 'Upload-Receipt']); ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-wallet"></i>
                    <span>Payments</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/Student/Modules/Payments/Balance.php">View Balance</a></li>
                    <li><a href="/Student/Modules/Payments/History.php">Payment History</a></li>
                    <li><a href="/Student/Modules/Payments/Upload-Receipt.php">Upload Receipt</a></li>
                </ul>
            </li>
        </ul>

        <p class="menu-label">SERVICES</p>
        <ul class="main-menu">
            <!-- Student ID -->
            <li class="has-dropdown <?php echo isDropdownOpen(['View-ID', 'Download-ID', 'Replacement']); ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-id-card"></i>
                    <span>Student ID</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/Student/Modules/ID/View.php">View Student ID</a></li>
                    <li><a href="/Student/Modules/ID/Download.php">Download / Print ID</a></li>
                    <li><a href="/Student/Modules/ID/Replacement.php">Replacement Request</a></li>
                </ul>
            </li>

            <!-- Support -->
            <li class="has-dropdown <?php echo isDropdownOpen(['Announcements', 'Messages', 'Help']); ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-headset"></i>
                    <span>Support</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/Student/Modules/Support/Announcements.php">Announcements</a></li>
                    <li><a href="/Student/Modules/Support/Messages.php">Messages</a></li>
                    <li><a href="/Student/Modules/Support/Help.php">Help / FAQs</a></li>
                </ul>
            </li>
        </ul>

        <p class="menu-label">ACCOUNT</p>
        <ul class="main-menu">
            <!-- Profile -->
            <li class="has-dropdown <?php echo isDropdownOpen(['profile', 'Change-Password', 'Settings']); ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-user-circle"></i>
                    <span>Profile</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/Student/Submodules/profile.php">Personal Information</a></li>
                    <li><a href="/Student/Submodules/Change-Password.php">Change Password</a></li>
                    <li><a href="/Student/Submodules/Settings.php">Account Settings</a></li>
                </ul>
            </li>

            <li>
                <a href="javascript:void(0)" onclick="openLogoutModal()" style="color: #ef4444;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-profile">
        <div class="profile-card">
            <?php
            $profile_img = isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image'])
                ? "/" . $_SESSION['profile_image']
                : "https://ui-avatars.com/api/?name=" . urlencode($student_name) . "&background=2563eb&color=fff";
            ?>
            <img src="<?php echo $profile_img; ?>" alt="Profile" style="object-fit: cover;">
            <div class="profile-info">
                <h4><?php echo htmlspecialchars($student_name); ?></h4>
                <p>Student Account</p>
            </div>
            <span class="status-dot"></span>
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
        gap: 12px;
        text-decoration: none;
    }

    .sidebar-brand h2 {
        color: var(--text-color);
        font-size: 1.3rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        display: flex;
        align-items: baseline;
    }

    .sidebar-menu {
        flex: 1;
        padding: 0 15px;
        overflow-y: auto;
    }

    .sidebar-menu::-webkit-scrollbar {
        width: 5px;
    }

    .sidebar-menu::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 10px;
    }

    .menu-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-muted);
        margin: 25px 0 10px 15px;
        letter-spacing: 1.2px;
        text-transform: uppercase;
    }

    .main-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .main-menu li {
        margin-bottom: 4px;
    }

    .main-menu a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        text-decoration: none;
        color: var(--text-color);
        font-size: 0.92rem;
        font-weight: 500;
        border-radius: 12px;
        transition: all 0.2s;
    }

    .main-menu a:hover {
        background: var(--hover-bg);
        color: var(--accent-color);
    }

    .main-menu li.active>a {
        background: var(--hover-bg);
        color: var(--accent-color);
        font-weight: 600;
    }

    .main-menu a i {
        width: 20px;
        font-size: 1.1rem;
    }

    /* Dropdown Styles */
    .sub-menu {
        display: none;
        list-style: none;
        padding-left: 20px;
        margin-top: 5px;
        margin-bottom: 10px;
    }

    .has-dropdown.open .sub-menu {
        display: block;
    }

    .has-dropdown.animating .sub-menu {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .arrow-icon {
        margin-left: auto;
        font-size: 0.75rem;
        transition: transform 0.3s;
        color: var(--text-muted);
    }

    .has-dropdown.open .arrow-icon {
        transform: rotate(90deg);
        color: var(--accent-color);
    }

    .sub-menu a {
        padding: 10px 15px;
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .sub-menu a:hover {
        color: var(--accent-color);
    }

    .sidebar-profile {
        padding: 20px;
        border-top: 1px solid var(--border-color);
    }

    .profile-card {
        background: var(--hover-bg);
        padding: 12px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
    }

    .profile-card img {
        width: 42px;
        height: 42px;
        border-radius: 12px;
    }

    .profile-info h4 {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-color);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 140px;
    }

    .profile-info p {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .status-dot {
        position: absolute;
        bottom: 12px;
        left: 45px;
        width: 10px;
        height: 10px;
        background: #22c55e;
        border: 2px solid var(--surface-color);
        border-radius: 50%;
    }
</style>

<script>
    // Toggle for Dropdowns
    document.querySelectorAll('.dropdown-toggle').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const parent = button.parentElement;

            // Add animating class for transition
            parent.classList.add('animating');

            // Remove animating class after animation completes
            setTimeout(() => {
                parent.classList.remove('animating');
            }, 300);

            parent.classList.toggle('open');
        });
    });

    function openLogoutModal() {
        document.getElementById('logoutModal').style.display = 'block';
    }

    function closeLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }
</script>

<!-- Logout Modal -->
<div id="logoutModal" class="modal"
    style="display:none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div class="sidebar-modal-content"
        style="background: var(--surface-color); width: 90%; max-width: 400px; margin: 15vh auto; border-radius: 24px; padding: 40px; text-align: center; border: 1px solid var(--border-color);">
        <div
            style="width: 70px; height: 70px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 1.8rem;">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <h2 class="sidebar-modal-title" style="font-weight: 800; color: var(--text-color); margin-bottom: 10px;">End Session?</h2>
        <p style="color: var(--text-muted); margin-bottom: 30px;">Are you sure you want to log out of the Student Portal?</p>
        <div style="display: flex; gap: 12px;">
            <button onclick="closeLogoutModal()" class="sidebar-modal-btn-cancel"
                style="flex: 1; padding: 12px; border-radius: 12px; border: 1px solid var(--border-color); background: var(--hover-bg); color: var(--text-color); font-weight: 600; cursor: pointer;">Cancel</button>
            <a href="/Student/auth/logout.php"
                style="flex: 1; padding: 12px; border-radius: 12px; background: #ef4444; color: white; font-weight: 600; text-decoration: none;">Log
                Out</a>
        </div>
    </div>
</div>