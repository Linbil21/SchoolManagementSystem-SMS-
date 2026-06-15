<?php
/**
 * Force Sync from GitHub Master
 * Place this in the root of your project and run it via browser:
 * https://ems.jampzdev.com/force_sync.php
 */

echo "<h1>Forcing Update from GitHub</h1>";

$filesToUpdate = [
    'Admission/Modules/New-Applications.php',
    'Admission/Modules/Student-Requirements.php',
    'Admission/Modules/Evaluation.php',
    'Admission/Modules/Enrollment-Validation.php',
    'auth/Security.php'
];

$githubRoot = "https://raw.githubusercontent.com/jampzdev/ems/main/";

foreach ($filesToUpdate as $file) {
    $url = $githubRoot . $file;
    $content = @file_get_contents($url);
    
    if ($content === false) {
        echo "<p style='color:red'>✘ Failed to fetch $file from GitHub.</p>";
    } else {
        $localPath = __DIR__ . '/' . $file;
        if (file_put_contents($localPath, $content)) {
            echo "<p style='color:green'>✔ Successfully overwritten $file with fresh code from GitHub.</p>";
        } else {
            echo "<p style='color:red'>✘ Failed to write to $localPath. (Check permissions)</p>";
        }
    }
}

// 1. Clear OPCache
if (function_exists('opcache_reset')) { opcache_reset(); }
if (function_exists('apcu_clear_cache')) { apcu_clear_cache(); }

echo "<h3>Sync Complete.</h3>";
echo "<p>Please hard refresh the page (Ctrl + F5 or Cmd + Shift + R) to verify on the live server.</p>";
?>
