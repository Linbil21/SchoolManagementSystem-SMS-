<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}

require_once '../../integration/Student_class.php';

// Handle AJAX Request for Live Fetch
if (isset($_GET['ajax']) && !empty($_GET['student_id'])) {
    header('Content-Type: application/json');
    $portal = new StudentPortal();
    $data = $portal->getStudentSubjects($_GET['student_id']);
    echo json_encode($data);
    exit;
}

$current_page = 'Student-Live-Sync.php';
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
        body { background: var(--bg); display: flex; min-height: 100vh; color: var(--text-main); transition: 0.3s; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; overflow-x: hidden; }
        .content-area { padding: 40px; }

        .search-container {
            background: var(--card-bg);
            padding: 15px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            margin-bottom: 25px;
        }

        .search-box { display: flex; gap: 12px; }
        .search-box input {
            flex: 1;
            padding: 12px 20px;
            padding-left: 45px;
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
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-fetch:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(22, 72, 188, 0.2); }
        .btn-fetch:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .data-table-container {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border);
            overflow: hidden;
            display: none; 
        }

        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th {
            text-align: left;
            padding: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            background: rgba(0,0,0,0.02);
            border-bottom: 2px solid var(--border);
        }
        .data-table td {
            padding: 18px 20px;
            font-size: 0.9rem;
            color: var(--text-main);
            border-bottom: 1px solid var(--border);
            transition: 0.2s;
        }
        .data-table tr:hover td { background: var(--bg); }

        .day-tag {
            background: #f1f5f9;
            color: #475569;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .sub-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
            background: rgba(22, 72, 188, 0.08);
            color: var(--primary);
        }

        /* Live Effects */
        .loading-state {
            text-align: center;
            padding: 100px 20px;
            display: none;
        }

        .placeholder-state {
            text-align: center;
            padding: 100px 20px;
            color: var(--text-muted);
            background: var(--card-bg);
            border-radius: 24px;
            border: 1px dashed var(--border);
        }

        .fade-in-row {
            animation: fadeIn 0.5s ease forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #sync-status {
            font-size: 0.75rem;
            margin-top: 10px;
            color: var(--success);
            font-weight: 600;
            display: none;
            align-items: center;
            gap: 5px;
        }

        .pulse {
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
            display: inline-block;
            animation: pulse-ring 1.5s infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            
            <div class="search-container">
                <form id="fetchForm" class="search-box">
                    <div style="position: relative; flex: 1;">
                        <i class="fas fa-search" style="position: absolute; left: 16px; top: 52%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.95rem;"></i>
                        <input type="text" id="student_id" placeholder="Enter Student ID for Live Data Table Sync..." required>
                    </div>
                    <button type="submit" class="btn-fetch" id="submitBtn">
                        <i class="fas fa-satellite-dish"></i> SYNC LIVE
                    </button>
                </form>
                <div id="sync-status">
                    <span class="pulse"></span> CONNECTED: Fetching real-time records...
                </div>
            </div>

            <!-- Table Container -->
            <div id="tableContainer" class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Time / Session</th>
                            <th>Day</th>
                            <th>Room</th>
                            <th>Instructor</th>
                            <th>Section</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Rows populated via JS -->
                    </tbody>
                </table>
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="loading-state">
                <i class="fas fa-circle-notch fa-spin" style="font-size: 3rem; color: var(--primary); margin-bottom: 20px;"></i>
                <h3 style="color: var(--text-main);">Syncing Academic Data...</h3>
                <p style="color: var(--text-muted);">Accessing https://css.jampzdev.com/api/student-subject.php</p>
            </div>

            <!-- Placeholder -->
            <div id="placeholderState" class="placeholder-state">
                <i class="fas fa-table" style="font-size: 3.5rem; margin-bottom: 20px; opacity: 0.15;"></i>
                <h3 style="color: var(--text-main);">Waiting for Feed...</h3>
                <p>Input a Student ID above to stream the live academic subjects into this table.</p>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('fetchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const studentId = document.getElementById('student_id').value;
            if (!studentId) return;

            // UI Feedback
            const btn = document.getElementById('submitBtn');
            const placeholder = document.getElementById('placeholderState');
            const loading = document.getElementById('loadingState');
            const container = document.getElementById('tableContainer');
            const tbody = document.getElementById('tableBody');
            const status = document.getElementById('sync-status');

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> SYNCING...';
            placeholder.style.display = 'none';
            loading.style.display = 'block';
            container.style.display = 'none';
            status.style.display = 'flex';
            status.innerHTML = '<span class="pulse"></span> CONNECTED: Fetching real-time records...';
            tbody.innerHTML = '';

            // Live Fetch
            fetch(`?ajax=1&student_id=${encodeURIComponent(studentId)}`)
                .then(response => response.json())
                .then(data => {
                    loading.style.display = 'none';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-satellite-dish"></i> SYNC LIVE';

                    if (data && data.success && data.subjects && data.subjects.length > 0) {
                        container.style.display = 'block';
                        
                        data.subjects.forEach((sub, index) => {
                            setTimeout(() => {
                                const row = `
                                    <tr class="fade-in-row">
                                        <td>
                                            <div style="font-weight: 700;">${sub.subjectName || sub.subject_name}</div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted);">${sub.subjectID || 'N/A'}</div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;">${sub.startTime} - ${sub.endTime}</div>
                                        </td>
                                        <td><span class="day-tag">${sub.day || sub.days}</span></td>
                                        <td><div class="sub-badge">${sub.roomName || sub.room_name}</div></td>
                                        <td>
                                            <div style="font-weight: 600;">${sub.teacherName || sub.instructor}</div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted);">${sub.teacherID || 'TBA'}</div>
                                        </td>
                                        <td><div style="font-weight: 600;">${sub.sectionName || sub.section_name}</div></td>
                                    </tr>
                                `;
                                tbody.insertAdjacentHTML('beforeend', row);
                            }, index * 80); 
                        });
                        
                        status.innerHTML = '<span class="pulse"></span> LIVE SYNC ACTIVE: ' + data.subjects.length + ' Records Streamed';
                    } else {
                        status.style.display = 'none';
                        placeholder.style.display = 'block';
                        placeholder.innerHTML = `
                            <i class="fas fa-search-minus" style="font-size: 3rem; margin-bottom: 20px; color: #ef4444;"></i>
                            <h3 style="color: #ef4444;">No Records Found</h3>
                            <p>Could not find any live subjects for ID: <b>${studentId}</b></p>
                        `;
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    loading.style.display = 'none';
                    placeholder.style.display = 'block';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-satellite-dish"></i> SYNC LIVE';
                    alert('Integration Error: Could not connect to the subject API repository.');
                });
        });
    </script>
</body>

</html>
