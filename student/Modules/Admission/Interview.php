<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Schedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Shared CSS Root & Body from other modules */
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

        .page-subtitle {
            color: var(--secondary);
            font-size: 0.95rem;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .empty-icon {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #1e293b;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #64748b;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Timeline for Process */
        .process-steps {
            display: flex;
            justify-content: center;
            margin-bottom: 50px;
            gap: 40px;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
        }


        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .step.completed .step-circle {
            background: #2563eb;
            color: white;
        }

        .step.active .step-circle {
            border: 2px solid #2563eb;
            background: white;
            color: #2563eb;
        }

        .step-label {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }

        .step.active .step-label {
            color: #2563eb;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Interview Schedule</h1>
                <p class="page-subtitle">Check your interview details after passing the entrance examination.</p>
            </div>

            <!-- Steps Indicator -->
            <div class="process-steps">
                <div class="step completed">
                    <div class="step-circle"><i class="fas fa-check"></i></div>
                    <span class="step-label">Application</span>
                </div>
                <div class="step completed">
                    <div class="step-circle"><i class="fas fa-check"></i></div>
                    <span class="step-label">Exam</span>
                </div>
                <div class="step active">
                    <div class="step-circle">3</div>
                    <span class="step-label">Interview</span>
                </div>
                <div class="step">
                    <div class="step-circle">4</div>
                    <span class="step-label">Enrollment</span>
                </div>
            </div>

            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <h3>No Schedule Available Yet</h3>
                <p>Your entrance exam results are still being processed. Please wait for the results to be released
                    before an interview schedule is assigned.</p>
                <button
                    style="margin-top: 25px; background: #eff6ff; color: var(--primary); border: none; padding: 12px 25px; border-radius: 12px; font-weight: 600; cursor: pointer;">
                    Check Exam Results
                </button>
            </div>

        </div>
    </div>
</body>

</html>