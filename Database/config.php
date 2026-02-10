<?php
// Database configuration

// Check if running on localhost (XAMPP) or Live Server
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // Local / XAMPP Credentials
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'SMS');
} else {
    // Live Server Credentials (ems.jampzdev.com)
    // ⚠️ PLEASE EDIT THESE VALUES WITH YOUR LIVE HOSTING DATABASE DETAILS ⚠️
    // You can usually find these in your hosting control panel (cPanel, Hostinger, etc.) under "MySQL Databases"
    
    define('DB_HOST', 'localhost'); // Often 'localhost', but sometimes an IP or URL provided by host
    define('DB_USER', 'u123456_change_me'); // CHANGE THIS to your live database username
    define('DB_PASS', 'change_me_password'); // CHANGE THIS to your live database password
    define('DB_NAME', 'u123456_change_me_db'); // CHANGE THIS to your live database name
}

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
            <p style='font-size:0.9rem'><strong>Host:</strong> " . htmlspecialchars($_SERVER['HTTP_HOST']) . "</p>
            <small style='color: gray;'>" . htmlspecialchars($e->getMessage()) . "</small>
            <br><br>
            <div style='background: #f1f5f9; padding: 15px; border-radius: 8px; max-width: 600px; margin: 0 auto; text-align: left;'>
                <strong>How to fix (for Live Site):</strong><br>
                1. Open <code>/sms/Database/config.php</code> in your file manager.<br>
                2. Edit the <code>else</code> block values (DB_USER, DB_PASS, DB_NAME).<br>
                3. Use the credentials provided by your hosting provider.
            </div>
            <br>
            <a href='Login.php' style='text-decoration: none; background: #0f172a; color: white; padding: 10px 20px; border-radius: 5px;'>Return to Home</a>
        </div>
    ");
}
?>