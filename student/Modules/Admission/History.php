<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission History</title>
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
        
        .history-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .history-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .history-info h3 {
            font-size: 1.1rem;
            color: #1e293b;
            margin-bottom: 5px;
        }
        
        .history-info p {
            color: #64748b;
            font-size: 0.9rem;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-badge.completed {
            background: #dcfce7;
            color: #16a34a;
        }
        
         .status-badge.pending {
            background: #fff7ed;
            color: #ea580c;
        }
        
        .history-date {
            font-size: 0.85rem;
            color: #94a3b8;
            font-weight: 500;
        }

    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Admission History</h1>
            </div>

            <div class="history-list">
                
                <div class="history-card">
                    <div class="history-icon"></div>
                    <div class="history-info">
                        <h3>Notice of Admission Result</h3>
                        <p>Your admission result for BSIT Program has been released.</p>
                    </div>
                    <div style="text-align: right;">
                        <span class="status-badge completed">Viewed</span>
                        <div class="history-date">Oct 28, 2026</div>
                    </div>
                </div>

                <div class="history-card">
                    <div class="history-info">
                        <h3>Interview Scheduled</h3>
                        <p>Interview scheduled with Department Head.</p>
                    </div>
                    <div style="text-align: right;">
                        <span class="status-badge completed">Completed</span>
                        <div class="history-date">Oct 26, 2026</div>
                    </div>
                </div>

                <div class="history-card">
                    <div class="history-info">
                        <h3>Entrance Exam Booking</h3>
                         <p>Exam slot confirmed for Oct 25, 2026 - Morning Session.</p>
                    </div>
                     <div style="text-align: right;">
                        <span class="status-badge completed">Completed</span>
                        <div class="history-date">Oct 20, 2026</div>
                    </div>
                </div>

                <div class="history-card">
                    <div class="history-info">
                        <h3>Documents Submission</h3>
                        <p>Uploaded PSA Birth Certificate, Form 138, and Good Moral.</p>
                    </div>
                     <div style="text-align: right;">
                        <span class="status-badge completed">Verified</span>
                        <div class="history-date">Oct 18, 2026</div>
                    </div>
                </div>

                 <div class="history-card">
                    <div class="history-info">
                        <h3>Initial Application</h3>
                        <p>Submitted application form for BS Information Technology.</p>
                    </div>
                     <div style="text-align: right;">
                        <span class="status-badge completed">Submitted</span>
                        <div class="history-date">Oct 18, 2026</div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</body>

</html>
