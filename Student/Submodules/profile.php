<?php
session_start();
require_once '../../Database/config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/Login.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->execute([$_SESSION['student_id']]);
    $student = $stmt->fetch();

    if (!$student) {
        die("Student profile not found.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$student_name = $student->first_name . ' ' . $student->last_name;
$profile_img = !empty($student->profile_image)
    ? "/sms/" . $student->profile_image
    : "https://ui-avatars.com/api/?name=" . urlencode($student_name) . "&background=2563eb&color=fff";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Information | Student Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --border: #e2e8f0;
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
            max-width: 1100px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            margin-bottom: 35px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .page-title h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .profile-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 30px;
        }

        /* Sidebar Card */
        .profile-side {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .card {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
        }

        .profile-photo-card {
            text-align: center;
        }

        .photo-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }

        .photo-container img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .upload-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 35px;
            height: 35px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 3px solid #fff;
            transition: 0.3s;
        }

        .upload-btn:hover {
            transform: scale(1.1);
            background: var(--primary-dark);
        }

        .profile-name h2 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .profile-name p {
            font-size: 0.85rem;
            color: var(--secondary);
            margin-bottom: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            background: #ecfdf5;
            color: #059669;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* Details Card */
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .info-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .info-group label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-group .value {
            padding: 12px 18px;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text-main);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .section-title {
            grid-column: span 2;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 15px 0 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        @media (max-width: 991px) {
            .profile-layout {
                grid-template-columns: 1fr;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .section-title {
                grid-column: span 1;
            }
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <div class="page-title">
                    <h1>Personal Information</h1>
                    <p style="color: #64748b;">View and manage your student profile details.</p>
                </div>
            </div>

            <div class="profile-layout">
                <div class="profile-side">
                    <!-- Profile Photo Card -->
                    <div class="card profile-photo-card">
                        <div class="photo-container">
                            <img src="<?php echo $profile_img; ?>" alt="Profile Photo">
                            <div class="upload-btn">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>
                        <div class="profile-name">
                            <h2>
                                <?php echo htmlspecialchars($student_name); ?>
                            </h2>
                            <p>Student ID:
                                <?php echo htmlspecialchars($student->student_id); ?>
                            </p>
                            <span class="status-badge">
                                <?php echo htmlspecialchars($student->status ?? 'Regular'); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card">
                        <h4 style="font-size: 0.9rem; margin-bottom: 20px; color: var(--text-main);">Academic Progress
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                                <span style="color: var(--secondary);">Course</span>
                                <span style="font-weight: 600; color: var(--text-main);">
                                    <?php echo htmlspecialchars($student->course); ?>
                                </span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                                <span style="color: var(--secondary);">Year Level</span>
                                <span style="font-weight: 600; color: var(--text-main);">
                                    <?php echo htmlspecialchars($student->year_level); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-main">
                    <div class="card">
                        <div class="details-grid">
                            <h3 class="section-title">Personal Details</h3>

                            <div class="info-group">
                                <label>First Name</label>
                                <div class="value">
                                    <?php echo htmlspecialchars($student->first_name); ?>
                                </div>
                            </div>
                            <div class="info-group">
                                <label>Last Name</label>
                                <div class="value">
                                    <?php echo htmlspecialchars($student->last_name); ?>
                                </div>
                            </div>
                            <div class="info-group">
                                <label>Email Address</label>
                                <div class="value">
                                    <?php echo htmlspecialchars($student->email); ?>
                                </div>
                            </div>
                            <div class="info-group">
                                <label>Gender</label>
                                <div class="value">
                                    <?php echo htmlspecialchars($student->gender ?? 'Not Specified'); ?>
                                </div>
                            </div>

                            <h3 class="section-title">Academic Details</h3>

                            <div class="info-group" style="grid-column: span 2;">
                                <label>Course / Program</label>
                                <div class="value">
                                    <?php echo htmlspecialchars($student->course); ?>
                                </div>
                            </div>
                            <div class="info-group">
                                <label>Current Year</label>
                                <div class="value">
                                    <?php echo htmlspecialchars($student->year_level); ?>
                                </div>
                            </div>
                            <div class="info-group">
                                <label>Student Status</label>
                                <div class="value">
                                    <?php echo htmlspecialchars($student->status ?? 'Regular'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>