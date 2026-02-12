<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>School Management System</title>
    <link rel="icon" type="image/png" href="../Assets/image/logo.png" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        },
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        .hero-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        .slanted-bg {
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: #f8fafc;
            clip-path: polygon(20% 0, 100% 0, 100% 100%, 0% 100%);
            z-index: 0;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.3);
        }

        .nav-link-ltr {
            position: relative;
        }

        .nav-link-ltr::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            display: block;
            margin-top: 5px;
            right: 0;
            background: #3b82f6;
            transition: width .3s ease;
            -webkit-transition: width .3s ease;
        }

        .nav-link-ltr:hover::after {
            width: 100%;
            left: 0;
            background: #3b82f6;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 overflow-x-hidden">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-nav">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a class="flex items-center gap-3 text-2xl font-extrabold text-primary-900 group" href="#">
                <div
                    class="bg-primary-600 p-1.5 rounded-lg group-hover:rotate-12 transition-transform duration-300 shadow-lg shadow-primary-200">
                    <img src="../Assets/image/logo.png" alt="Logo" class="w-8 h-8 object-contain brightness-0 invert">
                </div>
                <span class="tracking-tight">SMS <span class="text-primary-600">Pro</span></span>
            </a>

            <!-- Mobile Menu Toggle -->
            <button class="lg:hidden text-2xl" id="navToggle">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Navigation Links -->
            <div class="hidden lg:flex items-center gap-10">
                <ul class="flex items-center gap-8 text-sm font-semibold tracking-wide">
                    <li><a class="nav-link-ltr text-slate-600 hover:text-primary-600 transition-colors"
                            href="#">HOME</a></li>
                    <li><a class="nav-link-ltr text-slate-600 hover:text-primary-600 transition-colors"
                            href="#features">FEATURES</a></li>
                    <li><a class="nav-link-ltr text-slate-600 hover:text-primary-600 transition-colors"
                            href="#about-us">ABOUT US</a></li>
                    <li><a class="nav-link-ltr text-slate-600 hover:text-primary-600 transition-colors"
                            href="#contact">CONTACT</a></li>
                </ul>
                <a class="bg-primary-600 text-white px-8 py-2.5 rounded-full font-bold hover:bg-primary-700 hover:shadow-xl hover:shadow-primary-200 transition-all active:scale-95"
                    href="../auth/Login.php">
                    Get Started
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center pt-20 overflow-hidden bg-white">
        <!-- Slanted Background for Modern Look -->
        <div class="slanted-bg hidden lg:block"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="animate-fade-in-up">
                    <span
                        class="inline-block py-1 px-4 rounded-full bg-primary-50 text-primary-600 text-sm font-bold mb-6 tracking-wider">MODERN
                        EDUCATION PLATFORM</span>
                    <h1 class="text-5xl lg:text-7xl font-extrabold text-slate-900 leading-[1.1] mb-8">
                        Welcome to <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-primary-400">
                            School Management
                        </span>
                    </h1>
                    <p class="text-lg text-slate-600 mb-10 max-w-lg leading-relaxed">
                        Efficiently manage student records, faculty activities, and school operations — all in one
                        unified, intelligent platform designed for the future of education.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="../auth/Login.php"
                            class="bg-primary-600 text-white px-10 py-4 rounded-xl font-bold text-center hover:bg-primary-700 shadow-2xl shadow-primary-200 transition-all hover:-translate-y-1">
                            Launch Dashboard
                        </a>
                        <a href="#features"
                            class="bg-white text-slate-900 px-10 py-4 rounded-xl font-bold text-center border-2 border-slate-100 hover:border-primary-200 transition-all">
                            Explore Features
                        </a>
                    </div>
                </div>

                <div class="relative hidden lg:block animate-float">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=2070&auto=format&fit=crop"
                        alt="Student Management" class="relative z-10 w-full rounded-[2.5rem] shadow-2xl">
                    <!-- Decorative Elements -->
                    <div
                        class="absolute -top-10 -right-10 w-48 h-48 bg-primary-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-pulse">
                    </div>
                    <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-pulse"
                        style="animation-delay: 1s">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Features Section -->
    <section id="features" class="py-24 bg-[#0a0f1d] text-white">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                <div class="max-w-xl">
                    <h2 class="text-4xl font-extrabold mb-4">Powerful Features</h2>
                    <p class="text-slate-400 text-lg">Comprehensive tools designed to streamline every aspect of
                        educational institution management.</p>
                </div>
                <div class="hidden md:block">
                    <div class="h-1 w-24 bg-primary-500 rounded-full"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <?php
                $features = [
                    ['icon' => 'user-graduate', 'title' => 'Enrollment', 'desc' => 'Smooth registration'],
                    ['icon' => 'book-open', 'title' => 'Subjects', 'desc' => 'Course management'],
                    ['icon' => 'users', 'title' => 'Students', 'desc' => 'Profile tracking'],
                    ['icon' => 'chalkboard-teacher', 'title' => 'Faculty', 'desc' => 'Staff management'],
                    ['icon' => 'calendar-alt', 'title' => 'Schedule', 'desc' => 'Time optimization'],
                    ['icon' => 'clipboard-list', 'title' => 'Grades', 'desc' => 'Performance tracking'],
                    ['icon' => 'user-shield', 'title' => 'Roles', 'desc' => 'Access control'],
                    ['icon' => 'bell', 'title' => 'Notifications', 'desc' => 'Smart alerts'],
                    ['icon' => 'chart-line', 'title' => 'Analytics', 'desc' => 'Data insights'],
                    ['icon' => 'cogs', 'title' => 'Settings', 'desc' => 'Full configuration'],
                ];

                foreach ($features as $f): ?>
                    <div class="feature-card p-8 rounded-3xl group">
                        <div
                            class="w-12 h-12 bg-primary-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary-500 transition-colors">
                            <i class="fas fa-<?php echo $f['icon']; ?> text-xl text-primary-400 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2">
                            <?php echo $f['title']; ?>
                        </h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            <?php echo $f['desc']; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about-us" class="py-24 bg-white overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="lg:w-1/2 relative group">
                    <div
                        class="absolute -top-6 -left-6 w-24 h-24 bg-primary-50 rounded-3xl z-0 group-hover:-translate-x-2 group-hover:-translate-y-2 transition-transform">
                    </div>
                    <img src="https://images.unsplash.com/photo-1523240715639-9947f1c99437?q=80&w=2070&auto=format&fit=crop"
                        alt="About Us"
                        class="relative z-10 rounded-[2rem] shadow-2xl border-8 border-white transition-transform group-hover:scale-[1.02]">
                    <div
                        class="absolute -bottom-6 -right-6 w-32 h-32 bg-primary-900/5 rounded-3xl z-0 group-hover:translate-x-2 group-hover:translate-y-2 transition-transform">
                    </div>
                </div>
                <div class="lg:w-1/2">
                    <span class="text-primary-600 font-bold tracking-widest text-sm uppercase mb-4 block">OUR
                        MISSION</span>
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-slate-900 mb-8">Redefining Educational
                        Administration</h2>
                    <p class="text-slate-600 text-lg leading-relaxed mb-8">
                        Our School Management System is a modern solution designed to make academic and administrative
                        processes simple, efficient, and unified.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded-full bg-primary-100 flex items-center justify-center">
                                <i class="fas fa-check text-primary-600 text-xs"></i>
                            </div>
                            <span class="font-medium text-slate-700">Digital-first enrollment process</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded-full bg-primary-100 flex items-center justify-center">
                                <i class="fas fa-check text-primary-600 text-xs"></i>
                            </div>
                            <span class="font-medium text-slate-700">Real-time academic performance tracking</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded-full bg-primary-100 flex items-center justify-center">
                                <i class="fas fa-check text-primary-600 text-xs"></i>
                            </div>
                            <span class="font-medium text-slate-700">Unified communication for faculty and
                                students</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Us Section -->
    <section id="contact" class="py-24 bg-slate-50">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-4xl font-extrabold mb-4">Connect With Us</h2>
                <p class="text-slate-600">Have questions? We're here to help you revolutionize your school management.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Address -->
                <div class="bg-white p-8 rounded-3xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-map-marker-alt text-red-500"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-3">Our Campus</h3>
                    <p class="text-slate-500 text-sm">Bestlink College of the Philippines, Quirino Highway, QC</p>
                </div>

                <!-- Phone -->
                <div class="bg-white p-8 rounded-3xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-phone text-green-500"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-3">Phone Support</h3>
                    <p class="text-slate-500 text-sm">(02) 1234-5678<br>0917-123-4567</p>
                </div>

                <!-- Email -->
                <div class="bg-white p-8 rounded-3xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-envelope text-blue-500"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-3">Email Us</h3>
                    <p class="text-slate-500 text-sm">sms.support@bestlink.edu.ph</p>
                </div>

                <!-- Hours -->
                <div class="bg-white p-8 rounded-3xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-clock text-orange-500"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-3">Office Hours</h3>
                    <p class="text-slate-500 text-sm">Mon - Fri: 8:00 AM - 5:00 PM</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary-950 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-3">
                    <img src="../Assets/image/logo.png" alt="Logo" class="w-8 h-8 object-contain brightness-0 invert">
                    <span class="text-xl font-bold tracking-tight">SMS <span class="text-primary-400">Pro</span></span>
                </div>
                <p class="text-slate-400 text-sm">
                    &copy; 2026 School Management System. All rights reserved.
                </p>
                <div class="flex gap-6">
                    <a href="#" class="text-slate-400 hover:text-white transition-colors"><i
                            class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors"><i
                            class="fab fa-twitter"></i></a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors"><i
                            class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Navbar Scroll Effect
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 20) {
                nav.classList.add('shadow-xl', 'py-3');
                nav.classList.remove('py-4');
            } else {
                nav.classList.remove('shadow-xl', 'py-3');
                nav.classList.add('py-4');
            }
        });

        // Mobile Menu Toggle
        const navToggle = document.getElementById('navToggle');
        const navLinks = document.querySelector('.lg\\:flex.items-center.gap-10');

        navToggle.addEventListener('click', () => {
            navLinks.classList.toggle('hidden');
            navLinks.classList.toggle('absolute');
            navLinks.classList.toggle('top-full');
            navLinks.classList.toggle('left-0');
            navLinks.classList.toggle('right-0');
            navLinks.classList.toggle('bg-white');
            navLinks.classList.toggle('p-6');
            navLinks.classList.toggle('flex');
            navLinks.classList.toggle('flex-col');
            navLinks.classList.toggle('shadow-2xl');
        });
    </script>
</body>

</html>