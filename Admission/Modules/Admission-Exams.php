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
    <title>Admission Exams - SMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1648bc;
            --bg: #f7fafc;
            --text: #2d3748;
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

        .exam-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .exam-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-top: 5px solid var(--primary);
        }

        .exam-card h3 {
            color: var(--text);
            margin-bottom: 15px;
            font-weight: 700;
        }

        .exam-info {
            margin-bottom: 20px;
        }

        .exam-info p {
            font-size: 0.9rem;
            color: #718096;
            margin-bottom: 5px;
        }

        .exam-info i {
            margin-right: 8px;
            color: var(--primary);
        }

        .btn-schedule {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-schedule:hover {
            background: #0e3080;
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
            margin: 10vh auto;
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #475569;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            outline: none;
            font-family: inherit;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; color: var(--text);">Admission Exams</h1>
            <p style="color: #718096;">Schedule and manage entrance examinations for applicants.</p>

            <div class="exam-grid">
                <div class="exam-card">
                    <h3>Morning Session</h3>
                    <div class="exam-info">
                        <p><i class="fas fa-calendar"></i> January 15, 2024</p>
                        <p><i class="fas fa-clock"></i> 08:00 AM - 11:00 AM</p>
                        <p><i class="fas fa-map-marker-alt"></i> Examination Hall A</p>
                    </div>
                    <button class="btn-schedule" onclick="openExamModal('Morning Session')">Manage Exam</button>
                </div>
                <div class="exam-card">
                    <h3>Afternoon Session</h3>
                    <div class="exam-info">
                        <p><i class="fas fa-calendar"></i> January 15, 2024</p>
                        <p><i class="fas fa-clock"></i> 01:00 PM - 04:00 PM</p>
                        <p><i class="fas fa-map-marker-alt"></i> Examination Hall A</p>
                    </div>
                    <button class="btn-schedule" onclick="openExamModal('Afternoon Session')">Manage Exam</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Manage Exam Modal -->
    <div id="examModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalExamTitle" style="font-weight: 800; color: #1e293b;">Manage Session</h2>
                <i class="fas fa-times" style="cursor: pointer; color: #64748b;" onclick="closeExamModal()"></i>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Exam Date</label>
                    <input type="date" value="2024-01-15">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="time" value="08:00">
                    </div>
                    <div class="form-group">
                        <label>End Time</label>
                        <input type="time" value="11:00">
                    </div>
                </div>
                <div class="form-group">
                    <label>Venue / Room</label>
                    <input type="text" value="Examination Hall A">
                </div>
                <div class="form-group">
                    <label>Proctor Name</label>
                    <select>
                        <option>Prof. John Smith</option>
                        <option>Dr. Sarah Wilson</option>
                        <option>Engr. Mike Ross</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeExamModal()"
                    style="padding: 10px 20px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; font-weight: 600; cursor: pointer;">Cancel</button>
                <button onclick="saveExam()"
                    style="padding: 10px 25px; border-radius: 10px; background: var(--primary); color: white; border: none; font-weight: 600; cursor: pointer;">Update
                    Session</button>
            </div>
        </div>
    </div>

    <script>
        function openExamModal(title) {
            document.getElementById('modalExamTitle').textContent = 'Manage: ' + title;
            document.getElementById('examModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeExamModal() {
            document.getElementById('examModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function saveExam() {
            alert('Exam session has been updated successfully!');
            closeExamModal();
        }

        window.onclick = function (event) {
            const modal = document.getElementById('examModal');
            if (event.target == modal) {
                closeExamModal();
            }
        }
    </script>
</body>

</html>