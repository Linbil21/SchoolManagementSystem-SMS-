<?php
// Cache Buster - Run once on the server to clear OPCache
header('Content-Type: text/plain');

$files = [
    __DIR__ . '/Admission/Modules/New-Applications.php',
    __DIR__ . '/Admission/Modules/Student-Requirements.php',
    __DIR__ . '/Admission/Components/Sidebar.php',
    __DIR__ . '/Admission/Components/header.php',
    __DIR__ . '/Admission/Components/GlobalScripts.php',
];

echo "=== OPCache Clear Tool ===\n\n";

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPCache FULL RESET done.\n\n";
} else {
    echo "⚠️ opcache_reset() not available.\n\n";
}

foreach ($files as $file) {
    if (file_exists($file)) {
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($file, true);
            echo "✅ Invalidated: " . basename($file) . "\n";
        }
        // Touch the file to update its mtime
        touch($file);
        echo "✅ Touched: " . basename($file) . "\n";
    } else {
        echo "❌ Not found: $file\n";
    }
}

echo "\n✅ Done! Please hard-reload (Ctrl+Shift+R) the page now.\n";
echo "\n⚠️ DELETE this file after use for security!\n";
