<?php
session_start();
require_once '../../Database/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cashier') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = $_POST['payment_id'] ?? null;
    $status = $_POST['status'] ?? null;
    $remarks = $_POST['remarks'] ?? '';

    if (!$payment_id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit();
    }

    try {
        $pdo->beginTransaction();

        // 1. Update payment status
        $stmt = $pdo->prepare("UPDATE payments SET status = ?, description = CONCAT(IFNULL(description, ''), '\nNote: ', ?) WHERE payment_id = ?");
        $stmt->execute([$status, $remarks, $payment_id]);

        // 2. Notify student
        $payment_stmt = $pdo->prepare("SELECT e.email, e.first_name, p.amount FROM payments p JOIN enrollments e ON p.enrollment_id = e.enrollmentId WHERE p.payment_id = ?");
        $payment_stmt->execute([$payment_id]);
        $payment = $payment_stmt->fetch();

        if ($payment) {
            $notif_title = "Payment " . $status;
            $notif_message = "Your payment of ₱" . number_format($payment->amount, 2) . " has been " . strtolower($status) . ".";
            
            $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link, icon, icon_bg, icon_color) VALUES (NULL, 'payment', ?, ?, '/Student/Modules/Payments/History.php', 'fa-receipt', ?, ?)");
            
            $bg = ($status === 'Verified') ? '#dcfce7' : '#fee2e2';
            $color = ($status === 'Verified') ? '#16a34a' : '#ef4444';
            
            $notif_stmt->execute([$notif_title, $notif_message, $bg, $color]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Payment status updated to ' . $status]);

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
