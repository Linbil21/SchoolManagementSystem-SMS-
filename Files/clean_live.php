<?php
/**
 * Ultimate OPCache & File Cleaner for Live Server (Regex Version)
 * Place this in the root of your project and run it via browser:
 * https://ems.jampzdev.com/clean_live.php
 */

echo "<h1>Ultimate Forcible File Cleaner</h1>";

function cleanDirectory($dir) {
    if (!is_dir($dir)) return;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getRealPath();
            $content = file_get_contents($path);
            
            // Ultra-aggressive regex: Find ANYTHING containing student-modal-footer padding and nuke it entirely!
            // This includes quotes, spaces, newlines, etc.
            $pattern = '/"*\.?student-modal-footer\s*\{\s*padding:\s*20px\s*32px[^}]+\}\s*"*/i';
            
            if (preg_match($pattern, $content)) {
                $newContent = preg_replace($pattern, '', $content);
                
                if (file_put_contents($path, $newContent)) {
                    echo "<p style='color:green'>✔ Aggressively Cleaned stray text from: " . htmlspecialchars($path) . "</p>";
                } else {
                    echo "<p style='color:red'>✘ Failed to write to: " . htmlspecialchars($path) . " (Check permissions)</p>";
                }
            }
        }
    }
}

$directoriesToClean = [
    __DIR__ . '/Admission',
    __DIR__ . '/Components',
];

foreach ($directoriesToClean as $dir) {
    cleanDirectory($dir);
}

// ALSO let's do an exact replacement on the New-Applications.php specifically if the regex doesn't catch it
$newAppFile = __DIR__ . '/Admission/Modules/New-Applications.php';
if (file_exists($newAppFile)) {
    $content = file_get_contents($newAppFile);
    // Find everything between <body> and <!-- Premium View Modal --> or whatever is printing it
    $content = preg_replace('/"\.student-modal-footer\s*\{\s*padding:\s*20px\s*32px;\s*border-top:\s*1px\s*solid\s*#edf2f7;\s*display:\s*flex;\s*justify-content:\s*flex-end;\s*background:\s*#f8fafc;\s*\}\s*"/i', '', $content);
    $content = str_replace('".student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; } "', '', $content);
    file_put_contents($newAppFile, $content);
    echo "<p style='color:blue'>✔ Ran targeted cleanup on New-Applications.php.</p>";
}

// 1. Clear OPCache
if (function_exists('opcache_reset')) { opcache_reset(); }
if (function_exists('apcu_clear_cache')) { apcu_clear_cache(); }

echo "<h3>Action Complete.</h3>";
echo "<p>Please hard refresh the page (Ctrl + F5 or Cmd + Shift + R) to verify on the live server.</p>";
?>
