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

        /* Interactive Card Style */
        .id-card-preview {
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .id-card-preview:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        /* ID Viewer Modal */
        .id-viewer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            z-index: 200000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeIn 0.3s ease;
        }

        .id-viewer-overlay.show {
            display: flex;
        }

        .id-viewer-content {
            background: white;
            padding: 40px;
            border-radius: 40px;
            position: relative;
            box-shadow: 0 50px 100px rgba(0,0,0,0.3);
            max-width: 90%;
            transform: scale(0.9);
            opacity: 0;
            transition: 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .id-viewer-overlay.show .id-viewer-content {
            transform: scale(1);
            opacity: 1;
        }

        .close-viewer {
            position: absolute;
            top: -20px;
            right: -20px;
            width: 50px;
            height: 50px;
            background: #ef4444;
            color: white;
            border: 4px solid white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.3);
            transition: 0.3s;
        }

        .close-viewer:hover {
            transform: rotate(90deg) scale(1.1);
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .full-id-card {
            width: 600px;
            height: 350px;
            background: linear-gradient(135deg, #ffffff 0%, #f0f4f8 100%);
            border-radius: 35px;
            padding: 40px;
            display: flex;
            gap: 40px;
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }

        .full-id-card::after {
            content: 'SCHOOL MANAGEMENT SYSTEM';
            position: absolute;
            bottom: -15px;
            right: 40px;
            font-size: 3rem;
            font-weight: 900;
            color: rgba(0,0,0,0.03);
            white-space: nowrap;
            pointer-events: none;
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
                        <i class="fas fa-eye" style="color: #3b82f6;"></i> ID Template Preview (Click to Zoom)
                    </h3>
                    
                    <div class="id-card-preview" onclick="openIDViewer()">
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

    <!-- Full ID Viewer Modal -->
    <div id="idViewer" class="id-viewer-overlay">
        <div class="id-viewer-content">
            <div class="close-viewer" onclick="closeIDViewer()">
                <i class="fas fa-times"></i>
            </div>
            <div class="full-id-card">
                <div style="width: 220px; height: 220px; border-radius: 30px; overflow: hidden; border: 8px solid white; box-shadow: 0 20px 40px rgba(0,0,0,0.1); flex-shrink: 0;">
                    <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?q=80&w=400&h=400&auto=format&fit=crop" alt="Student" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="display: flex; flex-direction: column; justify-content: center; position: relative; z-index: 2;">
                    <span style="background: #1e293b; color: white; padding: 6px 15px; border-radius: 10px; font-size: 0.75rem; font-weight: 800; width: fit-content; margin-bottom: 20px; letter-spacing: 1px;">OFFICIAL STUDENT ID</span>
                    <h1 style="font-weight: 900; color: #1e293b; font-size: 2.8rem; line-height: 1; margin: 0 0 10px 0; letter-spacing: -1.5px;">JUAN DELA CRUZ</h1>
                    <p style="font-size: 1.4rem; color: #3b82f6; font-weight: 800; margin: 0;">ID: 2024-0001</p>
                    <p style="font-size: 1.2rem; color: #64748b; font-weight: 600; margin-top: 5px;">BS Computer Science</p>
                    
                    <div style="margin-top: 40px; display: flex; align-items: center; gap: 20px;">
                        <div style="background: white; padding: 10px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: 2px solid #f1f5f9;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=2024-0001" alt="QR Large">
                        </div>
                        <div>
                            <p style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Scan to Verify</p>
                            <p style="font-size: 0.65rem; color: #cbd5e1; max-width: 150px; font-weight: 500;">This ID remains property of the institution. If found, please return to any campus administrator.</p>
                        </div>
                    </div>
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

