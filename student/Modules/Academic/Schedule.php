<?php
session_start();
require_once '../../../auth/Security.php';
// checkRole(['student']); // Temporarily disable strict role check if needed or keep enabled

require_once '../../../integration/Student_class.php';

$student_name = $_SESSION['fullname'] ?? 'Student';
$student_id = $_SESSION['student_id'] ?? null;
$current_page = 'Schedule.php';

// Initialize Integration Class
$portal = new StudentPortal();

// FETCH FROM EXTERNAL API
$api_url = 'https://css.jampzdev.com/api/student-subject.php';
$json_data = @file_get_contents($api_url);
$api_response = json_decode($json_data, true);

$weekly_schedule = [];
$days_lookup = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

// Initialize empty days
foreach($days_lookup as $day) {
    $weekly_schedule[$day] = [];
}

if ($api_response && isset($api_response['status']) && $api_response['status'] === 'success') {
    $data_list = $api_response['data'];
    
    foreach ($data_list as $item) {
        $day = $item['day'];
        
        // Format Time
        $start_time = date("h:i A", strtotime($item['startTime']));
        $end_time = date("h:i A", strtotime($item['endTime']));
        
        // Assign color based on subject (simple hash or random for demo)
        $color_hash = md5($item['subjectName']);
        $colors = ['#2563eb', '#9333ea', '#16a34a', '#db2777', '#f59e0b', '#ea580c', '#0ea5e9'];
        $color = $colors[hexdec(substr($color_hash, 0, 1)) % count($colors)];

        $schedule_item = [
            'time' => $start_time,
            'end' => $end_time,
            'subject' => $item['subjectName'],
            'code' => $item['subjectID'], // Added code field
            'room' => $item['roomName'],
            'teacher' => $item['teacherName'],
            'color' => $color,
            'units' => 3.0 // Default units as API doesnt provide it
        ];
        
        if (isset($weekly_schedule[$day])) {
            $weekly_schedule[$day][] = $schedule_item;
        }
    }
} else {
    // Fallback Mock data if API fails
    $weekly_schedule = [
        'Monday' => [
            ['time' => '08:00 AM', 'end' => '10:00 AM', 'subject' => 'Web Development 101', 'code' => 'IT101', 'room' => 'Lab 3', 'teacher' => 'Mr. Anderson', 'color' => '#2563eb', 'units'=>3.0],
        ],
        // ... (minimal fallback to avoid empty page)
    ];
}

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedule - Student Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        [data-theme="dark"] {
            --bg: #000000;
            --card-bg: #111111;
            --text-main: #ffffff;
            --text-muted: #94a3b8;
            --border: #333333;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg); display: flex; min-height: 100vh; color: var(--text-main); }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        .content-area { padding: 40px; }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title h1 { font-size: 1.8rem; font-weight: 800; letter-spacing: -1px; }
        .page-title p { color: var(--text-muted); font-size: 0.95rem; }

        /* Tabular Layout Styles */
        .data-table-container {
            background: var(--card-bg);
            border-radius: 30px;
            padding: 30px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
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
            padding: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--border);
        }

        .data-table td {
            padding: 20px;
            font-size: 0.95rem;
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
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
        }

        .day-tag {
            background: #f1f5f9;
            color: #475569;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 5px;
        }

        @media print {
            @page {
                size: portrait;
                margin: 10mm;
            }
            body { background: white; color: black; font-size: 10pt; }
            .sidebar, .header, .header-actions, .page-header, .calendar-container, .data-table-container, #sidebar-toggle { display: none !important; }
            .main-wrapper { margin-left: 0 !important; }
            .content-area { padding: 0 !important; }
            .cor-print-only { display: block !important; padding: 0; }
        }

        .cor-print-only {
            display: none;
            width: 100%;
            background: white;
            color: black;
            padding: 40px;
            font-family: 'Times New Roman', Times, serif;
        }

        .cor-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .cor-header h2 { font-size: 1.3rem; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; }
        .cor-header p { font-size: 0.8rem; margin-bottom: 1px; }

        .cor-title {
            text-align: center;
            margin-bottom: 20px;
            text-decoration: underline;
            font-weight: 800;
            font-size: 1.1rem;
        }

        .student-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .info-row { display: flex; gap: 8px; margin-bottom: 4px; }
        .info-label { font-weight: 700; min-width: 110px; }

        .cor-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .cor-table th {
            background: #f3f4f6;
            border: 1px solid #000;
            padding: 8px;
            font-size: 0.8rem;
            text-align: left;
            text-transform: uppercase;
        }

        .cor-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 0.8rem;
        }

        .cor-footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .sig-box {
            text-align: center;
            width: 180px;
        }

        .sig-line { border-top: 1px solid #000; margin-top: 30px; padding-top: 5px; font-weight: 700; font-size: 0.75rem; }
    </style>
</head>
<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <!-- Professional Data Table Area -->

            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Schedule</th>
                            <th>Room</th>
                            <th>Instructor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $tabular_data = $portal->getConsolidatedSubjects($weekly_schedule);
                        if (!empty($tabular_data)):
                            foreach ($tabular_data as $row):
                        ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-main); margin-bottom: 2px;"><?php echo htmlspecialchars($row['subject']); ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">CODE: <?php echo $row['code']; ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; margin-bottom: 5px;"><?php echo $row['time']; ?></div>
                                    <?php foreach ($row['days'] as $d): ?>
                                        <span class="day-tag"><?php echo $d; ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <div class="sub-badge">
                                        <i class="fas fa-door-open" style="margin-right: 5px; opacity: 0.6;"></i>
                                        <?php echo htmlspecialchars($row['room']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?php echo htmlspecialchars($row['teacher']); ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">Academic Dept.</div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 0.75rem; color: #10b981;">
                                        <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                                        Verified
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            endforeach; 
                        else: 
                        ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 50px; color: var(--text-muted);">
                                    <i class="fas fa-calendar-times" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                                    No subjects found for current semester.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Professional COR Print Layout (Hidden on Screen) -->
            <div class="cor-print-only">
                <div class="cor-header">
                    <img src="/Assets/image/logo.png" style="width: 80px; height: 80px; margin-bottom: 10px;" alt="Logo">
                    <h2>Modern State University</h2>
                    <p>University Avenue, Knowledge Link, Philippines</p>
                    <p>Contact: info@msu.edu.ph | Tel: (02) 888-1234</p>
                    <p><strong>Office of the University Registrar</strong></p>
                </div>

                <div class="cor-title">CERTIFICATE OF REGISTRATION (COR)</div>

                <div class="student-info-grid">
                    <div class="info-left">
                        <div class="info-row"><span class="info-label">Student Name:</span> <span><?php echo strtoupper($student_name); ?></span></div>
                        <div class="info-row"><span class="info-label">Student ID:</span> <span><?php echo $_SESSION['student_id'] ?? '2024-0' . rand(100, 999); ?></span></div>
                        <div class="info-row"><span class="info-label">Course:</span> <span>BS Information Technology</span></div>
                    </div>
                    <div class="info-right">
                        <div class="info-row"><span class="info-label">Semester:</span> <span>2nd Semester</span></div>
                        <div class="info-row"><span class="info-label">School Year:</span> <span>2025-2026</span></div>
                        <div class="info-row"><span class="info-label">Date Issued:</span> <span><?php echo date('F d, Y'); ?></span></div>
                    </div>
                </div>

                <table class="cor-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Description</th>
                            <th>Schedule / Days</th>
                            <th>Room</th>
                            <th>Instructor</th>
                            <th>Units</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_units = 0;
                        $consolidated = $portal->getConsolidatedSubjects($weekly_schedule);

                        foreach ($consolidated as $data): 
                            $total_units += floatval($data['units']);
                        ?>
                        <tr>
                            <td><?php echo $data['code']; ?></td>
                            <td><?php echo htmlspecialchars($data['subject']); ?></td>
                            <td><?php echo implode('/', $data['days']) . ' ' . $data['time']; ?></td>
                            <td><?php echo htmlspecialchars($data['room']); ?></td>
                            <td><?php echo htmlspecialchars($data['teacher']); ?></td>
                            <td><?php echo $data['units']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr style="background: #f9fafb; font-weight: 800;">
                            <td colspan="5" style="text-align: right;">TOTAL ACADEMIC UNITS:</td>
                            <td><?php echo number_format($total_units, 1); ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="cor-footer">
                    <div class="sig-box">
                        <div class="sig-line">STUDENT SIGNATURE</div>
                    </div>
                    <div class="sig-box">
                        <div class="sig-line">REGISTRAR SIGNATURE</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
