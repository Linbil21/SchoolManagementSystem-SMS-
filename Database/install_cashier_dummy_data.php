<?php
/**
 * Cashier Dummy Data Installer
 * Populates fee assessments and payment history for testing.
 */
require_once 'config.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<div style='font-family: sans-serif; padding: 20px; white-space: pre-wrap;'>";
    echo "<b>🚀 Starting Cashier Dummy Data Installation...</b>\n\n";

    // 1. Ensure Table Schema is Up-to-Date
    echo "Checking table schema...\n";
    $pdo->exec("ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS tuition_fee DECIMAL(10,2) DEFAULT 0.00");
    $pdo->exec("ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS misc_fee DECIMAL(10,2) DEFAULT 0.00");
    $pdo->exec("ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS lab_fee DECIMAL(10,2) DEFAULT 0.00");
    $pdo->exec("ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS total_fee DECIMAL(10,2) DEFAULT 0.00");
    $pdo->exec("ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS balance DECIMAL(10,2) DEFAULT 0.00");

    $pdo->exec("CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        enrollment_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        transaction_id VARCHAR(100) NOT NULL,
        status VARCHAR(20) DEFAULT 'Completed',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Schema updated successfully.\n\n";

    // 2. Fetch Students to Associate with Enrollments
    $students = $pdo->query("SELECT * FROM students LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    if (!$students) {
        die("❌ No students found. Please run install_dummy_data.php first.");
    }

    echo "Found " . count($students) . " students. Generating assessments...\n";

    foreach ($students as $student) {
        // Check if enrollment exists for this student email
        $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE email = ? LIMIT 1");
        $stmt->execute([$student['email']]);
        $enrollment = $stmt->fetch();

        $tuition = 4975.00;
        $misc = 0.00;
        $lab = 0.00;
        $total = $tuition + $misc + $lab;
        
        if (!$enrollment) {
            // Create a new enrollment record
            $ref = "ENR-" . strtoupper(substr(md5(uniqid()), 0, 8));
            $ins = $pdo->prepare("INSERT INTO enrollments (email, first_name, last_name, reference_code, course_id, year_level, status, tuition_fee, misc_fee, lab_fee, total_fee, balance) VALUES (?, ?, ?, ?, ?, ?, 'Enrolled', ?, ?, ?, ?, ?)");
            $ins->execute([
                $student['email'], 
                $student['first_name'], 
                $student['last_name'], 
                $ref,
                rand(1, 5),
                $student['year_level'],
                $tuition, $misc, $lab, $total, $total
            ]);
            $enrollment_id = $pdo->lastInsertId();
            echo "✅ Created Enrollment for {$student['first_name']} (ID: $enrollment_id)\n";
        } else {
            $enrollment_id = $enrollment['id'];
            // Update existing enrollment with dummy fees
            $upd = $pdo->prepare("UPDATE enrollments SET tuition_fee = ?, misc_fee = ?, lab_fee = ?, total_fee = ?, balance = ? WHERE id = ?");
            $upd->execute([$tuition, $misc, $lab, $total, $total, $enrollment_id]);
            echo "📝 Updated Fees for {$student['first_name']} (Enrollment ID: $enrollment_id)\n";
        }

        // 3. Add some random payments
        $num_payments = rand(1, 3);
        $remaining_balance = $total;

        for ($j = 0; $j < $num_payments; $j++) {
            $pay_amount = rand(2000, 8000);
            if ($pay_amount > $remaining_balance) $pay_amount = $remaining_balance;
            if ($pay_amount <= 0) break;

            $method = ['Cash', 'Bank Transfer', 'G-Cash', 'Credit Card'][rand(0, 3)];
            $trans_id = "TRANS-" . time() . "-" . rand(100, 999);

            $pay_stmt = $pdo->prepare("INSERT INTO payments (enrollment_id, amount, payment_method, transaction_id, status) VALUES (?, ?, ?, ?, 'Completed')");
            $pay_stmt->execute([$enrollment_id, $pay_amount, $method, $trans_id]);

            $remaining_balance -= $pay_amount;
            
            // Sync balance in enrollments
            $sync = $pdo->prepare("UPDATE enrollments SET balance = ? WHERE id = ?");
            $sync->execute([$remaining_balance, $enrollment_id]);
        }
    }

    echo "\n<b>✨ SUCCESS! Cashier dummy data (Assessments & Payments) installed.</b>";
    echo "\n<a href='/Cashier/Modules/Student-Assessment.php' style='display:inline-block; margin-top:20px; padding:10px 20px; background:#1648bc; color:white; text-decoration:none; border-radius:8px;'>Go to Assessment Console</a>";
    echo "</div>";

} catch (PDOException $e) {
    echo "\n❌ INSTALLATION FAILED: " . $e->getMessage();
}
?>
