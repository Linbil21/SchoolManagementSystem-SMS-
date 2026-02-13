<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    // Ensure the path to Login is correct based on directory structure
    header("Location: ../../auth/Login.php");
    exit();
}

// Handle AJAX Request for Live Fetch via Proxy
if (isset($_GET['ajax_live'])) {
    header('Content-Type: application/json');
    $api_url = 'https://sis.jampzdev.com/config/api/get_masterlist.php';
    
    // Suppress errors and fetch content
    $json_data = @file_get_contents($api_url);
    
    if ($json_data === FALSE) {
        // Fallback or error response
        echo json_encode(['status' => 'error', 'message' => 'Failed to connect to API']);
    } else {
        echo $json_data;
    }
    exit;
}

$current_page = 'Fetch-Table.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masterlist Live Monitor</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #64748b;
            --success: #10b981;
            --bg-surface: #ffffff;
            --bg-body: #f1f5f9;
            --border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background: var(--bg-body); display: flex; min-height: 100vh; color: var(--text-main); }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; width: 100%; }
        .content-area { padding: 30px; max-width: 1600px; margin: 0 auto; width: 100%; }

        .header-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            background: var(--bg-surface);
            padding: 20px 25px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
        }

        .header-strip h1 { 
            font-size: 1.5rem; 
            font-weight: 700; 
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ecfdf5;
            color: var(--success);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid #d1fae5;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .table-container {
            background: var(--bg-surface);
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
            overflow: hidden;
            position: relative;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .data-table th {
            text-align: left;
            padding: 18px 24px;
            background: #f8fafc;
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
        }

        .data-table td {
            padding: 18px 24px;
            font-size: 0.875rem;
            color: var(--text-main);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            transition: background 0.2s;
        }

        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: #f8fafc; }

        .student-meta {
            display: flex;
            flex-direction: column;
        }
        .student-name { font-weight: 600; color: var(--text-main); }
        .student-id { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }

        .badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-section { background: #eff6ff; color: var(--primary); border: 1px solid #dbeafe; }
        .badge-room { background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; }
        .badge-schedule { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; display: inline-flex; align-items: center; gap: 5px;}

        .empty-state {
            padding: 60px;
            text-align: center;
            color: var(--text-muted);
        }

        /* Loading */
        #loader {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(2px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
            flex-direction: column;
            gap: 15px;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(37, 99, 235, 0.2);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        /* Animation for new rows */
        .fade-in { animation: fadeIn 0.5s ease-out forwards; opacity: 0; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    </style>
</head>
<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        
        <div class="content-area">
            <div class="header-strip">
                <h1>
                    <i class="fas fa-satellite-dish" style="color: var(--primary);"></i>
                    Live Academic Masterlist
                </h1>
                <div class="live-badge">
                    <div class="pulse-dot"></div>
                    LIVE FEED ACTIVE
                </div>
            </div>

            <div class="table-container">
                <div id="loader">
                    <div class="spinner"></div>
                    <div style="font-weight: 500; color: var(--text-muted);">Syncing data...</div>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student Information</th>
                            <th>Section / Program</th>
                            <th>Subject</th>
                            <th>Schedule</th>
                            <th>Room</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <!-- Data rows will populate here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.getElementById('table-body');
            const loader = document.getElementById('loader');
            let isFirstLoad = true;

            const fetchData = async () => {
                try {
                    const response = await fetch('?ajax_live=1');
                    const result = await response.json();

                    if (isFirstLoad) {
                        loader.style.display = 'none';
                        isFirstLoad = false;
                    }

                    if (result.status === 'success' && Array.isArray(result.data)) {
                        updateTable(result.data);
                    } else {
                        console.error('Invalid data format received');
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                }
            };

            const updateTable = (data) => {
                // Clear existing content securely or diff it (simple clear for now)
                tableBody.innerHTML = '';

                if (data.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 15px; opacity: 0.5;"></i>
                                    <p>No active records found in masterlist</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                data.forEach((item, index) => {
                    // Create row element
                    const tr = document.createElement('tr');
                    tr.className = 'fade-in';
                    tr.style.animationDelay = `${index * 50}ms`;

                    tr.innerHTML = `
                        <td>
                            <div class="student-meta">
                                <span class="student-name">${item.studentName}</span>
                                <span class="student-id"><i class="far fa-id-card"></i> ${item.studentID}</span>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                                    <i class="far fa-envelope"></i> ${item.studentEmail || 'N/A'}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span class="badge badge-section">${item.sectionName}</span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">${item.program} - ${item.gradeLevel}</span>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 500; color: var(--text-main);">${item.subjectName || 'N/A'}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">${item.subjectID || '-'}</div>
                            ${item.teacherName ? `<div style="font-size: 0.7rem; color: var(--primary); margin-top: 2px;"><i class="fas fa-chalkboard-teacher"></i> ${item.teacherName}</div>` : ''}
                        </td>
                        <td>
                            <div class="badge badge-schedule">
                                <i class="far fa-clock"></i>
                                ${item.schedule || (item.day + ' ' + item.startTime)}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-room">${item.roomName}</span>
                        </td>
                        <td>
                            <span style="color: var(--success); font-size: 0.75rem; font-weight: 600;">
                                <i class="fas fa-check-circle"></i> Active
                            </span>
                        </td>
                    `;
                    tableBody.appendChild(tr);
                });
            };

            // Initial fetch
            fetchData();

            // Live polling every 5 seconds
            setInterval(fetchData, 5000);
        });
    </script>
</body>
</html>
