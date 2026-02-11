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
    <title>Subject Enrollment - Admission</title>
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

        .enroll-container {
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
        }

        .search-student {
            margin-bottom: 30px;
            display: flex;
            gap: 10px;
        }

        .search-student input {
            flex: 1;
            padding: 12px 20px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            outline: none;
        }

        .subject-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .subject-item {
            border: 1px solid #f1f5f9;
            padding: 15px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .subject-item.selected {
            background: #eef2ff;
            border-color: var(--primary);
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 25px;">Subject Enrollment</h1>
            <div class="enroll-container">
                <div class="search-student">
                    <input type="text" placeholder="Search Student by ID or Name...">
                    <button
                        style="background: var(--primary); color: white; border: none; padding: 0 25px; border-radius: 12px; font-weight: 600;">Search</button>
                </div>

                <h3>Available Subjects for BSIT - 1st Semester</h3>
                <div class="subject-grid">
                    <div class="subject-item selected">
                        <div>
                            <p style="font-weight: 700;">ITP 101</p>
                            <p style="font-size: 0.8rem; color: #64748b;">Introduction to Computing</p>
                        </div>
                        <input type="checkbox" checked>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>