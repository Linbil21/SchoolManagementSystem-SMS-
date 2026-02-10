<?php
/**
 * Database Auto-Fixer & Environment Tester
 * Run this file to see what information the server is getting.
 */

echo "<h1>Environment Debugger</h1>";
echo "<p><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "</p>";
echo "<p><strong>SERVER_NAME:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'Not set') . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h2 style='color:green'>SUCCESS: Connected to the database!</h2>";
    
    // Check for tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "<p style='color:orange'>Warning: Database connected but no tables found. Would you like to import setup.sql?</p>";
        echo "<form method='POST'><button name='run_setup' style='padding:10px; background:blue; color:white;'>Run setup.sql Now</button></form>";
    } else {
        echo "<p>Found " . count($tables) . " tables: " . implode(', ', $tables) . "</p>";
    }
    
    if (isset($_POST['run_setup'])) {
        $sql = file_get_contents('setup.sql');
        // Remove 'USE sms;' if it exists as it might conflict with prefixed names
        $sql = preg_replace('/^USE\s+sms;/i', '', $sql);
        $pdo->exec($sql);
        echo "<h3 style='color:green'>SUCCESS: setup.sql executed!</h3>";
    }

} catch (PDOException $e) {
    echo "<h2 style='color:red'>FAILURE: Could not connect.</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<div style='background: #eee; padding: 10px; border: 1px solid #ccc;'>";
    echo "Current Config in config.php:<br>";
    echo "Host: " . DB_HOST . "<br>";
    echo "User: " . DB_USER . "<br>";
    echo "DB Name: " . DB_NAME . "<br>";
    echo "Password: " . (empty(DB_PASS) ? '(Empty)' : '(Hidden)') . "<br>";
    echo "</div>";
}
?>
