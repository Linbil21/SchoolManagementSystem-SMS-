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
    <title>Admission Statistics - SMS</title>
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .chart-placeholder {
            background: white;
            border-radius: 20px;
            padding: 30px;
            min-height: 350px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
        }

        .bar-container {
            flex: 1;
            display: flex;
            align-items: flex-end;
            gap: 15px;
            margin-top: 20px;
        }

        .bar {
            background: var(--primary);
            border-radius: 4px 4px 0 0;
            width: 40px;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 30px;">Admission Analytics</h1>
            <div class="stats-grid">
                <div class="chart-placeholder">
                    <h3>Applications by Course</h3>
                    <div class="bar-container">
                        <div class="bar" style="height: 80%;"></div>
                        <div class="bar" style="height: 60%;"></div>
                        <div class="bar" style="height: 95%;"></div>
                        <div class="bar" style="height: 40%;"></div>
                    </div>
                </div>
                <div class="chart-placeholder">
                    <h3>Success Rate</h3>
                    <div style="flex: 1; display: flex; align-items: center; justify-content: center;">
                        <div
                            style="width: 200px; height: 200px; border-radius: 50%; border: 15px solid #eef2ff; border-top: 15px solid var(--primary); display: flex; align-items: center; justify-content: center;">
                            <h2 style="font-size: 2.5rem; color: var(--primary);">78%</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>