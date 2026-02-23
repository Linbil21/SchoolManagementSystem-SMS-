<?php
$file = 'Admission/Modules/New-Applications.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    echo "FILE FOUND: $file\n";
    // Search for the stray text
    if (strpos($content, '.student-modal-footer') !== false) {
        echo "STRAY TEXT FOUND IN FILE!\n";
        // Show context
        $pos = strpos($content, '.student-modal-footer');
        echo "CONTEXT: " . substr($content, $pos, 200) . "\n";
    } else {
        echo "STRAY TEXT NOT FOUND IN FILE CONTENT.\n";
    }
} else {
    echo "FILE NOT FOUND: $file\n";
}
?>
