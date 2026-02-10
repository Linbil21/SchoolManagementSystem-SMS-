<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'SMS');

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);

    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set default fetch mode to object
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

} catch (PDOException $e) {
    // Graceful error handling
    die("
        <div style='font-family: sans-serif; text-align: center; padding: 50px;'>
            <h2 style='color: #e11d48;'>Database Connection Error</h2>
            <p>Could not connect to the database.</p>
            <small style='color: gray;'>" . htmlspecialchars($e->getMessage()) . "</small>
            <br><br>
            <a href='Login.php' style='text-decoration: none; background: #0f172a; color: white; padding: 10px 20px; border-radius: 5px;'>Return to Home</a>
        </div>
    ");
}
?>