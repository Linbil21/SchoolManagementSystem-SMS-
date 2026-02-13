<?php
// Admin Head-bar Component
require_once __DIR__ . '/../../Database/config.php';
require_once __DIR__ . '/../../Components/NotificationHelper.php';
require_once __DIR__ . '/../../auth/Security.php';

$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'Admin';
$initials = strtoupper(substr($user_email, 0, 1) . substr($user_email, 1, 1));
if (strpos($user_email, '@') !== false) {
    $parts = explode('@', $user_email);
    $initials = strtoupper(substr($parts[0], 0, 2));
}

// Fetch notifications
$unread_count = getUnreadNotificationsCount($pdo);
$notifications = getRecentNotifications($pdo);

// Robust absolute-relative path logic
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/Super-admin/', '/modules/', '/Admin/', '/submodules/', '/modules/', '/Cashier/', '/Admission/', '/auth/', '/student/'];
$project_base = '';

foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        $project_base = rtrim(substr($script_name, 0, $pos), '/');
        break;
    }
}

$root = $project_base . '/'; // Ensures trailing slash
?>
<?php if (isReadOnly()): ?>
    <div style="background: linear-gradient(90deg, #6366f1 0%, #4f46e5 100%); color: white; padding: 8px 15px; text-align: center; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; display: flex; align-items: center; justify-content: center; gap: 10px;">
        <i class="fas fa-eye"></i> <span>PEEK MODE: You are viewing the Admin Portal as a Super Admin. Data modification is disabled.</span>
        <a href="/Super-admin/Dashboard.php" style="color: white; text-decoration: underline; margin-left:10px;">Return to Control Center</a>
    </div>
<?php endif; ?>
<div class="head-bar">
    <div class="head-left">
        <div class="burger-btn" id="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search dashboard...">
        </div>
    </div>

    <!-- Real-time Clock -->
    <div class="header-clock" id="digital-clock">
        <div class="clock-time">00:00:00</div>
        <div class="clock-date">Month 00, 0000</div>
    </div>

    <div class="head-actions">
        <!-- Theme Toggle -->
        <div class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
            <i class="fas fa-moon" id="theme-icon"></i>
        </div>

        <!-- Notification Center -->
        <div class="notification-wrapper">
            <div class="notification" onclick="toggleDropdown('notifDropdown')">
                <i class="fas fa-bell"></i>
                <?php if ($unread_count > 0): ?>
                    <span class="badge"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </div>
            <div id="notifDropdown" class="dropdown-content">
                <div class="dropdown-header">
                    <h4>Notifications</h4>
                    <span class="mark-all" onclick="markAllAsRead()">Mark all</span>
                </div>
                <div class="dropdown-body">
                    <?php echo getNotificationsHtml($notifications); ?>
                </div>
            </div>
        </div>

        <!-- User Profile Center -->
        <div class="user-wrapper">
            <div class="head-user" onclick="toggleDropdown('userDropdown')">
                <div class="user-info">
                    <span class="name">Administrator</span>
                    <span class="role">System Access</span>
                </div>
                <div class="avatar-circle">
                    <?php echo $initials; ?>
                </div>
            </div>
            <div id="userDropdown" class="dropdown-content profile-dropdown">
                <div class="dropdown-header profile-head">
                    <div class="avatar-circle large">
                        <?php echo $initials; ?>
                    </div>
                    <div class="user-meta">
                        <h4>System Administrator</h4>
                        <p><?php echo $user_email; ?></p>
                    </div>
                </div>
                <div class="dropdown-body">
                    <a href="/modules/Profile.php" class="dropdown-link">
                        <i class="fas fa-user-cog"></i> Admin Settings
                    </a>
                    <a href="javascript:void(0)" onclick="openLogoutModal()" class="dropdown-link logout-link">
                        <i class="fas fa-power-off"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo $root; ?>Assets/css/theme.css">



<script>
    function initTheme() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);
    }

    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    }

    function updateThemeIcon(theme) {
        const icon = document.getElementById('theme-icon');
        if (icon) icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }

    initTheme();

    function toggleDropdown(id) {
        document.querySelectorAll('.dropdown-content').forEach(d => {
            if(d.id !== id) d.classList.remove('show');
        });
        document.getElementById(id).classList.toggle('show');
    }

    window.onclick = function(e) {
        if (!e.target.closest('.notification-wrapper') && !e.target.closest('.user-wrapper') && !e.target.closest('.theme-toggle')) {
            document.querySelectorAll('.dropdown-content').forEach(d => d.classList.remove('show'));
        }
    }

    function updateClock() {
        const clockTime = document.querySelector('.clock-time');
        const clockDate = document.querySelector('.clock-date');
        
        const now = new Date();
        
        // Time Formatting
        let hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; 
        
        if (clockTime) {
            clockTime.textContent = `${hours}:${minutes}:${seconds} ${ampm}`;
        }
        
        // Date Formatting
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        if (clockDate) {
            clockDate.textContent = now.toLocaleDateString('en-US', options);
        }
    }

    setInterval(updateClock, 1000);
    updateClock();

    // Mark all notifications as read
    function markAllAsRead() {
        fetch('/Admin/api/mark_notifications_read.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'}
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
<script>
    // Config for global search
    window.smsRoot = "<?php echo $root; ?>";
</script>
<script src="<?php echo $root; ?>Assets/js/global-search.js"></script>