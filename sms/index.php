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
            /* Global Background using the doodle image */
            background: url('../Assets/image/background.jpg') center/cover no-repeat fixed;
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
            background: rgba(255, 255, 255, 0.8); /* Slight legible backing */
            padding: 8px 15px;
            border-radius: 20px;
            backdrop-filter: blur(5px);
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

        /* Right Side Blue Overlay - 35% width */
        .bg-blue-right {
            position: absolute;
            top: 0;
            right: 0;
            width: 35%; 
            height: 100%;
            background: var(--gradient-blue);
            /* Steep diagonal cut - line position per request */
            clip-path: polygon(0 0, 100% 0, 100% 100%, 20% 100%); 
            z-index: 1;
            /* Texture blend */
            mix-blend-mode: multiply; 
        }
        
        /* Optional: Add a second layer for the blue to ensure it's not too transparent if multiply makes it too dark */
        .bg-blue-backdrop {
             position: absolute;
            top: 0;
            right: 0;
            width: 35%; 
            height: 100%;
            background: rgba(37, 99, 235, 0.8);
            clip-path: polygon(0 0, 100% 0, 100% 100%, 20% 100%); 
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
            max-width: 600px;
            margin-top: -50px;
            /* Add backdrop to ensure text readability over doodle background */
            background: rgba(255, 255, 255, 0.85);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
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
            max-width: 450px;
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

        /* Floating Badge - Right Side, 50% Opacity */
        .badge-container {
            position: absolute;
            top: 20%;
            right: 8%; /* Centered in the 35% blue strip roughly */
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
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            opacity: 0.5; /* Requested 50% opacity */
        }

        .badge-container img {
            width: 35px;
            margin-bottom: 5px;
        }

        .badge-container span {
            font-size: 0.6rem;
            font-weight: 700;
            color: #1e3a8a;
            line-height: 1.2;
        }

        /* Illustration - Smaller (50%) */
        .illustration-container {
            position: absolute;
            bottom: 5%;
            right: 0;
            width: 35%; /* Confined to the blue area */
            display: flex;
            justify-content: center;
            align-items: flex-end;
            z-index: 5;
            pointer-events: none;
        }

        .illustration-container img {
            width: 80%; /* Relative to container, making it visually smaller on page */
            max-width: 300px; /* Cap size */
            height: auto;
            filter: drop-shadow(-5px 5px 15px rgba(0,0,0,0.2));
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .bg-blue-right, .bg-blue-backdrop {
                width: 45%;
                clip-path: polygon(0 0, 100% 0, 100% 100%, 15% 100%);
            }
             .badge-container {
                right: 12%; 
            }
             .illustration-container {
                width: 45%;
            }
        }

        @media (max-width: 768px) {
            /* Mobile View */
            body {
                background-position: left center;
            }
            
            .bg-blue-right, .bg-blue-backdrop {
                width: 100%;
                height: 40%;
                top: auto;
                bottom: 0;
                clip-path: polygon(0 20%, 100% 0, 100% 100%, 0% 100%);
            }
            
            .text-section {
                padding: 25px;
                margin-top: 0;
                background: rgba(255, 255, 255, 0.9);
            }
            
            .text-section h1 {
                font-size: 2rem;
            }

            .badge-container {
                top: auto;
                bottom: 35%;
                right: 5%;
                width: 90px;
                height: 90px;
            }
            
            .badge-container img {
                width: 25px;
            }
            
            .badge-container span {
                font-size: 0.5rem;
            }

            .illustration-container {
                width: 50%;
                right: 0;
                bottom: 0;
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
        <!-- Blue Overlays -->
        <div class="bg-blue-backdrop"></div>
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
