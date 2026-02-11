<?php
require_once 'Database/config.php';

try {
    // Create some sample notifications
    $notifications = [
        [
            'type' => 'student_registration',
            'title' => 'New Student Registration',
            'message' => 'Juan Dela Cruz has registered successfully.',
            'icon' => 'fa-user-plus',
            'icon_bg' => '#d1fae5',
            'icon_color' => '#059669',
            'link' => '/sms/Admin/Submodules/Student-Accounts.php'
        ],
        [
            'type' => 'student_registration',
            'title' => 'New Student Registration',
            'message' => 'Maria Santos has registered successfully.',
            'icon' => 'fa-user-plus',
            'icon_bg' => '#d1fae5',
            'icon_color' => '#059669',
            'link' => '/sms/Admin/Submodules/Student-Accounts.php'
        ]
    ];
    
    foreach ($notifications as $notif) {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, type, title, message, icon, icon_bg, icon_color, link) 
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $notif['type'],
            $notif['title'],
            $notif['message'],
            $notif['icon'],
            $notif['icon_bg'],
            $notif['icon_color'],
            $notif['link']
        ]);
    }
    
    echo "✅ Sample notifications created successfully!\n";
    
    // Show current notifications
    $all = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC")->fetchAll();
    echo "\n📋 Current Notifications (" . count($all) . " total):\n";
    foreach ($all as $n) {
        $status = $n->is_read == 0 ? '🔔 UNREAD' : '✅ READ';
        echo "  {$status} - {$n->title}: {$n->message}\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
