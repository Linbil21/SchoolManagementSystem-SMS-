<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrance Exam Schedule</title>
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
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .page-subtitle {
            color: var(--secondary);
            font-size: 0.95rem;
        }

        .exam-status-card {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border-radius: 20px;
            padding: 30px;
            color: white;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .exam-status-card::after {
            content: '';
            position: absolute;
            right: -20px;
            bottom: -40px;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .status-info h2 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .status-info p {
            opacity: 0.9;
        }
        
        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .schedule-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            transition: transform 0.2s;
        }
        
        .schedule-card:hover {
            transform: translateY(-5px);
        }
        
        .schedule-card.active {
            border: 2px solid var(--primary);
        }

        .date-box {
            background: #eff6ff;
            color: var(--primary);
            padding: 10px 20px;
            border-radius: 12px;
            display: inline-block;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .exam-details h3 {
            color: var(--text-main);
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .detail-row {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--secondary);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        .slot-btn {
            background: white;
            color: var(--primary);
            border: 1px solid #e2e8f0;
            padding: 10px;
            width: 100%;
            border-radius: 10px;
            margin-top: 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .slot-btn:hover {
            border-color: var(--primary);
            background: #eff6ff;
        }
        
        .slot-btn.selected {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .warning-box {
            background: #fff7ed;
            border: 1px solid #ffedd5;
            color: #c2410c;
            padding: 15px;
            border-radius: 12px;
            margin-top: 30px;
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Entrance Exam Schedule</h1>
                <p class="page-subtitle">View available dates and book your examination slot.</p>
            </div>

            <!-- Status Banner -->
            <div class="exam-status-card">
                <div class="status-info">
                    <h2>Exam Status: Unscheduled</h2>
                    <p>Please select a schedule below to secure your slot.</p>
                </div>
                <div class="icon-circle" style="background: rgba(255,255,255,0.2); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-calendar-times"></i>
                </div>
            </div>

            <h3 style="margin-bottom: 20px; color: #1e293b;">Available Schedules</h3>
            
            <div class="schedule-grid">
                <!-- Schedule 1 -->
                <div class="schedule-card active">
                    <div class="date-box">OCT 25, 2026</div>
                    <div class="exam-details">
                        <h3>Batch 1 - Morning Session</h3>
                        <div class="detail-row">
                            <i class="far fa-clock"></i>
                            <span>8:00 AM - 11:30 AM</span>
                        </div>
                        <div class="detail-row">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Testing Center A, Main Bldg.</span>
                        </div>
                        <div class="detail-row">
                            <i class="fas fa-users"></i>
                            <span>45 Slots Available</span>
                        </div>
                        <button class="slot-btn selected">Selected</button>
                    </div>
                </div>

                <!-- Schedule 2 -->
                <div class="schedule-card">
                    <div class="date-box">OCT 25, 2026</div>
                    <div class="exam-details">
                        <h3>Batch 2 - Afternoon Session</h3>
                        <div class="detail-row">
                            <i class="far fa-clock"></i>
                            <span>1:00 PM - 4:30 PM</span>
                        </div>
                        <div class="detail-row">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Testing Center B, Main Bldg.</span>
                        </div>
                        <div class="detail-row">
                            <i class="fas fa-users"></i>
                            <span>12 Slots Available</span>
                        </div>
                         <button class="slot-btn">Select Slot</button>
                    </div>
                </div>

                <!-- Schedule 3 -->
                <div class="schedule-card">
                    <div class="date-box">OCT 26, 2026</div>
                    <div class="exam-details">
                        <h3>Batch 3 - Morning Session</h3>
                        <div class="detail-row">
                            <i class="far fa-clock"></i>
                            <span>8:00 AM - 11:30 AM</span>
                        </div>
                        <div class="detail-row">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Testing Center A, Main Bldg.</span>
                        </div>
                        <div class="detail-row">
                            <i class="fas fa-users"></i>
                            <span>50 Slots Available</span>
                        </div>
                         <button class="slot-btn">Select Slot</button>
                    </div>
                </div>
            </div>

            <div class="warning-box">
                <i class="fas fa-exclamation-triangle" style="margin-top: 3px;"></i>
                <div>
                    <h4 style="font-weight: 700; margin-bottom: 5px;">Important Reminder</h4>
                    <p style="font-size: 0.9rem;">Please bring your exam permit and a valid ID on the day of the examination. Late examinees will not be allowed to enter the testing room once the exam has started.</p>
                </div>
            </div>
            
             <button style="margin-top: 30px; background: #1e293b; color: white; border: none; padding: 15px 40px; border-radius: 12px; font-weight: 600; cursor: pointer; float: right;">Confirm Booking <i class="fas fa-check" style="margin-left: 8px;"></i></button>

        </div>
    </div>
</body>

</html>
