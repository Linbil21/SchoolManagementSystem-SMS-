<?php
session_start();
require_once '../../auth/Security.php';
require_once '../../Database/config.php';
checkRole(['admission']);

$message = '';
$error = '';

// Handle Evaluation Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $app_id = $_POST['application_id'];
        $status = $_POST['status'];
        $notes = $_POST['notes'];

        if ($status === 'Approved') {
            // Get application details
            $stmt = $pdo->prepare("SELECT * FROM admission_applications WHERE applicationId = ?");
            $stmt->execute([$app_id]);
            $app = $stmt->fetch();

            if ($app) {
                // 1. Update Application Status
                $pdo->prepare("UPDATE admission_applications SET status = 'Approved' WHERE applicationId = ?")
                    ->execute([$app_id]);

                // 2. Create/Update Student Record if doesn't exist
                $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
                $stmt->execute([$app->email]);
                $student = $stmt->fetch();

                // 3. Create or Update Enrollment & Assign Default Fees
                $tuition = 15000.00;
                $misc = 2500.00;
                $total = $tuition + $misc;

                // Check if already enrolled to avoid duplicate email error
                $stmt = $pdo->prepare("SELECT enrollmentId FROM enrollments WHERE email = ?");
                $stmt->execute([$app->email]);
                $existing_enr = $stmt->fetch();

                if ($existing_enr) {
                    // Update existing
                    $sql = "UPDATE enrollments SET 
                            tuition_fee = ?, 
                            misc_fee = ?, 
                            total_fee = ?, 
                            balance = ?,
                            status = 'Pending Payment'
                            WHERE enrollmentId = ?";
                    $pdo->prepare($sql)->execute([$tuition, $misc, $total, $total, $existing_enr->enrollmentId]);
                    $message = "Application #{$app->application_no} approved. Assessment updated. Status set to Pending Payment. Student can now proceed to Cashier.";
                } else {
                    // Insert new
                    $ref_code = "ENR-" . date('Y') . "-" . strtoupper(substr(md5(uniqid()), 0, 6));
                    $sql = "INSERT INTO enrollments (reference_code, admission_type, first_name, last_name, email, year_level, status, tuition_fee, misc_fee, total_fee, balance) 
                            VALUES (?, ?, ?, ?, ?, 'First Year', 'Pending Payment', ?, ?, ?, ?)";
                    $pdo->prepare($sql)->execute([
                        $ref_code, 
                        $app->student_type, 
                        $app->first_name, 
                        $app->last_name, 
                        $app->email,
                        $tuition,
                        $misc,
                        $total,
                        $total
                    ]);
                    $message = "Application #{$app->application_no} approved and student record created. Status set to Pending Payment (Direct to Cashier).";
                }
            }
        } else {
            $pdo->prepare("UPDATE admission_applications SET status = ? WHERE applicationId = ?")
                ->execute([$status, $app_id]);
            $message = "Application status updated to $status.";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch pending and processing applications
$apps = $pdo->query("SELECT * FROM admission_applications WHERE status IN ('Pending', 'Processing') ORDER BY submission_date DESC")->fetchAll();

// Stats
$pending_count = $pdo->query("SELECT COUNT(*) FROM admission_applications WHERE status = 'Pending'")->fetchColumn();
$approved_today = $pdo->query("SELECT COUNT(*) FROM admission_applications WHERE status = 'Approved' AND DATE(submission_date) = CURDATE()")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Evaluation - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1648bc;
            --bg-light: #f7fafc;
            --text-dark: #2d3748;
            --text-gray: #718096;
            --success: #22c55e;
            --warning: #f59e0b;
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
            overflow-x: hidden;
        }

        .content-area {
            padding: 30px;
            flex: 1;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-section h1 {
            font-size: 1.8rem;
            color: var(--text-dark);
            font-weight: 800;
        }

        .header-section p {
            color: var(--text-gray);
            font-size: 0.95rem;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .evaluation-table-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table-header {
            padding: 20px 25px;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px 25px;
            background: #f8fafc;
            color: var(--text-gray);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        td {
            padding: 18px 25px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-pending {
            background: #fee2e2;
            color: #ef4444;
        }

        .badge-processing {
            background: #fef3c7;
            color: #d97706;
        }

        .btn-evaluate {
            background: var(--primary-blue);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            font-size: 0.85rem;
        }

        .btn-evaluate:hover {
            background: #1a3a8a;
            transform: translateY(-2px);
        }

        /* Premium Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.8) !important;
            backdrop-filter: blur(12px) !important;
            overflow-y: auto;
            align-items: center;
            justify-content: center;
        }

        .modal[style*="display: block"] {
            display: flex !important;
        }

        .modal-content {
            background: white;
            margin: auto;
            width: 95%;
            max-width: 850px;
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            animation: modalPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes modalPop {
            from { transform: scale(0.9) translateY(20px); opacity: 0; }
            to { transform: scale(1) translateY(0); opacity: 1; }
        }

        .modal-header {
            padding: 24px 32px;
            background: #f8fafc;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 32px;
        }

        .modal-footer {
            padding: 24px 32px;
            background: #f8fafc;
            border-top: 1px solid #edf2f7;
            display: flex;
            justify-content: flex-end;
            gap: 16px;
        }

        .doc-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            margin-bottom: 12px;
            transition: 0.2s;
        }

        .doc-item:hover {
            border-color: var(--primary-blue);
            background: #fff;
        }

        .status-pill {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="header-section">
                <div>
                    <h1>Application Evaluation</h1>
                    <p>Review and verify submitted student documents and credentials.</p>
                </div>
                <button class="btn-evaluate"><i class="fas fa-filter"></i> Filter</button>
            </div>

            <?php if ($message): ?>
                <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #bbf7d0; font-size: 0.9rem;">
                    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #fecaca; font-size: 0.9rem;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #eef2ff; color: #1648bc;"><i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <p style="font-size: 0.8rem; color: var(--text-gray);">Pending Review</p>
                        <h3 style="font-size: 1.2rem;"><?php echo $pending_count; ?></h3>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #ecfdf5; color: #10b981;"><i
                            class="fas fa-check-circle"></i></div>
                    <div>
                        <p style="font-size: 0.8rem; color: var(--text-gray);">Approved Today</p>
                        <h3 style="font-size: 1.2rem;"><?php echo $approved_today; ?></h3>
                    </div>
                </div>
            </div>

            <div class="evaluation-table-card">
                <div class="table-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 1.1rem; font-weight: 700;">Recent Submissions</h3>
                    <div class="search-box" style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="text" id="evalSearch" onkeyup="filterTable('evalSearch', 'evalTable')" placeholder="Search submissions..." 
                            style="padding: 10px 15px 10px 40px; border-radius: 10px; border: 1px solid #edf2f7; outline: none; width: 280px; font-size: 0.9rem;">
                    </div>
                </div>
                <table id="evalTable">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Target Course</th>
                            <th>Documents</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($apps as $app): ?>
                            <tr>
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($app->first_name . ' ' . $app->last_name); ?></td>
                                <td><?php echo htmlspecialchars($app->preferred_course_1); ?></td>
                                <td>PSA, Report Card</td>
                                <td><span class="badge <?php echo ($app->status == 'Pending') ? 'badge-pending' : 'badge-processing'; ?>">
                                    <?php echo htmlspecialchars($app->status); ?></span>
                                </td>
                                <td><button class="btn-evaluate"
                                        onclick="openReviewModal(
                                            '<?php echo $app->applicationId; ?>',
                                            '<?php echo addslashes($app->first_name . ' ' . $app->last_name); ?>', 
                                            '<?php echo addslashes($app->preferred_course_1); ?>'
                                        )">Evaluate</button></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($apps)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">No applications for evaluation.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Review Modal -->
    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h2 id="modalStudentName" style="font-weight: 800; color: #1e293b; font-size: 1.4rem;">Alice Johnson
                    </h2>
                    <p id="modalStudentCourse" style="color: #64748b; font-size: 0.85rem;">Bachelor of Science in
                        Engineering</p>
                </div>
                <button onclick="closeReviewModal()"
                    style="background: #f1f5f9; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; color: #64748b;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 30px;">
                    <h4
                        style="color: #1e293b; font-size: 0.9rem; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-file-alt" style="color: var(--primary-blue);"></i> Submitted Documents
                    </h4>

                    <div class="doc-item">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div
                                style="width: 48px; height: 48px; background: #fee2e2; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ef4444;">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div>
                                <p style="font-weight: 600; font-size: 0.9rem; color: #1e293b;">High School Report Card
                                    (Form 138)</p>
                                <p style="font-size: 0.75rem; color: #64748b;">Verified by Admin on Jan 10</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span class="status-pill" style="background: #ecfdf5; color: #10b981;">Verified</span>
                            <button
                                style="color: var(--primary-blue); background: none; border: none; cursor: pointer;"><i
                                    class="fas fa-external-link-alt"></i></button>
                        </div>
                    </div>

                    <div class="doc-item">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div
                                style="width: 48px; height: 48px; background: #eef2ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary-blue);">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div>
                                <p style="font-weight: 600; font-size: 0.9rem; color: #1e293b;">PSA Birth Certificate
                                </p>
                                <p style="font-size: 0.75rem; color: #64748b;">Pending Verification</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span class="status-pill" style="background: #fef3c7; color: #d97706;">Pending</span>
                            <button
                                style="color: var(--primary-blue); background: none; border: none; cursor: pointer;"><i
                                    class="fas fa-check" title="Quick Approve"></i></button>
                        </div>
                    </div>
                </div>

                <form id="evaluationForm" method="POST">
                    <input type="hidden" name="action" value="submit_evaluation">
                    <input type="hidden" name="application_id" id="modalAppId">
                    <div>
                        <h4 style="color: #1e293b; font-size: 0.9rem; font-weight: 700; margin-bottom: 12px;">Evaluation Status & Notes</h4>
                        <select name="status" id="modalEvalStatus"
                            style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 16px; outline: none; font-family: inherit;">
                            <option value="Approved">Accept Documents & Verify For Payment</option>
                        </select>
                        <textarea name="notes" placeholder="Add internal notes for this evaluation..."
                            style="width: 100%; height: 120px; padding: 16px; border-radius: 16px; border: 1px solid #e2e8f0; outline: none; resize: none; font-family: inherit; font-size: 0.9rem; color: #475569;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button onclick="closeReviewModal()"
                    style="padding: 12px 24px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; color: #475569; font-weight: 600; cursor: pointer; transition: 0.2s;">Cancel Changes</button>
                <button onclick="saveEvaluation()"
                    style="padding: 12px 28px; border-radius: 12px; background: var(--primary-blue); color: white; border: none; font-weight: 600; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(22, 72, 188, 0.2);">Confirm & Save</button>
            </div>
        </div>
    </div>

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

        function openReviewModal(id, name, course) {
            document.getElementById('modalAppId').value = id;
            document.getElementById('modalStudentName').textContent = name;
            document.getElementById('modalStudentCourse').textContent = course;
            document.getElementById('reviewModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function saveEvaluation() {
            const status = document.getElementById('modalEvalStatus').value;
            const confirmMsg = status === 'Approved' 
                ? 'Approving this application will automatically create a Student Record and assign Tuition Fees. Proceed?' 
                : 'Save evaluation changes?';
            
            if (confirm(confirmMsg)) {
                document.getElementById('evaluationForm').submit();
            }
        }

        window.onclick = function (event) {
            const modal = document.getElementById('reviewModal');
            if (event.target == modal) {
                closeReviewModal();
            }
        }
    </script>
</body>

</html>