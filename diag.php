<?php
/**
 * DIAGNOSTIC - finds EXACTLY which server file outputs the stray CSS
 * Access: https://ems.jampzdev.com/diag.php
 * DELETE AFTER USE.
 */
header('Content-Type: text/plain; charset=utf-8');
echo "=== STRAY CSS DIAGNOSTIC ===\n\n";

$base = __DIR__;
$needle = 'student-modal-footer';

// Check ALL PHP files in the entire project
$dirs = [
    $base . '/Admission/Components',
    $base . '/Admission/Modules',
    $base . '/Components',
    $base . '/auth',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) { echo "DIR NOT FOUND: $dir\n"; continue; }
    foreach (glob($dir . '/*.php') as $fp) {
        $content = file_get_contents($fp);
        if (strpos($content, $needle) !== false) {
            $short = str_replace($base, '', $fp);
            // Find all occurrences and show context
            $offset = 0;
            while (($pos = strpos($content, $needle, $offset)) !== false) {
                $start = max(0, $pos - 60);
                $end   = min(strlen($content), $pos + 120);
                $ctx   = substr($content, $start, $end - $start);
                $ctx   = str_replace(["\r", "\n"], ['\\r', '\\n'], $ctx);
                echo "FOUND in $short at offset $pos:\n  ...{$ctx}...\n\n";
                $offset = $pos + 1;
            }
        }
    }
}

// Also check the actual rendered output of Sidebar.php
echo "\n--- SIDEBAR OUTPUT BUFFER CHECK ---\n";
ob_start();
@include $base . '/Admission/Components/Sidebar.php';
$sidebarOut = ob_get_clean();
if (strpos($sidebarOut, $needle) !== false) {
    $pos = strpos($sidebarOut, $needle);
    echo "STRAY TEXT FOUND IN SIDEBAR OUTPUT!\n";
    $ctx = substr($sidebarOut, max(0,$pos-80), 250);
    echo "Context: " . str_replace(["\r","\n"],'', $ctx) . "\n";
} else {
    echo "Sidebar output: clean (no stray CSS)\n";
}

// Also check DB config
$cfgFile = $base . '/Database/config.php';
if (file_exists($cfgFile)) {
    $cfg = file_get_contents($cfgFile);
    if (strpos($cfg, $needle) !== false) {
        echo "\nFOUND IN config.php!\n";
    } else {
        echo "config.php: clean\n";
    }
}

// Show first 200 chars of New-Applications.php on server 
$newApps = $base . '/Admission/Modules/New-Applications.php';
echo "\n--- New-Applications.php on SERVER (first 400 chars) ---\n";
echo str_replace(["\r","\n"], ['\\r','\\n'], substr(file_get_contents($newApps), 0, 400)) . "\n";

echo "\n=== DONE ===\n";
?>
