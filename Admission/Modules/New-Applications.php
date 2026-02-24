<?php
/**
 * New Applications Module
 */
require_once __DIR__ . '/../../auth/Security.php';
require_once __DIR__ . '/../../Database/config.php';
checkRole(['admission']);

// Handle AJAX requests (e.g., Move to Evaluation or Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    $appId = $_POST['application_no'] ?? null;

    if (!$appId) {
        echo json_encode(['success' => false, 'message' => 'Application ID missing.']);
        exit;
    }

    try {
        if ($action === 'evaluate') {
            $stmt = $pdo->prepare("UPDATE admission_applications SET status = 'Processing' WHERE application_no = ?");
            $stmt->execute([$appId]);
            echo json_encode(['success' => true, 'message' => 'Application moved to evaluation.']);
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM admission_applications WHERE application_no = ?");
            $stmt->execute([$appId]);
            echo json_encode(['success' => true, 'message' => 'Application deleted successfully.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch Pending Applications
try {
    $stmt = $pdo->query("SELECT a.*, COALESCE(c.course_name, a.preferred_course_1) as course_display_name 
                         FROM admission_applications a 
                         LEFT JOIN courses c ON (TRIM(a.preferred_course_1) = CAST(c.courseId AS CHAR) OR a.preferred_course_1 = c.course_name)
                         WHERE a.status = 'Pending' 
                         ORDER BY a.submission_date DESC");
    $applications = $stmt->fetchAll();
} catch (PDOException $e) {
    $applications = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Applications - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1648bc;
            --bg-light: #f8fafc;
            --surface: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; color: var(--text-main); }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; width: 100%; }
        .content-area { padding: 40px; max-width: 1400px; margin: 0 auto; width: 100%; }

        .header-section { margin-bottom: 35px; display: flex; justify-content: space-between; align-items: flex-end; }
        .header-section h1 { font-size: 2rem; font-weight: 800; color: #0f172a; margin-bottom: 5px; }
        .header-section p { color: var(--text-muted); font-weight: 500; }

        .table-card {
            background: var(--surface);
            border-radius: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .table-header {
            padding: 25px 35px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h3 { font-weight: 700; color: var(--text-main); font-size: 1.1rem; }

        .search-container { position: relative; width: 320px; }
        .search-container i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
        .search-input {
            width: 100%; padding: 12px 15px 12px 45px; border-radius: 12px;
            border: 2px solid #f1f5f9; outline: none; transition: 0.3s; font-size: 0.9rem;
        }
        .search-input:focus { border-color: var(--primary); background: white; }

        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left; padding: 18px 35px; background: #f8fafc;
            color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;
            letter-spacing: 0.05em; font-weight: 700;
        }
        td { padding: 20px 35px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; vertical-align: middle; }

        .student-info { display: flex; flex-direction: column; }
        .student-name { font-weight: 700; color: var(--text-main); }
        .student-email { font-size: 0.8rem; color: var(--text-muted); font-weight: 500; }

        .badge-course { padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; background: #eff6ff; color: var(--primary); border: 1px solid #dbeafe; }
        
        .action-btns { display: flex; gap: 8px; }
        .btn-action {
            width: 38px; height: 38px; border-radius: 10px; border: none;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: 0.2s; font-size: 1rem;
        }
        .btn-evaluate { background: #eef2ff; color: var(--primary); }
        .btn-evaluate:hover { background: var(--primary); color: white; transform: translateY(-2px); }
        .btn-delete { background: #fef2f2; color: #ef4444; }
        .btn-delete:hover { background: #ef4444; color: white; transform: translateY(-2px); }

        .empty-state { padding: 80px; text-align: center; color: var(--text-muted); }
        .empty-state i { font-size: 4rem; opacity: 0.2; margin-bottom: 20px; color: var(--primary); }
    </style>
</head>
<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="header-section">
                <div>
                    <h1>New Applications</h1>
                    <p>Process and manage incoming student applications for this semester.</p>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button class="search-input" style="width: auto; padding: 10px 20px; background: white; font-weight: 600; cursor: pointer;" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Refresh List
                    </button>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h3>Incoming Submissions (<?php echo count($applications); ?>)</h3>
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" id="appSearch" class="search-input" placeholder="Search applicant name or email..." onkeyup="filterTable()">
                    </div>
                </div>

                <table id="applicationsTable">
                    <thead>
                        <tr>
                            <th>Applicant Details</th>
                            <th>Reference No.</th>
                            <th>Applied Course</th>
                            <th>Submission Date</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($applications)): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h3>No New Applications</h3>
                                        <p>Check back later or refresh the feed.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td>
                                        <div class="student-info">
                                            <span class="student-name"><?php echo htmlspecialchars($app->first_name . ' ' . $app->last_name); ?></span>
                                            <span class="student-email"><?php echo htmlspecialchars($app->email); ?></span>
                                        </div>
                                    </td>
                                    <td><code style="font-weight: 700; color: #334155;"><?php echo htmlspecialchars($app->application_no); ?></code></td>
                                    <td><span class="badge-course"><?php echo htmlspecialchars($app->course_display_name); ?></span></td>
                                    <td style="color: #64748b; font-weight: 600; font-size: 0.85rem;">
                                        <?php echo date('M d, Y h:i A', strtotime($app->submission_date)); ?>
                                    </td>
                                    <td>
                                        <div class="action-btns" style="justify-content: flex-end;">
                                            <button class="btn-action btn-evaluate" title="Move to Evaluation" onclick="handleAction('evaluate', '<?php echo $app->application_no; ?>', '<?php echo addslashes($app->first_name . ' ' . $app->last_name); ?>')">
                                                <i class="fas fa-file-export"></i>
                                            </button>
                                            <button class="btn-action btn-delete" title="Delete Application" onclick="handleAction('delete', '<?php echo $app->application_no; ?>', '<?php echo addslashes($app->first_name . ' ' . $app->last_name); ?>')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function filterTable() {
            const input = document.getElementById('appSearch');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('applicationsTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                if (tr[i].getElementsByTagName('td').length < 2) continue;
                const name = tr[i].getElementsByTagName('td')[0].textContent.toLowerCase();
                const ref = tr[i].getElementsByTagName('td')[1].textContent.toLowerCase();
                tr[i].style.display = (name.includes(filter) || ref.includes(filter)) ? '' : 'none';
            }
        }

        async function handleAction(action, id, name) {
            const title = action === 'delete' ? 'Are you sure?' : 'Move to Evaluation?';
            const text = action === 'delete' ? `Delete ${name}'s application? This cannot be undone.` : `Submit ${name} for detailed credential review.`;
            
            const result = await Swal.fire({
                title: title,
                text: text,
                icon: action === 'delete' ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: action === 'delete' ? '#ef4444' : '#1648bc',
                confirmButtonText: action === 'delete' ? 'Yes, delete' : 'Yes, proceed'
            });

            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', action);
                formData.append('application_no', id);

                try {
                    const response = await fetch('New-Applications.php', { method: 'POST', body: formData });
                    const res = await response.json();
                    if (res.success) {
                        Swal.fire('Success!', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Server communication failed.', 'error');
                }
            }
        }
    </script>
</body>
</html>
