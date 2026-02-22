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
        $payment_stmt = $pdo->prepare("SELECT p.amount, p.description, p.enrollment_id, e.email, e.first_name FROM payments p LEFT JOIN enrollments e ON p.enrollment_id = e.enrollmentId WHERE p.payment_id = ?");
        $payment_stmt->execute([$payment_id]);
        $payment = $payment_stmt->fetch();

        if ($payment) {
            $notif_title = "Payment " . $status;
            $notif_message = "Your payment of ₱" . number_format($payment->amount, 2) . " has been " . strtolower($status) . ".";
            
            // Extract email from fallback description if standard query resulted in NULL email
            $target_email = $payment->email;
            if (!$target_email && preg_match('/Paid by:\s*([^\s\]]+)/i', $payment->description, $matches)) {
                $target_email = $matches[1];
            }

            // Get user_id if student exists
            $target_user_id = NULL;
            if ($target_email) {
                $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
                $stmt->execute([$target_email]);
                $student_ref = $stmt->fetch();
                if ($student_ref) {
                    $target_user_id = $student_ref->id;
                }
            }

            $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link, icon, icon_bg, icon_color) VALUES (?, 'payment', ?, ?, '/student/Modules/Payments/History.php', 'fa-receipt', ?, ?)");
            
            $bg = ($status === 'Verified') ? '#dcfce7' : '#fee2e2';
            $color = ($status === 'Verified') ? '#16a34a' : '#ef4444';
            
            $notif_stmt->execute([$target_user_id, $notif_title, $notif_message, $bg, $color]);

            // 3. If Verified: Deduct Balance & Update Status for Final Admission Check
            if ($status === 'Verified') {
                $enrollment_id = $payment->enrollment_id;
                $amount_paid = $payment->amount;

                if ($enrollment_id) {
                    // Standard update by enrollment id
                    $pdo->prepare("UPDATE enrollments SET balance = balance - ?, status = 'Validation' WHERE enrollmentId = ?")
                        ->execute([$amount_paid, $enrollment_id]);
                } elseif ($target_email) {
                    // Fallback aggressive update by email
                    $pdo->prepare("UPDATE enrollments SET balance = balance - ?, status = 'Validation' WHERE email = ?")
                        ->execute([$amount_paid, $target_email]);
                }
            }
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
