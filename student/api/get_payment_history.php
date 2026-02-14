<?php
/**
 * GET PAYMENT HISTORY API
 * Fetch paginated or full payment history for the logged-in student.
 */
header('Content-Type: application/json');
session_start();
require_once '../../Database/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$student_email = $_SESSION['email'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            p.paymentId,
            p.amount,
            p.payment_method,
            p.status,
            p.transaction_id,
            p.description,
            p.created_at,
            p.proof_of_payment
        FROM payments p 
        JOIN enrollments e ON p.enrollment_id = e.enrollmentId 
        WHERE e.email = ? 
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$student_email]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'count' => count($history),
        'data' => $history
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
