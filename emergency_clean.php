<?php
// Emergency Fix: Clean all PHP files on the server from the stray CSS text
$strayText = ".student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }";

function cleanDir($dir, $strayText) {
    if (!is_dir($dir)) return;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            cleanDir($path, $strayText);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $content = file_get_contents($path);
            if (strpos($content, $strayText) !== false) {
                // Check if it's outside of a style tag
                // If we find it at the beginning of the file or right after body, it's definitely stray
                if (strpos($content, "<body>\n" . $strayText) !== false || 
                    strpos($content, "<body>" . $strayText) !== false ||
                    strpos($content, "<?php\n" . $strayText) !== false) {
                    
                    echo "CLEANING: $path\n";
                    $newContent = str_replace($strayText, "", $content);
                    file_put_contents($path, $newContent);
                }
            }
        }
    }
}

echo "STARTING CLEANUP...\n";
cleanDir(__DIR__, $strayText);
echo "CLEANUP FINISHED.\n";
?>
