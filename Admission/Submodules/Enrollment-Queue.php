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
    <title>Enrollment Queue - Admission</title>
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

        .queue-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .queue-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            border-top: 4px solid var(--primary-blue);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }

        .queue-card h3 {
            font-size: 1rem;
            color: #2d3748;
            margin-bottom: 15px;
        }

        .student-item {
            background: #f8fafc;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .student-item .avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #eef2ff;
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="margin-bottom: 25px;">Registration Queue</h1>
            <div class="queue-container">
                <div class="queue-card">
                    <h3>Waiting for Approval</h3>
                    <div class="student-item">
                        <div class="avatar">JD</div>
                        <div>
                            <p style="font-size: 0.85rem; font-weight: 600;">John Doe</p>
                            <p style="font-size: 0.75rem; color: #718096;">BSIT - 1st Year</p>
                        </div>
                    </div>
                </div>
                <div class="queue-card">
                    <h3>Processing</h3>
                    <div class="student-item">
                        <div class="avatar">JS</div>
                        <div>
                            <p style="font-size: 0.85rem; font-weight: 600;">Jane Smith</p>
                            <p style="font-size: 0.75rem; color: #718096;">BSCS - 2nd Year</p>
                        </div>
                    </div>
                </div>
                <div class="queue-card">
                    <h3>Registered</h3>
                    <p style="font-size: 0.85rem; color: #a0aec0; text-align: center; margin-top: 20px;">No students
                        registered today.</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>