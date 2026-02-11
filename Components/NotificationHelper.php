<?php
/**
 * Notification Helper Functions
 * Shared across all user types (Admin, Super-admin, Admission, Student)
 */

// Fetch unread notifications count for current role
function getUnreadNotificationsCount($pdo, $role = null) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM notifications WHERE is_read = 0");
        return $stmt->fetch()->count ?? 0;
    } catch (PDOException $e) {
        return 0;
    }
}

// Fetch recent notifications (last 10)
function getRecentNotifications($pdo, $role = null) {
    try {
        $stmt = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Helper function to get relative time
function timeAgo($timestamp) {
    $time = strtotime($timestamp);
    $diff = time() - $time;
    
    if ($diff < 60) return "Just now";
    if ($diff < 3600) return floor($diff / 60) . " mins ago";
    if ($diff < 86400) return floor($diff / 3600) . " hours ago";
    if ($diff < 604800) return floor($diff / 86400) . " days ago";
    return date('M d, Y', $time);
}

// Create notification (for profile updates, registrations, etc.)
function createNotification($pdo, $type, $title, $message, $icon = 'fa-bell', $icon_bg = '#eef2ff', $icon_color = '#1648bc', $link = null, $profile_image = null) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, type, title, message, icon, icon_bg, icon_color, link, profile_image) 
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$type, $title, $message, $icon, $icon_bg, $icon_color, $link, $profile_image]);
    } catch (PDOException $e) {
        error_log("Failed to create notification: " . $e->getMessage());
        return false;
    }
}

// Get notification badge HTML
function getNotificationBadge($unread_count) {
    if ($unread_count > 0) {
        return "<span class=\"badge\">{$unread_count}</span>";
    }
    return "";
}

// Get notifications HTML dropdown body (Standardized for all portals)
function getNotificationsHtml($notifications) {
    if (empty($notifications)) {
        return '
            <div class="dropdown-item" style="text-align: center; padding: 30px;">
                <i class="fas fa-bell-slash" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                <p style="color: #94a3b8; font-size: 0.85rem;">No notifications yet</p>
            </div>
        ';
    }
    
    $html = '';
    foreach ($notifications as $notif) {
        $unreadClass = (isset($notif->is_read) && $notif->is_read == 0) ? 'unread' : '';
        $onclick = !empty($notif->link) ? "onclick=\"window.location.href='" . htmlspecialchars($notif->link) . "'\"" : "";
        
        // Extract name for fallback avatar if profile_image is missing
        $fallbackName = "User";
        if (preg_match('/^([^:]+)\s+has\s+(registered|submitted)/i', $notif->message, $matches)) {
             $fallbackName = $matches[1];
        } elseif (strpos($notif->message, ':') !== false) {
             $parts = explode(':', $notif->message);
             $fallbackName = trim($parts[0]);
        }
        
        $fallbackAvatar = "https://ui-avatars.com/api/?name=" . urlencode($fallbackName) . "&background=random&color=fff&size=100";
        
        $imageHtml = '';
        if (!empty($notif->profile_image) && $notif->profile_image !== 'default.jpg') {
            $imageHtml = "
                <img src=\"/sms/" . htmlspecialchars($notif->profile_image) . "\" 
                     class=\"notif-profile-img\" 
                     onerror=\"this.onerror=null; this.src='" . $fallbackAvatar . "';\"
                     alt=\"Profile\">
            ";
        } else {
            // Priority: UI Avatar with name > Icon
            $imageHtml = "
                <img src=\"" . $fallbackAvatar . "\" 
                     class=\"notif-profile-img\" 
                     alt=\"Avatar\">
            ";
        }

        $html .= "
            <div class=\"dropdown-item {$unreadClass}\" {$onclick}>
                <div style=\"position: relative;\">
                    {$imageHtml}
                    <div class=\"notif-type-icon\" style=\"background: " . htmlspecialchars($notif->icon_bg) . "; color: " . htmlspecialchars($notif->icon_color) . ";\">
                        <i class=\"" . htmlspecialchars($notif->icon) . "\" style=\"font-size: 0.6rem;\"></i>
                    </div>
                </div>
                <div class=\"notif-info\">
                    <p><strong>" . htmlspecialchars($notif->title) . ":</strong> " . htmlspecialchars($notif->message) . "</p>
                    <span>" . timeAgo($notif->created_at) . "</span>
                </div>
            </div>
        ";
    }
    
    return $html;
}
?>
