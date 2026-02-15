<?php
// Cashier Head-bar Component
require_once __DIR__ . '/../../Database/config.php';
require_once __DIR__ . '/../../Components/NotificationHelper.php';
require_once __DIR__ . '/../../auth/Security.php';

$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'Cashier';
$initials = strtoupper(substr($user_email, 0, 1) . substr($user_email, 1, 1));
if (strpos($user_email, '@') !== false) {
    $parts = explode('@', $user_email);
    $initials = strtoupper(substr($parts[0], 0, 2));
}

// Fetch dynamic notifications
$unread_count = getUnreadNotificationsCount($pdo);
$notifications = getRecentNotifications($pdo);

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
<?php if (isReadOnly()): ?>
    <div style="background: linear-gradient(90deg, #6366f1 0%, #4f46e5 100%); color: white; padding: 8px 15px; text-align: center; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; display: flex; align-items: center; justify-content: center; gap: 10px;">
        <i class="fas fa-eye"></i> <span>PEEK MODE: You are viewing the Finance Portal as a Super Admin. Data modification is disabled.</span>
        <a href="/super-admin/Dashboard.php" style="color: white; text-decoration: underline; margin-left:10px;">Return to Control Center</a>
    </div>
<?php endif; ?>
<div class="head-bar">
    <div class="head-left">
        <div class="burger-btn" id="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search here...">
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
                    <span class="name">Cashier Staff</span>
                    <span class="role">Finance Office</span>
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
                        <h4>Cashier Staff</h4>
                        <p><?php echo $user_email; ?></p>
                    </div>
                </div>
                <div class="dropdown-body">
                    <a href="<?php echo $root; ?>modules/Profile.php" class="dropdown-link">
                        <i class="fas fa-user-circle"></i> My Profile
                    </a>
                    <a href="javascript:void(0)" onclick="openLogoutModal()" class="dropdown-link logout-link">
                        <i class="fas fa-power-off"></i> End Session
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo $root; ?>Assets/css/theme.css">

<style>
    .head-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 30px;
        height: 70px;
        background: var(--header-bg);
        border-bottom: 1px solid var(--border-color);
        position: sticky;
        top: 0;
        z-index: 1000;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
    }

    body {
        background-color: var(--bg-color);
        color: var(--text-color);
        transition: background-color 0.3s, color 0.3s;
    }

    .head-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 30px;
        height: 70px;
        background: var(--header-bg);
        border-bottom: 1px solid var(--border-color);
        position: sticky;
        top: 0;
        z-index: 1001;
        transition: background 0.3s, border 0.3s;
    }

    .theme-toggle, .notification {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--hover-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        cursor: pointer;
        color: var(--text-color);
        transition: all 0.3s ease;
    }

    .theme-toggle:hover, .notification:hover {
        background: var(--accent-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(22, 72, 188, 0.2);
    }

    .head-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .burger-btn {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--hover-bg);
        border-radius: 8px;
        color: var(--text-color);
        cursor: pointer;
        transition: 0.3s;
    }

    .burger-btn:hover {
        background: var(--accent-color);
        color: white;
    }

    .search-box {
        position: relative;
        width: 300px;
    }

    .search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .search-box input {
        width: 100%;
        padding: 10px 15px 10px 42px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        background: var(--hover-bg);
        color: var(--text-color);
        font-size: 0.85rem;
        outline: none;
        transition: 0.3s;
    }

    .search-box input:focus {
        border-color: var(--accent-color);
        background: var(--surface-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .head-actions {
        display: flex;
        align-items: center;
        gap: 25px;
    }

    /* Notification Styling */
    .notification-wrapper, .user-wrapper {
        position: relative;
    }

    .notification .badge {
        position: absolute;
        top: -6px;
        right: -6px;
        min-width: 18px;
        height: 18px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        font-size: 0.7rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--header-bg);
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.3);
        animation: badgePulse 2s infinite;
    }

    @keyframes badgePulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .header-clock {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
    }

    .clock-time {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--accent-color);
        letter-spacing: 0.5px;
    }

    .clock-date {
        font-size: 0.65rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    @media (max-width: 992px) {
        .header-clock { display: none; }
    }

    /* Avatar Styling */
    .head-user {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 12px;
        transition: 0.3s;
    }

    .head-user:hover {
        background: var(--hover-bg);
    }

    .user-info {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        text-align: right;
    }

    .user-info .name {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-color);
        line-height: 1.2;
    }

    .user-info .role {
        font-size: 0.72rem;
        color: var(--text-muted);
        font-weight: 500;
        margin-top: 2px;
        line-height: 1.2;
    }

    .avatar-circle {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--accent-color) 0%, #0a2e7a 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 800;
        font-size: 0.95rem;
        border: 2px solid white;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        top: 55px;
        right: 0;
        background-color: var(--dropdown-bg);
        min-width: 250px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        overflow: hidden;
        animation: dropSlide 0.3s ease;
    }

    @keyframes dropSlide {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .dropdown-content.show { display: block; }

    .dropdown-header { 
        padding: 15px 20px; 
        border-bottom: 1px solid var(--border-color); 
        background: var(--hover-bg); 
        color: var(--text-color); 
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dropdown-header h4 {
        font-size: 0.9rem;
        font-weight: 700;
        margin: 0;
    }

    .mark-all {
        font-size: 0.75rem;
        color: var(--accent-color);
        cursor: pointer;
        font-weight: 600;
    }

    .dropdown-body { 
        max-height: 400px;
        overflow-y: auto;
    }

    .dropdown-item {
        padding: 12px 20px;
        display: flex;
        gap: 15px;
        transition: 0.3s;
        cursor: pointer;
        border-bottom: 1px solid var(--border-color);
        text-decoration: none;
        color: var(--text-color);
    }

    .dropdown-item:last-child { border-bottom: none; }
    .dropdown-item:hover { background: var(--hover-bg); }
    .dropdown-item.unread { background: rgba(22, 72, 188, 0.03); }

    .notif-profile-img { 
        width: 40px; 
        height: 40px; 
        border-radius: 10px; 
        object-fit: cover; 
        flex-shrink: 0; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
    }

    .notif-type-icon {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--dropdown-bg);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 2;
    }

    .notif-info { flex: 1; }
    .notif-info p { 
        font-size: 0.85rem; 
        color: var(--text-color); 
        line-height: 1.4; 
        margin: 0 0 4px 0; 
    }
    .notif-info span { font-size: 0.75rem; color: var(--text-muted); }

    .dropdown-link { display: flex; align-items: center; gap: 12px; padding: 12px 20px; text-decoration: none; color: var(--text-color); font-size: 0.85rem; }
    .dropdown-link:hover { background: var(--hover-bg); color: var(--accent-color); }
    .logout-link { color: #ef4444 !important; }
</style>

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
        if (icon) {
            icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    }

    initTheme();

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

    function markAllAsRead() {
        const badge = document.querySelector('.notification .badge');
        if(badge) badge.style.display = 'none';
        
        document.querySelectorAll('.dropdown-item.unread').forEach(item => {
            item.classList.remove('unread');
        });

        // Backend Sync
        fetch('/Admin/api/mark_notifications_read.php', {
            method: 'POST'
        }).catch(err => console.error(err));
    }

    function toggleDropdown(id) {
        document.querySelectorAll('.dropdown-content').forEach(d => {
            if(d.id !== id) d.classList.remove('show');
        });
        document.getElementById(id).classList.toggle('show');
    }

    window.addEventListener('click', function(e) {
        if (!e.target.closest('.notification-wrapper') && !e.target.closest('.user-wrapper') && !e.target.closest('.theme-toggle')) {
            document.querySelectorAll('.dropdown-content').forEach(d => d.classList.remove('show'));
        }
    });
</script>
<script>
    // Config for global search
    window.smsRoot = "<?php echo $root; ?>";
</script>
<script src="<?php echo $root; ?>Assets/js/global-search.js"></script>