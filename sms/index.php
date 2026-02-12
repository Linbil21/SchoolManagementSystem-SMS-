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
            z-index: 10;
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
        }
        
        .text-highlight {
            color: var(--primary);
        }

        .hero-text p {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin-bottom: 2.5rem;
            line-height: 1.6;
            max-width: 500px;
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
            width: 55%;
            height: 100%;
            z-index: 1;
            /* Create the diagonal shape */
            clip-path: polygon(20% 0%, 100% 0%, 100% 100%, 0% 100%);
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
        
        /* Doodle Pattern Overlay (Optional simulation) */
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
            top: 50%;
            left: 50%; /* Center relative to content-right */
            transform: translate(-50%, -50%);
            width: 280px;
            height: 280px;
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
            width: 100px;
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }

        .logo-badge h3 {
            color: white;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Illustration Container */
        .illustration-container {
            position: absolute;
            bottom: 0;
            right: 5%; 
            width: 400px;
            z-index: 5;
            pointer-events: none;
        }

        .illustration-container img {
            width: 100%;
            height: auto;
            display: block;
        }

        @keyframes float {
            0% { transform: translate(-50%, -50%) translateY(0px); }
            50% { transform: translate(-50%, -50%) translateY(-20px); }
            100% { transform: translate(-50%, -50%) translateY(0px); }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .content-left {
                width: 100%;
                text-align: center;
                align-items: center;
                padding-top: 4rem;
            }
            
            .content-right {
                width: 100%;
                height: 50vh;
                top: auto;
                bottom: 0;
                clip-path: polygon(0 15%, 100% 0, 100% 100%, 0% 100%);
            }
            
            .logo-badge {
                width: 200px;
                height: 200px;
                top: 60%; /* Position inside the blue area */
                left: 50%;
                transform: translate(-50%, -50%);
            }
            
            .logo-badge img {
                width: 70px;
            }
            
            .hero-text h1 {
                font-size: 2.5rem;
            }
            
            .illustration-container {
                display: none; /* Hide illustration on tablet/mobile to keep clean */
            }
        }

        @media (max-width: 480px) {
            .content-right {
                height: 45vh;
                clip-path: polygon(0 10%, 100% 0, 100% 100%, 0% 100%);
            }

            .logo-badge {
                width: 160px;
                height: 160px;
                top: 55%;
            }

            .logo-badge img {
                width: 60px;
            }

            .hero-text h1 {
                font-size: 2rem;
                margin-bottom: 1rem;
            }
            
            .nav {
                padding: 1rem;
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
                <h1 class="text-welcome">Welcome To</h1>
                <h1 class="text-highlight">School<br>Management<br>System</h1>
                <p>Efficiently manage student records, faculty activities, and school operations — all in one accessible platform.</p>
                <a href="../auth/Login.php" class="cta-btn">Get Started</a>
            </div>
        </div>

        <div class="content-right">
            <div class="doodle-overlay"></div>
            <div class="logo-badge">
                <img src="../Assets/image/logo.png" alt="School Logo">
                <h3>School<br>Management<br>System</h3>
            </div>
            
            <!-- Illustration positioned at bottom right if on desktop -->
            <div class="illustration-container">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/students-studying-online-2995844-2524673.png" alt="Education Illustration">
            </div>
        </div>
    </div>

</body>
</html>
