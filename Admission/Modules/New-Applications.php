<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}

require_once '../../Database/config.php';

// Handle AJAX Move to Evaluation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'proceed_to_evaluation') {
    header('Content-Type: application/json');
    $appId = $_POST['application_no'] ?? null;

    if (!$appId) {
        echo json_encode(['success' => false, 'message' => 'Application ID missing.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE admission_applications SET status = 'Processing' WHERE application_no = ?");
        $stmt->execute([$appId]);
        echo json_encode(['success' => true, 'message' => 'Application moved to evaluation.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()]);
    }
    exit;
}

// Handle AJAX Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_application') {
    header('Content-Type: application/json');
    $appId = $_POST['application_no'] ?? null;

    if (!$appId) {
        echo json_encode(['success' => false, 'message' => 'Application ID missing.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM admission_applications WHERE application_no = ?");
        $stmt->execute([$appId]);
        echo json_encode(['success' => true, 'message' => 'Application deleted successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()]);
    }
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT a.*, COALESCE(c.course_name, a.preferred_course_1) as course_display_name 
                           FROM admission_applications a 
                           LEFT JOIN courses c ON (TRIM(a.preferred_course_1) = CAST(c.courseId AS CHAR) OR a.preferred_course_1 = c.course_name)
                           WHERE a.status = 'Pending' 
                           ORDER BY a.submission_date DESC");
    $stmt->execute();
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
    <!-- Same styles as Settings.php (abbreviated for this example) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1648bc;
            --primary-dark: #0f172a;
            --bg-light: #f8fafc;
            --border-soft: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --shadow-premium: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: var(--bg-light);
            color: var(--text-dark);
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content-area {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .header-section {
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-section h1 {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--primary-dark);
            margin-bottom: 8px;
        }

        .header-section p {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .table-card {
            background: white;
            border-radius: 30px;
            box-shadow: var(--shadow-premium);
            border: 1px solid var(--border-soft);
            overflow: hidden;
        }

        .table-header {
            padding: 30px 40px;
            border-bottom: 1px solid var(--border-soft);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h3 { font-weight: 800; font-size: 1.25rem; color: var(--primary-dark); }

        table { width: 100%; border-collapse: collapse; }

        th {
            text-align: left;
            padding: 20px 40px;
            background: #f8fafc;
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        td {
            padding: 25px 40px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.95rem;
            vertical-align: middle;
        }

        .app-row { transition: all 0.2s; cursor: pointer; }
        .app-row:hover { background: #f8faff; }

        .badge-status {
            padding: 6px 14px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            background: #fff7ed;
            color: #c2410c;
        }

        .btn-action-pro {
            padding: 10px 18px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.85rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-view-pro { background: #f1f5f9; color: #475569; }
        .btn-view-pro:hover { background: #e2e8f0; color: #1e293b; }

        .btn-eval-pro { 
            background: var(--primary-blue); 
            color: white;
            box-shadow: 0 4px 12px rgba(22, 72, 188, 0.2);
        }
        .btn-eval-pro:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(22, 72, 188, 0.3);
        }
        .student-modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(12px);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .student-modal-content {
            background: white;
            width: 100%;
            max-width: 600px;
            border-radius: 35px;
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: modalScale 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes modalScale {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .modal-profile-header {
            padding: 40px;
            background: linear-gradient(135deg, #1648bc 0%, #1e3a8a 100%);
            color: white;
            text-align: center;
            position: relative;
        }

        .modal-avatar-pro {
            width: 100px; height: 100px; border-radius: 30px;
            background: rgba(255, 255, 255, 0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem; font-weight: 800; margin: 0 auto 20px;
            border: 3px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .modal-info-section { padding: 35px 40px; }

        .info-card-pro {
            background: #f8fafc;
            border-radius: 20px;
            padding: 20px;
            border: 1px solid #eef2ff;
            margin-bottom: 25px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-item label { 
            display: block; font-size: 0.7rem; font-weight: 800; 
            color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px; 
        }
        .info-item p { font-weight: 700; color: var(--primary-dark); font-size: 0.95rem; }

        .modal-footer-pro {
            padding: 30px 40px;
            background: white;
            border-top: 1px solid var(--border-soft);
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .btn-close-pro {
            padding: 12px 25px; border-radius: 14px; border: 2px solid var(--border-soft);
            background: white; color: var(--text-muted); font-weight: 700; cursor: pointer;
        }
        
        #evalSpinner { margin-right: 8px; display: none; }
    </style>
</head>

<body>
    <!-- Premium View Modal -->
    <div id="viewModal" class="student-modal-overlay">
        <div class="student-modal-content">
            <div class="modal-profile-header">
                <div class="modal-avatar-pro" id="modalAvatar">JD</div>
                <h2 id="modalProfileName" style="font-weight: 800; font-size: 1.6rem; letter-spacing: -0.02em;">John Doe</h2>
                <p id="modalProfileCourse" style="opacity: 0.8; font-weight: 500; font-size: 0.95rem;">BS Computer Science</p>
            </div>
            
            <div class="modal-info-section">
                <div class="info-card-pro">
                    <div class="info-item">
                        <label>Application ID</label>
                        <p id="modalAppId">#APP-10293</p>
                    </div>
                    <div class="info-item">
                        <label>Submission Date</label>
                        <p id="modalDate">Jan 12, 2024</p>
                    </div>
                </div>

                <div class="info-card-pro">
                    <div class="info-item">
                        <label>Email Address</label>
                        <p id="modalEmail">john@university.edu</p>
                    </div>
                    <div class="info-item">
                        <label>Contact Number</label>
                        <p id="modalContact">+63 912 345 6789</p>
                    </div>
                </div>

                <div style="background: #fffbeb; border: 1px solid #fef3c7; padding: 20px; border-radius: 20px; color: #92400e; font-size: 0.85rem; display: flex; gap: 12px; align-items: center;">
                    <i class="fas fa-info-circle fa-lg"></i>
                    <p style="font-weight: 600;">Moving to evaluation will alert the student and lock this application phase.</p>
                </div>
            </div>

            <div class="modal-footer-pro">
                <button class="btn-close-pro" onclick="closeViewModal()">Dismiss</button>
                <button class="btn-action-pro btn-eval-pro" onclick="proceedToEval(event)">
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
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem;"></i>
                    <input type="text" id="appSearch" onkeyup="filterTable('appSearch', 'appTable')" placeholder="Search applicants..." 
                        style="padding: 14px 20px 14px 45px; border-radius: 16px; border: 2px solid #f1f5f9; outline: none; width: 320px; font-size: 0.9rem; font-weight: 600; transition: 0.3s;"
                        onfocus="this.style.borderColor='#1648bc'">
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
                                <td colspan="5" style="text-align: center; padding: 80px;">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 20px;">
                                        <div style="width: 100px; height: 100px; background: #f8fafc; border-radius: 30px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-inbox fa-3x" style="color: #e2e8f0;"></i>
                                        </div>
                                        <p style="color: #64748b; font-weight: 600;">No pending applications in your queue.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($applications as $app): ?>
                                <tr class="app-row">
                                    <td style="font-weight: 800; color: var(--primary-dark); font-size: 0.9rem;">#<?php echo htmlspecialchars($app->application_no); ?></td>
                                    <td>
                                        <div style="font-weight: 800; color: var(--primary-dark);"><?php echo htmlspecialchars($app->first_name . ' ' . $app->last_name); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;"><?php echo htmlspecialchars($app->email); ?></div>
                                    </td>
                                    <td style="font-weight: 700; color: #475569; font-size: 0.85rem;"><?php echo htmlspecialchars($app->course_display_name); ?></td>
                                    <td style="color: var(--text-muted); font-weight: 600;"><?php echo date('M d, Y', strtotime($app->submission_date)); ?></td>
                                    <td>
                                        <?php 
                                            $appData = [
                                                'name' => $app->first_name . ' ' . $app->last_name,
                                                'no' => $app->application_no,
                                                'course' => $app->course_display_name,
                                                'date' => date('M d, Y', strtotime($app->submission_date)),
                                                'email' => $app->email,
                                                'phone' => $app->phone_number
                                            ];
                                        ?>
                                        <div style="display: flex; gap: 10px;">
                                            <button class="btn-action-pro btn-view-pro" onclick="openDetailModal(<?php echo htmlspecialchars(json_encode($appData), ENT_QUOTES, 'UTF-8'); ?>)">
                                                <i class="fas fa-eye"></i> Details
                                            </button>
                                            <button class="btn-action-pro" style="background: #fef2f2; color: #ef4444;" onclick="deleteApplication('<?php echo $app->application_no; ?>', '<?php echo addslashes($app->first_name . ' ' . $app->last_name); ?>')">
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
    <!-- View Application Modal -->

    <?php include '../Components/GlobalScripts.php'; ?>
    <script>
        function filterTable(inputId, tableId) {
            const input = document.getElementById(inputId);
            const filter = input.value.toLowerCase();
            const table = document.getElementById(tableId);
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let rowVisible = false;
                const td = tr[i].getElementsByTagName("td");
                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            rowVisible = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = rowVisible ? "" : "none";
            }
        }

        let currentAppId = null;

        function openDetailModal(data) {
            try {
                currentAppId = data.no;
                if (document.getElementById('modalProfileName')) document.getElementById('modalProfileName').textContent = data.name;
                if (document.getElementById('modalAvatar')) document.getElementById('modalAvatar').textContent = data.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
                if (document.getElementById('modalAppId')) document.getElementById('modalAppId').textContent = '#' + data.no;
                if (document.getElementById('modalProfileCourse')) document.getElementById('modalProfileCourse').textContent = data.course;
                if (document.getElementById('modalDate')) document.getElementById('modalDate').textContent = data.date;
                if (document.getElementById('modalEmail')) document.getElementById('modalEmail').textContent = data.email;
                if (document.getElementById('modalContact')) document.getElementById('modalContact').textContent = data.phone;

                const modal = document.getElementById('viewModal');
                if (modal) {
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            } catch (err) {
                console.error('Error opening detail modal:', err);
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
            const spinner = btn.querySelector('#evalSpinner');
            const icon = btn.querySelector('#evalIcon');
            
            if (spinner) spinner.style.display = 'inline-block';
            if (icon) icon.style.display = 'none';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('action', 'proceed_to_evaluation');
            formData.append('application_no', currentAppId);

            try {
                const response = await fetch('New-Applications.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        title: 'Moved to Evaluation!',
                        text: 'Redirecting you to the Evaluation Workspace...',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'Evaluation.php';
                    });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                    if (spinner) spinner.style.display = 'none';
                    if (icon) icon.style.display = 'inline-block';
                    btn.disabled = false;
                }
            } catch (error) {
                Swal.fire('Error!', 'Something went wrong.', 'error');
                if (spinner) spinner.style.display = 'none';
                if (icon) icon.style.display = 'inline-block';
                btn.disabled = false;
            }
        }

        async function deleteApplication(id, name) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete the application of ${name}. This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#718096',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete_application');
                formData.append('application_no', id);

                try {
                    const response = await fetch('New-Applications.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        Swal.fire('Deleted!', data.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error!', 'Something went wrong while deleting.', 'error');
                }
            }
        }

        window.onclick = function (event) {
            const modal = document.getElementById('viewModal');
            if (event.target == modal) {
                closeViewModal();
            }
        }
    </script>
</body>

</html>