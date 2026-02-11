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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Center - Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Assets/super-admin.css">
    <style>
        .id-card-preview {
            background: white;
            width: 350px;
            height: 200px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 2px solid #e2e8f0;
            position: relative;
            padding: 20px;
            display: flex;
            gap: 20px;
        }

        .photo-placeholder {
            width: 100px;
            height: 100px;
            background: #f1f5f9;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 30px;">Student ID Center</h1>
            <div style="display: flex; flex-direction: column; gap: 30px;">
                <div>
                    <h3 style="margin-bottom: 15px;">ID Template Preview</h3>
                    <div class="id-card-preview">
                        <div class="photo-placeholder"></div>
                        <div>
                            <p style="font-weight: 800; color: var(--primary);">JUAN DELA CRUZ</p>
                            <p style="font-size: 0.8rem; color: #64748b;">Student ID: 2024-0001</p>
                            <p style="font-size: 0.8rem; color: #64748b;">BS Computer Science</p>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=50x50&data=2024-0001"
                                style="margin-top: 15px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>