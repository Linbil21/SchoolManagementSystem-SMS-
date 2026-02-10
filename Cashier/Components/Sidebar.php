<?php
// Cashier Sidebar Component
$current_page = basename($_SERVER['PHP_SELF']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'cashier';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'cashier@sms.com';
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <a href="/sms/Cashier/Dashboard.php" class="brand-wrapper">
            <img src="/sms/Assets/image/logo.png" alt="Logo" class="sidebar-logo">
            <h2>Cashier</h2>
        </a>
    </div>

    <div class="sidebar-menu">
        <p class="menu-label">MAIN</p>
        <ul class="main-menu">
            <li class="<?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>">
                <a href="/sms/Cashier/Dashboard.php">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>

        <p class="menu-label">FINANCE MODULES</p>
        <ul class="main-menu">
            <!-- Payment Verification Dropdown -->
            <li
                class="has-dropdown <?php echo (strpos($_SERVER['PHP_SELF'], 'Verification') !== false) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-receipt"></i>
                    <span>Payment Verification</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/sms/Cashier/Modules/Uploaded-Receipts.php">Uploaded Receipts</a></li>
                    <li><a href="/sms/Cashier/Modules/Walk-in-Payments.php">Walk-in Payments</a></li>
                    <li><a href="/sms/Cashier/Modules/Online-Payments.php">Online Payments</a></li>
                </ul>
            </li>

            <!-- Assessment & Billing Dropdown -->
            <li
                class="has-dropdown <?php echo (strpos($_SERVER['PHP_SELF'], 'Billing') !== false) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Assessment & Billing</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/sms/Cashier/Modules/Student-Assessment.php">Student Assessment</a></li>
                    <li><a href="/sms/Cashier/Submodules/Fee-Breakdown.php">Fee Breakdown</a></li>
                    <li><a href="/sms/Cashier/Submodules/Discounts.php">Discounts / Scholarships</a></li>
                </ul>
            </li>

            <!-- Official Receipts Dropdown -->
            <li
                class="has-dropdown <?php echo (strpos($_SERVER['PHP_SELF'], 'Receipts') !== false) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-print"></i>
                    <span>Official Receipts</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/sms/Cashier/Modules/Issue-Receipt.php">Issue Receipt</a></li>
                    <li><a href="/sms/Cashier/Modules/Receipt-History.php">Receipt History</a></li>
                    <li><a href="/sms/Cashier/Modules/Refund-Requests.php">Void / Refund</a></li>
                </ul>
            </li>
        </ul>

        <p class="menu-label">STUDENT ACCOUNTS</p>
        <ul class="main-menu">
            <li
                class="has-dropdown <?php echo (strpos($_SERVER['PHP_SELF'], 'Accounts') !== false) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-users-cog"></i>
                    <span>Accounts Registry</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/sms/Cashier/Submodules/Payment-Status.php">Payment Status</a></li>
                    <li><a href="/sms/Cashier/Submodules/Outstanding-Balances.php">Outstanding Balances</a></li>
                    <li><a href="/sms/Cashier/Submodules/Payment-History.php">Payment History</a></li>
                </ul>
            </li>
        </ul>

        <p class="menu-label">REPORTS & ANALYTICS</p>
        <ul class="main-menu">
            <li
                class="has-dropdown <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['Daily-Collection.php', 'Monthly-Summary.php', 'Method-Reports.php', 'Outstanding-Report.php'])) ? 'active open' : ''; ?>">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fas fa-chart-line"></i>
                    <span>Financial Reports</span>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="/sms/Cashier/Submodules/Daily-Collection.php">Daily Collection</a></li>
                    <li><a href="/sms/Cashier/Submodules/Monthly-Summary.php">Monthly Summary</a></li>
                    <li><a href="/sms/Cashier/Submodules/Method-Reports.php">Payment Methods</a></li>
                    <li><a href="/sms/Cashier/Submodules/Outstanding-Report.php">Outstanding Report</a></li>
                </ul>
            </li>
        </ul>

        <p class="menu-label">ACCOUNT & SETTINGS</p>
        <ul class="main-menu">
            <li class="<?php echo ($current_page == 'Profile.php') ? 'active' : ''; ?>">
                <a href="/sms/modules/Profile.php">
                    <i class="fas fa-user-circle"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'Settings.php') ? 'active' : ''; ?>">
                <a href="/sms/modules/Settings.php">
                    <i class="fas fa-cogs"></i>
                    <span>Settings</span>
                </a>
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
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($role); ?>&background=1648bc&color=fff"
                alt="Profile">
            <div class="profile-info">
                <h4>Cashier Office</h4>
                <p>Cashier Manager</p>
            </div>
            <span class="status-dot"></span>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/sms/Assets/css/theme.css">

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
        width: 42px;
        height: auto;
    }

    .sidebar-brand h2 {
        color: var(--accent-color);
        font-size: 1.4rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: -0.5px;
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
        background: #edf2f7;
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
        border-left: 4px solid var(--accent-color);
        border-radius: 0 12px 12px 0;
    }

    .main-menu a i {
        width: 20px;
        font-size: 1.1rem;
    }

    /* Dropdown Logic */
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
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .arrow-icon {
        margin-left: auto;
        font-size: 0.75rem;
        transition: transform 0.3s;
    }

    .has-dropdown.open .arrow-icon {
        transform: rotate(90deg);
    }

    .sub-menu a {
        padding: 10px 15px;
        font-size: 0.85rem;
        color: var(--text-muted);
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
        border: 2px solid var(--sidebar-bg);
        border-radius: 50%;
    }
</style>

<script>
    document.querySelectorAll('.dropdown-toggle').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const parent = button.parentElement;

            // Add animating class for transition
            parent.classList.add('animating');

            // Remove animating class after animation completes to clean up
            setTimeout(() => {
                parent.classList.remove('animating');
            }, 300); // matches 0.3s animation duration

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
    <div
        style="background: var(--surface-color); width: 90%; max-width: 400px; margin: 15vh auto; border-radius: 24px; padding: 40px; text-align: center; border: 1px solid var(--border-color);">
        <div
            style="width: 70px; height: 70px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 1.8rem;">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <h2 style="font-weight: 800; color: var(--text-color); margin-bottom: 10px;">End Session?</h2>
        <p style="color: var(--text-muted); margin-bottom: 30px;">Are you sure you want to log out of the Cashier panel?</p>
        <div style="display: flex; gap: 12px;">
            <button onclick="closeLogoutModal()"
                style="flex: 1; padding: 12px; border-radius: 12px; border: 1px solid var(--border-color); background: var(--hover-bg); color: var(--text-color); font-weight: 600; cursor: pointer;">Cancel</button>
            <a href="/sms/auth/logout.php"
                style="flex: 1; padding: 12px; border-radius: 12px; background: #ef4444; color: white; font-weight: 600; text-decoration: none;">Log
                Out</a>
        </div>
    </div>
</div>