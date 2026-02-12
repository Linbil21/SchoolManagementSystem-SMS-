<?php
/**
 * Student Portal Login Process - AJAX Enabled
 */
session_start();
header('Content-Type: application/json');

require_once '../../Database/config.php';
require_once '../../auth/mail_helper.php';
require_once '../../auth/Security.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

try {
    verifyCsrfToken($_POST['csrf_token'] ?? '');
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Security token expired. Please refresh.']);
    exit();
}

$identifier = trim($_POST['student_identifier'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($identifier) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields.']);
    exit();
}

try {
    /**
     * PHASE 1: Check the 'users' table (Staff, Admins, etc.)
     */
    $userStmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $userStmt->execute([$identifier]);
    $user = $userStmt->fetch();

    if ($user) {
        $isValidPassword = ($password === $user->password) ||
            (isset($user->password_hash) && password_verify($password, $user->password_hash));

        if ($isValidPassword) {
            $_SESSION['userId'] = $user->userId;
            $_SESSION['email'] = $user->email;
            $_SESSION['role'] = strtolower($user->role);
            $_SESSION['fullname'] = $user->fullname ?? 'Administrator';
            $_SESSION['status'] = 'online';

            $update = $pdo->prepare("UPDATE users SET status = 'online', last_login = CURRENT_TIMESTAMP WHERE userId = ?");
            $update->execute([$user->userId]);

            $redirectMap = [
                'admin' => '../../Admin/Dashboard.php',
                'superadmin' => '../../Super-admin/Dashboard.php',
                'admission' => '../../Admission/Dashboard.php',
                'cashier' => '../../Cashier/Dashboard.php'
            ];
            $redirect = $redirectMap[$_SESSION['role']] ?? '../../auth/Login.php?error=unauthorized';
            echo json_encode(['status' => 'success', 'redirect' => $redirect]);
            exit();
        }
    }

    /**
     * PHASE 2: Check the 'students' table
     */
    $studentStmt = $pdo->prepare("SELECT * FROM students WHERE email = :id OR student_id = :id LIMIT 1");
    $studentStmt->bindParam(':id', $identifier);
    $studentStmt->execute();
    $student = $studentStmt->fetch();

    if ($student && (password_verify($password, $student->password) || $password === $student->password)) {
        // Generate 4-digit OTP
        $otp = rand(1000, 9999);
        $updateStmt = $pdo->prepare("UPDATE students SET verification_code = ? WHERE id = ?");
        $updateStmt->execute([$otp, $student->id]);

        if (sendOTP($student->email, $otp, 'Login Verification')) {
            echo json_encode([
                'status' => 'otp_required',
                'email' => $student->email,
                'masked_email' => maskEmail($student->email)
            ]);
        } else {
            $error_msg = get_last_mail_error();
            echo json_encode(['status' => 'error', 'message' => 'Failed to send verification code. ' . $error_msg]);
        }
        exit();
    }

    echo json_encode(['status' => 'error', 'message' => 'Invalid credentials.']);
    exit();

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'System error. Please try again.']);
    exit();
}
?>

