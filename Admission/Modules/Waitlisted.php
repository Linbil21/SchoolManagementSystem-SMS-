<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}
require_once '../../Database/config.php';

try {
    $stmt = $pdo->prepare("SELECT a.*, COALESCE(c.course_name, a.preferred_course_1) as course_display_name 
                           FROM admission_applications a 
                           LEFT JOIN courses c ON (a.preferred_course_1 = CAST(c.courseId AS CHAR) OR a.preferred_course_1 = c.course_name)
                           WHERE a.status = 'Waitlisted' 
                           ORDER BY a.submission_date DESC");
    $stmt->execute();
    $waitlisted_apps = $stmt->fetchAll();
} catch (PDOException $e) {
    $waitlisted_apps = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<!-- Use styles from other files -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waitlisted - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-blue: #1648bc; --bg-light: #f7fafc; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        .content-area { padding: 30px; }
        .module-header { margin-bottom: 25px; }
        .table-card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #edf2f7; color: #718096; font-size: 0.85rem; }
        td { padding: 12px; border-bottom: 1px solid #edf2f7; color: #2d3748; font-size: 0.9rem; }
    </style>
</head>
<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>Waitlisted</h1>
                <p>Students pending slot availability.</p>
            </div>
             <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Priority</th>
                            <th>Student Name</th>
                            <th>Preferred Course</th>
                            <th>Wait Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($waitlisted_apps)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">No waitlisted applications.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($waitlisted_apps as $app): ?>
                                <tr>
                                    <td>Medium</td>
                                    <td><?php echo htmlspecialchars($app->first_name . ' ' . $app->last_name); ?></td>
                                    <td><?php echo htmlspecialchars($app->course_display_name); ?></td>
                                    <td>1 Day</td>
                                    <td><button onclick="window.location.href='Evaluation.php'" style="border: none; background: #1648bc; color: white; padding: 6px 12px; border-radius: 6px; cursor: pointer;">Review</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include '../Components/GlobalScripts.php'; ?>
</body>
</html>
