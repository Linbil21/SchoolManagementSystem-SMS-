<?php
/**
 * verify_otp.php - AJAX OTP Verification for Student Login
 * Returns JSON instead of header() redirects so fetch() can handle it.
 */
session_start();
header('Content-Type: application/json');

require_once '../../Database/config.php';

// Robust absolute-relative path logic
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/student/', '/Super-admin/', '/Admin/', '/Cashier/', '/Admission/', '/auth/', '/modules/'];
$project_base = '';
foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        $project_base = rtrim(substr($script_name, 0, $pos), '/');
        break;
    }
}
$root = $project_base . '/';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit();
}

$email = trim($_POST['email'] ?? '');
$otp   = trim($_POST['otp'] ?? '');

if (empty($email) || empty($otp) || strlen($otp) !== 6) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid OTP format.']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $student = $stmt->fetch();

    // Check if student exists and OTP matches
    if ($student && $otp === $student->verification_code) {
        // Clear OTP & mark verified
        $upd = $pdo->prepare("UPDATE students SET is_verified = 1, verification_code = NULL WHERE id = ?");
        $upd->execute([$student->id]);

        // Set Session
        $_SESSION['user_id']       = $student->id;
        $_SESSION['student_id']    = $student->student_id;
        $_SESSION['email']         = $student->email;
        $_SESSION['fullname']      = $student->first_name . ' ' . $student->last_name;
        $_SESSION['role']          = 'student';
        $_SESSION['profile_image'] = $student->profile_image ?? 'default.jpg';

        // Fetch admission & enrollment status safely
        $_SESSION['admission_status'] = 'Pending';
        $_SESSION['enrollment_status'] = 'Pending';
        try {
            $app = $pdo->prepare("SELECT status FROM admission_applications WHERE email = ? ORDER BY submission_date DESC LIMIT 1");
            $app->execute([$student->email]);
            $status = $app->fetchColumn();
            if ($status) $_SESSION['admission_status'] = $status;
        } catch (Exception $e) {}

        try {
            $enr = $pdo->prepare("SELECT status FROM enrollments WHERE email = ? ORDER BY created_at DESC LIMIT 1");
            $enr->execute([$student->email]);
            $status = $enr->fetchColumn();
            if ($status) $_SESSION['enrollment_status'] = $status;
        } catch (Exception $e) {}

        // Log notification
        try {
            $notif = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, profile_image, icon, icon_bg, icon_color) VALUES (NULL, ?, ?, ?, ?, 'fa-check-circle', '#d1fae5', '#059669')");
            $notif->execute(['login_success', 'Student Logged In', $student->first_name . ' ' . $student->last_name . ' has logged in successfully.', $student->profile_image ?? '']);
        } catch (Exception $e) {
            // Notifications are non-critical, ignore errors
        }

        echo json_encode([
            'status'   => 'success',
            'redirect' => $root . 'student/Dashboard.php'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid verification code. Please try again.']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'System error: ' . $e->getMessage()]);
}
?>
