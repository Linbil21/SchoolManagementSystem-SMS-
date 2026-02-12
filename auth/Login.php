<?php
require_once 'Security.php';
$csrf_token = generateCsrfToken();

// Determine if we should show the login form or the role selection page
// Show login form if 'action' is set or if there is an error
$show_login = isset($_GET['action']) || isset($_GET['error']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN | SMS</title>
    <link rel="icon" type="image/x-icon" href="../Assets/image/logo.png">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/log-reg.css">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Override body background from log-reg.css for a cleaner look */
        body {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('../Assets/image/background.jpg') !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
            display: block !important; /* Reset flex to allow normal flow */
            overflow: hidden !important; /* Remove scrollbar */
        }

        /* Floating logo for registration to save space and move it up */
        .sign-up-form .logo-circle {
            margin: 10px auto 15px;
            width: 100px;
            height: 100px;
        }

        .sign-up-form .logo-circle img {
            max-width: 60px;
        }

        /* Style for the 'Already Enrolled?' button to look like the Next button */
        .btn-already-enrolled {
            background-color: var(--primary-blue);
            color: #fff;
            text-align: center;
            line-height: 49px;
            text-decoration: none;
            width: 100%;
            max-width: 180px;
            height: 49px;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
        }

        /* Password Toggle Styles */
        .password-container {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .password-container .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #1648bc !important;
            /* Make it clearly blue */
            cursor: pointer;
            z-index: 999 !important;
            font-size: 1.2rem;
            transition: 0.3s;
            display: block !important;
            visibility: visible !important;
        }

        .password-container .toggle-password:hover {
            color: #1648bc;
        }

        .password-container input {
            width: 100%;
            padding-right: 45px !important;
        }
        
        /* New Role Selection Styles */
        .role-selection-wrapper { 
            min-height: 100vh; 
            background: transparent; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 20px; 
            font-family: 'Poppins', sans-serif; 
        }
        
        .role-card { 
            background: white; 
            border-radius: 24px; 
            box-shadow: 0 0 50px rgba(50, 100, 255, 0.4); /* Soft, glowing blue shadow */
            display: flex; 
            overflow: hidden; 
            max-width: 1100px; 
            width: 100%; 
            min-height: 650px; 
            opacity: 0;
            animation: fadeIn 0.8s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .role-left { 
            flex: 1.2; 
            padding: 60px; 
            background: #ffffff; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            position: relative; 
            overflow: hidden; 
        }
        
        .role-left-content {
            opacity: 0;
            animation: slideRight 0.8s ease-out 0.3s forwards;
        }
        
        @keyframes slideRight {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .role-right { 
            flex: 1; 
            padding: 60px; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            background: white; 
            border-left: 1px solid #f1f5f9;
        }
        
        .role-right-content {
            opacity: 0;
            animation: slideLeft 0.8s ease-out 0.5s forwards;
        }

        @keyframes slideLeft {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .role-btn { 
            display: flex; 
            align-items: center; 
            padding: 16px; 
            border-radius: 12px; 
            text-decoration: none; 
            transition: all 0.3s ease; 
            margin-bottom: 15px; 
            position: relative; 
            overflow: hidden; 
            opacity: 0;
            animation: fadeInUp 0.5s ease-out forwards;
        }
        
        .role-btn:nth-child(1) { animation-delay: 0.6s; }
        .role-btn:nth-child(2) { animation-delay: 0.7s; }
        .role-btn:nth-child(3) { animation-delay: 0.8s; }
        .role-btn:nth-child(4) { animation-delay: 0.9s; }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .role-btn:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 10px 25px rgba(50, 100, 255, 0.15); 
        }
        .role-icon { 
            width: 54px; 
            height: 54px; 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin-right: 18px; 
            font-size: 1.4rem; 
            color: white; 
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }
        
        .role-btn:hover .role-icon {
            transform: scale(1.1) rotate(5deg);
        }

        /* Custom Scrollbar for Roles */
        .roles-scroll-container::-webkit-scrollbar {
            width: 6px;
        }
        .roles-scroll-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .roles-scroll-container::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 10px;
        }
        .roles-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #1d4ed8;
        }
        
        .roles-scroll-container {
            max-height: 420px;
            overflow-y: auto;
            padding-right: 8px;
            margin: 0 auto;
            width: 100%;
            scrollbar-width: thin;
            scrollbar-color: #3b82f6 #f1f5f9;
        }

        @media (max-width: 900px) {
            .role-card { flex-direction: column; height: auto; }
            .role-left { padding: 40px; text-align: center; }
            .role-right { padding: 40px; border-left: none; border-top: 1px solid #f1f5f9; }
        }

        /* Shadow Override for Login */
        .container {
            box-shadow: 0 0 50px rgba(50, 100, 255, 0.4) !important; /* Soft, glowing blue shadow */
        }
    </style>
</head>

<body>
    <?php if (!$show_login): ?>
    <!-- ROLE SELECTION SCREEN -->
    <div class="role-selection-wrapper">
        <div class="role-card">
            <!-- Left Side -->
            <div class="role-left">
                <!-- Decorative background elements could be added here -->
                
                <div class="role-left-content" style="position: relative; z-index: 1;">
                    <img src="../Assets/image/logo.png" alt="Logo" style="width: 100px; margin: 0 auto 30px; display: block; border: 1px solid #000; padding: 5px; border-radius: 8px;">
                    <h1 style="font-size: 3.5rem; font-weight: 800; line-height: 1.1; color: #1e3a8a; margin-bottom: 25px; text-align: center;">
                        Welcome to <br><span style="color: #3b82f6;">SMS</span>
                    </h1>
                    <p style="font-size: 1.1rem; color: #64748b; line-height: 1.6; margin-bottom: 40px; max-width: 90%; text-align: center; margin-left: auto; margin-right: auto;">
                        Empowering education through a unified academic management system that enhances learning, streamlines processes, and connects the academic community.
                    </p>
                    <div style="text-align: center;">
                        <a href="Login.php?action=login" style="background: #1e3a8a; color: white; padding: 15px 40px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s; box-shadow: 0 4px 14px 0 rgba(30, 58, 138, 0.39);">
                            Learn More <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="role-right">
                <div class="role-right-content">
                    <div style="text-align: center; margin-bottom: 40px;">
                        <div style="width: 80px; height: 80px; background: white; border-radius: 50%; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <img src="../Assets/image/logo.png" alt="SIS" style="width: 40px;">
                        </div>
                        <span style="color: #3b82f6; font-weight: 700; letter-spacing: 2px; font-size: 0.8rem; text-transform: uppercase;">SMS Portal</span>
                        <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 10px 0;">Choose Your Role</h2>
                        <p style="font-size: 0.9rem; color: #64748b;">Select your portal to continue</p>
                    </div>

                    <div class="roles-scroll-container">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; width: 100%;">
                            <!-- Administrator -->
                            <a href="Login.php?action=login&role=admin" class="role-btn" style="background: white; border: 2px solid #eff6ff;">
                                <div class="role-icon" style="background: #3b82f6;">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 1rem; color: #1e293b;">Administrator</div>
                                    <div style="font-size: 0.8rem; color: #64748b;">System Management</div>
                                </div>
                                <i class="fas fa-chevron-right" style="margin-left: auto; color: #cbd5e1;"></i>
                            </a>

                            <!-- Staff -->
                            <a href="Login.php?action=login&role=staff" class="role-btn" style="background: white; border: 2px solid #f3e8ff;">
                                <div class="role-icon" style="background: #8b5cf6;">
                                    <i class="fas fa-user-gear"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 1rem; color: #1e293b;">Staff</div>
                                    <div style="font-size: 0.8rem; color: #64748b;">Administrative Tasks</div>
                                </div>
                                <i class="fas fa-chevron-right" style="margin-left: auto; color: #cbd5e1;"></i>
                            </a>

                            <!-- Teacher -->
                            <a href="Login.php?action=login&role=teacher" class="role-btn" style="background: white; border: 2px solid #ffedd5;">
                                <div class="role-icon" style="background: #f97316;">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 1rem; color: #1e293b;">Teacher</div>
                                    <div style="font-size: 0.8rem; color: #64748b;">Academic Portal</div>
                                </div>
                                <i class="fas fa-chevron-right" style="margin-left: auto; color: #cbd5e1;"></i>
                            </a>

                            <!-- Student -->
                            <a href="Login.php?action=login&role=student" class="role-btn" style="background: white; border: 2px solid #dcfce7;">
                                <div class="role-icon" style="background: #22c55e;">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 1rem; color: #1e293b;">Student</div>
                                    <div style="font-size: 0.8rem; color: #64748b;">Student Portal</div>
                                </div>
                                <i class="fas fa-chevron-right" style="margin-left: auto; color: #cbd5e1;"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div style="text-align: center; margin-top: 30px; color: #94a3b8; font-size: 0.85rem;">
                        Need help? <a href="#" style="color: #3b82f6; text-decoration: none; font-weight: 600;">Contact support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- CENTER WRAPPER FOR LOGIN -->
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
    <!-- ORIGINAL LOGIN/REGISTER FORM -->
    <div class="container" id="main-container">
        <div class="forms-container">
            <div class="signin-signup">
                <!-- LOGIN FORM -->
                <form action="auth_process.php" method="POST" class="sign-in-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <h2 class="title" style="color: #1648bc; font-size: 1.8rem; font-weight: 800; line-height: 1.2;">
                        Enrollment Management</h2>
                    <div class="subtitle"
                        style="color: #1034a6; font-weight: 700; font-size: 1.15rem; margin-top: 10px;">Log in to your
                        account</div>

                    <?php if (isset($_GET['registered']) && $_GET['registered'] == 'true'): ?>
                        <div
                            style="color: #059669; background: #d1fae5; padding: 10px; border-radius: 6px; margin-top: 15px; font-size: 0.85rem; text-align: center; font-weight: 600;">
                            <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                            OFFICIAL ENROLLED - You can now log in to your account.
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div
                            style="color: #ef4444; background: #fee2e2; padding: 10px; border-radius: 6px; margin-top: 15px; font-size: 0.85rem; text-align: center; font-weight: 600;">
                            <?php
                            if ($_GET['error'] == 'user_not_found')
                                echo "User account not found.";
                            elseif ($_GET['error'] == 'invalid_password')
                                echo "Incorrect password.";
                            elseif ($_GET['error'] == 'empty_fields')
                                echo "Please fill in all fields.";
                            elseif ($_GET['error'] == 'unauthorized')
                                echo "You don't have permission to access the dashboard.";
                            elseif ($_GET['error'] == 'system_error')
                                echo "An error occurred: " . htmlspecialchars($_GET['msg'] ?? 'Please try again.');
                            else
                                echo "An error occurred. Please try again.";
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="input-group" style="margin-top: 30px;">
                        <label for="email">Email <span>*</span></label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Password <span>*</span></label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" required>
                            <i class="fas fa-eye toggle-password"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn login-btn"
                        style="max-width: 100%; border-radius: 4px; font-weight: 700; height: 50px;">Log In</button>

                    <a href="forgot_password.php" class="forgot-password"
                        style="margin-top: 20px; color: #3b82f6;">Forgot Password?</a>

                    <div style="margin-top: 30px; font-size: 0.9rem; color: #4b5563;">
                        New Student? <a href="#" id="sign-up-link-trigger"
                            style="color: var(--primary-blue); font-weight: 700; text-decoration: none;">Register
                            Here</a>
                    </div>
                </form>

                <!-- REGISTRATION FORM (Modern Modern Split Design) -->
                <form action="auth_process.php" method="POST" class="sign-up-form" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="registration-layout">
                        <!-- Left Panel: The Form -->
                        <div class="reg-left-panel">
                            <div class="reg-header">
                                <h2 class="form-title">Enrollment Form</h2>
                                <div class="step-indicator-wrapper">
                                    <div class="h-progress-steps">
                                        <div class="h-step active" data-step="1">1</div>
                                        <div class="h-step-line"></div>
                                        <div class="h-step" data-step="2">2</div>
                                        <div class="h-step-line"></div>
                                        <div class="h-step" data-step="3">3</div>
                                        <div class="h-step-line"></div>
                                        <div class="h-step" data-step="4">4</div>
                                        <div class="h-step-line"></div>
                                        <div class="h-step" data-step="5">5</div>
                                    </div>
                                    <span class="step-text" id="step-text">Part 1 of 5: Enrollment Information</span>
                                </div>
                            </div>

                            <!-- Form Content with Scroll -->
                            <div class="register-container-scroll">
                                <!-- Step 1: Enrollment Information -->
                                <div class="form-step form-step-active">
                                    <h3 class="step-title">Enrollment Information</h3>
                                    
                                    <div class="row">
                                        <div class="col col-full input-group">
                                            <label>Year Level <span>*</span></label>
                                            <select name="year_level" required>
                                                <option value="First Year">First Year</option>
                                                <option value="Second Year">Second Year</option>
                                                <option value="Third Year">Third Year</option>
                                                <option value="Fourth Year">Fourth Year</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col col-2 input-group">
                                            <label>Admission Type <span>*</span></label>
                                            <select name="admission_type" required>
                                                <option value="Freshman">Freshman</option>
                                                <option value="Transferee">Transferee</option>
                                            </select>
                                        </div>
                                        <div class="col col-2 input-group">
                                            <label>Course <span>*</span></label>
                                            <select name="course" required>
                                                <option value="">Select...</option>
                                                <option value="BSIT">BS Information Technology</option>
                                                <option value="BSCS">BS Computer Science</option>
                                                <option value="BSBA">BS Business Administration</option>
                                                <option value="BS Crim">BS Criminology</option>
                                                <option value="BSHM">BS Hospitality Management</option>
                                                <option value="BSA">BS Accountancy</option>
                                                <option value="BSCE">BS Civil Engineering</option>
                                                <option value="BEED">Bachelor of Elementary Education</option>
                                                <option value="BSED">Bachelor of Secondary Education</option>
                                            </select>
                                        </div>
                                    </div>

                                    <h4 class="sub-step-title">Other Documents (If Available)</h4>
                                    <div class="row row-3">
                                        <div class="col input-group">
                                            <label>Birth Cert (PSA)</label>
                                            <input type="file" name="birth_cert">
                                        </div>
                                        <div class="col input-group">
                                            <label>Form 138</label>
                                            <input type="file" name="form_138">
                                        </div>
                                        <div class="col input-group">
                                            <label>Passport Size ID <span>*</span></label>
                                            <input type="file" name="id_picture" required>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 5px;">
                                        <div class="col input-group col-full">
                                            <label style="color: var(--primary-blue); font-weight: 600;">Secondary Documents Requirements? <span>*</span></label>
                                            <div style="display: flex; gap: 30px; margin-top: 10px; background: #f8fafc; padding: 15px; border-radius: 10px; border: 1px dashed #e2e8f0; width: fit-content;">
                                                <label style="font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 10px; margin-bottom: 0;">
                                                    <input type="radio" name="has_secondary_docs" value="yes" checked style="width: 18px; height: 18px;"> Meron
                                                </label>
                                                <label style="font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 10px; margin-bottom: 0;">
                                                    <input type="radio" name="has_secondary_docs" value="no" style="width: 18px; height: 18px;"> Wala
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btns-group">
                                        <a href="#" id="sign-in-link-trigger" style="margin-right: auto; text-decoration: none; color: #64748b; font-weight: 700; font-size: 0.85rem;">Already Enrolled?</a>
                                        <a href="#" class="btn btn-next">CONTINUE <i class="fas fa-chevron-right" style="margin-left: 10px;"></i></a>
                                    </div>
                                </div>

                                <!-- Step 2: Student Information -->
                                <div class="form-step">
                                    <h3 class="step-title">Student Information</h3>
                                    <div class="row">
                                        <div class="col input-group">
                                            <label>First Name <span>*</span></label>
                                            <input type="text" name="first_name" placeholder="John" required>
                                        </div>
                                        <div class="col input-group">
                                            <label>Middle Name</label>
                                            <input type="text" name="middle_name" placeholder="Quincy">
                                        </div>
                                        <div class="col input-group">
                                            <label>Last Name <span>*</span></label>
                                            <input type="text" name="last_name" placeholder="Doe" required>
                                        </div>
                                        <div class="col input-group">
                                            <label>Gender <span>*</span></label>
                                            <select name="gender" required>
                                                <option value="">Select...</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col col-2 input-group">
                                            <label>Birthdate <span>*</span></label>
                                            <input type="date" name="birthdate" value="2010-01-10" required>
                                        </div>
                                        <div class="col col-2 input-group">
                                            <label>Contact Num <span>*</span></label>
                                            <input type="text" name="contact_number" placeholder="09123456789" required maxlength="12" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col col-full input-group">
                                            <label>Email Address <span>*</span></label>
                                            <input type="email" name="reg_email" placeholder="example@email.com" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col col-full input-group">
                                            <label>Complete Address <span>*</span></label>
                                            <input type="text" name="address" placeholder="123 Street, City, Province" required>
                                        </div>
                                    </div>

                                    <div class="btns-group">
                                        <a href="#" class="btn btn-prev"><i class="fas fa-chevron-left" style="margin-right: 10px;"></i> BACK</a>
                                        <a href="#" class="btn btn-next">CONTINUE <i class="fas fa-chevron-right" style="margin-left: 10px;"></i></a>
                                    </div>
                                </div>

                                <!-- Step 3: Secondary Documents -->
                                <div class="form-step">
                                    <h3 class="step-title">Secondary Documents</h3>
                                    <div class="row row-3">
                                        <div class="col input-group">
                                            <label>Form 137</label>
                                            <input type="file" name="form_137">
                                        </div>
                                        <div class="col input-group">
                                            <label>Good Moral</label>
                                            <input type="file" name="good_moral">
                                        </div>
                                        <div class="col input-group">
                                            <label>Brgy Clearance</label>
                                            <input type="file" name="barangay_clearance">
                                        </div>
                                    </div>

                                    <div class="btns-group">
                                        <a href="#" class="btn btn-prev"><i class="fas fa-chevron-left" style="margin-right: 10px;"></i> BACK</a>
                                        <a href="#" class="btn btn-next">CONTINUE <i class="fas fa-chevron-right" style="margin-left: 10px;"></i></a>
                                    </div>
                                </div>

                                <!-- Step 4: Parent/Guardian Information -->
                                <div class="form-step">
                                    <h3 class="step-title">Parent/Guardian Information</h3>
                                    <div class="row">
                                        <div class="col input-group">
                                            <label>First Name <span>*</span></label>
                                            <input type="text" name="guardian_first" required>
                                        </div>
                                        <div class="col input-group">
                                            <label>Middle Name</label>
                                            <input type="text" name="guardian_middle">
                                        </div>
                                        <div class="col input-group">
                                            <label>Last Name <span>*</span></label>
                                            <input type="text" name="guardian_last" required>
                                        </div>
                                        <div class="col input-group">
                                            <label>Relationship <span>*</span></label>
                                            <input type="text" name="relationship" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col col-2 input-group">
                                            <label>Guardian Email <span>*</span></label>
                                            <input type="email" name="guardian_email" required>
                                        </div>
                                        <div class="col col-2 input-group">
                                            <label>Contact Num <span>*</span></label>
                                            <input type="text" name="guardian_contact" required maxlength="12" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col col-full input-group">
                                            <label>Guardian Address <span>*</span></label>
                                            <input type="text" name="guardian_address" required>
                                        </div>
                                    </div>

                                    <div class="btns-group">
                                        <a href="#" class="btn btn-prev"><i class="fas fa-chevron-left" style="margin-right: 10px;"></i> BACK</a>
                                        <a href="#" class="btn btn-next">CONTINUE <i class="fas fa-chevron-right" style="margin-left: 10px;"></i></a>
                                    </div>
                                </div>

                                <!-- Step 5: Educational Background -->
                                <div class="form-step">
                                    <h3 class="step-title">Educational Background</h3>
                                    <div class="row">
                                        <div class="col input-group">
                                            <label>Primary School <span>*</span></label>
                                            <input type="text" name="primary_school" required>
                                        </div>
                                        <div class="col input-group">
                                            <label>Graduated <span>*</span></label>
                                            <input type="text" name="primary_year" placeholder="20XX" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        </div>
                                        <div class="col input-group">
                                            <label>Secondary School <span>*</span></label>
                                            <input type="text" name="secondary_school" required>
                                        </div>
                                        <div class="col input-group">
                                            <label>Graduated <span>*</span></label>
                                            <input type="text" name="secondary_year" placeholder="20XX" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col col-2 input-group">
                                            <label>Account Password <span>*</span></label>
                                            <div class="password-container">
                                                <input type="password" name="reg_password" placeholder="********" required>
                                                <i class="fas fa-eye toggle-password"></i>
                                            </div>
                                        </div>
                                        <div class="col col-2 input-group">
                                            <label>Confirm Password <span>*</span></label>
                                            <div class="password-container">
                                                <input type="password" name="confirm_password" placeholder="********" required>
                                                <i class="fas fa-eye toggle-password"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btns-group" style="justify-content: center;">
                                        <a href="#" class="btn btn-prev"><i class="fas fa-chevron-left" style="margin-right: 10px;"></i> BACK</a>
                                        <button type="submit" class="btn">FINISH <i class="fas fa-check-circle" style="margin-left: 10px;"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Panel: Branding & Vertical Progress -->
                        <div class="reg-right-panel">
                            <div class="brand-side">
                                <a href="Login.php" id="sign-in-link-logo-trigger" class="reg-logo">
                                    <img src="../Assets/image/logo.png" alt="Logo">
                                </a>
                                <h3 class="brand-name">SMS</h3>
                                <p class="brand-tagline">Quality education and lifelong learning through a modern school management system.</p>
                            </div>
                            
                            <ul class="vertical-progressbar">
                                <li class="v-step active-v-step" data-step="0">
                                    <span class="v-dot"></span> Enrollment Details
                                </li>
                                <li class="v-step" data-step="1">
                                    <span class="v-dot"></span> Personal Details
                                </li>
                                <li class="v-step" data-step="2">
                                    <span class="v-dot"></span> Secondary Documents
                                </li>
                                <li class="v-step" data-step="3">
                                    <span class="v-dot"></span> Guardian Background
                                </li>
                                <li class="v-step" data-step="4">
                                    <span class="v-dot"></span> Educational History
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- PANELS (The Sliding Interface) -->
        <div class="panels-container">
            <!-- Left Panel (Visible when showing Login) -->
            <div class="panel left-panel">
                <div class="content">
                    <div class="logo-circle">
                        <img src="../Assets/image/logo.png" alt="Logo">
                    </div>
                    <h1 style="font-size: 2.2rem; text-align: center; color: var(--primary-blue); font-weight: 800;">
                        Welcome to<br>SMS
                    </h1>
                    <p style="margin-top: 20px;">
                        Empowering education through a unified academic management system that enhances
                        learning, streamlines processes, and connects the academic community.
                    </p>

                    <!-- Small instruction text -->

                </div>
            </div>


        </div>
    </div>
    </div>

    <script src="../Assets/javascript/log-reg.js"></script>
    <script>

        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function () {
                const input = this.parentElement.querySelector('input');
                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            });
        });
    </script>
    <?php endif; ?>
</body>

</html>
        /* Shadow Override for Login */
        .container {
            box-shadow: 0 20px 60px rgba(59, 130, 246, 0.25) !important;
        }
