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
    // Sinubukan nating gayahin ang settings ng localhost mo:
    define('DB_HOST', 'localhost'); 
    define('DB_USER', 'root'); // Binago ko mula u123456_change_me papuntang root
    define('DB_PASS', '');     // Walang password
    define('DB_NAME', 'SMS');  // Siguraduhin na 'SMS' ang pangalan ng DB sa live
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
            <p style='font-size:0.9rem'><strong>Active Host:</strong> " . htmlspecialchars($_SERVER['HTTP_HOST']) . "</p>
            <small style='color: gray;'>" . htmlspecialchars($e->getMessage()) . "</small>
            <br><br>
            <div style='background: #f1f5f9; padding: 15px; border-radius: 8px; max-width: 600px; margin: 0 auto; text-align: left;'>
                <strong>How to fix:</strong><br>
                Kapag lumabas pa rin ang <i>'Access denied for user root'</i>, ibig sabihin ay hindi pwede ang walang password sa hosting mo. <br><br>
                1. Pumunta sa <b>Hosting Panel</b> (Hostinger/cPanel).<br>
                2. Gumawa ng <b>Database User</b> at <b>Password</b>.<br>
                3. Ilagay ang details na iyon sa <code>/sms/Database/config.php</code> sa bandang dulo.
            </div>
            <br>
            <a href='Login.php' style='text-decoration: none; background: #0f172a; color: white; padding: 10px 20px; border-radius: 5px;'>Return to Home</a>
        </div>
    ");
}
?>