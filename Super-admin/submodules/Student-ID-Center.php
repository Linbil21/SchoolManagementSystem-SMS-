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
    <title>Student ID Center - Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/super-admin.css">
    <style>
        .id-card-preview {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            width: 420px;
            height: 240px;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(226, 232, 240, 0.8);
            position: relative;
            padding: 30px;
            display: flex;
            gap: 25px;
            align-items: center;
            overflow: hidden;
        }

        .id-card-preview::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, rgba(22, 72, 188, 0.03) 0%, rgba(59, 130, 246, 0.05) 100%);
            border-radius: 50%;
            z-index: 0;
        }

        .photo-container {
            width: 130px;
            height: 130px;
            background: #f1f5f9;
            border-radius: 20px;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            z-index: 1;
            flex-shrink: 0;
        }

        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .id-details {
            z-index: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 30px; letter-spacing: -1px; color: #1e293b;">Student ID Center</h1>
            
            <div style="display: flex; flex-direction: column; gap: 40px;">
                <div class="preview-card-container">
                    <h3 style="margin-bottom: 20px; font-weight: 700; color: #64748b; font-size: 1.1rem; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-eye" style="color: #3b82f6;"></i> ID Template Preview
                    </h3>
                    
                    <div class="id-card-preview">
                        <div class="photo-container">
                            <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?q=80&w=256&h=256&auto=format&fit=crop" alt="Student Portrait">
                        </div>
                        <div class="id-details">
                            <h2 style="font-weight: 800; color: #1e293b; margin: 0; font-size: 1.4rem; letter-spacing: -0.5px;">JUAN DELA CRUZ</h2>
                            <p style="font-size: 0.9rem; color: #3b82f6; font-weight: 700; margin: 4px 0 0 0;">STUDENT ID: 2024-0001</p>
                            <p style="font-size: 0.85rem; color: #64748b; margin: 2px 0 15px 0; font-weight: 500;">BS Computer Science</p>
                            
                            <div style="background: white; padding: 6px; border-radius: 12px; display: inline-block; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=2024-0001" alt="QR Code" style="display: block;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

