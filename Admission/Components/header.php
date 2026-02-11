<?php
// Unified Head-bar Component
require_once __DIR__ . '/../../Database/config.php';
require_once __DIR__ . '/../../Components/NotificationHelper.php';

$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'Admission';
$initials = strtoupper(substr($user_email, 0, 1) . substr($user_email, 1, 1));
if (strpos($user_email, '@') !== false) {
    $parts = explode('@', $user_email);
    $initials = strtoupper(substr($parts[0], 0, 2));
}

// Fetch notifications
$unread_count = getUnreadNotificationsCount($pdo);
$notifications = getRecentNotifications($pdo);
?>
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
                    <span class="name">Admission Staff</span>
                    <span class="role">Admission Office</span>
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
                        <h4>Admission Staff</h4>
                        <p><?php echo $user_email; ?></p>
                    </div>
                </div>
                <div class="dropdown-body">
                    <a href="/modules/Profile.php" class="dropdown-link">
                        <i class="fas fa-user-circle"></i> My Profile
                    </a>
                    <a href="/modules/Settings.php" class="dropdown-link">
                        <i class="fas fa-cog"></i> Account Settings
                    </a>
                    <hr>
                    <a href="javascript:void(0)" onclick="openLogoutModal()" class="dropdown-link logout-link">
                        <i class="fas fa-power-off"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/Assets/css/theme.css">

<style>
/* Admission Header Styles */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    /* Premium Global Overrides */
    .sidebar, .dashboard-container, .card, .stat-card, .table-card, .table-container, .modal-content, .dropdown-content {
        background-color: var(--surface-color);
        color: var(--text-color);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .sidebar * { border-color: var(--border-color); }

    table th {
        background-color: var(--hover-bg);
        color: var(--text-muted);
        border-bottom: 2px solid var(--border-color);
    }

    table td {
        border-bottom: 1px solid var(--border-color);
        color: var(--text-color);
    }

    input, select, textarea {
        background-color: var(--surface-color);
        color: var(--text-color);
        border: 1px solid var(--border-color);
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
        z-index: 1000;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
    }

    .theme-toggle {
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

    .theme-toggle:hover {
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
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--hover-bg);
        border-radius: 10px;
        color: var(--text-color);
        cursor: pointer;
        transition: 0.3s;
        border: 1px solid var(--border-color);
    }

    .burger-btn:hover {
        background: var(--accent-color);
        color: white;
    }

    .search-box {
        position: relative;
        width: 320px;
    }

    .search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
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
        gap: 20px;
    }

    .notification {
        position: relative;
        cursor: pointer;
        color: var(--text-color);
        font-size: 1.2rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: var(--hover-bg);
        border: 1px solid var(--border-color);
    }

    .notification:hover {
        background: var(--accent-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(59, 130, 246, 0.2);
    }

    .notification .badge {
        position: absolute;
        top: -8px;
        right: -8px;
        min-width: 22px;
        height: 22px;
        padding: 0 5px;
        background: linear-gradient(135deg, #ff4d4d 0%, #e11d48 100%);
        border-radius: 11px;
        border: 2px solid var(--header-bg);
        color: white;
        font-size: 0.75rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(225, 29, 72, 0.4);
        animation: badgePulse 2s infinite;
    }

    @keyframes badgePulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.4); }
        70% { transform: scale(1.2); box-shadow: 0 0 0 6px rgba(225, 29, 72, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(225, 29, 72, 0); }
    }

    .avatar-circle {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-hover) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.95rem;
        cursor: pointer;
        border: 2px solid #fff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    /* Dropdown Styling */
    .dropdown-content {
        display: none;
        position: absolute;
        top: 55px;
        right: 0;
        min-width: 280px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        border-radius: 16px;
        overflow: hidden;
        z-index: 1100;
        background: var(--surface-color) !important;
        border: 1px solid var(--border-color) !important;
        animation: dropDown 0.3s ease;
    }

    @keyframes dropDown {
        from { opacity: 0; transform: translateY(-12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .dropdown-content.show { display: block; }


    @keyframes badgePulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.4); }
        70% { transform: scale(1.2); box-shadow: 0 0 0 6px rgba(225, 29, 72, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(225, 29, 72, 0); }
    }

    /* Enhanced Avatar Styling */
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
        box-shadow: 0 8px 16px -4px rgba(22, 72, 188, 0.3), inset 0 2px 4px rgba(255, 255, 255, 0.2);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        letter-spacing: -0.5px;
        position: relative;
        overflow: hidden;
    }

    .avatar-circle::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(255,255,255,0.2) 0%, transparent 50%);
        pointer-events: none;
    }

    .avatar-circle:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 12px 20px -5px rgba(22, 72, 188, 0.4);
        border-color: #fff;
    }

    .avatar-circle.large {
        width: 60px;
        height: 60px;
        font-size: 1.3rem;
    }

    /* Dropdown Common Styling */
    .dropdown-content {
        display: none;
        position: absolute;
        top: 55px;
        right: 0;
        background-color: var(--dropdown-bg);
        min-width: 320px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        overflow: hidden;
        animation: dropSlide 0.3s ease;
    }

    @keyframes dropSlide {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .dropdown-content.show {
        display: block;
    }

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

    .dropdown-header {
        padding: 15px 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--hover-bg);
    }

    .dropdown-header h4 { font-size: 0.95rem; font-weight: 700; color: var(--text-color); }
    .mark-all { font-size: 0.75rem; color: #1648bc; cursor: pointer; font-weight: 600; }

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
        color: var(--text-color);
        border-bottom: 1px solid var(--border-color);
    }

    .dropdown-item:hover { background: var(--hover-bg); }
    .dropdown-item.unread { background: var(--hover-bg); border-left: 3px solid #1648bc; }

    .notif-img {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
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
        border: 2px solid var(--surface-color);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 2;
    }

    .dropdown-item:hover .notif-profile-img {
        transform: scale(1.1);
        border-color: var(--accent-color);
    }

    .notif-info p { font-size: 0.85rem; color: var(--text-color); line-height: 1.4; margin: 0; }
    .notif-info span { font-size: 0.75rem; color: #94a3b8; }

    .dropdown-footer {
        padding: 12px;
        text-align: center;
        background: var(--hover-bg);
        border-top: 1px solid var(--border-color);
    }

    .dropdown-footer a {
        font-size: 0.8rem;
        color: #1648bc;
        text-decoration: none;
        font-weight: 600;
    }

    /* Profile Dropdown Styling */
    .profile-dropdown {
        min-width: 250px;
    }

    .profile-head {
        justify-content: flex-start;
        gap: 15px;
        background: var(--hover-bg);
    }

    .user-meta h4 { margin: 0; font-size: 0.9rem; color: var(--text-color); }
    .user-meta p { margin: 0; font-size: 0.75rem; color: #64748b; }

    .dropdown-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        text-decoration: none;
        color: var(--text-color);
        font-size: 0.85rem;
        transition: 0.2s;
    }

    .dropdown-link:hover {
        background: var(--hover-bg);
        color: #1648bc;
    }

    .dropdown-link i { width: 18px; color: #64748b; }
    .dropdown-link:hover i { color: #1648bc; }

    .logout-link { color: #ef4444 !important; }
    .logout-link i { color: #ef4444 !important; }

    /* Header Clock Styling */
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
</style>

<script>
    // Theme Management
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
        if (theme === 'dark') {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
        }
    }

    // Initialize theme on load
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

    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        const isOpen = dropdown.classList.contains('show');
        
        // Close all first
        document.querySelectorAll('.dropdown-content').forEach(d => d.classList.remove('show'));
        
        if (!isOpen) {
            dropdown.classList.add('show');
        }
    }

    // Close when clicking outside
    window.addEventListener('click', function(e) {
        if (!e.target.closest('.notification-wrapper') && !e.target.closest('.user-wrapper') && !e.target.closest('.theme-toggle')) {
            document.querySelectorAll('.dropdown-content').forEach(d => d.classList.remove('show'));
        }
    });

    // Sidebar Toggle Logic
    document.getElementById('sidebar-toggle').addEventListener('click', function() {
        const sidebar = document.querySelector('.sidebar');
        if(sidebar) {
            sidebar.classList.toggle('collapsed');
        }
    });

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