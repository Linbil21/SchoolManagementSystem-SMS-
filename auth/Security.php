<?php
// sms/auth/Security.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * EMERGENCY GLOBAL CSS FILTER
 * Nukes the stray .student-modal-footer text from any page output.
 */
ob_start(function($buffer) {
    if (empty($buffer)) return $buffer;
    // Aggressive nuke for the stray CSS text
    $pattern = '/\.student-modal-footer\s*\{[^}]*padding:\s*20px\s*32px[^}]*\}/is';
    $cleaned = preg_replace($pattern, '', $buffer);
    
    // Literal fallback for the exact string reported
    $literal = '.student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }';
    $cleaned = str_replace($literal, '', $cleaned);
    
    return $cleaned;
});



/**
 * Checks if the current user has access based on their role.
 * If not, redirects them to their appropriate dashboard.
 * 
 * @param array|string $allowed_roles List of roles allowed to access the page
 */
// Detect Project Root
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/Super-admin/', '/Admin/', '/Cashier/', '/Admission/', '/auth/', '/student/', '/modules/'];
$project_base = '';

foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        $project_base = rtrim(substr($script_name, 0, $pos), '/');
        break;
    }
}
$root_path = $project_base . '/';

/**
 * Checks if the current user has access based on their role.
 * If not, redirects them to their appropriate dashboard.
 * 
 * @param array|string $allowed_roles List of roles allowed to access the page
 */
function checkRole($allowed_roles) {
    global $root_path;
    // Normalize to array
    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }

    // Always include superadmin in allowed roles (Full Access)
    if (!in_array('superadmin', $allowed_roles)) {
        $allowed_roles[] = 'superadmin';
    }

    // 1. Check if logged in
    if (!isset($_SESSION['role'])) {
        header("Location: " . $root_path . "auth/Login.php");
        exit();
    }

    $current_role = $_SESSION['role'];

    // 2. Check permission
    if (!in_array($current_role, $allowed_roles)) {
        // Redirect based on their ACTUAL role
        switch ($current_role) {
            case 'superadmin':
                header("Location: " . $root_path . "super-admin/Dashboard.php");
                break;
            case 'admin':
                header("Location: " . $root_path . "Admin/Dashboard.php");
                break;
            case 'admission':
                header("Location: " . $root_path . "Admission/Dashboard.php");
                break;
            case 'cashier':
                header("Location: " . $root_path . "Cashier/Dashboard.php");
                break;
            case 'student':
                header("Location: " . $root_path . "student/Dashboard.php");
                break;
            default:
                header("Location: " . $root_path . "auth/Login.php");
                break;
        }
        exit();
    }
}

/**
 * Determines if the current user should be in "View Only" mode.
 * True if core role is superadmin but accessing pages outside Super-admin directory.
 */
function isReadOnly() {
    if (!isset($_SESSION['role'])) return false;
    if ($_SESSION['role'] !== 'superadmin') return false;

    $script = $_SERVER['SCRIPT_NAME'];
    // If NOT in Super-admin folder, then it's read-only for superadmin
    return stripos($script, '/Super-admin/') === false;
}

/**
 * Generates a CSRF token and stores it in the session.
 * 
 * @return string The generated token
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies the CSRF token from the request against the session token.
 * Stops execution if the token is invalid.
 * 
 * @param string|null $token The token from the form/request
 */
function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        // Invalid token
        die("CSRF Validation Failed: Security Violation. Please refresh the page and try again.");
    }
}

/**
 * Masks an email address for privacy.
 * Example: jdoe@example.com -> j***@example.com
 */
function maskEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $email;
    }
    $parts = explode('@', $email);
    $username = $parts[0];
    $domain = $parts[1];

    $len = strlen($username);
    if ($len <= 2) {
        return $username . '@' . $domain; // Too short to mask
    }

    $visible = substr($username, 0, 1);
    $masked = str_repeat('*', 3);
    
    return $visible . $masked . '@' . $domain;
}

/**
 * Masks a phone number for privacy.
 * Example: 09123456789 -> 0912***6789
 */
function maskPhone($phone) {
    $len = strlen($phone);
    if ($len < 7) {
        return $phone; 
    }
    
    $start = substr($phone, 0, 4);
    $end = substr($phone, -4);
    $masked = str_repeat('*', $len - 8);
    
    return $start . $masked . $end;
}
?>
