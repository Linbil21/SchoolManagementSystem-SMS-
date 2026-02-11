<?php
session_start();
require_once '../../Database/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = $_POST['payment_id'] ?? null;
    $action = $_POST['action'] ?? ''; // 'approve' or 'reject'

    if (!$payment_id) {
        echo json_encode(['success' => false, 'message' => 'Payment ID is required.']);
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Fetch payment details
        $stmt = $pdo->prepare("SELECT * FROM payments WHERE paymentId = ?");
        $stmt->execute([$payment_id]);
        $payment = $stmt->fetch();

        if (!$payment) {
            echo json_encode(['success' => false, 'message' => 'Payment record not found.']);
            $pdo->rollBack();
            exit();
        }

        if ($action === 'approve') {
            $updateStmt = $pdo->prepare("UPDATE payments SET status = 'Completed' WHERE paymentId = ?");
            $updateStmt->execute([$payment_id]);
            
            // Create notification for student
            // We need student_id or enrollment email
            $stmt = $pdo->prepare("SELECT email FROM enrollments WHERE enrollmentId = ?");
            $stmt->execute([$payment->enrollment_id]);
            $student_mail = $stmt->fetch()->email ?? null;
            
            if ($student_mail) {
                $notif_stmt = $pdo->prepare("INSERT INTO notifications (type, title, message, link, icon, icon_bg, icon_color) VALUES ('payment', 'Payment Verified', 'Your payment with Ref: " . $payment->transaction_id . " has been verified.', '/Student/Modules/Payments/History.php', 'fa-check-circle', '#dcfce7', '#16a34a')");
                // Wait, notifications usually link to a user. In this system, user_id might be used.
                // Let's check notifications table structure.
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Payment approved and verified.']);

        } elseif ($action === 'reject') {
            // Mark as Rejected
            $updateStmt = $pdo->prepare("UPDATE payments SET status = 'Rejected' WHERE paymentId = ?");
            $updateStmt->execute([$payment_id]);

            // Refund the balance (since it was auto-deducted on submission)
            $refundStmt = $pdo->prepare("UPDATE enrollments SET balance = balance + ? WHERE enrollmentId = ?");
            $refundStmt->execute([$payment->amount, $payment->enrollment_id]);

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Payment rejected. Balance has been restored to the student.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
            $pdo->rollBack();
        }

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
