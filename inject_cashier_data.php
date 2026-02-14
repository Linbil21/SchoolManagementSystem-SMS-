<?php
/**
 * COMPREHENSIVE DUMMY DATA INJECTION
 * Target: Students, Enrollments, Payments
 */
require_once 'Database/config.php';

try {
    $pdo->beginTransaction();

    echo "🚀 Initializing Comprehensive Dummy Data Injection...\n";

    // 2. COURSES CHECK
    $courses = $pdo->query("SELECT courseId FROM courses")->fetchAll(PDO::FETCH_COLUMN);
    if (!$courses) {
        $pdo->exec("INSERT INTO courses (course_name, course_code, department) VALUES 
            ('BS Information Technology', 'BSIT', 'CCS'),
            ('BS Computer Science', 'BSCS', 'CCS'),
            ('BS Hospitality Management', 'BSHM', 'CHM'),
            ('BS Business Administration', 'BSBA', 'CBA')");
        $courses = $pdo->query("SELECT courseId FROM courses")->fetchAll(PDO::FETCH_COLUMN);
    }

    // 3. GENERATE STUDENTS & ENROLLMENTS
    $names = [
        ['John', 'Doe', 'john.doe@test.com'],
        ['Jane', 'Smith', 'jane.smith@test.com'],
        ['Michael', 'Brown', 'mike.b@test.com'],
        ['Emily', 'Davis', 'emily.d@test.com'],
        ['David', 'Wilson', 'david.w@test.com'],
        ['Sophia', 'Martinez', 'sophia.m@test.com'],
        ['Daniel', 'Anderson', 'dan.a@test.com'],
        ['Olivia', 'Taylor', 'olivia.t@test.com'],
        ['Lucas', 'Hernandez', 'lucas.h@test.com'],
        ['Isabella', 'Garcia', 'isabella.g@test.com'],
        ['Ethan', 'Lopez', 'ethan.l@test.com'],
        ['Ava', 'Gonzalez', 'ava.g@test.com']
    ];

    $pass = password_hash('student123', PASSWORD_DEFAULT);
    $batch_date = date('Y-m-d H:i:s');

    foreach ($names as $i => $n) {
        $sid = "2024-" . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
        $email = $n[2];
        
        // Student Record
        $stmt = $pdo->prepare("INSERT IGNORE INTO students (student_id, first_name, last_name, email, password, course, year_level, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$sid, $n[0], $n[1], $email, $pass, 'BSIT', 'First Year']);

        // Enrollment Record
        echo "   - Processing enrollment for $email...\n";
        $ref = "REF-" . strtoupper(substr(md5($email), 0, 10));
        $tuition = rand(15000, 25000);
        $misc = rand(3000, 6000);
        $lab = rand(1000, 4000);
        $total = $tuition + $misc + $lab;
        
        $stmt = $pdo->prepare("INSERT IGNORE INTO enrollments 
            (reference_code, admission_type, course_id, year_level, first_name, last_name, gender, birthdate, contact_number, email, address, id_picture, guardian_first, guardian_last, guardian_email, guardian_contact, relationship, guardian_address, primary_school, primary_year, secondary_school, secondary_year, status, tuition_fee, misc_fee, lab_fee, total_fee, balance) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $params = [
            $ref, 'Freshman', $courses[array_rand($courses)], 'First Year', $n[0], $n[1], 'N/A', '2005-01-01', '09123456789', $email, 'Sample Address', 'uploads/id/default.png', 'Guardian', 'Last', 'g@test.com', '0999', 'Parent', 'Addr', 'Pri', '2017', 'Sec', '2023', 'Enrolled', $tuition, $misc, $lab, $total, $total
        ];
        $stmt->execute($params);
        
        $stmt = $pdo->prepare("SELECT enrollmentId FROM enrollments WHERE email = ?");
        $stmt->execute([$email]);
        $enrollment_id = $stmt->fetchColumn();

        // 4. GENERATE PAYMENTS
        // Scenario A: Completed Cash (Walk-in)
        $amt1 = rand(5000, 10000);
        $stmt = $pdo->prepare("INSERT INTO payments (enrollment_id, amount, payment_method, status, transaction_id, created_at, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$enrollment_id, $amt1, 'Cash', 'Completed', "CASH-" . uniqid(), date('Y-m-d H:i:s', strtotime("-$i days")), 'Initial Downpayment']);
        $pdo->prepare("UPDATE enrollments SET balance = balance - ? WHERE enrollmentId = ?")->execute([$amt1, $enrollment_id]);

        // Scenario B: Online Pending (Requires Verification)
        if ($i % 3 == 0) {
            $amt2 = rand(2000, 5000);
            $stmt = $pdo->prepare("INSERT INTO payments (enrollment_id, amount, payment_method, status, transaction_id, proof_of_payment, created_at, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$enrollment_id, $amt2, 'E-Wallet (GCash/Maya)', 'Pending', "TXN-" . rand(100000, 999999), "uploads/receipts/dummy_receipt.png", date('Y-m-d H:i:s'), 'Monthly Installment']);
        }

        // Scenario C: Refund Requested
        if ($i == 2 || $i == 5) {
            $amt3 = rand(1000, 3000);
            $stmt = $pdo->prepare("INSERT INTO payments (enrollment_id, amount, payment_method, status, transaction_id, created_at, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$enrollment_id, $amt3, 'Cash', 'Refund Pending', "VOID-" . rand(1000, 9999), date('Y-m-d H:i:s'), 'Refund Requested']);
        }
    }

    $pdo->commit();
    echo "✅ Success! Injected " . count($names) . " students with enrollment and payment history.\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "❌ Error Injection Failed: " . $e->getMessage() . "\n";
}
