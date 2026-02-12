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
            --primary-dark: #0f172a;
            --text-main: #020617;
            --text-light: #64748b;
            --gradient-blue: linear-gradient(135deg, rgba(37, 99, 235, 0.9) 0%, rgba(29, 78, 216, 0.95) 100%);
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
            padding: 20px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
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
            font-size: 1rem;
        }

        .menu-btn {
            font-size: 1.4rem;
            color: var(--primary);
            cursor: pointer;
        }

        /* Main Container */
        .page-container {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        /* Background Graphics */
        .bg-white-left {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            z-index: 0;
        }

        /* Blue Diagonal Section */
        .bg-blue-right {
            position: absolute;
            top: 0;
            right: 0;
            width: 65%; /* Adjusted width */
            height: 100%;
            background: var(--gradient-blue), url('../Assets/image/background.jpg');
            background-blend-mode: overlay;
            background-size: cover;
            /* Steep diagonal cut as requested */
            clip-path: polygon(30% 0%, 100% 0%, 100% 100%, 15% 100%);
            z-index: 1;
        }

        /* Doodle Pattern (Faint) on left */
        .bg-doodle {
            position: absolute;
            top: 0;
            left: 0;
            width: 40%;
            height: 100%;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.5;
            z-index: 0;
        }

        /* Content Area */
        .content-area {
            position: relative;
            z-index: 10;
            height: 100%;
            padding: 0 5%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .text-section {
            width: 100%;
            max-width: 500px;
            margin-top: -60px; /* Shift up slightly */
        }

        .text-section h1 {
            font-size: clamp(2rem, 5vw, 3.2rem);
            line-height: 1.2;
            font-weight: 800;
            margin-bottom: 25px;
            letter-spacing: -0.5px;
        }

        .text-welcome {
            color: var(--primary-dark); /* Dark Navy */
            display: block;
        }

        .text-brand {
            color: #2563eb; /* Bright Blue */
            display: block;
        }

        .text-section p {
            font-size: 1rem;
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 35px;
            max-width: 350px;
        }

        .cta-btn {
            display: inline-block;
            padding: 14px 35px;
            background: linear-gradient(90deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            font-weight: 600;
            border-radius: 30px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
            transition: transform 0.2s;
        }

        /* Floating Badge */
        .badge-container {
            position: absolute;
            top: 25%;
            left: 40%; /* Adjusted to sit on the diagonal line */
            transform: translateX(-50%);
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            z-index: 20;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .badge-container img {
            width: 60px;
            margin-bottom: 8px;
            filter: drop-shadow(0 2px 5px rgba(0,0,0,0.1));
        }

        .badge-container span {
            font-size: 0.8rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        /* Illustration at bottom right */
        .illustration-container {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 100%;
            max-width: 550px;
            z-index: 5;
            pointer-events: none;
            display: flex;
            justify-content: flex-end;
            align-items: flex-end;
        }

        .illustration-container img {
            width: 85%;
            height: auto;
        }

        /* Responsive Adjustments */
        @media (max-width: 1024px) {
            .bg-blue-right {
                width: 60%;
                clip-path: polygon(35% 0%, 100% 0%, 100% 100%, 20% 100%);
            }
            .badge-container {
                left: 45%;
            }
        }

        @media (max-width: 768px) {
            /* Exact Mobile Match */
            .bg-blue-right {
                width: 55%;
                /* Steep slant */
                clip-path: polygon(25% 0%, 100% 0%, 100% 100%, 10% 100%);
            }

            .badge-container {
                width: 140px;
                height: 140px;
                top: 20%;
                left: 50%; /* Center it roughly on the line */
                margin-left: 20px; /* Fine tune */
            }

            .badge-container img {
                width: 45px;
            }

            .text-section {
                margin-top: 50px;
                max-width: 60%; /* Ensure text wraps before hitting the blue too much */
            }

            .text-section h1 {
                font-size: 1.8rem;
            }

            .illustration-container {
                width: 100%;
                max-width: 100%;
                right: -20px;
            }
            
            .illustration-container img {
                width: 100%;
                max-width: 400px;
            }
        }

        @media (max-width: 480px) {
             .text-section h1 {
                font-size: 2rem;
            }
            
            .bg-blue-right {
                width: 50%;
                clip-path: polygon(20% 0%, 100% 0%, 100% 100%, 5% 100%);
            }
            
            .badge-container {
                left: 60%;
                top: 18%;
                width: 130px;
                height: 130px;
                margin: 0;
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
        <div class="bg-white-left">
            <div class="bg-doodle"></div>
        </div>
        <div class="bg-blue-right"></div>

        <!-- Floating Badge -->
        <div class="badge-container">
            <img src="../Assets/image/logo.png" alt="Logo">
            <span>School<br>Management System</span>
        </div>

        <!-- Text Content -->
        <div class="content-area">
            <div class="text-section">
                <h1>
                    <span class="text-welcome">WELCOME</span>
                    <span class="text-welcome">TO</span>
                    <span class="text-brand">SCHOOL</span>
                    <span class="text-brand">MANAGEMENT</span>
                    <span class="text-brand">SYSTEM</span>
                </h1>
                <p>Efficiently manage student records, faculty activities, and school operations — all in one accessible platform.</p>
                <a href="../auth/Login.php" class="cta-btn">Get Started</a>
            </div>
        </div>

        <!-- Illustration -->
        <div class="illustration-container">
            <img src="../Assets/image/hero.png" alt="Education Illustration">
        </div>
    </div>

</body>
</html>
