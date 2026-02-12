<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}

require_once '../../integration/Student_class.php';

$current_page = 'Student-Live-Sync.php';
$portal = new StudentPortal();
$student_id = $_GET['student_id'] ?? '';
$apiData = null;
$error = null;

if (!empty($student_id)) {
    $apiData = $portal->getStudentSubjects($student_id);
    if (!$apiData || !isset($apiData['success']) || $apiData['success'] !== true) {
        $error = "No live data found for Student ID: " . htmlspecialchars($student_id);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Student Fetcher - Admission Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1648bc;
            --success: #10b981;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        [data-theme="dark"] {
            --bg: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg); display: flex; min-height: 100vh; color: var(--text-main); }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; overflow-x: hidden; }
        .content-area { padding: 40px; }

        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 1.8rem; font-weight: 800; letter-spacing: -0.5px; }
        .page-header p { color: var(--text-muted); font-size: 0.95rem; }

        .search-container {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            margin-bottom: 30px;
            max-width: 800px;
        }

        .search-box {
            display: flex;
            gap: 15px;
        }

        .search-box input {
            flex: 1;
            padding: 12px 20px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-size: 0.95rem;
            outline: none;
            transition: 0.3s;
            background: var(--bg);
            color: var(--text-main);
        }

        .search-box input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(22, 72, 188, 0.1); }

        .btn-fetch {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0 30px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-fetch:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(22, 72, 188, 0.2); }

        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ecfdf5;
            color: #059669;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .pulse {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse-ring 1.5s infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .data-table-container {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .data-table th {
            text-align: left;
            padding: 18px 20px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--border);
        }

        .data-table td {
            padding: 18px 20px;
            font-size: 0.9rem;
            color: var(--text-main);
            border-bottom: 1px solid var(--border);
            transition: 0.3s;
        }

        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: var(--bg); }

        .sub-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
            background: rgba(22, 72, 188, 0.1);
            color: var(--primary);
        }

        .day-tag {
            background: #f1f5f9;
            color: #475569;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #10b981;
        }

        .status-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            background: #10b981;
            border-radius: 50%;
        }

        .error-msg {
            background: #fef2f2;
            color: #ef4444;
            padding: 20px;
            border-radius: 15px;
            border: 1px solid #fee2e2;
            display: flex;
            align-items: center;
            gap: 12px;
            max-width: 800px;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <div class="live-badge">
                    <span class="pulse"></span> LIVE API ACCESS
                </div>
                <h1>Student Academic Sync</h1>
                <p>Fetch real-time subject and schedule data directly from the central repository.</p>
            </div>

            <div class="search-container">
                <form method="GET" class="search-box">
                    <input type="text" name="student_id" placeholder="Enter Student ID (e.g., 2026-0001)" value="<?php echo htmlspecialchars($student_id); ?>" required>
                    <button type="submit" class="btn-fetch">
                        <i class="fas fa-satellite-dish"></i> Fetch Live Data
                    </button>
                </form>
            </div>

            <?php if ($error): ?>
                <div class="error-msg">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($apiData && isset($apiData['subjects'])): ?>
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Schedule</th>
                                <th>Room</th>
                                <th>Instructor</th>
                                <th>Section</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($apiData['subjects'] as $subject): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($subject['subjectName'] ?? $subject['subject_name']); ?></div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">CODE: <?php echo $subject['subjectID'] ?? 'N/A'; ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo $subject['startTime'] ?? $subject['start_time']; ?> - <?php echo $subject['endTime'] ?? $subject['end_time']; ?></div>
                                        <span class="day-tag"><?php echo $subject['day'] ?? $subject['days']; ?></span>
                                    </td>
                                    <td>
                                        <div class="sub-badge"><i class="fas fa-door-open" style="margin-right: 5px;"></i><?php echo htmlspecialchars($subject['roomName'] ?? $subject['room_name']); ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo htmlspecialchars($subject['teacherName'] ?? $subject['instructor']); ?></div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">Prof. ID: <?php echo $subject['teacherID'] ?? 'N/A'; ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo htmlspecialchars($subject['sectionName'] ?? $subject['section_name']); ?></div>
                                    </td>
                                    <td>
                                        <div class="status-badge">Active Sync</div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($student_id && !$error): ?>
                <div style="text-align: center; padding: 60px; color: var(--text-muted);">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 20px;"></i>
                    <p>Connecting to secure API servers...</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
