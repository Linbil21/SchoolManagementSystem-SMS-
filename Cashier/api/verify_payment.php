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
            
            // Get user_id if student exists
            $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
            $stmt->execute([$payment->email]);
            $student_ref = $stmt->fetch();
            $target_user_id = $student_ref ? $student_ref->id : NULL;

            $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link, icon, icon_bg, icon_color) VALUES (?, 'payment', ?, ?, '/student/Modules/Payments/History.php', 'fa-receipt', ?, ?)");
            
            $bg = ($status === 'Verified') ? '#dcfce7' : '#fee2e2';
            $color = ($status === 'Verified') ? '#16a34a' : '#ef4444';
            
            $notif_stmt->execute([$target_user_id, $notif_title, $notif_message, $bg, $color]);

            // 3. If Verified, Update Enrollment Status to Enrolled & Generate ID
            if ($status === 'Verified') {
                $stmt = $pdo->prepare("SELECT enrollment_id FROM payments WHERE payment_id = ?");
                $stmt->execute([$payment_id]);
                $enrollment_id = $stmt->fetch()->enrollment_id;

                $pdo->prepare("UPDATE enrollments SET status = 'Enrolled' WHERE enrollmentId = ?")
                    ->execute([$enrollment_id]);

                // Generate Official Student ID if it's currently a placeholder (starts with ENR)
                $stmt = $pdo->prepare("SELECT id, student_id FROM students WHERE email = ?");
                $stmt->execute([$payment->email]);
                $student = $stmt->fetch();

                if ($student && (strpos($student->student_id, 'ENR') === 0 || empty($student->student_id))) {
                    $year = date('Y');
                    $stmt = $pdo->query("SELECT MAX(id) as last_id FROM students");
                    $last_id = $stmt->fetch()->last_id ?? 0;
                    $new_id_num = str_pad($last_id + 1, 4, '0', STR_PAD_LEFT);
                    $official_id = "$year-$new_id_num";

                    $pdo->prepare("UPDATE students SET student_id = ? WHERE id = ?")
                        ->execute([$official_id, $student->id]);
                    
                    // Update session for the student if they are logged in (though they'll need to re-login or dashboard will refresh it)
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
