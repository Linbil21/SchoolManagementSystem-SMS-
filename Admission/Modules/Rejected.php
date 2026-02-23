<?php
require_once __DIR__ . '/../../auth/Security.php';
checkRole(['admission']);
require_once '../../Database/config.php';

try {
    $stmt = $pdo->prepare("SELECT a.*, COALESCE(c.course_name, a.preferred_course_1) as course_display_name 
                           FROM admission_applications a 
                           LEFT JOIN courses c ON (a.preferred_course_1 = CAST(c.courseId AS CHAR) OR a.preferred_course_1 = c.course_name)
                           WHERE a.status = 'Rejected' 
                           ORDER BY a.submission_date DESC");
    $stmt->execute();
    $rejected_apps = $stmt->fetchAll();
} catch (PDOException $e) {
    $rejected_apps = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejected Applications - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1648bc;
            --bg-light: #f7fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--bg-light);
            display: flex;
            min-height: 100vh;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .content-area {
            padding: 30px;
        }

        .module-header {
            margin-bottom: 25px;
        }

        .table-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #edf2f7;
            color: #718096;
            font-size: 0.85rem;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #edf2f7;
            color: #2d3748;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>Rejected Applications</h1>
                <p>History of rejected applications.</p>
            </div>
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Course</th>
                            <th>Date Rejected</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rejected_apps)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">No rejected applications.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rejected_apps as $app): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($app->application_no); ?></td>
                                    <td><?php echo htmlspecialchars($app->first_name . ' ' . $app->last_name); ?></td>
                                    <td><?php echo htmlspecialchars($app->course_display_name); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($app->submission_date)); ?></td>
                                    <td>Incomplete Documents</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>


