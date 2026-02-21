<?php
session_start();
// Security check
require_once '../auth/Security.php';
checkRole(['student']);

require_once '../integration/Student_class.php';

$student_name = $_SESSION['fullname'] ?? 'Student';
$student_id = $_SESSION['student_id'] ?? null;

// --- Dynamic Schedule Logic ---
$current_day = date('l'); // Get current day (e.g., 'Sunday', 'Monday')
$schedule = [];

if ($current_day !== 'Sunday') {
    $portal = new StudentPortal();
    $apiData = $portal->getStudentSubjects($student_id);

    if ($apiData && isset($apiData['success']) && $apiData['success'] === true) {
        $full_schedule = $portal->formatSchedule($apiData['subjects'] ?? []);
        $today_schedule = $full_schedule[$current_day] ?? [];
        
        foreach ($today_schedule as $item) {
            $schedule[] = [
                'time' => $item['time'],
                'duration' => 'N/A', // API might not provide duration directly
                'name' => $item['subject'],
                'location' => $item['room'] . ' • ' . $item['teacher'],
                'color' => $item['color']
            ];
        }
    } else {
        // Fallback Mock data
        $subjects = [
            ['name' => 'Web Development 101', 'teacher' => 'Mr. Anderson', 'room' => 'Lab 3'],
            ['name' => 'Database Management', 'teacher' => 'Ms. Roberts', 'room' => 'Room 404'],
            ['name' => 'Networking Fundamentals', 'teacher' => 'Engr. Dave', 'room' => 'CISCO Lab'],
            ['name' => 'Data Structures', 'teacher' => 'Prof. Smith', 'room' => 'Room 202'],
            ['name' => 'Discrete Mathematics', 'teacher' => 'Dr. Evans', 'room' => 'Hall B'],
            ['name' => 'UI/UX Design', 'teacher' => 'Ms. Lopez', 'room' => 'Design Lab'],
            ['name' => 'Artificial Intelligence', 'teacher' => 'Dr. Chen', 'room' => 'AI Room'],
        ];

        $times = ['08:00', '10:00', '01:00', '03:00', '05:00'];
        $durations = ['1 HR', '1.5 HRS', '2 HRS', '3 HRS'];
        $colors = ['#2563eb', '#16a34a', '#ea580c', '#9333ea', '#db2777'];

        // Select 3 random subjects for the day
        shuffle($subjects);
        $selected_subjects = array_slice($subjects, 0, 3);

        foreach ($selected_subjects as $index => $sub) {
            $schedule[] = [
                'time' => $times[$index],
                'duration' => $durations[array_rand($durations)],
                'name' => $sub['name'],
                'location' => $sub['room'] . ' • ' . $sub['teacher'],
                'color' => $colors[$index]
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #64748b;
            --bg: #f8fafc;
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
        }

        .content-area {
            padding: 40px;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border-radius: 24px;
            padding: 40px;
            color: white;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.2);
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            right: -50px;
            bottom: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .welcome-banner h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .welcome-banner p {
            opacity: 0.9;
            font-size: 1rem;
            max-width: 600px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
        }

        .schedule-item {
            display: flex;
            gap: 20px;
            padding: 15px;
            border-radius: 16px;
            background: #f8fafc;
            margin-bottom: 15px;
            border-left: 4px solid #2563eb;
            transition: all 0.2s;
        }

        .schedule-item:hover {
            background: #eff6ff;
        }

        .time-box {
            text-align: center;
            min-width: 80px;
        }

        .time-box .time {
            font-weight: 700;
            color: #1e293b;
            display: block;
        }

        .time-box .duration {
            font-size: 0.75rem;
            color: #64748b;
        }

        .class-info h4 {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .class-info p {
            font-size: 0.85rem;
            color: #64748b;
        }

        .announcement-item {
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 15px;
        }

        .announcement-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .tag {
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .tag-important {
            background: #fef2f2;
            color: #ef4444;
        }

        .tag-normal {
            background: #eff6ff;
            color: #2563eb;
        }

        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 550px;
            border-radius: 30px;
            padding: 40px;
            position: relative;
            transform: translateY(30px);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }

        .modal-icon {
            width: 70px;
            height: 70px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 25px;
        }

        .modal-header h2 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .modal-header p {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .modal-body {
            background: #f8fafc;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .modal-footer .btn-close {
            width: 100%;
            padding: 16px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
        }

        .modal-footer .btn-close:hover {
            background: #1e40af;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <?php include 'Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include 'Components/Header.php'; ?>
        <div class="content-area">

        <?php 
        $admission_status = $_SESSION['admission_status'] ?? 'Pending';
        if ($admission_status !== 'Approved'): 
        ?>
        <div class="welcome-banner" style="margin-bottom: 30px;">
            <h1 style="font-weight: 800;">Welcome to SMS! 🎓</h1>
            <p>Thank you for registering. Please follow these steps to officially enroll in this institution.</p>
            
            <div style="margin-top: 25px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <!-- Step 1 -->
                <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(5px); display:flex; flex-direction:column; gap:8px;">
                    <div style="font-size: 1.4rem; font-weight: 800; opacity: 0.6;">01</div>
                    <h4 style="font-size: 1rem; margin: 0;">Official Registration</h4>
                    <p style="font-size: 0.82rem; opacity: 0.9; line-height: 1.5; flex:1;">You have completed this step by signing up on our system. ✅</p>
                    <button disabled style="padding: 8px 16px; background: rgba(255,255,255,0.25); color: white; border: 1px solid rgba(255,255,255,0.4); border-radius: 10px; font-size: 0.8rem; font-weight: 600; cursor: not-allowed; opacity:0.7;">
                        <i class="fas fa-check-circle"></i> Completed
                    </button>
                </div>
                <!-- Step 2 -->
                <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(5px); display:flex; flex-direction:column; gap:8px;">
                    <div style="font-size: 1.4rem; font-weight: 800; opacity: 0.6;">02</div>
                    <h4 style="font-size: 1rem; margin: 0;">Go to Cashier</h4>
                    <p style="font-size: 0.82rem; opacity: 0.9; line-height: 1.5; flex:1;">Visit the school cashier to pay your <strong>Downpayment</strong>, <strong>Half Payment</strong>, or <strong>Full Payment</strong> to secure your slot.</p>
                    <button onclick="document.getElementById('cashierModal').style.display='flex'" style="padding: 8px 16px; background: white; color: #2563eb; border: none; border-radius: 10px; font-size: 0.8rem; font-weight: 700; cursor: pointer;">
                        <i class="fas fa-money-bill-wave"></i> View Payment Info
                    </button>
                </div>
                <!-- Step 3 -->
                <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(5px); display:flex; flex-direction:column; gap:8px;">
                    <div style="font-size: 1.4rem; font-weight: 800; opacity: 0.6;">03</div>
                    <h4 style="font-size: 1rem; margin: 0;">Upload Requirements</h4>
                    <p style="font-size: 0.82rem; opacity: 0.9; line-height: 1.5; flex:1;">Submit your documents (PSA, Form 138, ID Picture, etc.) in the Admission module for review and approval.</p>
                    <a href="/student/Modules/Admission/Requirements.php" style="padding: 8px 16px; background: white; color: #2563eb; border-radius: 10px; font-size: 0.8rem; font-weight: 700; text-decoration: none; display:inline-block; text-align:center;">
                        <i class="fas fa-file-upload"></i> Upload Now
                    </a>
                </div>
                <!-- Step 4 -->
                <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(5px); display:flex; flex-direction:column; gap:8px;">
                    <div style="font-size: 1.4rem; font-weight: 800; opacity: 0.6;">04</div>
                    <h4 style="font-size: 1rem; margin: 0;">Enrollment</h4>
                    <p style="font-size: 0.82rem; opacity: 0.9; line-height: 1.5; flex:1;">Once your admission is approved, the Enrollment module will be unlocked to select subjects and finalize enrolment.</p>
                    <button disabled style="padding: 8px 16px; background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); border: 1px solid rgba(255,255,255,0.3); border-radius: 10px; font-size: 0.8rem; font-weight: 600; cursor: not-allowed;">
                        <i class="fas fa-lock"></i> Locked
                    </button>
                </div>
            </div>
        </div>

        <!-- Cashier Info Modal -->
        <div id="cashierModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.75); backdrop-filter:blur(8px); z-index:99999; align-items:center; justify-content:center; padding:20px;">
            <div style="background:white; border-radius:24px; padding:40px; max-width:480px; width:100%; box-shadow:0 25px 50px rgba(0,0,0,0.3);">
                <div style="width:60px; height:60px; background:#eff6ff; color:#2563eb; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:1.7rem; margin-bottom:20px;">
                    <i class="fas fa-cash-register"></i>
                </div>
                <h2 style="font-size:1.4rem; font-weight:800; color:#1e293b; margin-bottom:10px;">Cashier Payment Info</h2>
                <p style="color:#64748b; font-size:0.9rem; line-height:1.6; margin-bottom:20px;">Visit the school cashier's office to select your preferred payment scheme:</p>
                <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:25px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:14px; background:#fffbeb; border-radius:12px; border:1px solid #fde68a;">
                        <div>
                            <div style="font-weight:700; color:#1e293b; font-size:0.9rem;"><i class="fas fa-hand-holding-usd" style="color:#f59e0b; margin-right:6px;"></i> Downpayment</div>
                            <div style="font-size:0.78rem; color:#64748b;">Minimum initial payment</div>
                        </div>
                        <span style="font-weight:700; color:#f59e0b; font-size:0.85rem;">Partial</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:14px; background:#eff6ff; border-radius:12px; border:1px solid #bfdbfe;">
                        <div>
                            <div style="font-weight:700; color:#1e293b; font-size:0.9rem;"><i class="fas fa-percentage" style="color:#2563eb; margin-right:6px;"></i> Half Payment</div>
                            <div style="font-size:0.78rem; color:#64748b;">50% of total tuition fee</div>
                        </div>
                        <span style="font-weight:700; color:#2563eb; font-size:0.85rem;">50%</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:14px; background:#f0fdf4; border-radius:12px; border:1px solid #bbf7d0;">
                        <div>
                            <div style="font-weight:700; color:#1e293b; font-size:0.9rem;"><i class="fas fa-check-double" style="color:#22c55e; margin-right:6px;"></i> Full Payment</div>
                            <div style="font-size:0.78rem; color:#64748b;">100% of total tuition fee</div>
                        </div>
                        <span style="font-weight:700; color:#22c55e; font-size:0.85rem;">Full</span>
                    </div>
                </div>
                <button onclick="document.getElementById('cashierModal').style.display='none'" style="width:100%; padding:14px; background:#2563eb; color:white; border:none; border-radius:14px; font-weight:700; font-size:0.95rem; cursor:pointer;">
                    Got it, thanks!
                </button>
            </div>
        </div>
        <script>
            window.addEventListener('click', function(e) {
                const modal = document.getElementById('cashierModal');
                if (e.target === modal) modal.style.display = 'none';
            });
        </script>
        <?php endif; ?>

        <div class="dashboard-grid">
            <!-- Schedule -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Today's Schedule (<?php echo $current_day; ?>)</h3>
                    <a href="/student/Modules/Academic/Schedule.php"
                        style="font-size: 0.85rem; color: var(--primary); text-decoration: none; font-weight: 600;">View
                        Full</a>
                </div>

                <?php if (empty($schedule)): ?>
                    <div style="text-align: center; padding: 40px 20px;">
                        <i class="fas fa-calendar-day"
                            style="font-size: 3rem; color: #e2e8f0; margin-bottom: 15px; display: block;"></i>
                        <h4 style="color: #64748b; font-weight: 600;">No Classes Scheduled</h4>
                        <p style="color: #94a3b8; font-size: 0.85rem;">It's <?php echo $current_day; ?>! Take some time to
                            rest or study.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($schedule as $item): ?>
                        <div class="schedule-item" style="border-left-color: <?php echo $item['color']; ?>;">
                            <div class="time-box">
                                <span class="time"><?php echo $item['time']; ?></span>
                                <span class="duration"><?php echo $item['duration']; ?></span>
                            </div>
                            <div class="class-info">
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p><i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i>
                                    <?php echo htmlspecialchars($item['location']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <script>
                function viewFullSchedule() {
                    const modal = document.getElementById('scheduleModal');
                    const day = "<?php echo $current_day; ?>";
                    const title = document.getElementById('modalTitle');
                    const desc = document.getElementById('modalDesc');
                    const body = document.getElementById('modalBody');
                    const icon = document.querySelector('.modal-icon i');

                    if (day === "Sunday") {
                        title.innerText = "No Classes Today";
                        desc.innerText = "It's Sunday! Take some time to recharge and prepare for the upcoming week.";
                        icon.className = "fas fa-calendar-times";
                        body.innerHTML = `
                            <div style="text-align: center; color: #64748b; padding: 10px;">
                                <p style="font-weight: 500; margin-bottom: 5px;">Rest Day Schedule</p>
                                <span style="font-size: 0.85rem; color: #94a3b8;">Next class will be on Monday at 08:00 AM</span>
                            </div>
                        `;
                    } else {
                        title.innerText = "Full Schedule for " + day;
                        desc.innerText = "Here is your complete list of classes and activities for today.";
                        icon.className = "fas fa-calendar-check";

                        let html = '<div style="display: flex; flex-direction: column; gap: 15px;">';
                        <?php foreach ($schedule as $item): ?>
                            html += `
                                <div style="display: flex; gap: 15px; align-items: center; padding: 10px; border-bottom: 1px solid #e2e8f0;">
                                    <div style="min-width: 60px; font-weight: 700; color: #2563eb;"><?php echo $item['time']; ?></div>
                                    <div>
                                        <div style="font-weight: 600; color: #1e293b; font-size: 0.9rem;"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <div style="font-size: 0.8rem; color: #64748b;"><?php echo htmlspecialchars($item['location']); ?></div>
                                    </div>
                                </div>
                            `;
                        <?php endforeach; ?>
                        html += '</div>';
                        body.innerHTML = html;
                    }

                    modal.classList.add('active');
                }

                function closeScheduleModal() {
                    document.getElementById('scheduleModal').classList.remove('active');
                }

                // Close on click outside
                window.onclick = function (event) {
                    const modal = document.getElementById('scheduleModal');
                    if (event.target == modal) {
                        closeScheduleModal();
                    }
                }
            </script>

            <!-- Sidebar Content (Calendar & Announcements) -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <!-- Mini Calendar -->
                <div class="card" style="padding: 20px;">
                    <div class="card-header" style="margin-bottom: 15px;">
                        <h3 class="card-title">Calendar</h3>
                    </div>
                    <?php
                    $month = date('F Y');
                    $days_in_month = date('t');
                    $today = date('j');
                    $first_day = date('w', strtotime(date('Y-m-01')));
                    ?>
                    <div style="text-align: center; margin-bottom: 15px;">
                        <span style="font-weight: 700; color: var(--primary); font-size: 0.9rem;"><?php echo $month; ?></span>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; text-align: center;">
                        <?php foreach (['S','M','T','W','T','F','S'] as $d): ?>
                            <span style="font-size: 0.7rem; font-weight: 700; color: #94a3b8;"><?php echo $d; ?></span>
                        <?php endforeach; ?>
                        
                        <?php for ($i = 0; $i < $first_day; $i++): ?>
                            <span></span>
                        <?php endfor; ?>
                        
                        <?php for ($d = 1; $d <= $days_in_month; $d++): ?>
                            <span style="
                                font-size: 0.8rem; 
                                padding: 8px 0; 
                                border-radius: 8px; 
                                <?php echo ($d == $today) ? 'background: var(--primary); color: white; font-weight: 700;' : 'color: #64748b;'; ?>
                            ">
                                <?php echo $d; ?>
                            </span>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Announcements -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Announcements</h3>
                    </div>

                <div class="announcement-item">
                    <div style="display: flex; gap: 10px; margin-bottom: 5px;">
                        <span class="tag tag-important">Important</span>
                        <span style="font-size: 0.75rem; color: #94a3b8;">Today</span>
                    </div>
                    <h4 style="font-size: 0.95rem; margin-bottom: 5px;">Midterm Examination Schedule</h4>
                    <p style="font-size: 0.85rem; color: #64748b; line-height: 1.5;">The examination schedule for the
                        midterm period has been released. Please check your portals.</p>
                </div>

                <div class="announcement-item">
                    <div style="display: flex; gap: 10px; margin-bottom: 5px;">
                        <span class="tag tag-normal">Event</span>
                        <span style="font-size: 0.75rem; color: #94a3b8;">Yesterday</span>
                    </div>
                    <h4 style="font-size: 0.95rem; margin-bottom: 5px;">University Week 2026</h4>
                    <p style="font-size: 0.85rem; color: #64748b; line-height: 1.5;">Join us for a week of fun and
                        activities starting next Monday! Don't miss out.</p>
                </div>
                </div> <!-- Close Announcements Card -->
            </div> <!-- Close Right Column Wrapper -->
        </div> <!-- Close Dashboard Grid -->
    </div> <!-- Close Content Area -->
</div> <!-- Close Main Wrapper -->

    <!-- Schedule Modal -->
    <div id="scheduleModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="modal-header">
                <h2 id="modalTitle">Full Schedule</h2>
                <p id="modalDesc">Details about your classes for today.</p>
            </div>
            <div id="modalBody" class="modal-body">
                <!-- Content injected via JS -->
            </div>
            <div class="modal-footer">
                <button onclick="closeScheduleModal()" class="btn-close">Got it, thanks!</button>
            </div>
        </div>
    </div>
</body>

</html>
