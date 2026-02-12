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
            padding: 20px 5%;
            display: flex;
            justify-content: flex-start; /* Align left */
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

        /* Main Container */
        .page-container {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        /* Background - "Blue Shadow" feel */
        .bg-shadow-right {
            position: absolute;
            top: 0;
            right: 0;
            width: 60%; 
            height: 100%;
            /* Soft blue gradient, low opacity */
            background: linear-gradient(120deg, rgba(255,255,255,0) 0%, rgba(37, 99, 235, 0.1) 100%);
            /* Steep diagonal cut */
            clip-path: polygon(20% 0%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 0;
        }

        /* Additional Right Side Pattern/Texture container */
        .bg-pattern-right {
           position: absolute;
           top: 0;
           right: 0;
           width: 55%;
           height: 100%;
           /* Using a radial gradient to simulate dots or texture if image missing */
           background-image: radial-gradient(#3b82f6 1px, transparent 1px);
           background-size: 30px 30px;
           opacity: 0.1; /* Very faint */
           clip-path: polygon(20% 0%, 100% 0%, 100% 100%, 0% 100%);
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
            max-width: 550px;
            margin-top: -40px; 
        }

        .text-section h1 {
            font-size: clamp(2.5rem, 5vw, 3.8rem);
            line-height: 1.1;
            font-weight: 900;
            margin-bottom: 25px;
            letter-spacing: -1px;
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
            max-width: 400px;
        }

        .cta-btn {
            display: inline-block;
            padding: 14px 40px;
            background: #2563eb;
            color: white;
            font-weight: 600;
            border-radius: 30px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: transform 0.2s;
        }
        
        .cta-btn:hover {
            transform: translateY(-2px);
            background: #1d4ed8;
        }

        /* Floating Badge - Top Right & Smaller */
        .badge-container {
            position: absolute;
            top: 100px; /* Below navbar */
            right: 5%;
            width: 120px;
            height: 120px;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            z-index: 20;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .badge-container img {
            width: 40px;
            margin-bottom: 5px;
        }

        .badge-container span {
            font-size: 0.65rem;
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
            max-width: 650px;
            z-index: 5;
            pointer-events: none;
            display: flex;
            justify-content: flex-end;
            align-items: flex-end;
        }

        .illustration-container img {
            width: 90%;
            max-width: 600px;
            height: auto;
        }

        /* Responsive Adjustments */
        @media (max-width: 1024px) {
            .bg-shadow-right {
                width: 70%;
                clip-path: polygon(30% 0%, 100% 0%, 100% 100%, 10% 100%);
            }
        }

        @media (max-width: 768px) {
            .bg-shadow-right {
                width: 80%;
                opacity: 0.5;
            }
            
            .text-section h1 {
                font-size: 2.2rem;
            }
            
            .badge-container {
                top: 80px;
                right: 20px;
                width: 90px;
                height: 90px;
            }
            
            .badge-container img {
                width: 30px;
            }
            
             .badge-container span {
                font-size: 0.5rem;
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
    </nav>

    <div class="page-container">
        <!-- Backgrounds -->
        <div class="bg-shadow-right"></div>
        <div class="bg-pattern-right"></div>

        <!-- Floating Badge (Top Right) -->
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
