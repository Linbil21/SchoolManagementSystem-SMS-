<?php
require_once 'Database/config.php';

try {
    // Delete old sample notifications
    $pdo->exec("DELETE FROM notifications");
    echo "🗑️  Cleared old notifications\n\n";
    
    // Get student info for creating sample notifications with profile images
    $students = $pdo->query("
        SELECT student_id, first_name, last_name, profile_image 
        FROM students 
        ORDER BY created_at DESC 
        LIMIT 2
    ")->fetchAll();
    
    if (count($students) > 0) {
        foreach ($students as $student) {
            $title = "New Student Registration";
            $message = htmlspecialchars($student->first_name . " " . $student->last_name) . " has registered successfully.";
            
            $stmt = $pdo->prepare("
                INSERT INTO notifications (user_id, type, title, message, profile_image, icon, icon_bg, icon_color, link) 
                VALUES (NULL, 'student_registration', ?, ?, ?, 'fa-user-plus', '#d1fae5', '#059669', '/SMS/Admin/submodules/Student-Accounts.php')
            ");
            $stmt->execute([$title, $message, $student->profile_image]);
            
            echo "✅ Created notification for: {$student->first_name} {$student->last_name}\n";
            echo "   📸 Profile image: " . ($student->profile_image ?: 'None') . "\n";
        }
    } else {
        echo "⚠️  No students found in database. Register a student first.\n";
    }
    
    // Show current notifications
    echo "\n📋 Current Notifications:\n";
    $all = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC")->fetchAll();
    foreach ($all as $n) {
        $status = $n->is_read == 0 ? '🔔 UNREAD' : '✅ READ';
        $photo = $n->profile_image ? '📸' : '🎨';
        echo "  {$status} {$photo} - {$n->title}: {$n->message}\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
