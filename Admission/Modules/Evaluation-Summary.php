<?php
require_once __DIR__ . '/../../auth/Security.php';
checkRole(['admission']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Summary - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1648bc;
            --bg-light: #f7fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--bg-light);
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

        .module-header {
            margin-bottom: 25px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            border-left: 5px solid #1648bc;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>Evaluation Summary</h1>
                <p>Overall admission metrics and performance.</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Evaluated</h3>
                    <h1>1,245</h1>
                    <p>Applications processed this year</p>
                </div>
                <div class="stat-card">
                    <h3>Passed</h3>
                    <h1>850</h1>
                    <p>Students qualified for enrollment</p>
                </div>
                <div class="stat-card">
                    <h3>Failed/Rejected</h3>
                    <h1>395</h1>
                    <p>Did not meet requirements</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


