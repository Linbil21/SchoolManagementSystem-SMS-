<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}

require_once '../../Database/config.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    $appId = $_POST['application_no'] ?? null;

    if (!$appId) {
        echo json_encode(['success' => false, 'message' => 'Application ID missing.']);
        exit;
    }

    try {
        if ($action === 'proceed_to_evaluation') {
            $stmt = $pdo->prepare("UPDATE admission_applications SET status = 'Processing' WHERE application_no = ?");
            $stmt->execute([$appId]);
            echo json_encode(['success' => true, 'message' => 'Application moved to evaluation.']);
        } elseif ($action === 'delete_application') {
            $stmt = $pdo->prepare("DELETE FROM admission_applications WHERE application_no = ?");
            $stmt->execute([$appId]);
            echo json_encode(['success' => true, 'message' => 'Application deleted successfully.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Operation failed: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch pending applications
try {
    $stmt = $pdo->prepare("
        SELECT a.*, COALESCE(c.course_name, a.preferred_course_1) as course_display_name 
        FROM admission_applications a 
        LEFT JOIN courses c ON (TRIM(a.preferred_course_1) = CAST(c.courseId AS CHAR) OR a.preferred_course_1 = c.course_name)
        WHERE a.status = 'Pending' 
        ORDER BY a.submission_date DESC
    ");
    $stmt->execute();
    $applications = $stmt->fetchAll();
} catch (PDOException $e) {
    $applications = [];
    error_log("Database error: " . $e->getMessage());
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
    <link rel="stylesheet" href="../../Assets/css/theme.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        :root {
            --primary-blue: #1648bc;
            --primary-dark: #0f172a;
            --bg-light: #f8fafc;
            --border-soft: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning-bg: #fffbeb;
            --warning-border: #fef3c7;
            --warning-text: #92400e;
            --shadow-premium: 0 20px 25px -5px rgba(0,0,0,0.05), 0 10px 10px -5px rgba(0,0,0,0.02);
        }

        body { display: flex; min-height: 100vh; background: var(--bg-light); color: var(--text-dark); }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .content-area { padding: 40px; max-width: 1400px; margin: 0 auto; width: 100%; }

        .header-section {
            margin-bottom: 40px; display: flex; justify-content: space-between;
            align-items: center; flex-wrap: wrap; gap: 20px;
        }
        .header-section h1 { font-size: 2.2rem; font-weight: 800; letter-spacing: -0.02em; color: var(--primary-dark); margin-bottom: 8px; }
        .header-section p { color: var(--text-muted); font-size: 1rem; }

        .search-wrapper { position: relative; }
        .search-icon { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem; }
        .search-input {
            padding: 14px 20px 14px 45px; border-radius: 16px; border: 2px solid #f1f5f9;
            outline: none; width: 320px; font-size: 0.9rem; font-weight: 600; transition: all 0.3s ease;
        }
        .search-input:focus { border-color: var(--primary-blue); }

        .table-card { background: white; border-radius: 30px; box-shadow: var(--shadow-premium); border: 1px solid var(--border-soft); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 20px 40px; background: #f8fafc; color: var(--text-muted); font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 25px 40px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; vertical-align: middle; }

        .app-row { transition: all 0.2s ease; cursor: pointer; }
        .app-row:hover { background: #f8faff; }
        .app-id { font-weight: 800; color: var(--primary-dark); font-size: 0.9rem; }
        .student-name { font-weight: 800; color: var(--primary-dark); }
        .student-email { font-size: 0.8rem; color: var(--text-muted); font-weight: 500; }
        .course-name { font-weight: 700; color: #475569; font-size: 0.85rem; }
        .submission-date { color: var(--text-muted); font-weight: 600; }

        .btn { padding: 10px 18px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; border: none; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; }
        .btn-view { background: #f1f5f9; color: #475569; }
        .btn-view:hover { background: #e2e8f0; color: #1e293b; }
        .btn-delete { background: #fef2f2; color: var(--danger); }
        .btn-delete:hover { background: #fee2e2; }
        .btn-eval { background: var(--primary-blue); color: white; box-shadow: 0 4px 12px rgba(22,72,188,0.2); }
        .btn-eval:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(22,72,188,0.3); }
        .btn-close { padding: 12px 25px; border-radius: 14px; border: 2px solid var(--border-soft); background: white; color: var(--text-muted); font-weight: 700; cursor: pointer; transition: all 0.3s ease; }
        .btn-close:hover { background: #f8fafc; }
        .action-group { display: flex; gap: 10px; }

        .empty-state { text-align: center; padding: 80px; }
        .empty-icon { width: 100px; height: 100px; background: #f1f5f9; border-radius: 30px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .empty-icon i { color: #e2e8f0; font-size: 3rem; }
        .empty-text { color: #64748b; font-weight: 600; }

        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.7); backdrop-filter: blur(12px); z-index: 99999; display: none; align-items: center; justify-content: center; padding: 20px; }
        .modal-box { background: white; width: 100%; max-width: 600px; border-radius: 35px; box-shadow: 0 30px 60px -12px rgba(0,0,0,0.3); overflow: hidden; animation: modalScale 0.4s cubic-bezier(0.34,1.56,0.64,1); }
        @keyframes modalScale { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        .modal-head { padding: 40px; background: linear-gradient(135deg, #1648bc 0%, #1e3a8a 100%); color: white; text-align: center; }
        .modal-avatar { width: 100px; height: 100px; border-radius: 30px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 800; margin: 0 auto 20px; border: 3px solid rgba(255,255,255,0.2); }
        .modal-head h2 { font-weight: 800; font-size: 1.6rem; letter-spacing: -0.02em; margin-bottom: 5px; }
        .modal-head p { opacity: 0.8; font-weight: 500; font-size: 0.95rem; }
        .modal-body { padding: 35px 40px; }
        .info-grid { background: #f8fafc; border-radius: 20px; padding: 20px; border: 1px solid #eef2ff; margin-bottom: 25px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .info-item label { display: block; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px; }
        .info-item p { font-weight: 700; color: var(--primary-dark); font-size: 0.95rem; }
        .warning-note { background: var(--warning-bg); border: 1px solid var(--warning-border); padding: 20px; border-radius: 20px; color: var(--warning-text); font-size: 0.85rem; display: flex; gap: 12px; align-items: center; }
        .warning-note p { font-weight: 600; }
        .modal-actions { padding: 30px 40px; background: white; border-top: 1px solid var(--border-soft); display: flex; justify-content: flex-end; gap: 15px; }
        #evalSpinner { margin-right: 8px; display: none; }
    </style>
</head>
<body>
    <!-- View Modal -->
    <div id="viewModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-head">
                <div class="modal-avatar" id="modalAvatar">JD</div>
                <h2 id="modalProfileName">John Doe</h2>
                <p id="modalProfileCourse">BS Computer Science</p>
            </div>
            <div class="modal-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Application ID</label>
                        <p id="modalAppId">#APP-10293</p>
                    </div>
                    <div class="info-item">
                        <label>Submission Date</label>
                        <p id="modalDate">Jan 12, 2024</p>
                    </div>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Email Address</label>
                        <p id="modalEmail">john@university.edu</p>
                    </div>
                    <div class="info-item">
                        <label>Contact Number</label>
                        <p id="modalContact">+63 912 345 6789</p>
                    </div>
                </div>
                <div class="warning-note">
                    <i class="fas fa-info-circle fa-lg"></i>
                    <p>Moving to evaluation will alert the student and lock this application phase.</p>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn-close" onclick="closeViewModal()">Dismiss</button>
                <button class="btn btn-eval" onclick="proceedToEval(event)">
                    <i class="fas fa-circle-notch fa-spin" id="evalSpinner"></i>
                    <i class="fas fa-arrow-right" id="evalIcon"></i>
                    Move to Evaluation
                </button>
            </div>
        </div>
    </div>

    <?php include '../Components/Sidebar.php'; ?>

    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>

        <div class="content-area">
            <div class="header-section">
                <div>
                    <h1>New Applications</h1>
                    <p>Identify and process newly submitted enrollment requests.</p>
                </div>
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="appSearch" onkeyup="filterTable()" placeholder="Search applicants..." class="search-input">
                </div>
            </div>

            <div class="table-card">
                <table id="appTable">
                    <thead>
                        <tr>
                            <th>Application ID</th>
                            <th>Student Information</th>
                            <th>Applied Course</th>
                            <th>Date Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($applications)): ?>
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                                    <p class="empty-text">No pending applications in your queue.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($applications as $app): ?>
                                <tr class="app-row">
                                    <td class="app-id">#<?php echo htmlspecialchars($app->application_no); ?></td>
                                    <td>
                                        <div class="student-name"><?php echo htmlspecialchars($app->first_name . ' ' . $app->last_name); ?></div>
                                        <div class="student-email"><?php echo htmlspecialchars($app->email); ?></div>
                                    </td>
                                    <td class="course-name"><?php echo htmlspecialchars($app->course_display_name); ?></td>
                                    <td class="submission-date"><?php echo date('M d, Y', strtotime($app->submission_date)); ?></td>
                                    <td>
                                        <?php
                                            $appData = [
                                                'name'   => $app->first_name . ' ' . $app->last_name,
                                                'no'     => $app->application_no,
                                                'course' => $app->course_display_name,
                                                'date'   => date('M d, Y', strtotime($app->submission_date)),
                                                'email'  => $app->email,
                                                'phone'  => $app->phone_number
                                            ];
                                        ?>
                                        <div class="action-group">
                                            <button class="btn btn-view" onclick='openDetailModal(<?php echo json_encode($appData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                                                <i class="fas fa-eye"></i> Details
                                            </button>
                                            <button class="btn btn-delete" onclick="deleteApplication('<?php echo $app->application_no; ?>', '<?php echo addslashes($app->first_name . ' ' . $app->last_name); ?>')">
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

    <?php include '../Components/GlobalScripts.php'; ?>
    <script>
        let currentAppId = null;

        function filterTable() {
            const filter = document.getElementById('appSearch').value.toLowerCase();
            const rows = document.getElementById('appTable').getElementsByTagName('tr');
            for (let i = 1; i < rows.length; i++) {
                let visible = false;
                const cells = rows[i].getElementsByTagName('td');
                for (let j = 0; j < cells.length; j++) {
                    if ((cells[j].textContent || cells[j].innerText).toLowerCase().includes(filter)) {
                        visible = true; break;
                    }
                }
                rows[i].style.display = visible ? '' : 'none';
            }
        }

        function openDetailModal(data) {
            try {
                currentAppId = data.no;
                document.getElementById('modalProfileName').textContent = data.name;
                document.getElementById('modalAvatar').textContent = data.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
                document.getElementById('modalAppId').textContent = '#' + data.no;
                document.getElementById('modalProfileCourse').textContent = data.course;
                document.getElementById('modalDate').textContent = data.date;
                document.getElementById('modalEmail').textContent = data.email;
                document.getElementById('modalContact').textContent = data.phone;
                document.getElementById('viewModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Failed to open application details.', 'error');
            }
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            currentAppId = null;
        }

        async function proceedToEval(event) {
            if (!currentAppId) return;
            const btn = event.currentTarget;
            const spinner = document.getElementById('evalSpinner');
            const icon = document.getElementById('evalIcon');
            spinner.style.display = 'inline-block';
            icon.style.display = 'none';
            btn.disabled = true;
            const formData = new FormData();
            formData.append('action', 'proceed_to_evaluation');
            formData.append('application_no', currentAppId);
            try {
                const res = await fetch(window.location.href, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    await Swal.fire({ title: 'Moved to Evaluation!', text: 'Redirecting...', icon: 'success', timer: 1500, showConfirmButton: false });
                    window.location.href = 'Evaluation.php';
                } else {
                    Swal.fire('Error!', data.message, 'error');
                    resetBtn(spinner, icon, btn);
                }
            } catch (e) {
                Swal.fire('Error!', 'Something went wrong.', 'error');
                resetBtn(spinner, icon, btn);
            }
        }

        async function deleteApplication(id, name) {
            const result = await Swal.fire({ title: 'Are you sure?', text: `Delete application of ${name}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#718096', confirmButtonText: 'Yes, delete it!' });
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete_application');
                formData.append('application_no', id);
                try {
                    const res = await fetch(window.location.href, { method: 'POST', body: formData });
                    const data = await res.json();
                    if (data.success) { await Swal.fire('Deleted!', data.message, 'success'); location.reload(); }
                    else Swal.fire('Error!', data.message, 'error');
                } catch (e) { Swal.fire('Error!', 'Something went wrong.', 'error'); }
            }
        }

        function resetBtn(spinner, icon, btn) {
            spinner.style.display = 'none';
            icon.style.display = 'inline-block';
            btn.disabled = false;
        }

        window.onclick = function(event) {
            if (event.target === document.getElementById('viewModal')) closeViewModal();
        }
    </script>
</body>
</html>