<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}
require_once '../../Database/config.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM admission_applications WHERE status = 'Processing' ORDER BY submission_date DESC");
    $stmt->execute();
    $processing_apps = $stmt->fetchAll();
} catch (PDOException $e) {
    $processing_apps = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<!-- Head content same as before -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>For Evaluation - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1648bc;
            --primary-dark: #0f172a;
            --bg-light: #f8fafc;
            --border-soft: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --shadow-premium: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { 
            display: flex;
            min-height: 100vh;
            background: var(--bg-light); 
            color: var(--text-dark); 
        }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .content-area { padding: 40px; max-width: 1400px; margin: 0 auto; width: 100%; }

        .header-section { margin-bottom: 40px; }
        .header-section h1 { font-size: 2.2rem; font-weight: 800; color: var(--primary-dark); }
        .header-section p { color: var(--text-muted); }

        .table-card {
            background: white;
            border-radius: 30px;
            box-shadow: var(--shadow-premium);
            border: 1px solid var(--border-soft);
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left; padding: 20px 40px; background: #f8fafc;
            color: var(--text-muted); font-weight: 700; font-size: 0.75rem;
            text-transform: uppercase; letter-spacing: 0.05em;
        }
        td { padding: 25px 40px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; vertical-align: middle; }

        .app-row:hover { background: #f8faff; transition: 0.3s; }
        
        .badge-review {
            padding: 6px 14px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;
            background: #e0f2fe; color: #0369a1; display: inline-flex; align-items: center; gap: 6px;
        }

        .btn-eval-action {
            padding: 10px 20px; border-radius: 12px; background: var(--primary-blue);
            color: white; font-weight: 700; border: none; cursor: pointer; transition: 0.3s;
            box-shadow: 0 4px 12px rgba(22, 72, 188, 0.2);
        }
        .btn-eval-action:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(22, 72, 188, 0.3); }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="header-section">
                <h1>For Evaluation</h1>
                <p>Track applications currently being reviewed by the admission team.</p>
            </div>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Application ID</th>
                            <th>Student Information</th>
                            <th>Preferred Course</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($processing_apps)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 80px;">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 20px;">
                                        <div style="width: 100px; height: 100px; background: #f8fafc; border-radius: 30px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-clipboard-check fa-3x" style="color: #e2e8f0;"></i>
                                        </div>
                                        <p style="color: #64748b; font-weight: 600;">No applications are currently in the evaluation queue.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($processing_apps as $app): ?>
                                <tr class="app-row">
                                    <td style="font-weight: 800; color: var(--primary-dark);">#<?php echo htmlspecialchars($app->application_no); ?></td>
                                    <td>
                                        <div style="font-weight: 800; color: var(--primary-dark);"><?php echo htmlspecialchars($app->first_name . ' ' . $app->last_name); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($app->email); ?></div>
                                    </td>
                                    <td style="font-weight: 700; color: #475569;"><?php echo htmlspecialchars($app->preferred_course_1); ?></td>
                                    <td>
                                        <span class="badge-review">
                                            <i class="fas fa-spinner fa-spin"></i> Under Review
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-eval-action" onclick="window.location.href='Evaluation.php'">
                                            Workspace <i class="fas fa-arrow-right" style="margin-left: 5px; font-size: 0.8rem;"></i>
                                        </button>
                                    </td>
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
