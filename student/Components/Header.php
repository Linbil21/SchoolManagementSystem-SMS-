<?php
// Student Header Component
require_once __DIR__ . '/../../Database/config.php';
require_once __DIR__ . '/../../Components/NotificationHelper.php';
require_once __DIR__ . '/../../auth/Security.php';
?>
<?php if (isReadOnly()): ?>
    <div style="background: linear-gradient(90deg, #6366f1 0%, #4f46e5 100%); color: white; padding: 8px 15px; text-align: center; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; display: flex; align-items: center; justify-content: center; gap: 10px;">
        <i class="fas fa-eye"></i> <span>PEEK MODE: You are viewing the Student Portal as a Super Admin. Data modification is disabled.</span>
        <a href="/Super-admin/Dashboard.php" style="color: white; text-decoration: underline; margin-left:10px;">Return to Control Center</a>
    </div>
<?php endif; ?>
<?php
$student_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Student';
$student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : '2026-0000';
$profile_pic = isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : '';

// Fetch dynamic notifications
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

$root = $project_base . '/'; 

// Improved initials logic
$name_parts = explode(' ', trim($student_name));
$initials = '';
if (count($name_parts) >= 2) {
    $initials = strtoupper(substr($name_parts[0], 0, 1) . substr($name_parts[count($name_parts)-1], 0, 1));
} else {
    $initials = strtoupper(substr($student_name, 0, 2));
}
?>
<div class="header">
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search courses, documents...">
        </div>
    </div>

    <!-- Real-time Clock -->
    <div class="header-clock" id="digital-clock">
        <div class="clock-time">00:00:00</div>
        <div class="clock-date">Month 00, 0000</div>
    </div>

    <div class="header-right">
        <!-- Theme Toggle -->
        <div class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
            <i class="fas fa-moon" id="theme-icon"></i>
        </div>

        <!-- Notifications -->
        <div class="notification-wrapper">
            <div class="action-btn notification-btn" onclick="toggleDropdown('notifDropdown')">
                <i class="fas fa-bell"></i>
                <?php if ($unread_count > 0): ?>
                    <span class="badge"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </div>
            <div id="notifDropdown" class="dropdown-content">
                <div class="dropdown-header">
                    <h4>Notifications</h4>
                    <span class="mark-all" onclick="markAllRead()">Mark all</span>
                </div>
                <div class="dropdown-body">
                    <?php echo getNotificationsHtml($notifications); ?>
                </div>
            </div>
        </div>

        <!-- User Profile -->
        <div class="user-wrapper">
            <div class="user-profile" onclick="toggleDropdown('userDropdown')">
                <div class="user-info">
                    <span class="name"><?php echo htmlspecialchars($student_name); ?></span>
                    <span class="role">Student</span>
                </div>
                <div class="avatar-circle">
                    <?php if (!empty($profile_pic) && $profile_pic !== 'default.jpg'): ?>
                        <img src="/<?php echo $profile_pic; ?>" alt="Profile">
                    <?php else: ?>
                        <?php echo $initials; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div id="userDropdown" class="dropdown-content profile-dropdown">
                <div class="dropdown-header profile-head">
                    <div class="avatar-circle large">
                        <?php if (!empty($profile_pic) && $profile_pic !== 'default.jpg'): ?>
                            <img src="/<?php echo $profile_pic; ?>" alt="Profile">
                        <?php else: ?>
                            <?php echo $initials; ?>
                        <?php endif; ?>
                    </div>
                    <div class="user-meta">
                        <h4><?php echo htmlspecialchars($student_name); ?></h4>
                        <p>Student ID: <?php echo htmlspecialchars($student_id); ?></p>
                    </div>
                </div>
                <div class="dropdown-body">
                    <a href="<?php echo $root; ?>student/Submodules/profile.php" class="dropdown-link">
                        <i class="fas fa-user-circle"></i> My Profile
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

<style>
    .header {
        height: 70px;
        background: var(--header-bg);
        padding: 0 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-color);
        position: sticky;
        top: 0;
        z-index: 1000;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
    }

    .theme-toggle, .action-btn {
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

    .theme-toggle:hover, .action-btn:hover {
        background: var(--accent-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(22, 72, 188, 0.2);
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .menu-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 1.2rem;
        color: var(--text-color);
        cursor: pointer;
    }

    .search-bar {
        display: flex;
        align-items: center;
        background: var(--hover-bg);
        padding: 10px 18px;
        border-radius: 10px;
        width: 300px;
        border: 1px solid var(--border-color);
        transition: 0.3s;
    }

    .search-bar:focus-within {
        border-color: var(--accent-color);
        background: var(--surface-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .search-bar input {
        border: none;
        background: none;
        outline: none;
        width: 100%;
        color: var(--text-color);
        font-size: 0.85rem;
        padding-left: 12px;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    /* Notification Badge */
    .badge {
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
    }

    .avatar-circle {
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #1648bc 0%, #0a2e7a 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 800;
        font-size: 0.95rem;
        cursor: pointer;
        border: 2px solid #fff;
        overflow: hidden;
    }

    .avatar-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 6px;
        border-radius: 12px;
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

    /* Dropdown Styling */
    .notification-wrapper, .user-wrapper {
        position: relative;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        top: 60px;
        right: 0;
        background-color: var(--card-bg, #ffffff); 
        min-width: 250px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        z-index: 2500;
        overflow: hidden;
        animation: dropSlide 0.3s ease;
    }

    @keyframes dropSlide {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .dropdown-content.show { display: block; }
    
    [data-theme="dark"] .dropdown-content {
        background-color: #1e293b; /* Slate 800 for dark mode */
        border-color: #334155;
    }

    .dropdown-header { 
        padding: 20px; 
        border-bottom: 1px solid var(--border-color); 
        background: transparent;
        text-align: center;
    }
    
    .dropdown-header h4 { 
        font-size: 0.95rem; 
        font-weight: 700; 
        color: var(--text-color); 
        margin-bottom: 4px;
    }
    
    .dropdown-header p {
        color: var(--text-muted);
        font-size: 0.75rem;
    }

    .mark-all { font-size: 0.75rem; color: #1648bc; cursor: pointer; display: block; margin-top: 5px; text-align: right; }

    .dropdown-item { padding: 12px 20px; border-bottom: 1px solid var(--border-color); cursor: pointer; transition: 0.2s; position: relative; }
    .dropdown-item:hover { background: var(--hover-bg); }
    .dropdown-item.unread { background: #eff6ff; }
    [data-theme="dark"] .dropdown-item.unread { background: #0c1c36; }
    .dropdown-item.unread::after { content: ''; position: absolute; right: 20px; top: 18px; width: 8px; height: 8px; background: #1648bc; border-radius: 50%; }
    
    .notif-img {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    .notif-profile-img {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
        border: 2px solid #f1f5f9;
        transition: 0.3s;
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
        border: 2px solid var(--header-bg);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 2;
    }

    .dropdown-item:hover .notif-profile-img {
        transform: scale(1.1);
        border-color: #1648bc;
    }

    .notif-info p { font-size: 0.85rem; margin-bottom: 3px; line-height: 1.4; color: var(--text-color); }
    .notif-info span { font-size: 0.75rem; color: #64748b; }

    .dropdown-link { display: flex; align-items: center; gap: 12px; padding: 12px 20px; text-decoration: none; color: var(--text-color); font-size: 0.85rem; transition: 0.2s; }
    .dropdown-link:hover { background: var(--hover-bg); color: #1648bc; }
    .logout-link { color: #ef4444 !important; }

    .profile-head { flex-direction: column; text-align: center; gap: 10px; padding: 20px; }
    .avatar-circle.large { width: 50px; height: 50px; font-size: 1.2rem; margin: 0 auto; }
    .user-meta h4 { font-size: 0.95rem; margin-top: 5px; color: var(--text-color); }
    .user-meta p { font-size: 0.75rem; color: #64748b; }

    /* Header Clock Styling */
    .header-clock {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 5px 20px;
        border-radius: 10px;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
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
</style>

<script>
    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    }

    function updateThemeIcon(theme) {
        const icon = document.getElementById('theme-icon');
        if(icon) icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }

    // Init
    (function() {
        const theme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', theme);
        updateThemeIcon(theme);
    })();

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

    function toggleDropdown(id) {
        document.querySelectorAll('.dropdown-content').forEach(d => {
            if(d.id !== id) d.classList.remove('show');
        });
        const element = document.getElementById(id);
        if(element) element.classList.toggle('show');
    }

    function markAllRead() {
        const badge = document.querySelector('.notification-btn .badge');
        
        fetch('/student/api/mark_notifications_read.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (badge) badge.style.display = 'none';
                    document.querySelectorAll('.dropdown-item.unread').forEach(item => {
                        item.classList.remove('unread');
                    });
                    // Refresh if needed or just update UI
                }
            })
            .catch(err => console.error('Error marking notifications as read:', err));
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
