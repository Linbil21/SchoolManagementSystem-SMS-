<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cashier') {
    header("Location: ../../auth/Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Breakdown - Submodule</title>
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
            padding: 40px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .sub-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .sub-card h3 {
            color: var(--primary);
            margin-bottom: 15px;
            border-bottom: 2px solid #eef2ff;
            padding-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 25px;">Cashier Submodule: Fee Structure</h1>
            <div class="grid">
                <div class="sub-card">
                    <h3>Basic Tuition</h3>
                    <p style="font-size: 1.5rem; font-weight: 700;">₱750 / Unit</p>
                    <p style="color: #64748b; font-size: 0.8rem; margin-top: 5px;">Level: Undergraduate</p>
                </div>
                <div class="sub-card">
                    <h3>Miscellaneous</h3>
                    <p style="font-size: 1.5rem; font-weight: 700;">₱4,200 / Sem</p>
                    <p style="color: #64748b; font-size: 0.8rem; margin-top: 5px;">Includes ID, Library, Medical</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>