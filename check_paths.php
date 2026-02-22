<?php
require_once 'Database/config.php';
try {
    $stmt = $pdo->query("SELECT email, birth_cert, form_138, id_picture FROM enrollments WHERE birth_cert IS NOT NULL LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Enrollments:\n" . json_encode($rows, JSON_PRETTY_PRINT) . "\n\n";

    $stmt = $pdo->query("SELECT enrollment_id, proof_of_payment FROM payments WHERE proof_of_payment IS NOT NULL LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Payments:\n" . json_encode($rows, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
