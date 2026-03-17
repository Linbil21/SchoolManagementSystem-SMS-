<?php
session_start();
require_once '../../../Database/config.php';

// Robust absolute-relative path logic
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/student/', '/Super-admin/', '/Admin/', '/Cashier/', '/Admission/', '/auth/', '/modules/'];
$project_base = '';
foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        $project_base = rtrim(substr($script_name, 0, $pos), '/');
        break;
    }
}
$root = $project_base . '/';

// Security check
require_once '../../../auth/Security.php';
checkRole(['student']);

try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->execute([$_SESSION['student_id']]);
    $student_data = $stmt->fetch();

    if (!$student_data) {
        die("Student profile not found.");
    }

    $middle_name = !empty($student_data->middle_name) ? $student_data->middle_name . ' ' : '';
    $student = [
        'name' => strtoupper($student_data->first_name . ' ' . $middle_name . $student_data->last_name),
        'id' => $student_data->student_id,
        'course' => strtoupper($student_data->course),
        'year' => strtoupper($student_data->year_level),
        'school_year' => '2025-2026',
        'profile_image' => $student_data->profile_image
            ? $root . $student_data->profile_image
            : "https://ui-avatars.com/api/?name=" . urlencode($student_data->first_name . ' ' . $student_data->last_name) . "&background=random&size=128"
    ];
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student ID</title>
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
            --id-gold: #fbbf24;
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
            text-align: center;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        /* ID Card Styles */
        .id-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
        }

        .student-id-card {
            width: 320px;
            height: 500px;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border-radius: 20px;
            box-shadow: 0 20px 40px -5px rgba(30, 58, 138, 0.4);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            color: white;
            transition: transform 0.3s;
        }

        .student-id-card:hover {
            transform: translateY(-10px);
        }

        /* Decorative Elements */
        .card-bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 100% 0%, rgba(255, 255, 255, 0.1) 20%, transparent 20%),
                radial-gradient(circle at 0% 100%, rgba(255, 255, 255, 0.1) 20%, transparent 20%);
            z-index: 0;
        }

        .id-header {
            padding: 20px;
            text-align: center;
            z-index: 1;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }

        .id-header img {
            width: 40px;
            margin-bottom: 5px;
        }

        .school-name {
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .id-photo-area {
            padding: 20px 0;
            display: flex;
            justify-content: center;
            z-index: 1;
        }

        .photo-frame {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            border: 4px solid var(--id-gold);
            overflow: hidden;
            background: white;
            padding: 3px;
        }

        .photo-frame img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .id-details {
            text-align: center;
            padding: 0 20px;
            flex-grow: 1;
            z-index: 1;
        }

        .student-name {
            font-size: 1.2rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 5px;
            color: var(--id-gold);
        }

        .student-no {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .course-info {
            font-size: 0.75rem;
            background: rgba(255, 255, 255, 0.15);
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 20px;
        }

        .validity {
            font-size: 0.7rem;
            opacity: 0.7;
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
            left: 0;
        }

        .qr-section {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .qr-box {
            background: white;
            padding: 5px;
            border-radius: 8px;
        }

        .qr-box img {
            width: 50px;
            height: 50px;
        }

        .action-bar {
            margin-top: 40px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: white;
            border: 1px solid #cbd5e1;
            color: #475569;
        }

        .btn-secondary:hover {
            background: #f1f5f9;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 30px;
            border-radius: 24px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .qr-full {
            width: 200px;
            height: 200px;
            margin: 20px auto;
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Digital Student ID</h1>
                <p style="color: #64748b;">This digital ID is valid for school transactions and entry.</p>
            </div>

            <div class="id-container">
                <!-- Front of ID -->
                <div class="student-id-card">
                    <div class="card-bg-pattern"></div>

                    <div class="id-header">
                        <div class="school-name">UNIVERSITY OF TECHNOLOGY</div>
                        <div style="font-size: 0.65rem; opacity: 0.8;">ESTABLISHED 2026</div>
                    </div>

                    <div class="id-photo-area">
                        <div class="photo-frame">
                            <img src="<?php echo $student['profile_image']; ?>" alt="Student Photo">
                        </div>
                    </div>

                    <div class="id-details">
                        <div class="student-name">
                            <?php echo $student['name']; ?>
                        </div>
                        <div class="student-no">
                            <?php echo $student['id']; ?>
                        </div>
                        <div class="course-info">
                            <?php echo $student['course']; ?>
                        </div>

                        <div class="qr-section">
                            <div class="qr-box" onclick="openQRModal()" style="cursor: pointer;"
                                title="Click to Expand">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $student['id']; ?>"
                                    alt="QR Code">
                            </div>
                        </div>
                    </div>

                    <div class="validity">
                        VALID UNTIL: JULY 2027<br>
                        STUDENT SIGNATURE
                    </div>
                </div>

                <div class="action-bar">
                    <a href="<?php echo $root; ?>student/Modules/ID/Download.php" class="btn btn-primary">
                        <i class="fas fa-download"></i> Download ID
                    </a>
                    <a href="<?php echo $root; ?>student/Modules/ID/Replacement.php" class="btn btn-secondary">
                        <i class="fas fa-exclamation-circle"></i> Report Lost Header
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- QR Modal -->
    <div id="qrModal" class="modal">
        <div class="modal-content">
            <h2 style="color: #1e293b; margin-bottom: 5px;">Scan QR Code</h2>
            <p style="color: #64748b; font-size: 0.9rem;">Show this code at the gate or library.</p>
            <img class="qr-full"
                src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?php echo $student['id']; ?>"
                alt="Formatted Details">
            <button onclick="closeQRModal()" class="btn btn-secondary"
                style="width: 100%; justify-content: center; margin-top: 10px;">Close</button>
        </div>
    </div>

    <script>
        const modal = document.getElementById('qrModal');

        function openQRModal() {
            modal.style.display = "flex";
        }

        function closeQRModal() {
            modal.style.display = "none";
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                closeQRModal();
            }
        }
    </script>
</body>

</html>
