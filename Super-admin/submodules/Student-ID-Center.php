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
        :root {
            --id-gold: #fbbf24;
            --id-blue: #1e3a8a;
        }

        /* Official Vertical ID Card Style */
        .student-id-card {
            width: 320px;
            height: 500px;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border-radius: 20px;
            box-shadow: 0 20px 40px -5px rgba(30, 58, 138, 0.4);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            color: white;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            margin: 0 auto;
        }

        .student-id-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 30px 60px rgba(30, 58, 138, 0.5);
        }

        .card-bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 100% 0%, rgba(255, 255, 255, 0.1) 20%, transparent 20%),
                radial-gradient(circle at 0% 100%, rgba(255, 255, 255, 0.1) 20%, transparent 20%);
            z-index: 0;
        }

        .id-header {
            padding: 24px 20px;
            text-align: center;
            z-index: 1;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .school-name {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .id-photo-area {
            padding: 30px 0 20px;
            display: flex;
            justify-content: center;
            z-index: 1;
            position: relative;
        }

        .photo-frame {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 4px solid var(--id-gold);
            overflow: hidden;
            background: white;
            padding: 4px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .photo-frame img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .id-details {
            text-align: center;
            padding: 0 20px;
            flex-grow: 1;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .student-name {
            font-size: 1.3rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 4px;
            color: var(--id-gold);
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .student-no {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 8px;
            letter-spacing: 1px;
            font-weight: 500;
        }

        .course-info {
            font-size: 0.75rem;
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 14px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .qr-section {
            background: white;
            padding: 8px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .validity {
            font-size: 0.65rem;
            opacity: 0.7;
            position: absolute;
            bottom: 18px;
            width: 100%;
            text-align: center;
            left: 0;
            z-index: 1;
            letter-spacing: 0.5px;
        }

        /* Modal Styles */
        .id-viewer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(8px);
            z-index: 200000;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: 0.3s ease;
        }

        .id-viewer-overlay.show {
            display: flex;
            opacity: 1;
        }

        .id-viewer-content {
            transform: scale(0.9);
            transition: 0.3s ease;
        }

        .id-viewer-overlay.show .id-viewer-content {
            transform: scale(1);
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
            z-index: 200001;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
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
                    <h3 style="margin-bottom: 25px; font-weight: 700; color: #64748b; font-size: 1.1rem; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-eye" style="color: #3b82f6;"></i> Official ID Template Preview
                    </h3>
                    
                    <!-- Vertical ID Card -->
                    <div class="student-id-card" onclick="openIDViewer()">
                        <div class="card-bg-pattern"></div>
                        <div class="id-header">
                            <div class="school-name">UNIVERSITY OF TECHNOLOGY</div>
                            <div style="font-size: 0.6rem; opacity: 0.8; letter-spacing: 0.5px;">ESTABLISHED 2026</div>
                        </div>

                        <div class="id-photo-area">
                            <div class="photo-frame">
                                <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?q=80&w=256&h=256&auto=format&fit=crop" alt="Student Portrait">
                            </div>
                        </div>

                        <div class="id-details">
                            <div class="student-name">JUAN DELA CRUZ</div>
                            <div class="student-no">2024-0001</div>
                            <div class="course-info">BS COMPUTER SCIENCE</div>
                            
                            <div class="qr-section">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=2024-0001" alt="QR Code" style="display: block;">
                            </div>
                        </div>

                        <div class="validity">
                            VALID UNTIL: JULY 2027<br>
                            STUDENT SIGNATURE
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Full ID Viewer Modal -->
    <div id="idViewer" class="id-viewer-overlay">
        <button class="close-btn" onclick="closeIDViewer()">
            <i class="fas fa-times"></i>
        </button>
        <div class="id-viewer-content">
            <!-- Reuse vertical card class but slightly larger via transform scale -->
            <div class="student-id-card" style="cursor: default; width: 360px; height: 560px;">
                <div class="card-bg-pattern"></div>
                <div class="id-header">
                    <div class="school-name" style="font-size: 0.95rem;">UNIVERSITY OF TECHNOLOGY</div>
                    <div style="font-size: 0.7rem; opacity: 0.8; letter-spacing: 0.5px;">ESTABLISHED 2026</div>
                </div>

                <div class="id-photo-area">
                    <div class="photo-frame" style="width: 150px; height: 150px;">
                        <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?q=80&w=400&h=400&auto=format&fit=crop" alt="Student">
                    </div>
                </div>

                <div class="id-details">
                    <div class="student-name" style="font-size: 1.5rem; margin-top: 5px;">JUAN DELA CRUZ</div>
                    <div class="student-no" style="font-size: 1.1rem;">2024-0001</div>
                    <div class="course-info" style="font-size: 0.85rem; margin-bottom: 30px;">BS COMPUTER SCIENCE</div>
                    
                    <div class="qr-section">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=2024-0001" alt="QR Code" style="display: block;">
                    </div>
                </div>

                <div class="validity" style="bottom: 25px; font-size: 0.75rem;">
                    VALID UNTIL: JULY 2027<br>
                    STUDENT SIGNATURE
                </div>
            </div>
        </div>
    </div>

    <script>
        function openIDViewer() {
            const overlay = document.getElementById('idViewer');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeIDViewer() {
            const overlay = document.getElementById('idViewer');
            overlay.classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        // Close on outside click
        window.onclick = function(event) {
            const overlay = document.getElementById('idViewer');
            if (event.target == overlay) {
                closeIDViewer();
            }
        }
    </script>
</body>

</html>

