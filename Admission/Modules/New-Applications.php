<?php
/**
 * New Applications Module
 */
require_once __DIR__ . '/../../auth/Security.php';
require_once __DIR__ . '/../../Database/config.php';
checkRole(['admission']);

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

    // DUMMY DATA INJECTION - For demonstration if database is empty
    if (empty($applications)) {
        $applications = [
            (object)[
                'application_no' => 'ENR260000001',
                'first_name' => 'Leon',
                'last_name' => 'Kennedy',
                'email' => 'l.kennedy@raccoon-pd.gov',
                'preferred_course_1' => 'BS Criminology',
                'course_display_name' => 'B.S. in Criminology',
                'submission_date' => date('Y-m-d H:i:s'),
                'phone_number' => '0917-123-4567'
            ],
            (object)[
                'application_no' => 'ENR260000002',
                'first_name' => 'Ada',
                'last_name' => 'Wong',
                'email' => 'ada.w@spy.org',
                'preferred_course_1' => 'BS Computer Science',
                'course_display_name' => 'B.S. in Computer Science',
                'submission_date' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'phone_number' => '0922-000-8888'
            ],
            (object)[
                'application_no' => 'ENR260000003',
                'first_name' => 'Claire',
                'last_name' => 'Redfield',
                'email' => 'claire@terrasave.org',
                'preferred_course_1' => 'BS Hospitality Management',
                'course_display_name' => 'B.S. in Hospitality Management',
                'submission_date' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'phone_number' => '0945-333-2222'
            ],
            (object)[
                'application_no' => 'ENR260000004',
                'first_name' => 'Albert',
                'last_name' => 'Wesker',
                'email' => 'wesker@umbrella.corp',
                'preferred_course_1' => 'BS Biology',
                'course_display_name' => 'B.S. in Biology',
                'submission_date' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'phone_number' => '0966-666-6666'
            ]
        ];
    }
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
    <title>New Applications - Admission Workspace</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../Assets/css/theme.css">
    <style>
        :root {
            --primary: #1648bc;
            --primary-dark: #0f172a;
            --surface: #ffffff;
            --background: #f8fafc;
            --border: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { background: var(--background); color: var(--text-main); display: flex; min-height: 100vh; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .content-area { padding: 30px; max-width: 1400px; margin: 0 auto; width: 100%; animation: fadeIn 0.4s ease-out; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .header-section {
            margin-bottom: 30px; display: flex; justify-content: space-between;
            align-items: center; flex-wrap: wrap; gap: 20px;
        }
        .header-section h1 { font-size: 1.8rem; font-weight: 800; color: var(--primary-dark); letter-spacing: -0.02em; }
        .header-section p { color: var(--text-muted); font-size: 0.95rem; }

        .search-box { position: relative; }
        .search-box i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
        .search-input {
            padding: 12px 20px 12px 45px; border-radius: 14px; border: 1px solid var(--border);
            outline: none; width: 300px; font-size: 0.9rem; transition: all 0.2s;
            background: white;
        }
        .search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(22, 72, 188, 0.05); }

        .table-container {
            background: white; border-radius: 20px; border: 1px solid var(--border);
            overflow: hidden; box-shadow: var(--shadow-sm);
        }
        table { width: 100%; border-collapse: collapse; }
        th { 
            text-align: left; padding: 18px 24px; background: #fcfdfe; 
            color: var(--text-muted); font-weight: 700; font-size: 0.75rem; 
            text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--border);
        }
        td { padding: 20px 24px; border-bottom: 1px solid var(--background); transition: 0.2s; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8faff; }

        .app-id { font-weight: 700; color: var(--primary); font-family: monospace; font-size: 1rem; }
        .student-name { font-weight: 700; color: var(--primary-dark); font-size: 0.95rem; }
        .student-email { font-size: 0.8rem; color: var(--text-muted); font-weight: 500; }
        .course-tag { 
            display: inline-block; padding: 4px 10px; border-radius: 8px; 
            background: #eef2ff; color: #4338ca; font-size: 0.75rem; font-weight: 700;
        }
        .date-text { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; }

        .btn-group { display: flex; gap: 8px; }
        .btn-icon {
            width: 36px; height: 36px; border-radius: 10px; display: flex; 
            align-items: center; justify-content: center; border: 1px solid var(--border);
            background: white; color: var(--text-muted); cursor: pointer; transition: 0.2s;
        }
        .btn-icon:hover { background: #f1f5f9; color: var(--primary-dark); border-color: #cbd5e1; }
        .btn-icon.delete:hover { background: #fef2f2; color: var(--danger); border-color: #fecaca; }

        .btn-primary-sm {
            padding: 8px 16px; border-radius: 10px; background: var(--primary);
            color: white; border: none; font-weight: 700; font-size: 0.85rem;
            cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-primary-sm:hover { background: #1a3a8a; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(22, 72, 188, 0.2); }

        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-icon { font-size: 3rem; color: var(--border); margin-bottom: 15px; }
        .empty-text { color: var(--text-muted); font-weight: 600; }

        /* Modal enhancements */
        .modal-overlay { 
            position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); 
            backdrop-filter: blur(8px); z-index: 10000; display: none; 
            align-items: center; justify-content: center; padding: 20px;
        }
        .modal-card { 
            background: white; width: 100%; max-width: 550px; border-radius: 24px; 
            overflow: hidden; box-shadow: var(--shadow-lg); animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .modal-header { padding: 30px; background: var(--primary-dark); color: white; display: flex; align-items: center; gap: 20px; }
        .modal-avatar { 
            width: 64px; height: 64px; border-radius: 18px; background: rgba(255, 255, 255, 0.1); 
            display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        .modal-body { padding: 30px; }
        .info-card { 
            background: #f8fafc; border: 1px solid var(--border); border-radius: 16px; 
            padding: 20px; margin-bottom: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;
        }
        .info-box label { display: block; font-size: 0.65rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px; }
        .info-box p { font-weight: 700; color: var(--primary-dark); font-size: 0.9rem; }
        
        .alert-box { 
            background: #fffbeb; border: 1px solid #fef3c7; color: #92400e; 
            padding: 15px; border-radius: 12px; font-size: 0.85rem; display: flex; gap: 12px; align-items: center;
        }

        .modal-footer { 
            padding: 20px 32px; 
            border-top: 1px solid #edf2f7; 
            display: flex; 
            justify-content: flex-end; 
            background: #f8fafc;
            gap: 12px; 
        }
        .student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }
        .btn-outline { 
            padding: 10px 20px; border-radius: 12px; border: 1px solid var(--border); 
            background: white; color: var(--text-muted); font-weight: 700; cursor: pointer; transition: 0.2s;
        }
        .btn-outline:hover { background: #f8fafc; color: var(--primary-dark); border-color: #cbd5e1; }
    </style>
</head>
<body>
    <!-- Modal -->
    <div id="appModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-avatar" id="mAvatar">JD</div>
                <div>
                    <h2 id="mName" style="font-size: 1.25rem;">Student Name</h2>
                    <p id="mCourse" style="opacity: 0.7; font-size: 0.85rem;">Course Title</p>
                </div>
            </div>
            <div class="modal-body">
                <div class="info-card">
                    <div class="info-box">
                        <label>Application ID</label>
                        <p id="mID">#0000</p>
                    </div>
                    <div class="info-box">
                        <label>Submission Date</label>
                        <p id="mDate">N/A</p>
                    </div>
                    <div class="info-box">
                        <label>Email Address</label>
                        <p id="mEmail">N/A</p>
                    </div>
                    <div class="info-box">
                        <label>Contact Number</label>
                        <p id="mPhone">N/A</p>
                    </div>
                </div>
                <div class="alert-box">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Moving to evaluation will notify the applicant and move this record to the evaluation queue.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-outline" onclick="closeModal()">Dismiss</button>
                <button class="btn-primary-sm" onclick="moveSubmitting(this)">
                    <span id="btnText">Proceed to Evaluation</span>
                    <i class="fas fa-circle-notch fa-spin" id="btnSpinner" style="display:none;"></i>
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
                    <p>List of newly submitted applications pending initial review.</p>
                </div>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="tableSearch" onkeyup="searchTable()" placeholder="Search applications..." class="search-input">
                </div>
            </div>

            <div class="table-container">
                <table id="appTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Details</th>
                            <th>Preferred Course</th>
                            <th>Submission Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($applications)): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                                        <p class="empty-text">No pending applications found.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><span class="app-id">#<?php echo htmlspecialchars($app->application_no); ?></span></td>
                                    <td>
                                        <div class="student-name"><?php echo htmlspecialchars($app->first_name . ' ' . $app->last_name); ?></div>
                                        <div class="student-email"><?php echo htmlspecialchars($app->email); ?></div>
                                    </td>
                                    <td><span class="course-tag"><?php echo htmlspecialchars($app->course_display_name); ?></span></td>
                                    <td><span class="date-text"><?php echo date('M d, Y', strtotime($app->submission_date)); ?></span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn-primary-sm" onclick='showModal(<?php echo json_encode($app); ?>)'>
                                                Review
                                            </button>
                                            <button class="btn-icon delete" onclick="deleteApp('<?php echo $app->application_no; ?>', '<?php echo addslashes($app->first_name." ".$app->last_name); ?>')">
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
        let selectedAppID = null;

        function searchTable() {
            const input = document.getElementById('tableSearch');
            const filter = input.value.toLowerCase();
            const rows = document.getElementById('appTable').getElementsByTagName('tr');
            for (let i = 1; i < rows.length; i++) {
                const text = rows[i].textContent.toLowerCase();
                rows[i].style.display = text.includes(filter) ? '' : 'none';
            }
        }

        function showModal(data) {
            selectedAppID = data.application_no;
            document.getElementById('mName').textContent = data.first_name + ' ' + data.last_name;
            document.getElementById('mAvatar').textContent = (data.first_name[0] + data.last_name[0]).toUpperCase();
            document.getElementById('mCourse').textContent = data.course_display_name;
            document.getElementById('mID').textContent = '#' + data.application_no;
            document.getElementById('mEmail').textContent = data.email;
            document.getElementById('mPhone').textContent = data.phone_number;
            document.getElementById('mDate').textContent = new Date(data.submission_date).toLocaleDateString();
            
            document.getElementById('appModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('appModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            selectedAppID = null;
        }

        async function moveSubmitting(btn) {
            if (!selectedAppID) return;
            
            const btnText = document.getElementById('btnText');
            const spinner = document.getElementById('btnSpinner');
            
            btn.disabled = true;
            btnText.style.display = 'none';
            spinner.style.display = 'inline-block';

            const formData = new FormData();
            formData.append('action', 'proceed_to_evaluation');
            formData.append('application_no', selectedAppID);

            try {
                const response = await fetch(window.location.href, { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: result.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'Evaluation.php';
                    });
                } else {
                    Swal.fire('Error', result.message, 'error');
                    resetBtn(btn, btnText, spinner);
                }
            } catch (error) {
                Swal.fire('Error', 'An unexpected error occurred.', 'error');
                resetBtn(btn, btnText, spinner);
            }
        }

        async function deleteApp(id, name) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete ${name}'s application.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete_application');
                formData.append('application_no', id);
                
                try {
                    const response = await fetch(window.location.href, { method: 'POST', body: formData });
                    const res = await response.json();
                    if (res.success) {
                        Swal.fire('Deleted', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Failed to delete application', 'error');
                }
            }
        }

        function resetBtn(btn, text, spinner) {
            btn.disabled = false;
            text.style.display = 'inline-block';
            spinner.style.display = 'none';
        }

        window.onclick = function(e) {
            if (e.target === document.getElementById('appModal')) closeModal();
        }
    </script>
</body>
</html>
