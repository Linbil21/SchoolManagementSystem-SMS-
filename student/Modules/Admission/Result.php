<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Result</title>
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

        .result-card {
            background: white;
            border-radius: 24px;
            padding: 50px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 40px auto;
            position: relative;
            overflow: hidden;
        }
        
        .confetti-decoration {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 10px;
            background: linear-gradient(90deg, #ff0000, #ffa500, #ffff00, #008000, #0000ff, #4b0082, #ee82ee);
        }

        .result-icon {
            width: 100px;
            height: 100px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 30px;
        }
        
        .result-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 10px;
        }
        
        .result-message {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .score-box {
            background: #f8fafc;
            padding: 20px;
            border-radius: 16px;
            display: inline-flex;
            gap: 40px;
            margin-bottom: 30px;
        }
        
        .score-item h4 {
            font-size: 2rem;
            font-weight: 800;
            color: #2563eb;
            margin-bottom: 0;
        }
        
        .score-item span {
            font-size: 0.85rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .action-btn {
            background: #2563eb;
            color: white;
            padding: 15px 40px;
            border-radius: 12px;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
        }

    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            
            <div class="result-card">
                <div class="confetti-decoration"></div>
                <div class="result-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="result-title">Congratulations!</h1>
                <p class="result-message">
                    We are pleased to inform you that you have <strong>PASSED</strong> the Entrance Examination for the <strong>BS Information Technology</strong> program.
                </p>

                <div class="score-box">
                    <div class="score-item">
                        <h4>92%</h4>
                        <span>Exam Score</span>
                    </div>
                    <div class="score-item">
                        <h4>Passed</h4>
                        <span>Interview</span>
                    </div>
                </div>

                <a href="../Enrollment/Subject-Selection.php" class="action-btn">
                    Proceed to Enrollment <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                </a>
            </div>

        </div>
    </div>
</body>

</html>
