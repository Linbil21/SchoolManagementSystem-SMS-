<?php
/**
 * Student Portal Login Process
 * This script handles authentication for the student portal.
 * It also supports staff/admin login for convenience.
 */
session_start();
require_once '../../Database/config.php';

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: Login.php");
    exit();
}

require_once '../../auth/Security.php';
// Validate CSRF token
verifyCsrfToken($_POST['csrf_token'] ?? '');

$identifier = trim($_POST['student_identifier'] ?? '');
$password = $_POST['password'] ?? '';

// Basic Validation
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
        // Support both plain text (for initial setup) and hashed passwords
        $isValidPassword = ($password === $user->password) ||
            (isset($user->password_hash) && password_verify($password, $user->password_hash));

        if ($isValidPassword) {
            // Populate Session with Admin/Staff data
            $_SESSION['userId'] = $user->userId;
            $_SESSION['email'] = $user->email;
            $_SESSION['role'] = strtolower($user->role);
            $_SESSION['fullname'] = $user->fullname ?? 'Administrator';
            $_SESSION['status'] = 'online';

            // Update user status in database
            $update = $pdo->prepare("UPDATE users SET status = 'online', last_login = CURRENT_TIMESTAMP WHERE userId = ?");
            $update->execute([$user->userId]);

            // Redirect based on role (Relative to Student/auth/)
            $redirectMap = [
                'admin' => '../../Admin/Dashboard.php',
                'superadmin' => '../../Super-admin/Dashboard.php',
                'admission' => '../../Admission/Dashboard.php',
                'cashier' => '../../Cashier/Dashboard.php'
            ];

            $target = $redirectMap[$_SESSION['role']] ?? '../../auth/Login.php?error=unauthorized';
            header("Location: " . $target);
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

    if ($student) {
        // Students typically use hashed passwords
        if (password_verify($password, $student->password) || $password === $student->password) {
            // Populate Session with Student data
            $_SESSION['user_id'] = $student->id;
            $_SESSION['student_id'] = $student->student_id;
            $_SESSION['email'] = $student->email;
            $_SESSION['fullname'] = $student->first_name . ' ' . $student->last_name;
            $_SESSION['role'] = 'student';
            $_SESSION['profile_image'] = $student->profile_image;

            header("Location: ../Dashboard.php");
            exit();
        }
    }

    // PHASE 3: Authentication Failed
    header("Location: Login.php?error=invalid_credentials");
    exit();

} catch (PDOException $e) {
    // Log system errors and show generic message to user
    error_log("Login Error: " . $e->getMessage());
    header("Location: Login.php?error=system_error");
    exit();
}
?>