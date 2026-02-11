<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Verification - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1648bc;
            --bg-light: #f7fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--bg-light);
            display: flex;
            min-height: 100vh;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .content-area {
            padding: 30px;
        }

        .module-header {
            margin-bottom: 25px;
        }

        .verification-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        .scan-icon {
            font-size: 4rem;
            color: #1648bc;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>ID Verification</h1>
                <p>Scan or enter ID to verify student status.</p>
            </div>
            
            <div class="verification-box">
                <i class="fas fa-qrcode scan-icon"></i>
                <h3>Scan QR Code or ID</h3>
                <p style="color: #64748b; margin: 15px 0;">Place scanner over the student ID</p>
                <input type="text" placeholder="Or enter Student ID manually" style="padding: 10px; width: 80%; border: 1px solid #cbd5e1; border-radius: 8px;">
            </div>
        </div>
    </div>
</body>
</html>
