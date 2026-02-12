<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Status</title>
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

        .status-tracker {
            background: white;
            border-radius: 24px;
            padding: 40px 10px;
            /* Reduced side padding on container */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 40px;
            overflow-x: auto;
            /* Allow Scroll on small screens */
        }

        .steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }

        .steps::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 0;
            width: 100%;
            height: 4px;
            background: #e2e8f0;
            z-index: 0;
        }

        .step-item {
            position: relative;
            z-index: 1;
            text-align: center;
            width: 120px;
        }

        .step-circle {
            width: 50px;
            height: 50px;
            background: white;
            border: 3px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 700;
            color: #94a3b8;
            transition: all 0.3s;
        }

        .step-item.completed .step-circle {
            border-color: #22c55e;
            background: #22c55e;
            color: white;
        }

        .step-item.active .step-circle {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.1);
        }

        .step-label {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }

        .step-item.active .step-label {
            color: var(--primary);
            font-weight: 700;
        }

        .info-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }

        .status-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #ea580c;
            /* Orange for validation */
        }

        .info-card h2 {
            margin-bottom: 10px;
            color: #1e293b;
        }

        .info-card p {
            color: #64748b;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <h1 class="page-title" style="margin-bottom: 30px; font-weight: 800; color: #1e293b;">Enrollment Status</h1>

            <div class="status-tracker">
                <div class="steps">
                    <!-- Step 1 -->
                    <div class="step-item completed">
                        <div class="step-circle"><i class="fas fa-check"></i></div>
                        <span class="step-label">Subjects</span>
                    </div>
                    <!-- Step 2 -->
                    <div class="step-item completed">
                        <div class="step-circle"><i class="fas fa-check"></i></div>
                        <span class="step-label">Assessment</span>
                    </div>
                    <!-- Step 3 -->
                    <div class="step-item completed">
                        <div class="step-circle"><i class="fas fa-check"></i></div>
                        <span class="step-label">Payment</span>
                    </div>
                    <!-- Step 4 -->
                    <div class="step-item active">
                        <div class="step-circle">4</div>
                        <span class="step-label">Validation</span>
                    </div>
                    <!-- Step 5 -->
                    <div class="step-item">
                        <div class="step-circle">5</div>
                        <span class="step-label">Enrolled</span>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <i class="fas fa-search-dollar status-icon"></i>
                <h2>Payment Under Validation</h2>
                <p>We have received your payment proof and it is currently being verified by the Cashier's Office. This
                    process usually takes 24-48 hours. You will be notified once your enrollment is officially
                    confirmed.</p>
                <div style="margin-top: 25px;">
                    <button
                        style="background: #f1f5f9; color: #475569; border: none; padding: 12px 25px; border-radius: 10px; cursor: pointer; font-weight: 600;">Check
                        Again Later</button>
                </div>
            </div>

        </div>
    </div>
</body>

</html>