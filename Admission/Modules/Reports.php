<?php
require_once __DIR__ . '/../../auth/Security.php';
checkRole(['admission']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Reports - SMS</title>
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

        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .report-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            border: 1px solid #f1f5f9;
            transition: 0.3s;
        }

        .report-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }

        .report-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .btn-download {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            margin-top: 15px;
            cursor: pointer;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 30px;">Reports Repository</h1>
            <div class="report-grid">
                <div class="report-card">
                    <i class="fas fa-file-invoice"></i>
                    <h3>Monthly Enrollment</h3>
                    <p style="font-size: 0.85rem; color: #64748b; margin: 10px 0;">Summary of all enrollments for the
                        current month.</p>
                    <button class="btn-download">Download PDF</button>
                </div>
                <div class="report-card">
                    <i class="fas fa-user-check"></i>
                    <h3>Admission Status</h3>
                    <p style="font-size: 0.85rem; color: #64748b; margin: 10px 0;">Real-time status of all applicant
                        evaluations.</p>
                    <button class="btn-download">Generate Report</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

