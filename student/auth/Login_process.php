<?php
/**
 * Student Portal Login Process
 */
session_start();
require_once '../../Database/config.php';
require_once '../../auth/mail_helper.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: Login.php");
    exit();
}

require_once '../../auth/Security.php';
verifyCsrfToken($_POST['csrf_token'] ?? '');

$identifier = trim($_POST['student_identifier'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($identifier) || empty($password)) {
    header("Location: Login.php?error=empty_fields");
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
            // Automatic verification for admins
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
            header("Location: " . ($redirectMap[$_SESSION['role']] ?? '../../auth/Login.php?error=unauthorized'));
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
        // Verification Code for Students
        $otp = rand(100000, 999999);
        $updateStmt = $pdo->prepare("UPDATE students SET verification_code = ? WHERE id = ?");
        $updateStmt->execute([$otp, $student->id]);

        // Notification for Student Login attempt
        $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, profile_image, icon, icon_bg, icon_color) VALUES (NULL, 'login_attempt', 'Student Login Attempt', ?, ?, 'fa-sign-in-alt', '#fef3c7', '#d97706')");
        $notif_stmt->execute([$student->first_name . " " . $student->last_name . " is trying to log in.", $student->profile_image]);

        if (sendOTP($student->email, $otp, 'Login Verification')) {
            header("Location: ../../auth/Verification.php?email=" . urlencode($student->email) . "&type=login");
        } else {
            header("Location: Login.php?error=mail_error");
        }
        exit();
    }

    header("Location: Login.php?error=invalid_credentials");
    exit();

} catch (PDOException $e) {
    error_log("Login Error: " . $e->getMessage());
    header("Location: Login.php?error=system_error");
    exit();
}
?>

