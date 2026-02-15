<?php
session_start();
require_once '../../auth/Security.php';
require_once '../../Database/config.php';
checkRole(['superadmin']);

// Fetch students for the ID center
$stmt = $pdo->query("SELECT * FROM students ORDER BY last_name ASC");
$students = $stmt->fetchAll();
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
            width: 350px;
            height: 500px;
            background: linear-gradient(135deg, #1648bc 0%, #2563eb 100%);
            border-radius: 20px;
            padding: 20px;
            color: white;
            position: relative;
            box-shadow: 0 15px 35px rgba(22, 72, 188, 0.3);
            margin: 20px auto;
        }
        .id-photo {
            width: 150px;
            height: 150px;
            background: white;
            border-radius: 15px;
            margin: 40px auto 20px;
            border: 4px solid rgba(255,255,255,0.3);
            overflow: hidden;
        }
        .id-photo img { width: 100%; height: 100%; object-fit: cover; }
        .student-item {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: 0.2s;
        }
        .student-item:hover { background: #f8fafc; }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
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

