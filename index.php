<?php
session_start();
require_once 'Database/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS - Welcome to School Management System</title>
    <link rel="icon" type="image/png" href="Assets/image/logo.png">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1648bc;
            --accent: #3b82f6;
            --white: #ffffff;
            --text-main: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #fff;
            overflow-x: hidden;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Diagonal Background Wrapper */
        .page-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: url('Assets/image/background.jpg') center center;
            background-size: cover;
        }

        /* The blue angled overlay */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.85) 0%, rgba(255, 255, 255, 0.8) 45%, rgba(22, 72, 188, 0.7) 100%);
            z-index: 1;
        }

        /* Diagonal section cut like in screenshot */
        .diagonal-cut {
            position: absolute;
            top: 0;
            right: 0;
            width: 45%;
            height: 100%;
            background: rgba(30, 64, 175, 0.1);
            clip-path: polygon(25% 0%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 2;
        }

        /* Navbar */
        nav {
            position: relative;
            width: 100%;
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            z-index: 100;
        }

        .logo-nav {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-nav img {
            width: 35px;
        }

        .logo-nav span {
            font-weight: 800;
            color: var(--primary);
            font-size: 0.95rem;
            text-transform: capitalize;
        }

        .menu-icon {
            font-size: 1.5rem;
            color: var(--primary);
            cursor: pointer;
        }

        /* Hero Content */
        .hero-section {
            position: relative;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 8%;
            z-index: 10;
        }

        /* Floating Logo Circle */
        .logo-main-container {
            position: absolute;
            top: 15%;
            right: 15%;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            text-align: center;
            padding: 20px;
            animation: fadeInScale 1s ease-out;
            z-index: 30;
        }

        .logo-main-container img {
            width: 100px;
            margin-bottom: 10px;
        }

        .logo-main-container p {
            font-weight: 800;
            font-size: 0.8rem;
            color: var(--primary);
            line-height: 1.2;
            text-transform: uppercase;
        }

        /* Text Area */
        .intro-text {
            max-width: 600px;
            animation: slideUp 0.8s ease-out;
            z-index: 40;
        }

        .intro-text h1 {
            font-size: 3.8rem;
            font-weight: 900;
            color: var(--primary);
            line-height: 1.1;
            margin-bottom: 25px;
            letter-spacing: -1px;
        }

        .intro-text p {
            color: #4b5563;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 30px;
            max-width: 450px;
        }

        /* Get Started Button */
        .btn-get-started {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 10px 25px rgba(22, 72, 188, 0.3);
            transition: 0.3s;
        }

        .btn-get-started:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(22, 72, 188, 0.4);
            filter: brightness(1.1);
        }

        /* Illustration at bottom */
        .illustration-container {
            position: absolute;
            bottom: -20px;
            right: 0;
            width: 100%;
            max-width: 500px;
            z-index: 5;
            pointer-events: none;
        }

        .illustration-container img {
            width: 100%;
        }

        /* Mobile View Specifics */
        @media (max-width: 1024px) {
            .logo-main-container {
                position: relative;
                top: 0;
                right: 0;
                margin: 0 auto 40px;
                width: 200px;
                height: 200px;
            }
            .hero-section {
                padding: 50px 5% 100px;
                text-align: center;
                align-items: center;
            }
            .intro-text h1 {
                font-size: 2.8rem;
            }
            .intro-text p {
                margin: 0 auto 30px;
            }
            .diagonal-cut {
                width: 100%;
                height: 50%;
                top: auto;
                bottom: 0;
                clip-path: polygon(0 25%, 100% 0%, 100% 100%, 0% 100%);
            }
            .illustration-container {
                max-width: 350px;
                margin: 0 auto;
                left: 50%;
                transform: translateX(-50%);
            }
        }

        @media (max-width: 480px) {
            .intro-text h1 {
                font-size: 2.2rem;
            }
            nav {
                padding: 15px 5%;
            }
            .logo-main-container {
                width: 160px;
                height: 160px;
            }
        }

        /* Animations */
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="page-wrapper">
        <!-- Navbar -->
        <nav>
            <div class="logo-nav">
                <img src="Assets/image/logo.png" alt="SMS Logo">
                <span>School Management</span>
            </div>
            <div class="menu-icon">
                <i class="fas fa-bars"></i>
            </div>
        </nav>

        <div class="overlay"></div>
        <div class="diagonal-cut"></div>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="logo-main-container">
                <img src="Assets/image/logo.png" alt="Logo">
                <p>School<br>Management System</p>
            </div>

            <div class="intro-text">
                <h1>WELCOME<br>TO<br>SCHOOL<br>MANAGEMENT<br>SYSTEM</h1>
                <p>Efficiently manage student records, faculty activities, and school operations — all in one place.</p>
                <a href="auth/Login.php" class="btn-get-started">Get Started</a>
            </div>
        </section>

        <!-- Illustration -->
        <div class="illustration-container">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/online-education-2863486-2374661.png" alt="Illustration">
        </div>
    </div>

</body>
</html>
