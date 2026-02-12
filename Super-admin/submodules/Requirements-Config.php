<?php
session_start();
require_once '../../auth/Security.php';
checkRole(['superadmin']);
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

    <style>
        .page-header-premium {
            background: linear-gradient(135deg, var(--accent-color) 0%, #4f46e5 100%);
            padding: 40px;
            border-radius: 30px;
            color: white;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.2);
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .header-bg-accent {
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            filter: blur(80px);
        }

        .requirement-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 25px;
        }

        .requirement-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 25px;
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .requirement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
            border-color: var(--accent-color);
        }

        .req-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .req-icon-box {
            width: 55px;
            height: 55px;
            background: var(--hover-bg);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: var(--accent-color);
            transition: 0.3s;
        }

        .requirement-card:hover .req-icon-box {
            background: var(--accent-color);
            color: white;
            transform: scale(1.1);
        }

        .req-details h4 {
            font-size: 1rem;
            font-weight: 750;
            color: var(--text-color);
            margin-bottom: 4px;
        }

        .req-details p {
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        /* Modern Toggle Switch */
        .switch-premium {
            position: relative;
            display: inline-block;
            width: 54px;
            height: 30px;
        }

        .switch-premium input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider-premium {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--border-color);
            transition: .4s;
            border-radius: 34px;
        }

        .slider-premium:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        input:checked+.slider-premium {
            background-color: var(--accent-color);
        }

        input:checked+.slider-premium:before {
            transform: translateX(24px);
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="page-header-premium">
                <div class="header-bg-accent"></div>
                <div class="header-content">
                    <h1 style="font-weight: 850; font-size: 2.2rem; letter-spacing: -1.5px; margin-bottom: 10px;">Requirements Config</h1>
                    <p style="opacity: 0.9; font-weight: 500;">Manage the documents required for student admission and enrollment.</p>
                </div>
            </div>

            <div class="action-bar">
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-color);">Current Document List</h2>
                <button class="btn-premium">
                    <i class="fas fa-plus-circle"></i>
                    Add New Requirement
                </button>
            </div>

            <div class="requirement-grid">
                <!-- Requirement 1 -->
                <div class="requirement-card">
                    <div class="req-info">
                        <div class="req-icon-box">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="req-details">
                            <h4>PSA Birth Certificate</h4>
                            <p>Original copy required for identity verification.</p>
                        </div>
                    </div>
                    <label class="switch-premium">
                        <input type="checkbox" checked>
                        <span class="slider-premium"></span>
                    </label>
                </div>

                <!-- Requirement 2 -->
                <div class="requirement-card">
                    <div class="req-info">
                        <div class="req-icon-box">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="req-details">
                            <h4>Form 138</h4>
                            <p>Grade 12 Report Card for academic evaluation.</p>
                        </div>
                    </div>
                    <label class="switch-premium">
                        <input type="checkbox" checked>
                        <span class="slider-premium"></span>
                    </label>
                </div>

                <!-- Requirement 3 -->
                <div class="requirement-card">
                    <div class="req-info">
                        <div class="req-icon-box">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="req-details">
                            <h4>Good Moral Certificate</h4>
                            <p>Validation of student conduct from previous school.</p>
                        </div>
                    </div>
                    <label class="switch-premium">
                        <input type="checkbox" checked>
                        <span class="slider-premium"></span>
                    </label>
                </div>

                <!-- Requirement 4 -->
                <div class="requirement-card">
                    <div class="req-info">
                        <div class="req-icon-box">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="req-details">
                            <h4>Medical Certificate</h4>
                            <p>Health clearance for campus safety.</p>
                        </div>
                    </div>
                    <label class="switch-premium">
                        <input type="checkbox">
                        <span class="slider-premium"></span>
                    </label>
                </div>

                <!-- Requirement 5 -->
                <div class="requirement-card">
                    <div class="req-info">
                        <div class="req-icon-box">
                            <i class="fas fa-image"></i>
                        </div>
                        <div class="req-details">
                            <h4>Passport Size Photos</h4>
                            <p>4 mandatory copies with white background.</p>
                        </div>
                    </div>
                    <label class="switch-premium">
                        <input type="checkbox" checked>
                        <span class="slider-premium"></span>
                    </label>
                </div>
            </div>

            <div style="margin-top: 50px; text-align: center; padding: 40px; background: var(--hover-bg); border-radius: 30px; border: 2px dashed var(--border-color);">
                <i class="fas fa-info-circle" style="color: var(--accent-color); font-size: 1.5rem; margin-bottom: 15px;"></i>
                <p style="color: var(--text-muted); font-weight: 500; font-size: 0.95rem;">Changes to these requirements will take effect immediately for all new applicants.</p>
            </div>
        </div>
    </div>
</body>

</html>

