<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}
require_once '../../integration/faculty.php';

// Handle AJAX Request for Faculty Fetch
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    $portal = new FacultyPortal();
    $data = $portal->getAllFaculty();
    
    // Auto-adapter for API response structure
    $response = [
        'success' => false,
        'data' => []
    ];

    if ($data) {
        $response['success'] = true;
        // Handle if data is directly the array or wrapped in a 'data'/'faculty' key
        $response['data'] = $data['data'] ?? $data['faculty'] ?? $data['list'] ?? (is_array($data) ? $data : []);
    }
    
    echo json_encode($response);
    exit;
}

$current_page = 'Teacher-Management.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Management</title>
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
            color: #3b82f6;
            background: #eff6ff;
            padding: 5px 12px;
            border-radius: 20px;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #3b82f6;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
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
                    <h1>Teacher Management</h1>
                    <p style="color: var(--text-muted); font-size: 0.85rem;">Live Faculty Repository Feed</p>
                </div>
                <div class="live-indicator">
                    <span class="pulse-dot"></span> FACULTY API CONNECTED
                </div>
            </div>

            <div class="table-card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Faculty Name</th>
                            <th>Department</th>
                            <th>Subject</th>
                            <th>Section</th>
                            <th>Room</th>
                            <th>Day</th>
                            <th>Schedule</th>
                        </tr>
                    </thead>
                    <tbody id="fetch-body">
                        <!-- Content populates here -->
                    </tbody>
                </table>
                <div id="loading-overlay">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #3b82f6; margin-bottom: 15px;"></i>
                    <p style="color: var(--text-muted); font-weight: 600;">Fetching Faculty Data...</p>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('fetch-body');
            const loader = document.getElementById('loading-overlay');

            // Automatic Fetch from Teacher API
            fetch('?ajax=1')
                .then(response => response.json())
                .then(res => {
                    loader.style.display = 'none';
                    const faculties = res.data?.faculties || [];
                    if (res && res.success && faculties.length > 0) {
                        let globalIndex = 0;
                        faculties.forEach((faculty) => {
                            const prof = faculty.professorInfo || {};
                            const fullName = `${prof.firstName} ${prof.lastName}`;
                            const email = prof.workEmail || 'N/A';
                            const position = prof.position || 'Professor';
                            
                            (faculty.sections || []).forEach((section) => {
                                const sectionName = section.name || 'N/A';
                                const room = section.room || 'TBA';
                                const program = section.program?.toUpperCase() || 'GE';
                                
                                (section.subjects || []).forEach((subject) => {
                                    setTimeout(() => {
                                        const row = `
                                            <tr class="fade-in">
                                                <td><span style="font-weight: 700; color: var(--text-muted);">${++globalIndex}</span></td>
                                                <td>
                                                    <div style="font-weight: 700;">${fullName}</div>
                                                    <div style="font-size: 0.7rem; color: var(--text-muted);">${email}</div>
                                                </td>
                                                <td><div style="font-weight: 600;">${position}</div></td>
                                                <td>
                                                    <div style="font-weight: 700;">${subject.name || 'Unknown'}</div>
                                                    <div style="font-size: 0.7rem; color: var(--text-muted);">ID: ${subject.id?.substring(0,8) || 'N/A'}</div>
                                                </td>
                                                <td><div style="font-weight: 600;">${sectionName} [${program}]</div></td>
                                                <td><span class="badge-room">${room}</span></td>
                                                <td><span class="badge-day">${subject.day || '-'}</span></td>
                                                <td><div style="font-weight: 600;">${subject.startTime} - ${subject.endTime}</div></td>
                                            </tr>
                                        `;
                                        tbody.insertAdjacentHTML('beforeend', row);
                                    }, globalIndex * 60);
                                });
                            });
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px; color: #ef4444; font-weight: 600;">No live faculty data available.</td></tr>';
                    }
                })
                .catch(err => {
                    loader.innerHTML = '<i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #ef4444;"></i><p style="color: #ef4444; margin-top: 15px;">Connection to Faculty API failed.</p>';
                });
        });
    </script>
</body>
</html>
