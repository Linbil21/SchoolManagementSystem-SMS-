<?php
session_start();
// Ideally, check for student role here
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--bg);
            display: flex;
            min-height: 100vh;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .content-area {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .page-subtitle {
            color: var(--secondary);
            font-size: 0.95rem;
        }

        /* Form Card */
        .form-card {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            color: #1e293b;
            transition: all 0.2s;
            font-family: inherit;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .submit-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover {
            background: #1d4ed8;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Application for Admission</h1>
                <p class="page-subtitle">Fill out the form below to start your journey with us.</p>
            </div>

            <form action="" method="POST">
                <div class="form-card">
                    <h3 class="section-title">
                        <i class="fas fa-user" style="color: var(--primary);"></i> Personal Information
                    </h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-input" placeholder="e.g. John" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-input" placeholder="e.g. Doe" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Gender</label>
                            <select class="form-select" required>
                                <option value="">Select Gender</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-input" placeholder="e.g. john@example.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-input" placeholder="e.g. 09123456789" required>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <h3 class="section-title">
                        <i class="fas fa-graduation-cap" style="color: var(--primary);"></i> Program Preference
                    </h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Student Type</label>
                            <select class="form-select" required>
                                <option value="">Select Type</option>
                                <option>Incoming Freshman</option>
                                <option>Transferee</option>
                                <option>Second Courser</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Preferred Course (1st Choice)</label>
                            <select class="form-select" required>
                                <option value="">Select Course</option>
                                <option>BS Information Technology</option>
                                <option>BS Computer Science</option>
                                <option>BS Accountancy</option>
                                <option>BS Civil Engineering</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Preferred Course (2nd Choice)</label>
                            <select class="form-select">
                                <option value="">Select Course</option>
                                <option>BS Information Technology</option>
                                <option>BS Computer Science</option>
                                <option>BS Accountancy</option>
                                <option>BS Civil Engineering</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last School Attended</label>
                            <input type="text" class="form-input" placeholder="School Name">
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    Submit Application <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</body>

</html>