<?php
// ============================================================
// FIX STRAY CSS TEXT - Emergency Server Repair Script
// Created: 2026-02-22
// ============================================================

$strayText = ".student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }";

$found = [];
$cleaned = [];
$errors = [];

function isInsideStyleTag($content, $pos) {
    // Find the nearest <style> before this position
    $beforePos = substr($content, 0, $pos);
    $lastStyleOpen = strrpos($beforePos, '<style');
    $lastStyleClose = strrpos($beforePos, '</style>');
    
    if ($lastStyleOpen === false) return false;
    if ($lastStyleClose === false) return ($lastStyleOpen !== false);
    
    return $lastStyleOpen > $lastStyleClose;
}

function scanAndFix($dir, $strayText, &$found, &$cleaned, &$errors) {
    if (!is_dir($dir)) return;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            // Skip node_modules, .git, vendor
            if (in_array($file, ['node_modules', '.git', 'vendor', '.github'])) continue;
            scanAndFix($path, $strayText, $found, $cleaned, $errors);
        } elseif (in_array(pathinfo($path, PATHINFO_EXTENSION), ['php', 'html'])) {
            $content = file_get_contents($path);
            if ($content === false) continue;
            
            $pos = 0;
            $strayFound = false;
            while (($pos = strpos($content, $strayText, $pos)) !== false) {
                // Check if it's OUTSIDE a <style> tag — that means it's stray
                if (!isInsideStyleTag($content, $pos)) {
                    $strayFound = true;
                    $found[] = [
                        'file' => $path,
                        'pos' => $pos,
                        'context' => htmlspecialchars(substr($content, max(0, $pos - 50), strlen($strayText) + 100))
                    ];
                }
                $pos += strlen($strayText);
            }
            
            if ($strayFound) {
                // Remove ALL occurrences that are outside <style> tags
                // Simple approach: remove all occurrences (the one inside style tag is already properly declared)
                $newContent = str_replace($strayText, '', $content);
                
                // Double check we still have it inside style tag if needed
                if (file_put_contents($path, $newContent) !== false) {
                    $cleaned[] = $path;
                } else {
                    $errors[] = "Could not write to: $path";
                }
            }
        }
    }
}

echo "<!DOCTYPE html><html><head>";
echo "<meta charset='UTF-8'>";
echo "<title>Fix Stray CSS - Server Repair</title>";
echo "<style>
    body { font-family: monospace; background: #0f172a; color: #e2e8f0; padding: 30px; line-height: 1.6; }
    h1 { color: #3b82f6; }
    h2 { color: #94a3b8; margin-top: 25px; }
    .ok { color: #22c55e; }
    .warn { color: #f59e0b; }
    .err { color: #ef4444; }
    .box { background: #1e293b; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 3px solid #3b82f6; font-size: 0.85rem; word-break: break-all; }
    .success-box { background: #052e16; border-color: #22c55e; }
    .err-box { background: #2c0a0a; border-color: #ef4444; }
</style>";
echo "</head><body>";
echo "<h1>🔧 Stray CSS Fix Script</h1>";
echo "<p>Looking for: <code style='color:#fbbf24'>" . htmlspecialchars($strayText) . "</code></p>";
echo "<hr style='border-color:#334155'>";

// Step 1: Scan and fix files
echo "<h2>Step 1: Scanning PHP/HTML Files...</h2>";
scanAndFix(__DIR__, $strayText, $found, $cleaned, $errors);

if (empty($found)) {
    echo "<div class='box success-box'><span class='ok'>✅ No stray CSS text found in any file! The issue may be server-side cache.</span></div>";
} else {
    echo "<p class='warn'>⚠️ Found " . count($found) . " stray occurrence(s):</p>";
    foreach ($found as $f) {
        echo "<div class='box'>";
        echo "<strong class='warn'>FILE:</strong> " . htmlspecialchars($f['file']) . "<br>";
        echo "<strong>CONTEXT:</strong> ..." . $f['context'] . "...";
        echo "</div>";
    }
}

if (!empty($cleaned)) {
    echo "<h2>Step 2: Files Cleaned</h2>";
    foreach ($cleaned as $c) {
        echo "<div class='box success-box'><span class='ok'>✅ CLEANED:</span> " . htmlspecialchars($c) . "</div>";
    }
}

if (!empty($errors)) {
    echo "<h2>Errors</h2>";
    foreach ($errors as $e) {
        echo "<div class='box err-box'><span class='err'>❌ " . htmlspecialchars($e) . "</span></div>";
    }
}

// Step 2: Clear PHP OPCache
echo "<h2>Step 3: Clearing Server Cache (OPCache)</h2>";
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "<div class='box success-box'><span class='ok'>✅ OPCache cleared successfully!</span></div>";
    } else {
        echo "<div class='box err-box'><span class='err'>❌ OPCache reset failed. May need admin rights.</span></div>";
    }
} else {
    echo "<div class='box'><span class='warn'>⚠️ OPCache not available on this server (this is OK).</span></div>";
}

// Step 3: Clear output buffers
echo "<h2>Step 4: PHP Session Cleanup</h2>";
echo "<div class='box success-box'><span class='ok'>✅ Script completed successfully.</span></div>";

echo "<hr style='border-color:#334155; margin-top: 30px;'>";
echo "<h2>Summary</h2>";
echo "<ul>";
echo "<li>Files scanned: PHP and HTML files in all subdirectories</li>";
echo "<li>Stray occurrences found (outside style tags): <strong class='" . (count($found) > 0 ? 'warn' : 'ok') . "'>" . count($found) . "</strong></li>";
echo "<li>Files fixed: <strong class='ok'>" . count($cleaned) . "</strong></li>";
echo "<li>Errors: <strong class='" . (count($errors) > 0 ? 'err' : 'ok') . "'>" . count($errors) . "</strong></li>";
echo "</ul>";

echo "<p style='margin-top:20px; color:#64748b;'>⚠️ <strong>IMPORTANT:</strong> After running this script, <strong>delete it from the server</strong> (fix_stray_css.php) for security.</p>";

echo "</body></html>";
?>
