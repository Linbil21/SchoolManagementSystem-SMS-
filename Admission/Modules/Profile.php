<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}
$email = $_SESSION['email'] ?? 'admission@sms.com';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1648bc;
            --bg: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--bg);
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

        .profile-header {
            background: linear-gradient(135deg, var(--primary), #3b82f6);
            height: 200px;
            border-radius: 20px;
            position: relative;
            margin-bottom: 80px;
        }

        .profile-img-container {
            position: absolute;
            bottom: -60px;
            left: 50px;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid white;
            background: #fff;
        }

        .profile-info-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .info-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="profile-header">
                <div class="profile-img-container">
                    <img src="https://ui-avatars.com/api/?name=Admission+Staff&size=120&background=fff&color=1648bc"
                        class="profile-img" alt="Profile">
                </div>
            </div>

            <div class="profile-info-grid">
                <div class="info-card">
                    <h2 style="margin-bottom: 20px;">Personal Information</h2>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <p style="font-size: 0.8rem; color: #64748b;">Full Name</p>
                            <p style="font-weight: 600;">Admission Staff Official</p>
                        </div>
                        <div>
                            <p style="font-size: 0.8rem; color: #64748b;">Email Address</p>
                            <p style="font-weight: 600;">
                                <?php echo $email; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="info-card">
                    <h2 style="margin-bottom: 20px;">Account Settings</h2>
                    <button
                        style="width: 100%; height: 45px; border-radius: 12px; background: var(--primary); color: white; border: none; font-weight: 600; cursor: pointer;">Edit
                        Profile</button>
                    <button
                        style="width: 100%; height: 45px; border-radius: 12px; border: 1px solid #e2e8f0; background: transparent; color: #475569; font-weight: 600; margin-top: 10px; cursor: pointer;">Change
                        Password</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>