<?php
/**
 * NUCLEAR FIX - student-modal-footer stray text removal
 * 
 * Run ONCE at: https://ems.jampzdev.com/nuke_fix.php
 * DELETE THIS FILE AFTER!
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head><title>NUCLEAR FIX</title>
<style>
body { font-family: monospace; background: #0f172a; color: #e2e8f0; padding: 40px; line-height: 1.8; }
.ok { color: #22c55e; font-weight: bold; }
.bad { color: #ef4444; font-weight: bold; }
.warn { color: #f59e0b; }
.info { color: #3b82f6; }
h1 { color: #60a5fa; border-bottom: 2px solid #1e40af; padding-bottom: 10px; }
h2 { color: #93c5fd; margin-top: 30px; }
pre { background: #1e293b; padding: 15px; border-radius: 8px; overflow-x: auto; border: 1px solid #334155; }
</style>
</head>
<body>
<h1>🔥 NUCLEAR FIX - student-modal-footer</h1>
<p>Time: <?php echo date('Y-m-d H:i:s T'); ?></p>

<?php
$base = __DIR__;
$totalFixed = 0;
$totalScanned = 0;
$fixLog = [];

// The stray text pattern - matches the CSS rule when it appears OUTSIDE of <style> tags
$strayPatterns = [
    '.student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }',
    '.student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content:flex-end; background: #f8fafc; }',
    '.student-modal-footer {  padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc;  }',
];

echo "<h2>📋 STEP 1: Scanning ALL PHP files for stray text</h2>";
echo "<pre>";

// Recursively scan ALL PHP files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    if ($file->getExtension() !== 'php') continue;
    
    // Skip this fix script and other debug scripts
    $filename = $file->getBasename();
    if (in_array($filename, ['nuke_fix.php', 'force_fix.php', 'diag.php', 'emergency_clean.php', 'fix_now.php', 'clear_cache.php', 'diag_file.php', 'deploy_new_apps.php'])) continue;
    
    $totalScanned++;
    $filepath = $file->getPathname();
    $relPath = str_replace($base, '', $filepath);
    $content = file_get_contents($filepath);
    
    $hasStray = false;
    
    // Method 1: Check for literal stray patterns
    foreach ($strayPatterns as $pattern) {
        if (strpos($content, $pattern) !== false) {
            // Check if it's inside a <style> tag - that's OK
            // Find the position
            $pos = strpos($content, $pattern);
            $before = substr($content, 0, $pos);
            $lastStyleOpen = strrpos($before, '<style');
            $lastStyleClose = strrpos($before, '</style>');
            
            // If the last <style> open is AFTER the last </style> close, we're inside a style tag = OK
            if ($lastStyleOpen !== false && ($lastStyleClose === false || $lastStyleOpen > $lastStyleClose)) {
                // Inside <style> tag, this is fine
                continue;
            }
            
            // It's stray text! Remove it
            $content = str_replace($pattern, '', $content);
            $hasStray = true;
        }
    }
    
    // Method 2: Regex - find .student-modal-footer CSS outside of <style> tags
    // Look for it after ?> (PHP closing tag) and before any < (HTML tag)
    if (preg_match('/\?>[\s]*\.student-modal-footer\s*\{[^}]+\}/s', $content)) {
        $content = preg_replace('/(\?>[\s]*)\.student-modal-footer\s*\{[^}]+\}/s', '$1', $content);
        $hasStray = true;
    }
    
    // Method 3: Look for it as standalone text (not inside any tag)
    if (preg_match('/^\.student-modal-footer\s*\{[^}]+\}/m', $content)) {
        // Check context
        $lines = explode("\n", $content);
        $newLines = [];
        $inStyle = false;
        foreach ($lines as $line) {
            if (preg_match('/<style/i', $line)) $inStyle = true;
            if (preg_match('/<\/style>/i', $line)) $inStyle = false;
            
            if (!$inStyle && preg_match('/^\s*\.student-modal-footer\s*\{/', $line)) {
                $hasStray = true;
                continue; // Skip this line
            }
            $newLines[] = $line;
        }
        if ($hasStray) {
            $content = implode("\n", $newLines);
        }
    }
    
    if ($hasStray) {
        // Clean up any resulting double blank lines
        $content = preg_replace("/\n{3,}/", "\n\n", $content);
        
        file_put_contents($filepath, $content);
        $totalFixed++;
        $fixLog[] = $relPath;
        echo "<span class='bad'>FIXED:</span> $relPath\n";
    }
}

if ($totalFixed === 0) {
    echo "<span class='ok'>✅ No stray text found in any PHP files!</span>\n";
} else {
    echo "\n<span class='warn'>Fixed $totalFixed file(s)</span>\n";
}
echo "Scanned: $totalScanned files\n";
echo "</pre>";

// STEP 2: Verify New-Applications.php specifically
echo "<h2>🔍 STEP 2: Verifying New-Applications.php</h2>";
echo "<pre>";

$newAppsFile = $base . '/Admission/Modules/New-Applications.php';
if (file_exists($newAppsFile)) {
    $content = file_get_contents($newAppsFile);
    $size = strlen($content);
    $lines = count(explode("\n", $content));
    
    echo "File exists: <span class='ok'>YES</span>\n";
    echo "Size: $size bytes\n";
    echo "Lines: $lines\n";
    
    // Check for stray
    if (strpos($content, 'student-modal-footer') !== false) {
        echo "Contains 'student-modal-footer': <span class='bad'>YES</span>\n";
        
        // Find where
        $pos = strpos($content, 'student-modal-footer');
        $start = max(0, $pos - 50);
        $snippet = substr($content, $start, 150);
        echo "Context: " . htmlspecialchars($snippet) . "\n";
    } else {
        echo "Contains 'student-modal-footer': <span class='ok'>NO (CLEAN!)</span>\n";
    }
    
    // Show first 5 lines
    $firstLines = array_slice(explode("\n", $content), 0, 5);
    echo "\nFirst 5 lines:\n";
    foreach ($firstLines as $i => $line) {
        echo ($i+1) . ": " . htmlspecialchars(trim($line)) . "\n";
    }
    
    // Check if the file starts correctly
    if (strpos(trim($content), '<?php') === 0) {
        echo "\n<span class='ok'>✅ File starts with &lt;?php correctly</span>\n";
    } else {
        echo "\n<span class='bad'>❌ File does NOT start with &lt;?php!</span>\n";
        echo "First 100 chars: " . htmlspecialchars(substr($content, 0, 100)) . "\n";
        
        // Try to fix by removing anything before <?php
        $phpStart = strpos($content, '<?php');
        if ($phpStart !== false && $phpStart > 0) {
            $garbage = substr($content, 0, $phpStart);
            echo "Garbage before &lt;?php: " . htmlspecialchars($garbage) . "\n";
            $content = substr($content, $phpStart);
            file_put_contents($newAppsFile, $content);
            echo "<span class='ok'>FIXED: Removed " . strlen($garbage) . " bytes of garbage</span>\n";
        }
    }
} else {
    echo "<span class='bad'>File does not exist!</span>\n";
}
echo "</pre>";

// STEP 3: Check ALL included files
echo "<h2>📦 STEP 3: Checking included components</h2>";
echo "<pre>";

$includes = [
    'Admission/Components/Sidebar.php',
    'Admission/Components/header.php',
    'Admission/Components/GlobalScripts.php',
    'Assets/css/theme.css',
];

foreach ($includes as $inc) {
    $path = $base . '/' . $inc;
    if (file_exists($path)) {
        $c = file_get_contents($path);
        $hasIt = strpos($c, 'student-modal-footer') !== false;
        
        if ($hasIt) {
            // Is it inside a <style> tag or CSS file?
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if ($ext === 'css') {
                echo "<span class='info'>$inc</span> - contains in CSS (OK)\n";
            } else {
                // Check if inside <style>
                $pos = strpos($c, 'student-modal-footer');
                $before = substr($c, 0, $pos);
                $lastStyleOpen = strrpos($before, '<style');
                $lastStyleClose = strrpos($before, '</style>');
                
                if ($lastStyleOpen !== false && ($lastStyleClose === false || $lastStyleOpen > $lastStyleClose)) {
                    echo "<span class='info'>$inc</span> - inside &lt;style&gt; tag (OK)\n";
                } else {
                    echo "<span class='bad'>$inc</span> - STRAY TEXT FOUND!\n";
                }
            }
        } else {
            echo "<span class='ok'>$inc</span> - clean ✅\n";
        }
    } else {
        echo "<span class='warn'>$inc</span> - file not found\n";
    }
}
echo "</pre>";

// STEP 4: Clear ALL caches
echo "<h2>🧹 STEP 4: Clearing ALL caches</h2>";
echo "<pre>";

// OPCache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "<span class='ok'>✅ OPCache reset!</span>\n";
} else {
    echo "<span class='warn'>⚠ OPCache not available</span>\n";
}

if (function_exists('opcache_invalidate')) {
    // Invalidate specific files
    $filesToInvalidate = [
        $base . '/Admission/Modules/New-Applications.php',
        $base . '/Admission/Components/Sidebar.php',
        $base . '/Admission/Components/header.php',
        $base . '/Admission/Components/GlobalScripts.php',
    ];
    foreach ($filesToInvalidate as $f) {
        if (file_exists($f)) {
            opcache_invalidate($f, true);
            echo "Invalidated: " . str_replace($base, '', $f) . "\n";
        }
    }
}

// Clear any PHP file caches
if (function_exists('clearstatcache')) {
    clearstatcache(true);
    echo "<span class='ok'>✅ Stat cache cleared!</span>\n";
}

// APC cache
if (function_exists('apc_clear_cache')) {
    apc_clear_cache();
    apc_clear_cache('user');
    echo "<span class='ok'>✅ APC cache cleared!</span>\n";
}

// Realpath cache
if (function_exists('realpath_cache_size')) {
    echo "Realpath cache size: " . realpath_cache_size() . " bytes\n";
}

echo "</pre>";

// STEP 5: Final verification - actually read the file fresh
echo "<h2>✅ STEP 5: Final Verification</h2>";
echo "<pre>";

clearstatcache(true);
$finalContent = file_get_contents($base . '/Admission/Modules/New-Applications.php');

if (strpos($finalContent, '.student-modal-footer {') !== false) {
    // Double check if it's inside a style tag
    $pos = strpos($finalContent, '.student-modal-footer {');
    $before = substr($finalContent, 0, $pos);
    $lastStyleOpen = strrpos($before, '<style');
    $lastStyleClose = strrpos($before, '</style>');
    
    if ($lastStyleOpen !== false && ($lastStyleClose === false || $lastStyleOpen > $lastStyleClose)) {
        echo "<span class='ok'>✅ .student-modal-footer found INSIDE &lt;style&gt; tag only - THIS IS CORRECT</span>\n";
    } else {
        echo "<span class='bad'>❌ STRAY .student-modal-footer STILL EXISTS!</span>\n";
        echo "Position: $pos\n";
        echo "Context: " . htmlspecialchars(substr($finalContent, max(0, $pos-80), 200)) . "\n";
    }
} else {
    echo "<span class='ok'>✅ No .student-modal-footer found at all - COMPLETELY CLEAN!</span>\n";
}

echo "\nFile MD5: " . md5($finalContent) . "\n";
echo "File size: " . strlen($finalContent) . " bytes\n";
echo "Last modified: " . date('Y-m-d H:i:s', filemtime($base . '/Admission/Modules/New-Applications.php')) . "\n";

echo "</pre>";

echo "<h2>🎯 What to do next</h2>";
echo "<ol style='line-height: 2.5;'>";
echo "<li>Hard reload the New Applications page: <strong>Ctrl+Shift+R</strong></li>";
echo "<li>If still showing stray text, try <strong>Incognito/Private window</strong></li>";
echo "<li>If STILL showing, your hosting provider might have a <strong>server-side page cache/CDN</strong> — contact them to purge it</li>";
echo "<li><strong style='color:#ef4444;'>DELETE this file (nuke_fix.php) when done!</strong></li>";
echo "</ol>";
?>
</body>
</html>
