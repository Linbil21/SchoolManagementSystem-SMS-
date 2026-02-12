<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}
require_once '../../integration/Student_class.php';

// Handle AJAX Request for Live Fetch
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    $student_id = $_GET['student_id'] ?? '1'; // Default ID for live feed
    $portal = new StudentPortal();
    $data = $portal->getStudentSubjects($student_id);
    echo json_encode($data);
    exit;
}

$current_page = 'Fetch-Table.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Data Fetch Table</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1648bc;
            --bg: #f7fafc;
            --border: #edf2f7;
            --text: #2d3748;
            --text-muted: #718096;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg); display: flex; min-height: 100vh; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        .content-area { padding: 30px; }

        .header-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header-strip h1 { font-size: 1.5rem; font-weight: 800; color: #1e293b; }
        
        .live-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #059669;
            background: #ecfdf5;
            padding: 5px 12px;
            border-radius: 20px;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            text-align: left;
            padding: 15px 20px;
            background: #f8fafc;
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--border);
        }

        .data-table td {
            padding: 15px 20px;
            font-size: 0.85rem;
            color: var(--text);
            border-bottom: 1px solid var(--border);
        }

        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover { background: #fdfdfd; }

        .badge-room {
            background: rgba(22, 72, 188, 0.1);
            color: var(--primary);
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .badge-day {
            background: #edf2f7;
            color: #4a5568;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.7rem;
        }

        #loading-overlay {
            padding: 100px;
            text-align: center;
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            
            <div class="header-strip">
                <div>
                    <h1>Live Fetch Table</h1>
                    <p style="color: var(--text-muted); font-size: 0.85rem;">Streaming subjects from https://css.jampzdev.com/api/student-subject.php</p>
                </div>
                <div class="live-indicator">
                    <span class="pulse-dot"></span> API CONNECTED
                </div>
            </div>

            <div class="table-card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Subject Name</th>
                            <th>Instructor</th>
                            <th>Section</th>
                            <th>Room</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody id="fetch-body">
                        <!-- Content populates here -->
                    </tbody>
                </table>
                <div id="loading-overlay">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary); margin-bottom: 15px;"></i>
                    <p style="color: var(--text-muted); font-weight: 600;">Fetching Real-time Academic Data...</p>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('fetch-body');
            const loader = document.getElementById('loading-overlay');

            // Automatic Fetch
            fetch('?ajax=1&student_id=1')
                .then(response => response.json())
                .then(data => {
                    loader.style.display = 'none';
                    if (data && data.success && data.subjects) {
                        data.subjects.forEach((item, index) => {
                            setTimeout(() => {
                                const row = `
                                    <tr class="fade-in">
                                        <td><span style="font-weight: 700; color: var(--text-muted);">${item.id}</span></td>
                                        <td>
                                            <div style="font-weight: 700;">${item.subjectName}</div>
                                            <div style="font-size: 0.7rem; color: var(--text-muted);">CODE: ${item.subjectID}</div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;">${item.teacherName}</div>
                                            <div style="font-size: 0.7rem; color: var(--text-muted);">ID: ${item.teacherID}</div>
                                        </td>
                                        <td><div style="font-weight: 600;">${item.sectionName}</div></td>
                                        <td><span class="badge-room">${item.roomName}</span></td>
                                        <td><span class="badge-day">${item.day}</span></td>
                                        <td><div style="font-weight: 600;">${item.startTime} - ${item.endTime}</div></td>
                                        <td style="color: var(--text-muted); font-style: italic;">${item.notes || '-'}</td>
                                    </tr>
                                `;
                                tbody.insertAdjacentHTML('beforeend', row);
                            }, index * 80);
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px; color: #ef4444; font-weight: 600;">No live data available from the repository.</td></tr>';
                    }
                })
                .catch(err => {
                    loader.innerHTML = '<i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #ef4444;"></i><p style="color: #ef4444; margin-top: 15px;">Connection to API failed.</p>';
                });
        });
    </script>
</body>
</html>
