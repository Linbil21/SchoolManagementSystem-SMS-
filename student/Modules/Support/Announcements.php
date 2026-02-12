<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
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
            overflow-x: hidden;
        }

        .content-area {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .announcements-grid {
            display: grid;
            gap: 20px;
        }

        .announcement-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border-left: 5px solid transparent;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .announcement-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .announcement-card.priority-high {
            border-left-color: #ef4444;
        }

        .announcement-card.priority-normal {
            border-left-color: #3b82f6;
        }

        .announcement-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .tag {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .tag-urgent {
            background: #fee2e2;
            color: #ef4444;
        }

        .tag-info {
            background: #eff6ff;
            color: #3b82f6;
        }

        .tag-event {
            background: #f0fdf4;
            color: #16a34a;
        }

        .date {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .announcement-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .announcement-preview {
            color: #64748b;
            font-size: 0.9rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 40px;
            border-radius: 24px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            position: relative;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-close {
            position: absolute;
            top: 25px;
            right: 25px;
            font-size: 1.5rem;
            color: #94a3b8;
            cursor: pointer;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.2s;
        }

        .modal-close:hover {
            background: #f1f5f9;
        }

        .modal-date {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 5px;
            display: block;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 20px;
            line-height: 1.3;
        }

        .modal-body {
            color: #475569;
            line-height: 1.7;
            font-size: 0.95rem;
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Announcements</h1>
            </div>

            <div class="announcements-grid">
                <!-- Card 1 -->
                <div class="announcement-card priority-high"
                    onclick="openModal('Suspension of Classes', 'Please be advised that classes are suspended tomorrow, October 25, 2026, due to the expected typhoon. Stay safe everyone.', 'Oct 24, 2026', 'urgent')">
                    <div class="announcement-header">
                        <span class="tag tag-urgent">Urgent</span>
                        <span class="date">Oct 24, 2026</span>
                    </div>
                    <h3 class="announcement-title">Suspension of Classes</h3>
                    <p class="announcement-preview">Please be advised that classes are suspended tomorrow, October 25,
                        2026, due to the expected typhoon. Stay safe everyone.</p>
                </div>

                <!-- Card 2 -->
                <div class="announcement-card priority-normal"
                    onclick="openModal('Midterm Examination Schedule', 'The schedule for the Midterm Examinations has been released. Please check the Exam Schedule module for your specific room assignments and time slots.', 'Oct 20, 2026', 'info')">
                    <div class="announcement-header">
                        <span class="tag tag-info">Academic</span>
                        <span class="date">Oct 20, 2026</span>
                    </div>
                    <h3 class="announcement-title">Midterm Examination Schedule</h3>
                    <p class="announcement-preview">The schedule for the Midterm Examinations has been released. Please
                        check the Exam Schedule module for your specific room assignments and time slots.</p>
                </div>

                <!-- Card 3 -->
                <div class="announcement-card priority-normal"
                    onclick="openModal('University Week 2026', 'Get ready for a week of fun, sports, and celebration! The University Week 2026 will kick off on November 5. Registration for sports events is now open.', 'Oct 15, 2026', 'event')">
                    <div class="announcement-header">
                        <span class="tag tag-event">Event</span>
                        <span class="date">Oct 15, 2026</span>
                    </div>
                    <h3 class="announcement-title">University Week 2026</h3>
                    <p class="announcement-preview">Get ready for a week of fun, sports, and celebration! The University
                        Week 2026 will kick off on November 5. Registration for sports events is now open.</p>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div id="announcementModal" class="modal">
        <div class="modal-content">
            <div class="modal-close" onclick="closeModal()">&times;</div>
            <span class="modal-date" id="mDate">--</span>
            <h2 class="modal-title" id="mTitle">--</h2>
            <div class="modal-body" id="mBody">
                --
            </div>
            <div style="margin-top: 30px; text-align: right;">
                <button onclick="closeModal()"
                    style="background: #2563eb; color: white; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 600; cursor: pointer;">Close</button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('announcementModal');

        function openModal(title, body, date, type) {
            document.getElementById('mTitle').textContent = title;
            document.getElementById('mBody').textContent = body;
            document.getElementById('mDate').textContent = date;
            modal.classList.add('active');
        }

        function closeModal() {
            modal.classList.remove('active');
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>