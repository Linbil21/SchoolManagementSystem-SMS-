<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../../auth/Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel='icon' type='image/png' href='../../Assets/image/logo.png'>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requirements Config - Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/super-admin.css">
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 30px;">Requirements Config</h1>
            <div class="config-card">
                <div class="req-item">
                    <span>PSA Birth Certificate (Original)</span>
                    <input type="checkbox" checked>
                </div>
                <div class="req-item">
                    <span>Form 138 (Grade 12 Report Card)</span>
                    <input type="checkbox" checked>
                </div>
                <div class="req-item">
                    <span>Good Moral Certificate</span>
                    <input type="checkbox" checked>
                </div>
                <div class="req-item">
                    <span>Medical Certificate</span>
                    <input type="checkbox">
                </div>
            </div>
        </div>
    </div>
</body>

</html>

