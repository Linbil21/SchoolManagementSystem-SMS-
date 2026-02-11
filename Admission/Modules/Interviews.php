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
    <title>Interview Management - Admission</title>
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

        .calendar-view {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .schedule-list {
            margin-top: 25px;
        }

        .schedule-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            margin-bottom: 12px;
            transition: 0.3s;
        }

        .schedule-item:hover {
            border-color: var(--primary);
            transform: translateX(5px);
        }

        .time-box {
            min-width: 100px;
            background: #eef2ff;
            color: var(--primary);
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            font-weight: 700;
            font-size: 0.9rem;
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
            width: 95%;
            max-width: 700px;
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

        .candidate-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .candidate-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #eef2ff;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 800;
        }

        .score-group {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .score-item {
            flex: 1;
            background: #f8fafc;
            padding: 15px;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
            text-align: center;
        }

        .score-item label {
            display: block;
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .score-item select {
            width: 100%;
            border: none;
            background: transparent;
            text-align: center;
            font-weight: 700;
            color: var(--primary);
            outline: none;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; color: #1e293b; margin-bottom: 30px;">Interview Schedule</h1>

            <div class="calendar-view">
                <h3>Today's Interviews - Jan 11, 2024</h3>
                <div class="schedule-list">
                    <div class="schedule-item">
                        <div class="time-box">09:00 AM</div>
                        <div style="margin-left: 20px;">
                            <p style="font-weight: 700; color: #1e293b;">Robert Fox</p>
                            <p style="font-size: 0.8rem; color: #64748b;">Course: BS Architecture | Room 202</p>
                        </div>
                        <button onclick="openInterviewModal('Robert Fox', 'BS Architecture')"
                            style="margin-left: auto; background: var(--primary); color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer;">Start</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Interview Assessment Modal -->
    <div id="interviewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-weight: 800; color: #1e293b;">Interview Assessment</h2>
                <i class="fas fa-times" style="cursor: pointer; color: #64748b;" onclick="closeModal()"></i>
            </div>
            <div class="modal-body">
                <div class="candidate-header">
                    <div class="candidate-avatar">RF</div>
                    <div>
                        <h3 id="modalCandidateName" style="color: #1e293b; font-weight: 700;">Robert Fox</h3>
                        <p id="modalCandidateCourse" style="color: #64748b; font-size: 0.9rem;">Bachelor of Science in
                            Architecture</p>
                    </div>
                </div>

                <div class="score-group">
                    <div class="score-item">
                        <label>Communication</label>
                        <select>
                            <option>5/5</option>
                            <option>4/5</option>
                            <option>3/5</option>
                            <option>2/5</option>
                            <option>1/5</option>
                        </select>
                    </div>
                    <div class="score-item">
                        <label>Knowledge</label>
                        <select>
                            <option>5/5</option>
                            <option>4/5</option>
                            <option>3/5</option>
                            <option>2/5</option>
                            <option>1/5</option>
                        </select>
                    </div>
                    <div class="score-item">
                        <label>Attitude</label>
                        <select>
                            <option>5/5</option>
                            <option>4/5</option>
                            <option>3/5</option>
                            <option>2/5</option>
                            <option>1/5</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 25px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #475569;">Interviewer's
                        Remarks</label>
                    <textarea placeholder="Write final interview notes and recommendation..."
                        style="width: 100%; height: 150px; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0; outline: none; resize: none; font-family: inherit; font-size: 0.9rem;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal()"
                    style="padding: 10px 20px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; font-weight: 600; cursor: pointer;">Cancel</button>
                <button onclick="submitAssessment()"
                    style="padding: 10px 25px; border-radius: 10px; background: var(--primary); color: white; border: none; font-weight: 600; cursor: pointer;">Submit
                    Assessment</button>
            </div>
        </div>
    </div>

    <script>
        function openInterviewModal(name, course) {
            document.getElementById('modalCandidateName').textContent = name;
            document.getElementById('modalCandidateCourse').textContent = course;
            document.getElementById('interviewModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('interviewModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function submitAssessment() {
            alert('Interview assessment has been successfully submitted!');
            closeModal();
        }

        window.onclick = function (event) {
            const modal = document.getElementById('interviewModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>