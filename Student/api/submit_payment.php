<?php
session_start();
require_once '../../Database/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$student_email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'] ?? 0;
    $payment_date = $_POST['payment_date'] ?? date('Y-m-d');
    $reference_number = $_POST['reference_number'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $description = $_POST['description'] ?? '';
    $semester = $_POST['semester'] ?? '';
    $purpose = $_POST['purpose'] ?? '';

    if (empty($amount) || empty($reference_number) || empty($payment_method)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit();
    }

    // Handle File Upload (Optional now)
    $proof_path = null;
    if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] == 0) {
        $target_dir = "../../Assets/image/uploads/payments/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES["proof_of_payment"]["name"], PATHINFO_EXTENSION);
        $new_filename = "PYMT_" . time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["proof_of_payment"]["tmp_name"], $target_file)) {
            $proof_path = "Assets/image/uploads/payments/" . $new_filename;
        }
    }

    try {
        $pdo->beginTransaction();

        // 1. Find enrollment record
        $stmt = $pdo->prepare("SELECT enrollmentId, balance FROM enrollments WHERE email = ? AND status IN ('Enrolled', 'Validated', 'Pending Review') LIMIT 1");
        $stmt->execute([$student_email]);
        $enrollment = $stmt->fetch();

        if (!$enrollment) {
            // If no active enrollment found, try to find the latest one
            $stmt = $pdo->prepare("SELECT enrollmentId, balance FROM enrollments WHERE email = ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$student_email]);
            $enrollment = $stmt->fetch();
        }

        if (!$enrollment) {
            echo json_encode(['success' => false, 'message' => 'No active enrollment found for this student.']);
            $pdo->rollBack();
            exit();
        }

        $enrollment_id = $enrollment->enrollmentId;
        $current_balance = $enrollment->balance;

        // 2. Insert into payments table
        $insertStmt = $pdo->prepare("INSERT INTO payments (enrollment_id, amount, payment_method, description, purpose, semester, status, transaction_id, proof_of_payment, payment_date) VALUES (?, ?, ?, ?, ?, ?, 'Pending', ?, ?, ?)");
        $insertStmt->execute([
            $enrollment_id,
            $amount,
            $payment_method,
            $description,
            $purpose,
            $semester,
            $reference_number,
            $proof_path,
            $payment_date
        ]);

        // 3. Deduct from balance (per user request: "mababawasan balance nila")
        $new_balance = $current_balance - $amount;
        $updateStmt = $pdo->prepare("UPDATE enrollments SET balance = ? WHERE enrollmentId = ?");
        $updateStmt->execute([$new_balance, $enrollment_id]);

        // 4. Create notification for admin/cashier
        $notif_title = "New Payment Submitted";
        $notif_message = $_SESSION['fullname'] . " has submitted a payment of ₱" . number_format($amount, 2);
        $profile_image = $_SESSION['profile_image'] ?? null;
        
        $notif_stmt = $pdo->prepare("INSERT INTO notifications (type, title, message, link, icon, icon_bg, icon_color, profile_image) VALUES ('payment', ?, ?, '/sms/Cashier/Modules/Online-Payments.php', 'fa-receipt', '#dbeafe', '#2563eb', ?)");
        $notif_stmt->execute([$notif_title, $notif_message, $profile_image]);

        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Payment submitted successfully! Balance updated.']);

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
