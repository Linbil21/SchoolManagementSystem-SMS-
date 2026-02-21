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

// Robust absolute-relative path logic
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/student/', '/Super-admin/', '/Admin/', '/Cashier/', '/Admission/', '/auth/', '/modules/'];
$project_base = '';
foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        $project_base = rtrim(substr($script_name, 0, $pos), '/');
        break;
    }
}
$root = $project_base . '/';

// --- Refresh Enrollment Status & Student ID ---
if (isset($_SESSION['email'])) {
    try {
        require_once $_SERVER['DOCUMENT_ROOT'] . $root . 'Database/config.php';
        $stmt = $pdo->prepare("
            SELECT s.student_id, e.status as enrollment_status, a.status as admission_status 
            FROM students s 
            LEFT JOIN enrollments e ON s.email = e.email 
            LEFT JOIN admission_applications a ON s.email = a.email 
            WHERE s.email = ?
        ");
        $stmt->execute([$_SESSION['email']]);
        $fresh = $stmt->fetch(PDO::FETCH_OBJ);
        if ($fresh) {
            $_SESSION['student_id'] = $fresh->student_id;
            $_SESSION['enrollment_status'] = $fresh->enrollment_status;
            $_SESSION['admission_status'] = $fresh->admission_status ?? 'Pending';
        }
    } catch (Exception $e) {}
}
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <a href="/student/Dashboard.php" class="brand-wrapper">
            <i class="fas fa-graduation-cap" style="font-size: 1.8rem; color: #2563eb;"></i>
            <h2>Student<span style="color: #64748b; font-weight: 400; font-size: 1rem; margin-left: 5px;">Portal</span>
            </h2>
        </a>
    </div>

    <div class="sidebar-menu">
        <p class="menu-label">MAIN</p>
        <ul class="main-menu">
            <li class="<?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>">
                <a href="/student/Dashboard.php">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>

        <p class="menu-label">ACADEMIC</p>
        <ul class="main-menu">
            <!-- Enrollment -->
            <?php 
            $admission_status = $_SESSION['admission_status'] ?? 'Pending';
            if ($admission_status === 'Approved'): 
            ?>
            <li
                class="has-dropdown <?php echo isDropdownOpen(['Subject-Selection', 'Assessment', 'Enrollment-Status', 'Upload-Payment', 'Enrollment-History']); ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-book-open"></i>
                    <span>Enrollment</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/student/Modules/Enrollment/Subject-Selection.php">Subject Selection</a></li>
                    <li><a href="/student/Modules/Enrollment/View-Assessment.php">View Assessment</a></li>
                    <li><a href="/student/Modules/Enrollment/Enrollment-Status.php">Enrollment Status</a></li>
                    <li><a href="/student/Modules/Enrollment/Upload-Payment.php">Upload Payment</a></li>
                    <li><a href="/student/Modules/Enrollment/History.php">Enrollment History</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <!-- My Studies -->
            <li class="has-dropdown <?php echo isDropdownOpen(['Schedule.php', 'Grades.php', 'Attendance.php']); ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-user-graduate"></i>
                    <span>My Studies</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li class="<?php echo ($current_page == 'Schedule.php') ? 'active' : ''; ?>">
                        <a href="/student/Modules/Academic/Schedule.php">Class Schedule</a>
                    </li>
                    <li><a href="javascript:void(0)" style="opacity: 0.5;">My Grades (Soon)</a></li>
                    <li><a href="javascript:void(0)" style="opacity: 0.5;">Attendance (Soon)</a></li>
                </ul>
            </li>

            <!-- Admission -->
            <li class="has-dropdown <?php echo isDropdownOpen(['Requirements']); ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-university"></i>
                    <span>Admission</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/student/Modules/Admission/Requirements.php">Student Requirements List</a></li>
                </ul>
            </li>
        </ul>

        <?php 
        $enrollment_status = $_SESSION['enrollment_status'] ?? 'Pending';
        $allowed_payment_statuses = ['Pending Payment', 'Validation', 'Enrolled'];
        if (in_array($enrollment_status, $allowed_payment_statuses)): 
        ?>
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
                    <li><a href="/student/Modules/Payments/Balance.php">View Balance</a></li>
                    <li><a href="/student/Modules/Payments/History.php">Payment History</a></li>
                    <li><a href="/student/Modules/Payments/Upload-Receipt.php">Upload Receipt</a></li>
                </ul>
            </li>
        </ul>
        <?php endif; ?>

        <p class="menu-label">SERVICES</p>
        <ul class="main-menu">
            <!-- Student ID - Only show if Enrolled -->
            <?php 
            $enrollment_status = $_SESSION['enrollment_status'] ?? 'Pending';
            if ($enrollment_status === 'Enrolled'): 
            ?>
            <li class="has-dropdown <?php echo isDropdownOpen(['View-ID', 'Download-ID', 'Replacement']); ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-id-card"></i>
                    <span>Student ID</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/student/Modules/ID/View.php">View Student ID</a></li>
                    <li><a href="/student/Modules/ID/Download.php">Download / Print ID</a></li>
                    <li><a href="/student/Modules/ID/Replacement.php">Replacement Request</a></li>
                </ul>
            </li>
            <?php endif; ?>

            <!-- Support -->
            <li class="has-dropdown <?php echo isDropdownOpen(['Announcements', 'Messages', 'Help']); ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-headset"></i>
                    <span>Support</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/student/Modules/Support/Announcements.php">Announcements</a></li>
                    <li><a href="/student/Modules/Support/Messages.php">Messages</a></li>
                    <li><a href="/student/Modules/Support/Help.php">Help / FAQs</a></li>
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
                    <li><a href="/student/Submodules/profile.php">Personal Information</a></li>
                    <li><a href="/student/Submodules/Change-Password.php">Change Password</a></li>
                    <li><a href="/student/Submodules/Settings.php">Account Settings</a></li>
                </ul>
            </li>

            <li>
                <a href="javascript:void(0)" onclick="openLogoutModal()" style="color: #ef4444;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>End Session</span>
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

    window.addEventListener('click', function (event) {
        const modal = document.getElementById('logoutModal');
        if (event.target == modal) {
            closeLogoutModal();
        }
    });
</script>

<!-- Logout Modal -->
<div id="logoutModal" class="logout-modal-overlay" style="display: none;">
    <div class="logout-modal-content">
        <div class="logout-modal-icon">
            <i class="fas fa-power-off"></i>
        </div>
        <h2>End Session?</h2>
        <p>Your student session will be terminated. For your security, an automatic session timeout occurs after periods of inactivity.</p>
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
