<?php
/**
 * Ultimate OPCache & File Cleaner for Live Server
 * Place this in the root of your project and run it via browser:
 * https://ems.jampzdev.com/clean_live.php
 */

echo "<h1>Ultimate OPCache & File Cleaner</h1>";

// 1. Clear OPCache
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "<p style='color:green'>✔ OPCache successfully reset.</p>";
    } else {
        echo "<p style='color:red'>✘ Failed to reset OPCache. (Check opcache.restrict_api)</p>";
    }
} else {
    echo "<p style='color:orange'>⚠ OPCache is not enabled on this server.</p>";
}

// 2. Clear APCu
if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo "<p style='color:green'>✔ APCu Cache successfully cleared.</p>";
}

// 3. Scan and Clean Files
$strayString = '.student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }';

function cleanDirectory($dir, $strayString) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getRealPath();
            $content = file_get_contents($path);
            
            // Check for the stray string outside of PHP tags
            if (strpos($content, $strayString) !== false) {
                // Remove all occurrences of the exact stray string
                $newContent = str_replace($strayString, '', $content);
                
                // Write the cleaned content back
                if (file_put_contents($path, $newContent)) {
                    echo "<p style='color:green'>✔ Cleaned stray string from: " . htmlspecialchars($path) . "</p>";
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
    __DIR__ . '/auth'
];

foreach ($directoriesToClean as $dir) {
    if (is_dir($dir)) {
        cleanDirectory($dir, $strayString);
    }
}

echo "<h3>Action Complete.</h3>";
echo "<p>Please hard refresh the page (Ctrl + F5 or Cmd + Shift + R) to verify.</p>";
?>
