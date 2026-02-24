<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System | Efficient. Integrated. Modern.</title>
    <link rel="icon" type="image/png" href="Assets/image/logo.png">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0047AB;
            --primary-dark: #002D6B;
            --primary-light: #4D88FF;
            --accent-color: #00D1FF;
            --text-main: #1A1A1A;
            --text-muted: #666666;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.3);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            overflow-x: hidden;
            background: #F8FAFC;
        }

        h1, h2, h3, h4, .navbar-brand {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
        }

        /* --- Custom Background --- */
        .page-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(204,224,255,0.8) 100%), 
                        url('Assets/image/background.jpg') no-repeat center center;
            background-size: cover;
            filter: blur(2px);
        }

        /* --- Navbar --- */
        .navbar {
            padding: 1.25rem 0;
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--glass-border);
            transition: all 0.4s ease;
        }

        .navbar.scrolled {
            padding: 0.8rem 0;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .navbar-brand {
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            letter-spacing: -0.5px;
        }

        .navbar-brand img {
            transition: transform 0.4s ease;
        }

        .navbar-brand:hover img {
            transform: scale(1.1) rotate(-5deg);
        }

        .nav-link {
            font-weight: 600;
            color: var(--text-main) !important;
            padding: 0.5rem 1.2rem !important;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 60%;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white !important;
            border-radius: 50px;
            padding: 0.6rem 2rem !important;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 71, 171, 0.25);
            transition: all 0.3s ease;
            border: none;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 71, 171, 0.4);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        }

        /* --- Hero Section --- */
        .hero {
            padding: 180px 0 100px;
            position: relative;
        }

        .hero-title {
            font-size: 4rem;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: var(--primary-dark);
        }

        .hero-title span {
            background: linear-gradient(to right, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-description {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            max-width: 550px;
        }

        .hero-buttons {
            display: flex;
            gap: 1.2rem;
        }

        .btn-hero-primary {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
        }

        .hero-image-wrapper {
            position: relative;
            z-index: 1;
        }

        .hero-image-wrapper img {
            width: 100%;
            height: auto;
            border-radius: 2rem;
            box-shadow: 0 50px 100px rgba(0,0,0,0.1);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .hero-shape {
            position: absolute;
            top: -10%;
            right: -10%;
            width: 120%;
            height: 120%;
            background: radial-gradient(circle, rgba(0,71,171,0.05) 0%, transparent 70%);
            z-index: -1;
        }

        /* --- Features section --- */
        .section-tag {
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 2px;
            color: var(--primary-color);
            display: block;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 1.5rem;
            padding: 2rem;
            transition: all 0.4s ease;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            background: white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            border-color: var(--primary-light);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(0,71,171,0.1), rgba(0,209,255,0.1));
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            font-size: 1.8rem;
            margin: 0 auto 1.5rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .feature-card h5 {
            margin-bottom: 0.75rem;
            font-weight: 700;
        }

        .feature-card p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0;
        }

        /* --- Contact --- */
        .contact-section {
            padding: 100px 0;
            background: white;
            border-radius: 4rem 4rem 0 0;
        }

        .info-box {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-icon {
            width: 50px;
            height: 50px;
            background: #F0F7FF;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.4rem;
        }

        .info-content h6 {
            margin-bottom: 0.2rem;
            font-weight: 700;
        }

        .info-content p {
            margin-bottom: 0;
            color: var(--text-muted);
        }

        /* --- Footer --- */
        footer {
            background: #001A3D;
            padding: 3rem 0;
            color: rgba(255,255,255,0.7);
        }

        footer h5 {
            color: white;
            margin-bottom: 1.5rem;
        }

        .footer-link {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            display: block;
            margin-bottom: 0.8rem;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: var(--accent-color);
        }

        .social-link {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 0.8rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-link:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
            color: white;
        }

        @media (max-width: 991px) {
            .hero-title {
                font-size: 3rem;
            }
            .hero {
                padding: 140px 0 60px;
                text-align: center;
            }
            .hero-description {
                margin-left: auto;
                margin-right: auto;
            }
            .hero-buttons {
                justify-content: center;
            }
            .hero-image-wrapper {
                margin-top: 4rem;
            }
        }
    </style>
</head>
<body>

    <div class="page-bg"></div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="Assets/image/logo.png" alt="Logo" width="45" height="45" class="me-3">
                <span>School Management</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                </ul>
                <div class="d-flex">
                    <a href="auth/Login.php" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Portal Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                    <span class="section-tag">Next-Gen Education Management</span>
                    <h1 class="hero-title">Empowering<br>Education with<span> Efficiency</span></h1>
                    <p class="hero-description">Transform your school's productivity with our comprehensive management system. Streamline records, simplify administration, and enhance communication.</p>
                    <div class="hero-buttons">
                        <a href="auth/Login.php" class="btn btn-login btn-hero-primary">
                            Get Started Now <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1200">
                    <div class="hero-image-wrapper">
                        <div class="hero-shape"></div>
                        <img src="Assets/image/hero.png" alt="Students studying" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5" style="background: rgba(240, 247, 255, 0.4);">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-tag">Powerful Features</span>
                <h2 class="display-5 fw-bold mb-3">One Platform, Endless Possibilities</h2>
                <div class="mx-auto" style="width: 80px; height: 4px; background: var(--primary-color); border-radius: 2px;"></div>
            </div>

            <div class="feature-grid">
                <!-- Enrollment -->
                <div class="feature-card" data-aos="zoom-in" data-aos-delay="100">
                    <div class="feature-icon"><i class="fas fa-user-graduate"></i></div>
                    <h5>Fast Enrollment</h5>
                    <p>Streamlined registration process for students and parents.</p>
                </div>
                <!-- Subjects -->
                <div class="feature-card" data-aos="zoom-in" data-aos-delay="200">
                    <div class="feature-icon"><i class="fas fa-book-open"></i></div>
                    <h5>Curriculum</h5>
                    <p>Dynamic subject management and course mapping.</p>
                </div>
                <!-- Faculty -->
                <div class="feature-card" data-aos="zoom-in" data-aos-delay="300">
                    <div class="feature-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <h5>Staff Portal</h5>
                    <p>Complete management tools for faculty and staff.</p>
                </div>
                <!-- Grades -->
                <div class="feature-card" data-aos="zoom-in" data-aos-delay="400">
                    <div class="feature-icon"><i class="fas fa-clipboard-check"></i></div>
                    <h5>Academic Tracking</h5>
                    <p>Real-time grading and performance analytics.</p>
                </div>
                <!-- Finance -->
                <div class="feature-card" data-aos="zoom-in" data-aos-delay="500">
                    <div class="feature-icon"><i class="fas fa-wallet"></i></div>
                    <h5>Billing & Fees</h5>
                    <p>Automated tuition fee tracking and digital receipts.</p>
                </div>
                <!-- Notifications -->
                <div class="feature-card" data-aos="zoom-in" data-aos-delay="600">
                    <div class="feature-icon"><i class="fas fa-bell"></i></div>
                    <h5>Smart Alerts</h5>
                    <p>Keep everyone informed with instant notifications.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6 pr-lg-5" data-aos="fade-right">
                    <img src="Assets/image/contract.jpg" alt="About Us" class="img-fluid rounded-4 shadow-lg mb-4 mb-lg-0">
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <span class="section-tag">About Our System</span>
                    <h2 class="display-6 fw-bold mb-4">A Modern Solution for Modern Schools</h2>
                    <p class="lead mb-4">Our School Management System is designed to bridge the gap between technology and education.</p>
                    <p class="text-muted mb-4">We provide a unified ecosystem where administrators, teachers, and students can collaborate effectively. From basic student information to complex financial reporting, we have everything covered.</p>
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-primary"><i class="fas fa-check-circle fa-2x"></i></div>
                                <div class="fw-bold">Secured Database</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-primary"><i class="fas fa-check-circle fa-2x"></i></div>
                                <div class="fw-bold">24/7 Support</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4" data-aos="fade-up">
                    <h2 class="fw-bold mb-5">Get In Touch</h2>
                    
                    <div class="info-box">
                        <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="info-content">
                            <h6>Location</h6>
                            <p>Bestlink College, Quezon City, Metro Manila</p>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <div class="info-icon"><i class="fas fa-phone"></i></div>
                        <div class="info-content">
                            <h6>Call Us</h6>
                            <p>(+63) 123-456-7890</p>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <div class="info-icon"><i class="fas fa-envelope"></i></div>
                        <div class="info-content">
                            <h6>Email Address</h6>
                            <p>support@sms-portal.edu</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4" style="background: #F8FAFC;">
                        <form>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-600">Full Name</label>
                                    <input type="text" class="form-control form-control-lg border-0" placeholder="John Doe">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600">Email Address</label>
                                    <input type="email" class="form-control form-control-lg border-0" placeholder="john@example.com">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-600">Subject</label>
                                    <input type="text" class="form-control form-control-lg border-0" placeholder="How can we help?">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-600">Message</label>
                                    <textarea class="form-control border-0" rows="5" placeholder="Type your message here..."></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-login btn-hero-primary w-100 py-3">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <div class="mb-4">
                <img src="Assets/image/logo.png" alt="Logo" width="60" class="mb-3">
                <h4 class="text-white">School Management System</h4>
                <p>Leading the digital transformation of education.</p>
            </div>
            
            <div class="mb-4">
                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
            </div>
            
            <hr class="border-secondary my-4">
            
            <p class="mb-0">&copy; 2025 School Management System. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Init AOS
        AOS.init({
            once: true,
            duration: 800
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
