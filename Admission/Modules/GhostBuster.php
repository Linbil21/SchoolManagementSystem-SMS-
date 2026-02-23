<?php
/**
 * GHOST BUSTER v2 - Ultimate Nuke for Stray CSS
 * This script will scan EVERY file in the project and remove the problematic CSS text using complex regex.
 */
require_once __DIR__ . '/../../auth/Security.php';
checkRole(['admission']);

$base_dir = realpath(__DIR__ . '/../../');
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_dir));
$cleaned_files = [];

// The complex regex to catch variations in whitespace/formatting
$target_regex = '/\.student-modal-footer\s*\{\s*padding:\s*20px\s*32px;\s*border-top:\s*1px\s*solid\s*#edf2f7;\s*display:\s*flex;\s*justify-content:\s*flex-end;\s*background:\s*#f8fafc;\s*\}\s*/is';

foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    if (!in_array($ext, ['php', 'html', 'css', 'js'])) continue;

    $path = $file->getPathname();
    if (basename($path) === 'GhostBuster.php') continue;

    $content = file_get_contents($path);
    $original_content = $content;

    // 1. Nuke the stray CSS using Regex
    $content = preg_replace($target_regex, '', $content);
    
    // 2. Fix the inconsistent comment found in screenshot
    $content = str_replace('<!-- Premium View Modal -->', '<!-- View Modal -->', $content);

    if ($content !== $original_content) {
        file_put_contents($path, $content);
        $cleaned_files[] = str_replace($base_dir, '', $path);
    }
}

// Aggressive Cache Purge
if (function_exists('opcache_reset')) opcache_reset();
if (function_exists('apcu_clear_cache')) apcu_clear_cache();
clearstatcache(true);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Ghost Buster Clean-up</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background:#0f172a; color:white; font-family:sans-serif; padding:40px; text-align:center;">
    <script>
        Swal.fire({
            title: 'Nuke Complete!',
            html: 'Cleaned <?php echo count($cleaned_files); ?> files.<br><br><b>Items Fixed:</b><br><div style="text-align:left; font-size:0.8rem; height:150px; overflow:auto; background:#f1f5f9; padding:10px; border-radius:10px; margin-top:10px; color:#333;"><?php echo implode("<br>", $cleaned_files); ?></div>',
            icon: 'success',
            confirmButtonText: 'Hard Refresh Page Now'
        }).then(() => {
            window.location.href = 'New-Applications.php';
        });
    </script>
</body>
</html>
