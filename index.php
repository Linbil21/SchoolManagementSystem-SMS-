<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRAD System | Center for Research and Development</title>
    <link rel="icon" type="image/png" href="Assets/image/logo.png">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #0A58CA;
            --secondary-blue: #D0E3FF;
            --accent-blue: #007BFF;
            --dark-blue: #003366;
            --text-dark: #1E293B;
            --text-muted: #64748B;
            --white: #FFFFFF;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            background: var(--white);
        }

        h1, h2, h3, h4, .navbar-brand {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
        }

        /* --- Dynamic Background --- */
        .page-wrapper {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
        }

        .bg-split {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: url('Assets/image/crad_bg.png');
            background-size: 500px;
            background-repeat: repeat;
            opacity: 0.15;
        }

        .bg-diagonal {
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: linear-gradient(135deg, rgba(208, 227, 255, 0.4) 0%, rgba(10, 88, 202, 0.1) 100%);
            clip-path: polygon(25% 0%, 100% 0%, 100% 100%, 0% 100%);
            z-index: -1;
        }

        /* --- Navbar --- */
        .navbar {
            padding: 1.5rem 0;
            background: transparent;
            transition: all 0.4s ease;
        }

        .navbar.scrolled {
            padding: 1rem 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .navbar-brand img {
            height: 50px;
            width: auto;
        }

        .hamburger-menu {
            border: none;
            background: #F1F5F9;
            padding: 10px;
            border-radius: 8px;
            color: var(--primary-blue);
        }

        /* --- Hero Section --- */
        .hero {
            padding: 120px 0 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
        }

        .hero-content {
            z-index: 2;
        }

        .welcome-text {
            font-size: 2.5rem;
            color: var(--dark-blue);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .system-name {
            font-size: 4.5rem;
            color: var(--primary-blue);
            font-weight: 900;
            margin-bottom: 1.5rem;
            line-height: 1;
        }

        .system-description {
            font-size: 1.15rem;
            color: var(--text-dark);
            max-width: 500px;
            line-height: 1.6;
            margin-bottom: 2.5rem;
            font-weight: 500;
        }

        .btn-get-started {
            background: linear-gradient(to right, #0A58CA, #4D88FF);
            color: white !important;
            padding: 0.8rem 2.5rem;
            font-weight: 700;
            border-radius: 50px;
            border: none;
            box-shadow: 0 10px 20px rgba(10, 88, 202, 0.2);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-get-started:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(10, 88, 202, 0.3);
            filter: brightness(1.1);
        }

        /* --- Circular Logo and Illustrations --- */
        .hero-visuals {
            position: absolute;
            right: 0;
            top: 0;
            width: 50%;
            height: 100%;
            pointer-events: none;
        }

        .circular-logo-wrapper {
            position: absolute;
            top: 25%;
            right: 20%;
            width: 200px;
            height: 200px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            padding: 20px;
            z-index: 5;
            animation: float 5s ease-in-out infinite;
        }

        .circular-logo-wrapper img {
            width: 100%;
            height: auto;
        }

        .student-illustration {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 80%;
            max-width: 600px;
            z-index: 1;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Mobile Adjustments */
        @media (max-width: 991px) {
            .bg-diagonal {
                width: 100%;
                clip-path: polygon(0 40%, 100% 20%, 100% 100%, 0% 100%);
            }
            .hero {
                text-align: center;
                padding-top: 100px;
            }
            .system-name {
                font-size: 3rem;
            }
            .system-description {
                margin: 0 auto 2.5rem;
            }
            .hero-visuals {
                position: relative;
                width: 100%;
                height: 400px;
                margin-top: 3rem;
            }
            .circular-logo-wrapper {
                top: 0;
                right: 50%;
                transform: translateX(50%) !important;
                width: 150px;
                height: 150px;
            }
            .student-illustration {
                width: 100%;
                left: 0;
            }
        }
    </style>
</head>
<body>

    <div class="page-wrapper">
        <div class="bg-split"></div>
        <div class="bg-diagonal"></div>

        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="Assets/image/logo.png" alt="Logo">
                </a>
                
                <div class="ms-auto d-flex align-items-center">
                    <button class="hamburger-menu d-lg-none">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="collapse navbar-collapse d-none d-lg-block">
                        <ul class="navbar-nav me-4">
                            <li class="nav-item"><a class="nav-link fw-bold text-dark" href="#">Home</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold text-dark" href="#features">Services</a></li>
                        </ul>
                    </div>
                    <a href="auth/Login.php" class="btn btn-get-started d-none d-sm-block">
                        Portal Login
                    </a>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 hero-content" data-aos="fade-up">
                        <h2 class="welcome-text">Welcome to</h2>
                        <h1 class="system-name">CRAD System</h1>
                        <p class="system-description">
                            Center for Research and Development - Intelligent Progressive Research Submission & Tracking System
                        </p>
                        <a href="auth/Login.php" class="btn btn-get-started">
                            Get Started
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="hero-visuals">
                <div class="circular-logo-wrapper" data-aos="zoom-in" data-aos-delay="200">
                    <img src="Assets/image/circular_logo.png" alt="School Management System Logo">
                </div>
                <img src="Assets/image/students_hero.png" alt="Students studying" class="student-illustration" data-aos="fade-up" data-aos-delay="400">
            </div>
        </section>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Init AOS
        AOS.init({
            once: true,
            duration: 1000
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>

