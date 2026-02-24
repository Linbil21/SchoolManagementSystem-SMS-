<?php
require_once __DIR__ . '/../../auth/Security.php';
checkRole(['admission']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Section Assignment - Admission</title>
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

        .section-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
        }

        .capacity-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            margin: 10px 0;
            overflow: hidden;
        }

        .capacity-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 4px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 5vh auto;
            width: 90%;
            max-width: 600px;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: modalSlide 0.3s ease-out;
        }

        @keyframes modalSlide {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 20px 30px;
            background: #f8fafc;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 30px;
        }

        .modal-footer {
            padding: 20px 30px;
            background: #f8fafc;
            border-top: 1px solid #edf2f7;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .student-lookup {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .student-lookup input {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            outline: none;
        }

        .unassigned-list {
            max-height: 250px;
            overflow-y: auto;
        }

        .unassigned-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .unassigned-item:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 30px;">Section Assignment</h1>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="section-card">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <h3 style="color: var(--primary);">BSIT 1-A</h3>
                            <p style="font-size: 0.85rem; color: #64748b;">Schedule: Mon-Fri 08:00 AM - 12:00 PM</p>
                        </div>
                        <span
                            style="background: #eef2ff; color: var(--primary); padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">ACTIVE</span>
                    </div>

                    <div style="margin-top: 20px;">
                        <div
                            style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600;">
                            <span>Capacity</span>
                            <span>35 / 45</span>
                        </div>
                        <div class="capacity-bar">
                            <div class="capacity-fill" style="width: 77%;"></div>
                        </div>
                    </div>

                    <button onclick="openAssignModal('BSIT 1-A')"
                        style="width: 100%; margin-top: 20px; border: 1px solid var(--primary); color: var(--primary); background: transparent; padding: 10px; border-radius: 12px; font-weight: 600; cursor: pointer;">Assign
                        Students</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Assign Students Modal -->
    <div id="assignModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalSectionName" style="font-weight: 800; color: #1e293b;">Assign Students</h2>
                <i class="fas fa-times" style="cursor: pointer; color: #64748b;" onclick="closeModal()"></i>
            </div>
            <div class="modal-body">
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">Search and select unassigned students to add to this section.</p>
                
                <div class="student-lookup">
                    <input type="text" placeholder="Enter Student ID or Name...">
                    <button style="background: #eef2ff; color: var(--primary); border: none; padding: 0 15px; border-radius: 10px; font-weight: 600;"><i class="fas fa-search"></i></button>
                </div>

                <div class="unassigned-list">
                    <div class="unassigned-item">
                        <div>
                            <p style="font-weight: 600; font-size: 0.9rem;">Sarah Jenkins</p>
                            <p style="font-size: 0.75rem; color: #718096;">ID: #STU-2024-056</p>
                        </div>
                        <input type="checkbox">
                    </div>
                    <div class="unassigned-item">
                        <div>
                            <p style="font-weight: 600; font-size: 0.9rem;">David Miller</p>
                            <p style="font-size: 0.75rem; color: #718096;">ID: #STU-2024-089</p>
                        </div>
                        <input type="checkbox">
                    </div>
                    <div class="unassigned-item">
                        <div>
                            <p style="font-weight: 600; font-size: 0.9rem;">Kevin Smith</p>
                            <p style="font-size: 0.75rem; color: #718096;">ID: #STU-2024-102</p>
                        </div>
                        <input type="checkbox">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal()" style="padding: 10px 20px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; font-weight: 600; cursor: pointer;">Cancel</button>
                <button onclick="confirmAssignment()" style="padding: 10px 25px; border-radius: 10px; background: var(--primary); color: white; border: none; font-weight: 600; cursor: pointer;">Assign Selected</button>
            </div>
        </div>
    </div>

    <script>
        function openAssignModal(section) {
            document.getElementById('modalSectionName').textContent = 'Assign Students to ' + section;
            document.getElementById('assignModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('assignModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function confirmAssignment() {
            alert('Selected students have been assigned successfully!');
            closeModal();
        }

        window.onclick = function(event) {
            const modal = document.getElementById('assignModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>

