<?php
require_once __DIR__ . '/../../auth/Security.php';
checkRole(['admission']);

/**
 * CACHE PURGE TOOL - Admission Module
 * Clears OPCache and other PHP caches to resolve stray CSS and stale file issues.
 */

$success = false;
$message = "";

if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        $success = true;
        $message = "OPCache has been successfully purged. Stale PHP versions should now be gone.";
    } else {
        $message = "OPCache reset failed. Your hosting provider may have disabled this function.";
    }
} else {
    $message = "OPCache is not enabled on this server or reset function is unavailable.";
}

// Clear realpath cache
clearstatcache(true);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Cache Purge - Admission</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background: #f8fafc; font-family: sans-serif;">
    <script>
        Swal.fire({
            title: '<?php echo $success ? "Success!" : "Result"; ?>',
            text: '<?php echo addslashes($message); ?>',
            icon: '<?php echo $success ? "success" : "info"; ?>',
            confirmButtonColor: '#1648bc'
        }).then(() => {
            window.location.href = '../Dashboard.php';
        });
    </script>
</body>
</html>
