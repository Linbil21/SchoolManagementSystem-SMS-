<?php
session_start();
require_once '../../../auth/Security.php';
checkRole(['student']);

$student_name = $_SESSION['fullname'] ?? 'Student';
$current_page = 'Schedule.php';

// Mock schedule data for the week
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$weekly_schedule = [
    'Monday' => [
        ['time' => '08:00 AM', 'end' => '10:00 AM', 'subject' => 'Web Development 101', 'room' => 'Lab 3', 'teacher' => 'Mr. Anderson', 'color' => '#2563eb'],
        ['time' => '01:00 PM', 'end' => '02:30 PM', 'subject' => 'Data Structures', 'room' => 'Room 202', 'teacher' => 'Prof. Smith', 'color' => '#9333ea'],
    ],
    'Tuesday' => [
        ['time' => '10:00 AM', 'end' => '11:30 AM', 'subject' => 'Database Management', 'room' => 'Room 404', 'teacher' => 'Ms. Roberts', 'color' => '#16a34a'],
        ['time' => '03:00 PM', 'end' => '05:00 PM', 'subject' => 'UI/UX Design', 'room' => 'Design Lab', 'teacher' => 'Ms. Lopez', 'color' => '#db2777'],
    ],
    'Wednesday' => [
        ['time' => '08:00 AM', 'end' => '10:00 AM', 'subject' => 'Web Development 101', 'room' => 'Lab 3', 'teacher' => 'Mr. Anderson', 'color' => '#2563eb'],
        ['time' => '11:00 AM', 'end' => '12:30 PM', 'subject' => 'Discrete Mathematics', 'room' => 'Hall B', 'teacher' => 'Dr. Evans', 'color' => '#f59e0b'],
    ],
    'Thursday' => [
        ['time' => '10:00 AM', 'end' => '11:30 AM', 'subject' => 'Database Management', 'room' => 'Room 404', 'teacher' => 'Ms. Roberts', 'color' => '#16a34a'],
        ['time' => '01:00 PM', 'end' => '03:00 PM', 'subject' => 'Networking Fundamentals', 'room' => 'CISCO Lab', 'teacher' => 'Engr. Dave', 'color' => '#ea580c'],
    ],
    'Friday' => [
        ['time' => '08:00 AM', 'end' => '10:00 AM', 'subject' => 'Data Structures', 'room' => 'Room 202', 'teacher' => 'Prof. Smith', 'color' => '#9333ea'],
        ['time' => '03:00 PM', 'end' => '05:00 PM', 'subject' => 'Artificial Intelligence', 'room' => 'AI Room', 'teacher' => 'Dr. Chen', 'color' => '#0ea5e9'],
    ],
    'Saturday' => [
        ['time' => '09:00 AM', 'end' => '12:00 PM', 'subject' => 'National Service Training', 'room' => 'Field', 'teacher' => 'Maj. Garcia', 'color' => '#64748b'],
    ],
];
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

        /* Calendar Grid */
        .calendar-container {
            background: var(--card-bg);
            border-radius: 30px;
            padding: 30px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 20px;
        }

        .day-column {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .day-header {
            text-align: center;
            padding: 15px;
            background: var(--bg);
            border-radius: 15px;
            margin-bottom: 10px;
        }

        .day-header h3 { font-size: 0.9rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; }

        .schedule-card {
            background: var(--bg);
            padding: 15px;
            border-radius: 18px;
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .schedule-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .class-time { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 5px; display: block; }
        .class-name { font-size: 0.85rem; font-weight: 700; margin-bottom: 8px; line-height: 1.3; }
        
        .class-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .detail-item i { width: 12px; font-size: 0.7rem; }

        @media print {
            @page {
                size: portrait;
                margin: 10mm;
            }
            body { background: white; color: black; font-size: 10pt; }
            .sidebar, .header, .header-actions, .page-header, .calendar-container, #sidebar-toggle { display: none !important; }
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
            <!-- Normal UI Area -->
            <div class="page-header">
                <div class="page-title">
                    <h1>Weekly Schedule</h1>
                    <p>Track your academic activities for the current semester.</p>
                </div>
                <div class="header-actions">
                    <button onclick="window.print()" style="padding: 12px 20px; background: var(--primary); color: white; border: none; border-radius: 15px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-print"></i> Print Schedule
                    </button>
                </div>
            </div>

            <div class="calendar-container">
                <div class="calendar-grid">
                    <?php foreach ($days as $day): ?>
                        <div class="day-column">
                            <div class="day-header">
                                <h3><?php echo substr($day, 0, 3); ?></h3>
                            </div>
                            
                            <?php if (isset($weekly_schedule[$day])): ?>
                                <?php foreach ($weekly_schedule[$day] as $class): ?>
                                    <div class="schedule-card" style="border-left-color: <?php echo $class['color']; ?>;">
                                        <span class="class-time"><?php echo $class['time']; ?> - <?php echo $class['end']; ?></span>
                                        <h4 class="class-name"><?php echo htmlspecialchars($class['subject']); ?></h4>
                                        <div class="class-details">
                                            <div class="detail-item">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span><?php echo htmlspecialchars($class['room']); ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-user-tie"></i>
                                                <span><?php echo htmlspecialchars($class['teacher']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="text-align: center; padding: 20px; opacity: 0.5;">
                                    <span style="font-size: 0.75rem;">No Classes</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
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
                        $processed_subjects = [];
                        foreach ($weekly_schedule as $day => $classes) {
                            foreach ($classes as $class) {
                                $sub_key = $class['subject'];
                                if (!isset($processed_subjects[$sub_key])) {
                                    $processed_subjects[$sub_key] = [
                                        'days' => [substr($day, 0, 3)],
                                        'time' => $class['time'] . ' - ' . $class['end'],
                                        'room' => $class['room'],
                                        'teacher' => $class['teacher']
                                    ];
                                } else {
                                    $processed_subjects[$sub_key]['days'][] = substr($day, 0, 3);
                                }
                            }
                        }

                        foreach ($processed_subjects as $name => $data): 
                            $units = rand(2, 3);
                            $total_units += $units;
                        ?>
                        <tr>
                            <td><?php echo strtoupper(substr($name, 0, 3)) . '-' . rand(100, 999); ?></td>
                            <td><?php echo htmlspecialchars($name); ?></td>
                            <td><?php echo implode('/', $data['days']) . ' ' . $data['time']; ?></td>
                            <td><?php echo htmlspecialchars($data['room']); ?></td>
                            <td><?php echo htmlspecialchars($data['teacher']); ?></td>
                            <td><?php echo $units; ?>.0</td>
                        </tr>
                        <?php endforeach; ?>
                        <tr style="background: #f9fafb; font-weight: 800;">
                            <td colspan="5" style="text-align: right;">TOTAL ACADEMIC UNITS:</td>
                            <td><?php echo $total_units; ?>.0</td>
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
