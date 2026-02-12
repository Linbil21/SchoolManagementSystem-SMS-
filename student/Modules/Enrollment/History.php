<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment History</title>
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

        .history-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 25px;
            border: 1px solid #f1f5f9;
        }
        
        .card-header {
            background: #f8fafc;
            padding: 20px 30px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .sem-label {
            font-weight: 700;
            color: #1e293b;
            font-size: 1.1rem;
        }
        
        .year-label {
            color: #64748b;
            font-size: 0.9rem;
        }
        
        .status-badge {
            background: #dcfce7;
            color: #16a34a;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .card-body {
            padding: 25px 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-item label {
            display: block;
            color: #94a3b8;
            font-size: 0.8rem;
            margin-bottom: 3px;
        }
        
        .info-item span {
            color: #1e293b;
            font-weight: 600;
        }

        .view-btn {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .view-btn:hover {
            text-decoration: underline;
        }

    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Enrollment History</h1>
            </div>

            <!-- Current Enrollment (If any, mostly handled in status) -->
            <!-- Previous Enrollments -->

            <div class="history-card">
                <div class="card-header">
                    <div>
                        <div class="sem-label">2nd Semester</div>
                        <div class="year-label">Academic Year 2025-2026</div>
                    </div>
                    <span class="status-badge">Enrolled</span>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Course</label>
                            <span>BS Information Technology</span>
                        </div>
                        <div class="info-item">
                            <label>Year Level</label>
                            <span>2nd Year</span>
                        </div>
                         <div class="info-item">
                            <label>Total Units</label>
                            <span>21.0</span>
                        </div>
                        <div class="info-item">
                            <label>Date Enrolled</label>
                            <span>Jan 15, 2026</span>
                        </div>
                    </div>
                    <a href="#" class="view-btn">View Registration Form <i class="fas fa-external-link-alt"></i></a>
                </div>
            </div>

            <div class="history-card">
                <div class="card-header">
                    <div>
                        <div class="sem-label">1st Semester</div>
                        <div class="year-label">Academic Year 2025-2026</div>
                    </div>
                    <span class="status-badge">Completed</span>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Course</label>
                            <span>BS Information Technology</span>
                        </div>
                        <div class="info-item">
                            <label>Year Level</label>
                            <span>2nd Year</span>
                        </div>
                         <div class="info-item">
                            <label>Total Units</label>
                            <span>23.0</span>
                        </div>
                        <div class="info-item">
                            <label>Date Enrolled</label>
                            <span>Aug 10, 2025</span>
                        </div>
                    </div>
                    <a href="#" class="view-btn">View Registration Form <i class="fas fa-external-link-alt"></i></a>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
