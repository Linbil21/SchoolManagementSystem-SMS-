<?php
require_once 'config.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

    echo "Starting Dummy Data Installation...<br>";

    // Column Checking Helper
    function ensureColumn($pdo, $table, $column, $definition) {
        try {
            $stmt = $pdo->query("SELECT $column FROM $table LIMIT 1");
            $test = $stmt->fetch();
            $stmt->closeCursor(); 
        } catch (Exception $e) {
            echo "Adding missing column: $column to $table...<br>";
            $pdo->exec("ALTER TABLE $table ADD COLUMN $column $definition");
        }
    }

    ensureColumn($pdo, 'users', 'first_name', "VARCHAR(100) NULL");
    ensureColumn($pdo, 'users', 'last_name',  "VARCHAR(100) NULL");
    ensureColumn($pdo, 'users', 'password_hash', "VARCHAR(255) NULL");
    ensureColumn($pdo, 'users', 'profile_image', "VARCHAR(255) DEFAULT 'Assets/image/uploads/staff/default.jpg'");

    $password = password_hash('password123', PASSWORD_DEFAULT);

    // 2. STAFF ACCOUNTS
    $staff = [
        ['admin.one@example.com', $password, 'Admin', 'Arthur', 'Adminov', 'active', 'Assets/image/uploads/staff/default.jpg'],
        ['admission.one@example.com', $password, 'Admission', 'Alice', 'Admisson', 'active', 'Assets/image/uploads/staff/default.jpg'],
        ['cashier.one@example.com', $password, 'Cashier', 'Charlie', 'Cash', 'active', 'Assets/image/uploads/staff/default.jpg'],
        ['super.admin@example.com', $password, 'SuperAdmin', 'Sarah', 'Super', 'active', 'Assets/image/uploads/staff/default.jpg']
    ];

    $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role, first_name, last_name, status, profile_image, password) VALUES (?, ?, ?, ?, ?, ?, ?, 'legacy')");
    
    foreach ($staff as $s) {
        $check = $pdo->prepare("SELECT userId FROM users WHERE email = ?");
        $check->execute([$s[0]]);
        if (!$check->fetch()) {
            $stmt->execute($s);
            echo "Created Staff: {$s[2]} - {$s[0]}<br>";
        }
        $check->closeCursor();
    }

    // 3. STUDENTS
    $courses = ['BS Information Technology', 'BS Computer Science', 'BS Business Administration', 'BS Criminology'];
    $years = ['First Year', 'Second Year', 'Third Year', 'Fourth Year'];
    
    $student_stmt = $pdo->prepare("INSERT INTO students (student_id, first_name, mid_name, last_name, email, password, course, year_level, status, profile_image, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");

    for ($i = 1; $i <= 20; $i++) {
        $id = "2026-" . str_pad($i, 4, '0', STR_PAD_LEFT);
        $email = "student{$i}@example.com";
        $fname = "Student{$i}";
        $lname = "User";
        $course = $courses[array_rand($courses)];
        $year = $years[array_rand($years)];
        
        $check = $pdo->prepare("SELECT student_id FROM students WHERE student_id = ?");
        $check->execute([$id]);
        if (!$check->fetch()) {
            $student_stmt->execute([
                $id, $fname, 'M', $lname, $email, $password, 
                $course, $year, 'Regular', 'Assets/image/uploads/students/default.png'
            ]);
            echo "Created Student: $id - $email<br>";
        }
        $check->closeCursor();
    }

    // 4. ENROLLMENT APPLICATIONS
    $application_stmt = $pdo->prepare("INSERT INTO enrollments (reference_code, first_name, last_name, email, course_id, year_level, status, admission_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    for ($i = 1; $i <= 10; $i++) {
        $ref = "ENR" . date('y') . str_pad($i + 500, 5, '0', STR_PAD_LEFT);
        $email = "applicant{$i}@example.com";
        
        // CHECK IF REF EXISTS
        // Enrollments likely has 'id' or just 'reference_code' as unique
        $check = $pdo->prepare("SELECT * FROM enrollments WHERE email = ? LIMIT 1");
        $check->execute([$email]);
        if (!$check->fetch()) {
            $application_stmt->execute([
                $ref, "Applicant{$i}", "Doe", $email, 
                rand(1, 5), 'First Year', 'Pending', 'Freshman'
            ]);
            echo "Created Application: $ref<br>";
        }
        $check->closeCursor();
    }

    // 5. NOTIFICATIONS
    $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, icon, icon_bg, icon_color, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    
    $notif_stmt->execute([1, 'system', 'System Update', 'Maintenance scheduled for tonight at 10 PM.', 'fa-cog', '#fff', '#000']);
    $notif_stmt->execute([1, 'alert', 'Backup Complete', 'Daily database backup completed successfully.', 'fa-database', '#d1fae5', '#059669']);

    echo "<br><b>SUCCESS! Dummy data installation complete.</b>";
    echo "<br><br><a href='install_cashier_dummy_data.php' style='padding:10px 20px; background:#1648bc; color:white; text-decoration:none; border-radius:5px;'>👉 Install Cashier Dummy Data (Assessments & Payments)</a>";

} catch (PDOException $e) {
    echo "Installation Failed: " . $e->getMessage();
}
?>
