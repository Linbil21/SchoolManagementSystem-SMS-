<?php
session_start();
require_once '../Database/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS - School Management System</title>
    <link rel="icon" type="image/png" href="../Assets/image/logo.png">
    
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --text-main: #1e293b;
            --text-secondary: #64748b;
            --gradient-accent: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8fafc;
            overflow-x: hidden;
            height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Navbar */
        nav {
            position: absolute;
            top: 0;
            width: 100%;
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-container img {
            width: 35px;
        }

        .logo-container span {
            font-weight: 700;
            color: var(--primary);
            font-size: 0.95rem;
        }

        .menu-btn {
            font-size: 1.2rem;
            color: var(--primary);
            cursor: pointer;
            padding: 8px;
            background: #eff6ff;
            border-radius: 6px;
        }

        /* Main Container */
        .page-container {
            flex: 1;
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            display: flex;
        }

        /* Background Shapes */
        .bg-shape-right {
            position: absolute;
            top: 0;
            right: 0;
            width: 45%;
            height: 100%;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.9) 0%, rgba(30, 64, 175, 0.95) 100%),
                        url('../Assets/image/background.jpg') center/cover;
            clip-path: polygon(30% 0%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 1;
        }

        /* Doodle Pattern on Left */
        .bg-doodles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.05"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
            z-index: 0;
        }

        /* Content Area */
        .content-area {
            position: relative;
            z-index: 10;
            width: 100%;
            height: 100%;
            padding: 80px 5% 0;
            display: flex;
            flex-direction: column;
        }

        .text-section {
            width: 55%;
            padding-top: 40px;
        }

        .text-section h1 {
            font-size: clamp(2.2rem, 4vw, 3.5rem);
            line-height: 1.2;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .text-dark {
            color: var(--text-main);
            display: block;
        }

        .text-blue {
            color: var(--primary);
            display: block;
        }

        .text-section p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 30px;
            max-width: 400px;
        }

        .cta-btn {
            display: inline-block;
            padding: 12px 30px;
            background: var(--gradient-accent);
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 25px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: transform 0.2s;
        }

        /* Floating Logo Badge */
        .logo-badge {
            position: absolute;
            top: 25%;
            right: 15%; /* Position relative to screen width */
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.4);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            z-index: 20;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .logo-badge img {
            width: 70px;
            margin-bottom: 10px;
        }

        .logo-badge p {
            font-size: 0.8rem;
            font-weight: 700;
            color: #0f172a; /* Dark text for better visibility on glass */
            line-height: 1.2;
        }

        /* Illustration at Bottom */
        .illustration-container {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 100%;
            height: 45%; 
            pointer-events: none;
            z-index: 5;
            display: flex;
            justify-content: flex-end;
            align-items: flex-end;
        }

        .illustration-container img {
            max-width: 500px;
            width: 80%;
            object-fit: contain;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .bg-shape-right {
                width: 55%; 
                clip-path: polygon(25% 0%, 100% 0%, 100% 100%, 0% 100%);
            }
            .text-section {
                width: 60%;
            }
            .logo-badge {
                width: 160px;
                height: 160px;
                right: 10%;
                top: 20%;
            }
        }

        @media (max-width: 768px) {
            /* Mobile Layout Matching Screenshot */
            .bg-shape-right {
                width: 40%;
                background: linear-gradient(135deg, rgba(37, 99, 235, 0.8) 0%, rgba(30, 64, 175, 0.9) 100%);
                clip-path: polygon(0 0%, 100% 0%, 100% 100%, 40% 100%);
            }
            
            .text-section {
                width: 100%;
                padding-right: 20%; /* Space for the diagonal cut */
                padding-top: 60px;
            }

            .logo-badge {
                right: 5%;
                top: 18%;
                width: 140px;
                height: 140px;
                background: rgba(255, 255, 255, 0.3);
                border: 2px solid rgba(255, 255, 255, 0.5);
            }

            .logo-badge img {
                width: 50px;
            }

            .illustration-container {
                justify-content: flex-end;
            }
            
            .illustration-container img {
                width: 90%;
                max-width: 400px;
            }
        }

        @media (max-width: 480px) {
             .bg-shape-right {
                width: 35%;
                clip-path: polygon(0 0%, 100% 0%, 100% 100%, 20% 100%);
            }

            .text-section h1 {
                font-size: 2rem;
            }
            
            .text-section {
                padding-top: 40px;
                max-width: 70%;
            }

            .logo-badge {
                width: 130px;
                height: 130px;
                top: 15%;
                right: 5%;
            }
            
            .illustration-container img {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo-container">
            <img src="../Assets/image/logo.png" alt="SMS Logo">
            <span>School Management</span>
        </div>
        <div class="menu-btn">
            <i class="fas fa-bars"></i>
        </div>
    </nav>

    <div class="page-container">
        <!-- Backgrounds -->
        <div class="bg-doodles"></div>
        <div class="bg-shape-right"></div>

        <!-- Floating Badge -->
        <div class="logo-badge">
            <img src="../Assets/image/logo.png" alt="Badge Logo">
            <p>School<br>Management System</p>
        </div>

        <!-- Main Content -->
        <div class="content-area">
            <div class="text-section">
                <h1>
                    <span class="text-dark">WELCOME</span><br>
                    <span class="text-dark">TO</span><br>
                    <span class="text-blue">SCHOOL</span><br>
                    <span class="text-blue">MANAGEMENT</span><br>
                    <span class="text-blue">SYSTEM</span>
                </h1>
                <p>Efficiently manage student records, faculty activities, and school operations — all in one place.</p>
                <a href="../auth/Login.php" class="cta-btn">Get Started</a>
            </div>
        </div>

        <!-- Illustration -->
        <div class="illustration-container">
            <img src="../Assets/image/hero.png" alt="Illustration">
        </div>
    </div>

</body>
</html>
