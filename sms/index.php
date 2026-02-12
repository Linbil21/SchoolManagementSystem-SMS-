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
            --gradient-blue: linear-gradient(135deg, rgba(37, 99, 235, 1) 0%, rgba(30, 64, 175, 1) 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #ffffff;
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
            padding: 25px 5%;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            z-index: 100;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-container img {
            width: 35px;
        }

        .logo-container span {
            font-weight: 700;
            color: var(--primary);
            font-size: 1rem;
        }

        /* Main Container */
        .page-container {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        /* Left Side Background */
        .bg-white-left {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            z-index: 0;
        }
        
        /* Optional faint doodles on left only */
        .bg-doodle-left {
            position: absolute;
            top: 0;
            left: 0;
            width: 40%;
            height: 100%;
            background-image: radial-gradient(#e2e8f0 1.5px, transparent 1.5px);
            background-size: 25px 25px;
            opacity: 0.6;
        }

        /* Right Side Blue Background */
        .bg-blue-right {
            position: absolute;
            top: 0;
            right: 0;
            width: 60%; 
            height: 100%;
            background: var(--gradient-blue);
            /* Overlay texture/image if desired, keeping it clean blue per 'Picture 2' mobile look */
            background-blend-mode: multiply;
            /* Steep diagonal cut */
            clip-path: polygon(30% 0%, 100% 0%, 100% 100%, 15% 100%);
            z-index: 1;
        }
        
        /* Add faint texture to blue area to give depth */
        .bg-blue-right::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.3;
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
            max-width: 550px;
            margin-top: -50px;
        }

        .text-section h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            line-height: 1.1;
            font-weight: 800;
            margin-bottom: 25px;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .text-welcome {
            color: var(--primary-dark);
            display: block;
        }

        .text-brand {
            color: #2563eb;
            display: block;
        }

        .text-section p {
            font-size: 1rem;
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 35px;
            max-width: 420px;
        }

        .cta-btn {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(90deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            font-weight: 600;
            border-radius: 30px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
            transition: transform 0.2s;
        }
        
        .cta-btn:hover {
            transform: translateY(-2px);
        }

        /* Floating Badge - Center on line */
        .badge-container {
            position: absolute;
            top: 25%;
            left: 48%; /* Adjust to sit exactly on the edge */
            transform: translateX(-50%);
            width: 160px;
            height: 160px;
            background: rgba(255, 255, 255, 0.15); /* Translucent */
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            z-index: 20;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .badge-container img {
            width: 50px;
            margin-bottom: 8px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .badge-container span {
            font-size: 0.75rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
        }

        /* Illustration */
        .illustration-container {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 100%;
            max-width: 600px;
            z-index: 5;
            pointer-events: none;
            display: flex;
            justify-content: flex-end;
            align-items: flex-end;
        }

        .illustration-container img {
            width: 90%;
            height: auto;
            /* Enhance shadow for depth */
            filter: drop-shadow(-10px 10px 20px rgba(0,0,0,0.15)); 
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .bg-blue-right {
                width: 65%;
                clip-path: polygon(30% 0%, 100% 0%, 100% 100%, 15% 100%);
            }
            .badge-container {
                left: 55%;
            }
        }

        @media (max-width: 768px) {
            /* Mobile Layout per 'Picture 2' */
            .bg-blue-right {
                width: 55%;
                clip-path: polygon(25% 0%, 100% 0%, 100% 100%, 10% 100%);
            }
            
            .text-section {
                padding-right: 20px;
                margin-top: 50px;
            }
            
            .text-section h1 {
                font-size: 2rem;
            }

            .badge-container {
                width: 130px;
                height: 130px;
                left: 60%;
                top: 20%;
            }
            
            .badge-container img {
                width: 40px;
            }

            .illustration-container {
                right: -20px;
            }
            
            .illustration-container img {
                width: 100%;
                max-width: 450px;
            }
        }
        
         @media (max-width: 480px) {
            .bg-blue-right {
                width: 50%;
                clip-path: polygon(20% 0%, 100% 0%, 100% 100%, 5% 100%);
            }
            .badge-container {
                left: 65%;
                top: 18%;
            }
            .text-section h1 {
                font-size: 2.2rem; /* Make heading slightly bigger on small screens */
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
    </nav>

    <div class="page-container">
        <!-- Backgrounds -->
        <div class="bg-white-left">
            <div class="bg-doodle-left"></div>
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
