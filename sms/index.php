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
            /* Primary Colors */
            --primary: #2563eb;       /* Bright Blue */
            --primary-dark: #1e40af;  /* Darker Blue */
            --secondary: #3b82f6;     /* Light Blue Accent */
            
            /* Gradients */
            --gradient-main: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); 
            --gradient-accent: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            
            /* Neutral Colors */
            --text-main: #1e293b;
            --text-secondary: #64748b;
            --white: #ffffff;
            
            /* Shadows */
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 1.2rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-container img {
            width: 40px;
            height: auto;
        }

        .logo-container span {
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 1.1rem;
            letter-spacing: -0.5px;
        }

        .menu-btn {
            font-size: 1.25rem;
            color: var(--primary);
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            background: #eff6ff;
            transition: all 0.3s ease;
        }

        .menu-btn:hover {
            background: #dbeafe;
            color: var(--primary-dark);
        }

        /* Main Layout */
        .main-wrapper {
            position: relative;
            flex: 1;
            display: flex;
            padding-top: 80px; /* Navbar height */
            height: 100vh;
            overflow: hidden;
        }

        /* Left Side (Text) */
        .content-left {
            flex: 1;
            padding: 2rem 5% 2rem 8%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center; /* Center horizontally */
            text-align: center;  /* Center text */
            z-index: 10;
        }

        .hero-text {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-text h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: var(--text-main);
            text-transform: uppercase;
        }

        .text-welcome {
            display: block;
            color: var(--text-main);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .text-highlight {
            color: var(--primary);
        }

        .hero-text p {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin: 0 auto 2.5rem;
            line-height: 1.6;
            max-width: 600px;
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 16px 40px;
            background: var(--gradient-accent);
            color: white;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4);
            transition: all 0.3s ease;
            width: fit-content;
        }

        .cta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.5);
        }

        /* Right Side (Graphics/Background) */
        .content-right {
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            z-index: 1;
            /* Less aggressive clip, or remove specifically for centered design if needed, 
               but let's keep it for style and adjust z-index */
            clip-path: polygon(15% 0%, 100% 0%, 100% 100%, 0% 100%);
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.05) 100%),
                        url('../Assets/image/background.jpg') center/cover no-repeat;
        }

        .content-right::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(37,99,235,0.85), rgba(30,64,175,0.95));
            opacity: 0.9;
            z-index: 1;
        }
        
        /* Doodle Pattern Overlay */
        .doodle-overlay {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(#ffffff 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.1;
            z-index: 2;
        }

        /* Logo Badge */
        .logo-badge {
            position: absolute;
            top: 20%;
            right: 10%;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            z-index: 10;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: float 6s ease-in-out infinite;
        }

        .logo-badge img {
            width: 60px;
            margin-bottom: 0.5rem;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }

        .logo-badge h3 {
            color: white;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Illustration Container */
        .illustration-container {
            position: absolute;
            bottom: 50px;
            right: 5%;
            width: 450px;
            z-index: 5;
            pointer-events: none;
            /* Make it responsive */
            max-width: 45%; 
        }

        .illustration-container img {
            width: 100%;
            height: auto;
            display: block;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.15));
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        /* Mobile / Tablet Responsive Design */
        @media (max-width: 1024px) {
            .main-wrapper {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
                padding-bottom: 2rem;
            }

            .content-left {
                width: 100%;
                padding: 3rem 5% 0; /* Add top padding */
                justify-content: flex-start;
                flex: none; /* Don't grow, take content height */
            }

            .hero-text h1 {
                font-size: 2.5rem;
            }

            /* Adjust background for mobile */
            .content-right {
                position: relative;
                width: 100%;
                height: 400px; /* Fixed height for graphics area on mobile */
                clip-path: none; /* Remove diagonal clip on mobile usually looks cleaner or use a bottom wave */
                clip-path: polygon(0 15%, 100% 0, 100% 100%, 0% 100%);
                background: linear-gradient(135deg, rgba(37,99,235,0.9), rgba(30,64,175,0.95));
                margin-top: 2rem;
                border-radius: 30px 30px 0 0;
            }

            .content-right::before {
                opacity: 0.6;
            }

            .illustration-container {
                display: block; /* Ensure it is visible */
                position: absolute;
                bottom: 0px;
                right: 50%;
                transform: translateX(50%);
                width: 300px;
                max-width: 80%;
            }

            .logo-badge {
                display: none; /* Hide badge on mobile to simplify/avoid overlap */
            }
            
            /* Center the CTA/Button */
            .cta-btn {
                width: 100%;
                max-width: 300px;
            }
        }

        @media (max-width: 480px) {
            .hero-text h1 {
                font-size: 2rem;
            }
            
            .content-right {
                height: 350px;
                clip-path: polygon(0 10%, 100% 0, 100% 100%, 0% 100%);
            }
            
            .illustration-container {
                width: 250px;
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

    <div class="main-wrapper">
        <div class="content-left">
            <div class="hero-text">
                <span class="text-welcome">Welcome To</span>
                <h1 class="text-highlight">School Management<br>System</h1>
                <p>Efficiently manage student records, faculty activities, and school operations — all in one accessible platform.</p>
                <a href="../auth/Login.php" class="cta-btn">Get Started</a>
            </div>
        </div>

        <div class="content-right">
            <div class="doodle-overlay"></div>
            
            <!-- Logo badge floating (hidden on mobile) -->
            <div class="logo-badge">
                <img src="../Assets/image/logo.png" alt="School Logo">
                <h3>School<br>Management<br>System</h3>
            </div>
            
            <!-- Hero Image / Illustration -->
            <div class="illustration-container">
                <img src="../Assets/image/hero.png" alt="School Management Hero">
            </div>
        </div>
    </div>

</body>
</html>
