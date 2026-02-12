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
            --text-main: #0f172a;
            --text-secondary: #64748b;
            --gradient-accent: linear-gradient(90deg, #2563eb 0%, #3b82f6 100%);
            --blue-overlay: linear-gradient(135deg, rgba(219, 234, 254, 0.4) 0%, rgba(37, 99, 235, 0.1) 100%);
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

        /* Navbar (Hidden on strictly mobile landing usually, but keeping simplistic) */
        nav {
            position: absolute;
            top: 0;
            width: 100%;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-container img {
            width: 30px;
        }

        .logo-container span {
            font-weight: 600;
            color: var(--primary);
            font-size: 0.9rem;
        }

        .menu-btn {
            font-size: 1.2rem;
            color: var(--primary);
            cursor: pointer;
            padding: 8px;
            background: rgba(239, 246, 255, 0.5);
            border-radius: 6px;
        }

        /* Main Container */
        .page-container {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        /* Background Graphics */
        /* Left side doodles (using an image or CSS radial gradient pattern) */
        .bg-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            /* Subtle doodle-like pattern */
            background-image: 
                radial-gradient(#cbd5e1 1.5px, transparent 1.5px),
                radial-gradient(#cbd5e1 1.5px, transparent 1.5px);
            background-size: 30px 30px;
            background-position: 0 0, 15px 15px;
            opacity: 0.3;
            z-index: 0;
        }

        /* The Right-side Blue Shape */
        .bg-shape-right {
            position: absolute;
            top: 0;
            right: 0;
            width: 50%; /* Start broadly */
            height: 100%;
            background: linear-gradient(180deg, rgba(239,246,255,0.8) 0%, rgba(59,130,246,0.3) 100%);
            /* The diagonal split */
            clip-path: polygon(25% 0%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 1;
            backdrop-filter: blur(2px);
            border-left: 1px solid rgba(255,255,255,0.2);
        }

        /* Content Positioning */
        .content-area {
            position: relative;
            z-index: 10;
            height: 100%;
            padding: 0 8%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .text-section {
            max-width: 55%;
            margin-top: -50px; /* Slight offset upwards */
        }

        .text-section h1 {
            font-size: clamp(2rem, 5vw, 3rem);
            line-height: 1.25;
            font-weight: 700;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .text-line-1, .text-line-2 {
            display: block;
            color: #0f172a; /* Dark Navy */
        }

        .text-highlight {
            display: block;
            color: #2563eb; /* Bright Blue */
            font-weight: 800; /* Extra bold */
        }

        .text-section p {
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 30px;
            width: 90%;
        }

        /* Button */
        .cta-btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(90deg, #1d4ed8 0%, #3b82f6 100%);
            color: #fff;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 25px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        /* Floating Logo Circle */
        .logo-circle {
            position: absolute;
            top: 22%;
            right: 22%; /* Center-ish horizontally on the diagonal line */
            width: 140px;
            height: 140px;
            background: rgba(255, 255, 255, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(8px);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            z-index: 20;
        }

        .logo-circle img {
            width: 50px;
            margin-bottom: 8px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .logo-circle span {
            font-size: 0.7rem;
            font-weight: 700;
            color: #0f172a;
            text-align: center;
            line-height: 1.2;
        }

        /* Bottom Illustration (Optional, faintly visible behind) */
        .illustration-bg {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 60%;
            opacity: 0.8;
            z-index: 2;
            pointer-events: none;
        }

        .illustration-bg img {
            width: 100%;
            display: block;
            /* Fade it into the bottom */
            mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
            -webkit-mask-image: linear-gradient(to top, transparent 0%, black 20%);
        }

        /* Mobile Specific Overrides to MATCH SCREENSHOT */
        @media (max-width: 768px) {
            .bg-shape-right {
                width: 45%;
                background: linear-gradient(180deg, rgba(219,234,254,0.6) 0%, rgba(59,130,246,0.3) 100%);
                /* The specific slant */
                clip-path: polygon(15% 0%, 100% 0%, 100% 100%, 0% 100%);
                border-left: 1px solid rgba(255,255,255,0.4);
            }
            
            .text-section {
                max-width: 65%;
                margin-top: 20px;
                padding-right: 10px;
            }
            
            .text-section h1 {
                font-size: 1.8rem;
                margin-bottom: 15px;
            }

            .logo-circle {
                width: 120px;
                height: 120px;
                top: 18%;
                right: 12%; 
                background: rgba(255,255,255,0.3);
            }

            .logo-circle img {
                width: 45px;
            }
            
            .illustration-bg {
                width: 70%;
                opacity: 0.9;
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
        <!-- Background Assets -->
        <div class="bg-pattern"></div>
        <div class="bg-shape-right"></div>
        
        <!-- Center Floating Logo -->
        <div class="logo-circle">
            <img src="../Assets/image/logo.png" alt="Logo">
            <span>School<br>Management System</span>
        </div>

        <!-- Main Text Content -->
        <div class="content-area">
            <div class="text-section">
                <h1>
                    <span class="text-line-1">WELCOME</span>
                    <span class="text-line-2">TO</span>
                    <br>
                    <span class="text-highlight">SCHOOL</span>
                    <span class="text-highlight">MANAGEMENT</span>
                    <span class="text-highlight">SYSTEM</span>
                </h1>
                <p>Efficiently manage student records, faculty activities, and school operations — all in one place.</p>
                <a href="../auth/Login.php" class="cta-btn">Get Started</a>
            </div>
        </div>

        <!-- Illustration -->
        <div class="illustration-bg">
             <!-- Using header/hero image as illustration or background graphic -->
            <img src="../Assets/image/hero.png" alt="School Background">
        </div>
    </div>

</body>
</html>
