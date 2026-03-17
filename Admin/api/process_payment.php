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
            
            // 1. Deduct from balance
            $deduct = $pdo->prepare("UPDATE enrollments SET balance = balance - ? WHERE enrollmentId = ?");
            $deduct->execute([$payment->amount, $payment->enrollment_id]);

            // 2. Create notification for student
            $stmt = $pdo->prepare("SELECT email FROM enrollments WHERE enrollmentId = ?");
            $stmt->execute([$payment->enrollment_id]);
            $student_mail = $stmt->fetch()->email ?? null;
            
            if ($student_mail) {
                // Try to get user_id from students table
                $userStmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
                $userStmt->execute([$student_mail]);
                $user_id = $userStmt->fetch()->id ?? NULL;

                $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link, icon, icon_bg, icon_color) VALUES (?, 'payment', 'Payment Verified', ?, '/student/Modules/Payments/History.php', 'fa-check-circle', '#dcfce7', '#16a34a')");
                $notif_msg = "Your payment of ₱" . number_format($payment->amount, 2) . " (Ref: " . $payment->transaction_id . ") has been verified.";
                $notif_stmt->execute([$user_id, $notif_msg]);

                // Send Email Receipt
                try {
                    require_once '../../auth/mail_helper.php';
                    $enroll_stmt = $pdo->prepare("SELECT first_name, last_name, balance FROM enrollments WHERE enrollmentId = ?");
                    $enroll_stmt->execute([$payment->enrollment_id]);
                    $enr = $enroll_stmt->fetch();

                    sendPaymentReceiptEmail($student_mail, [
                        'first_name' => $enr->first_name ?? 'Student',
                        'last_name' => $enr->last_name ?? '',
                        'amount' => $payment->amount,
                        'method' => $payment->payment_method,
                        'transaction_id' => $payment->transaction_id,
                        'description' => $payment->description,
                        'new_balance' => $enr->balance ?? 0
                    ]);
                } catch (Exception $e) { /* Email error shouldn't stop the process */ }
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Payment approved and balance updated.']);

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
