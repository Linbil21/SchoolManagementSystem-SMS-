<?php
// Cashier Sidebar Component
$current_page = basename($_SERVER['PHP_SELF']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'cashier';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'cashier@sms.com';

// Robust absolute-relative path logic
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/Super-admin/', '/Admin/', '/Cashier/', '/Admission/', '/auth/', '/student/', '/modules/'];
$project_base = '';

foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        $project_base = rtrim(substr($script_name, 0, $pos), '/');
        break;
    }
}
$root = $project_base . '/'; 
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <a href="<?php echo $root; ?>Cashier/Dashboard.php" class="brand-wrapper">
            <img src="<?php echo $root; ?>Assets/image/logo.png" alt="Logo" class="sidebar-logo">
            <h2>Cashier</h2>
        </a>
    </div>

    <div class="sidebar-menu">
        <p class="menu-label">MAIN</p>
        <ul class="main-menu">
            <li class="<?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>">
                <a href="<?php echo $root; ?>Cashier/Dashboard.php">
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
                    <li><a href="<?php echo $root; ?>Cashier/Modules/Uploaded-Receipts.php">Uploaded Receipts</a></li>
                    <li><a href="<?php echo $root; ?>Cashier/Modules/Walk-in-Payments.php">Walk-in Payments</a></li>
                    <li><a href="<?php echo $root; ?>Cashier/Modules/Online-Payments.php">Online Payments</a></li>
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
                    <li><a href="<?php echo $root; ?>Cashier/Modules/Student-Assessment.php">Student Assessment</a></li>
                    <li><a href="<?php echo $root; ?>Cashier/Submodules/Fee-Breakdown.php">Fee Breakdown</a></li>
                    <li><a href="<?php echo $root; ?>Cashier/Submodules/Discounts.php">Discounts / Scholarships</a></li>
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
                    <li><a href="<?php echo $root; ?>Cashier/Modules/Issue-Receipt.php">Issue Receipt</a></li>
                    <li><a href="<?php echo $root; ?>Cashier/Modules/Receipt-History.php">Receipt History</a></li>
                    <li><a href="<?php echo $root; ?>Cashier/Modules/Refund-Requests.php">Void / Refund</a></li>
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
                    <li><a href="<?php echo $root; ?>Cashier/Submodules/Payment-Status.php">Payment Status</a></li>
                    <li><a href="<?php echo $root; ?>Cashier/Submodules/Outstanding-Balances.php">Outstanding Balances</a></li>
                    <li><a href="<?php echo $root; ?>Cashier/Submodules/Payment-History.php">Payment History</a></li>
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
                    <li><a href="<?php echo $root; ?>Cashier/Submodules/Daily-Collection.php">Daily Collection</a></li>
                    <li><a href="<?php echo $root; ?>Cashier/Submodules/Monthly-Summary.php">Monthly Summary</a></li>
                    <li><a href="<?php echo $root; ?>Cashier/Submodules/Method-Reports.php">Payment Methods</a></li>
                    <li><a href="<?php echo $root; ?>Cashier/Submodules/Outstanding-Report.php">Outstanding Report</a></li>
                </ul>
            </li>
        </ul>

        <p class="menu-label">ACCOUNT & SETTINGS</p>
        <ul class="main-menu">
            <li class="<?php echo ($current_page == 'Profile.php') ? 'active' : ''; ?>">
                <a href="<?php echo $root; ?>modules/Profile.php">
                    <i class="fas fa-user-circle"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'Settings.php') ? 'active' : ''; ?>">
                <a href="<?php echo $root; ?>modules/Settings.php">
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

<link rel="stylesheet" href="/Assets/css/theme.css">

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
        <p>Are you sure you want to log out of the Cashier panel? Your current transaction work will be saved.</p>
        <div class="logout-modal-buttons">
            <button onclick="closeLogoutModal()" class="btn-cancel">Cancel</button>
            <a href="<?php echo $root; ?>auth/logout.php" class="btn-logout">Log Out</a>
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