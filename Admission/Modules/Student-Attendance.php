<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-blue: #1648bc; --bg-light: #f7fafc; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        .content-area { padding: 30px; }
        .module-header { margin-bottom: 25px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02); margin-bottom: 25px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
        .stat-item { background: #f8fafc; padding: 20px; border-radius: 12px; text-align: center; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: #1648bc; }
        .stat-label { font-size: 0.85rem; color: #64748b; }
        .search-row { display: flex; gap: 15px; margin-bottom: 25px; align-items: flex-end; }
        .input-group { flex: 1; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #4a5568; }
        .input-group input, .input-group select { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; }
        .btn-search { background: #1648bc; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.3s; height: 46px; }
        .attendance-table { width: 100%; border-collapse: collapse; }
        .attendance-table th { background: #f8fafc; text-align: left; padding: 15px; color: #64748b; border-bottom: 2px solid #edf2f7; }
        .attendance-table td { padding: 15px; border-bottom: 1px solid #edf2f7; color: #2d3748; }
        .status-badge { padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; }
        .status-present { background: #f0fdf4; color: #16a34a; }
        .status-absent { background: #fef2f2; color: #dc2626; }
        .status-late { background: #fffbeb; color: #d97706; }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>Student Attendance</h1>
                <p>Monitor daily attendance records and statistics.</p>
            </div>

            <div class="card">
                <div class="search-row">
                    <div class="input-group">
                        <label>Student Search</label>
                        <input type="text" placeholder="Search by ID or Name...">
                    </div>
                    <div class="input-group">
                        <label>Date Range</label>
                        <input type="date" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <button class="btn-search">Filter Records</button>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value">94%</div>
                        <div class="stat-label">Average Attendance</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">12</div>
                        <div class="stat-label">Total Absences</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">5</div>
                        <div class="stat-label">Lates Recorded</div>
                    </div>
                </div>

                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Subject / Class</th>
                            <th>Time-In</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Feb 08, 2026</td>
                            <td>Web Development 101</td>
                            <td>08:05 AM</td>
                            <td><span class="status-badge status-present">Present</span></td>
                        </tr>
                        <tr>
                            <td>Feb 07, 2026</td>
                            <td>Database Systems</td>
                            <td>10:15 AM</td>
                            <td><span class="status-badge status-late">Late</span></td>
                        </tr>
                        <tr>
                            <td>Feb 06, 2026</td>
                            <td>Data Structures</td>
                            <td>--:--</td>
                            <td><span class="status-badge status-absent">Absent</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php include '../Components/GlobalScripts.php'; ?>
</body>
</html>
