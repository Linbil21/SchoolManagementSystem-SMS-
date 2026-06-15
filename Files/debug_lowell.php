<?php
require_once 'Database/config.php';
$email = 'lowelltoribio@gmail.com'; // Extracted from context or just a guess?
// Wait, I don't know his email. Let me find him by name from students table first.

try {
    $stmt = $pdo->query("SELECT * FROM students WHERE first_name LIKE '%Lowell%' OR last_name LIKE '%Toribio%'");
    $students = $stmt->fetchAll();
    echo "<h3>Students found:</h3>";
    print_r($students);

    if ($students) {
        $email = $students[0]->email;
        echo "<h3>Enrollment for $email:</h3>";
        $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE email = ?");
        $stmt->execute([$email]);
        print_r($stmt->fetchAll());

        echo "<h3>Payments for $email:</h3>";
        $stmt = $pdo->prepare("SELECT * FROM payments WHERE enrollment_id IN (SELECT enrollmentId FROM enrollments WHERE email = ?) OR description LIKE ?");
        $stmt->execute([$email, "%$email%"]);
        print_r($stmt->fetchAll());
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
