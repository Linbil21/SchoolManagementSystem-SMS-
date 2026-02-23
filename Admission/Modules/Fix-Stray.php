<?php
/**
 * STRAY CSS AUTO-FIX TOOL
 * This script will find and remove the troublesome CSS rule from any PHP file.
 */
header('Content-Type: text/plain');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$target_rule = ".student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }";
$base_dir = realpath(__DIR__ . "/../../"); 

echo "Starting Deep Scan in: $base_dir\n";

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base_dir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$fixed_count = 0;
$scanned_count = 0;

foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    if ($file->getExtension() !== 'php') continue;
    
    $filename = $file->getBasename();
    if ($filename === 'Fix-Stray.php' || $filename === 'emergency_scan.php' || $filename === 'Nuke-Stray.php') continue;


    $scanned_count++;
    $path = $file->getPathname();
    $content = file_get_contents($path);
    
    // Check for target rule (even with varying whitespace)
    if (strpos($content, ".student-modal-footer") !== false) {
        $modified = false;
        
        // Literal match
        if (strpos($content, $target_rule) !== false) {
            $content = str_replace($target_rule, "", $content);
            $modified = true;
        }
        
        // Regex match (looser)
        $pattern = '/\.student-modal-footer\s*\{\s*padding:\s*20px\s*32px;\s*border-top:\s*1px\s*solid\s*#edf2f7;\s*display:\s*flex;\s*justify-content:\s*flex-end;\s*background:\s*#f8fafc;\s*\}\s*/is';
        
        // We only want to remove it if it's appearing as PLAIN TEXT (not inside <style> tags)
        // However, to be safe and "nuke" it, we can just remove all occurrences and let the valid ones in Student-Requirements remain IF they are correctly formatted.
        // Actually, the user wants it GONE. 
        
        if (preg_match($pattern, $content)) {
            // Context check: is it inside <style>?
            $temp_content = $content;
            preg_match_all($pattern, $temp_content, $matches, PREG_OFFSET_CAPTURE);
            foreach (array_reverse($matches[0]) as $match) {
                $offset = $match[1];
                $string = $match[0];
                $before = substr($temp_content, 0, $offset);
                $lastOpen = strrpos($before, '<style');
                $lastClose = strrpos($before, '</style>');
                
                if ($lastOpen === false || ($lastClose !== false && $lastClose > $lastOpen)) {
                    $temp_content = substr_replace($temp_content, '', $offset, strlen($string));
                    $modified = true;
                }
            }
            $content = $temp_content;
        }
        
        if ($modified) {
            file_put_contents($path, $content);
            echo "FIXED: " . str_replace($base_dir, "", $path) . "\n";
            $fixed_count++;
        }
    }
}

echo "\nScan complete.\n";
echo "Scanned: $scanned_count files.\n";
echo "Fixed: $fixed_count files.\n";

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPCache has been reset.\n";
}
?>
