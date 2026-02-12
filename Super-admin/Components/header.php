<?php
// Unified Head-bar Component
require_once __DIR__ . '/../../Database/config.php';
require_once __DIR__ . '/../../Components/NotificationHelper.php';

$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'SuperAdmin';
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
$sa_pos = strpos($script_name, '/Super-admin/');
$project_base = ($sa_pos !== false) ? substr($script_name, 0, $sa_pos) : '';
$root = $project_base . '/';
?>
<link rel="stylesheet" href="<?php echo $root; ?>Assets/css/theme.css">
<div class="head-bar">
    <div class="head-left">
        <div class="burger-btn" id="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search system...">
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
                    <span class="name">Super Admin</span>
                    <span class="role">System Root</span>
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
                        <h4>Super Admin</h4>
                        <p><?php echo $user_email; ?></p>
                    </div>
                </div>
                <div class="dropdown-body">
                    <a href="<?php echo $root; ?>modules/Profile.php" class="dropdown-link">
                        <i class="fas fa-user-shield"></i> Security Profile
                    </a>
                    <a href="javascript:void(0)" onclick="openLogoutModal()" class="dropdown-link logout-link">
                        <i class="fas fa-power-off"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>




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

    // Sidebar Toggle Logic
    document.getElementById('sidebar-toggle').addEventListener('click', () => {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('collapsed');
        localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
    });

    // Restore sidebar state
    if (localStorage.getItem('sidebar-collapsed') === 'true') {
        document.querySelector('.sidebar').classList.add('collapsed');
    }

    function updateClock() {
        const now = new Date();
        const clockTime = document.querySelector('.clock-time');
        const clockDate = document.querySelector('.clock-date');
        
        if (clockTime) {
            let hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            clockTime.textContent = `${hours}:${minutes}:${seconds} ${ampm}`;
        }
        
        if (clockDate) {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            clockDate.textContent = now.toLocaleDateString('en-US', options);
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

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

    // Mark all notifications as read
    function markAllAsRead() {
        fetch('<?php echo $root; ?>Admin/api/mark_notifications_read.php', {
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
