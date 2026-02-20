<?php
session_start();
require_once '../../auth/Security.php';
require_once '../../Database/config.php';
require_once '../../auth/mail_helper.php';
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
            $stmt = $pdo->prepare("SELECT a.*, COALESCE(c.course_name, a.preferred_course_1) as course_display_name 
                                   FROM admission_applications a 
                                   LEFT JOIN courses c ON (a.preferred_course_1 = CAST(c.courseId AS CHAR) OR a.preferred_course_1 = c.course_name)
                                   WHERE a.applicationId = ?");
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
                $ref_code = "";

                $c_stmt = $pdo->prepare("SELECT courseId FROM courses WHERE course_name = ?");
                $c_stmt->execute([$app->course_display_name]);
                $course_res = $c_stmt->fetch();
                $course_id = $course_res ? $course_res->courseId : NULL;

                // Check if already enrolled to avoid duplicate email error
                $stmt = $pdo->prepare("SELECT enrollmentId, reference_code FROM enrollments WHERE email = ?");
                $stmt->execute([$app->email]);
                $existing_enr = $stmt->fetch();

                if ($existing_enr) {
                    // Update existing
                    $ref_code = $existing_enr->reference_code;
                    $sql = "UPDATE enrollments SET 
                            course_id = ?,
                            tuition_fee = ?, 
                            misc_fee = ?, 
                            total_fee = ?, 
                            balance = ?,
                            status = 'Pending Payment'
                            WHERE enrollmentId = ?";
                    $pdo->prepare($sql)->execute([$course_id, $tuition, $misc, $total, $total, $existing_enr->enrollmentId]);
                    $message = "Application #{$app->application_no} approved. Assessment updated. Status set to Pending Payment. Student can now proceed to Cashier.";
                } else {
                    // Insert new
                    $ref_code = "ENR-" . date('Y') . "-" . strtoupper(substr(md5(uniqid()), 0, 6));
                    $sql = "INSERT INTO enrollments (reference_code, admission_type, course_id, first_name, last_name, gender, birthdate, contact_number, email, year_level, status, tuition_fee, misc_fee, total_fee, balance) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'First Year', 'Pending Payment', ?, ?, ?, ?)";
                    $pdo->prepare($sql)->execute([
                        $ref_code, 
                        $app->student_type, 
                        $course_id,
                        $app->first_name, 
                        $app->last_name, 
                        $app->gender,
                        $app->date_of_birth,
                        $app->phone_number,
                        $app->email,
                        $tuition,
                        $misc,
                        $total,
                        $total
                    ]);
                    $message = "Application #{$app->application_no} approved and student record created. Status set to Pending Payment (Direct to Cashier).";
                }

                // 4. Send Email Notification with Payment Instructions
                sendPaymentInstructionEmail($app->email, [
                    'first_name' => $app->first_name,
                    'last_name' => $app->last_name,
                    'reference_code' => $ref_code,
                    'total_fee' => $total
                ]);
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

// Fetch pending and processing applications with their documents from enrollments
$apps = $pdo->query("SELECT a.*, s.is_verified, 
                          COALESCE(c.course_name, a.preferred_course_1) as course_display_name,
                          e.birth_cert, e.form_138, e.form_137, e.good_moral, e.barangay_clearance, e.id_picture
                   FROM admission_applications a 
                   LEFT JOIN students s ON LOWER(a.email) = LOWER(s.email) 
                   LEFT JOIN enrollments e ON LOWER(a.email) = LOWER(e.email)
                   LEFT JOIN courses c ON (a.preferred_course_1 = CAST(c.courseId AS CHAR) OR a.preferred_course_1 = c.course_name)
                   WHERE a.status IN ('Pending', 'Processing') 
                   ORDER BY a.submission_date DESC")->fetchAll();

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

        /* Robust Modal Styles */
        .eval-modal-overlay { 
            position: fixed !important; top: 0 !important; left: 0 !important; 
            width: 100vw !important; height: 100vh !important; 
            background: rgba(15, 23, 42, 0.85) !important; 
            display: none; align-items: center; justify-content: center; 
            z-index: 999999 !important; backdrop-filter: blur(8px) !important; 
            padding: 20px; opacity: 1 !important; visibility: visible !important;
        }

        .eval-modal-content { 
            background: white !important; width: 100%; max-width: 600px; 
            border-radius: 28px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); 
            overflow: hidden; position: relative; z-index: 1000000 !important;
        }

        .eval-modal-header { 
            padding: 24px 32px; border-bottom: 1px solid #edf2f7; 
            display: flex; justify-content: space-between; align-items: center; 
            background: #f8fafc; 
        }

        .eval-modal-body { 
            padding: 32px; max-height: 70vh; overflow-y: auto;
        }

        .eval-modal-footer { 
            padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; gap: 12px;
        }

        .doc-item {
            background: #f8fafc;
            padding: 16px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            transition: 0.2s;
        }
        .doc-item:hover { border-color: var(--primary-blue); background: #ffffff; }

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

        .status-pill {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .eval-row:hover {
            background-color: #f0f7ff !important;
            transition: background-color 0.2s ease;
        }

        .eval-row:active {
            background-color: #e0efff !important;
        }
    </style>
</head>

<body>
    <!-- Review Modal -->
    <div id="reviewModal" class="eval-modal-overlay">
        <div class="eval-modal-content">
            <div class="eval-modal-header">
                <div>
                    <h2 id="modalStudentName" style="font-weight: 800; color: #1e293b; font-size: 1.4rem;">Student Name</h2>
                    <p id="modalStudentCourse" style="color: #64748b; font-size: 0.85rem;">Course Title</p>
                </div>
                <button onclick="closeReviewModal()"
                    style="background: #f1f5f9; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; color: #64748b;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="eval-modal-body">
                <div style="margin-bottom: 30px;">
                    <h4
                        style="color: #1e293b; font-size: 0.9rem; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-file-alt" style="color: var(--primary-blue);"></i> Submitted Documents
                    </h4>
                    <div id="modalDocList">
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
                            <option value="Processing">Pre-verify & Keep Processing</option>
                            <option value="Rejected">Reject Application (Incomplete/Invalid)</option>
                        </select>
                        <textarea name="notes" placeholder="Add internal notes for this evaluation..."
                            style="width: 100%; height: 120px; padding: 16px; border-radius: 16px; border: 1px solid #e2e8f0; outline: none; resize: none; font-family: inherit; font-size: 0.9rem; color: #475569;"></textarea>
                    </div>
                </form>
            </div>
            <div class="eval-modal-footer">
                <button onclick="closeReviewModal()"
                    style="padding: 12px 24px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; color: #475569; font-weight: 600; cursor: pointer;">Cancel</button>
                <button onclick="saveEvaluation()"
                    style="padding: 12px 28px; border-radius: 12px; background: var(--primary-blue); color: white; border: none; font-weight: 600; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(22, 72, 188, 0.2);">Confirm & Save</button>
            </div>
        </div>
    </div>
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
                            <th>Verification</th>
                            <th>Target Course</th>
                            <th>Documents</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($apps as $app): ?>
                            <tr class="eval-row" style="cursor: pointer;"
                                onclick="openReviewModal(
                                    '<?php echo $app->applicationId; ?>',
                                    '<?php echo addslashes($app->first_name . ' ' . $app->last_name); ?>', 
                                    '<?php echo addslashes($app->course_display_name); ?>',
                                    '<?php echo $app->birth_cert; ?>',
                                    '<?php echo $app->form_138; ?>',
                                    '<?php echo $app->form_137; ?>',
                                    '<?php echo $app->good_moral; ?>',
                                    '<?php echo $app->barangay_clearance; ?>',
                                    '<?php echo $app->id_picture; ?>'
                                )">
                                <td style="font-weight: 600;">
                                    <?php echo htmlspecialchars($app->first_name . ' ' . $app->last_name); ?>
                                    <div style="font-size: 0.75rem; font-weight: 400; color: #64748b;"><?php echo htmlspecialchars($app->email); ?></div>
                                </td>
                                <td>
                                    <?php if ($app->is_verified): ?>
                                        <span class="badge" style="background: #dcfce7; color: #166534; font-size: 0.7rem;">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background: #fee2e2; color: #991b1b; font-size: 0.7rem;">
                                            <i class="fas fa-times-circle"></i> Unverified
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($app->course_display_name); ?></td>
                                <td>
                                    <div style="font-size: 0.75rem; color: #64748b;">
                                        <?php 
                                        $docs = [];
                                        if ($app->birth_cert) $docs[] = "PSA";
                                        if ($app->form_138) $docs[] = "F-138";
                                        if ($app->form_137) $docs[] = "F-137";
                                        if ($app->good_moral) $docs[] = "Moral";
                                        if ($app->barangay_clearance) $docs[] = "Brgy";
                                        if ($app->id_picture) $docs[] = "ID";
                                        echo !empty($docs) ? implode(", ", $docs) : "No documents";
                                        ?>
                                    </div>
                                </td>
                                <td><span class="badge <?php echo ($app->status == 'Pending') ? 'badge-pending' : 'badge-processing'; ?>">
                                    <?php echo htmlspecialchars($app->status); ?></span>
                                </td>
                                <td><button class="btn-evaluate">Evaluate</button></td>
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

        function openReviewModal(id, name, course, psa, f138, f137, moral, brgy, idpic) {
            document.getElementById('modalAppId').value = id;
            document.getElementById('modalStudentName').textContent = name;
            document.getElementById('modalStudentCourse').textContent = course;
            
            // Build Doc List
            const list = document.getElementById('modalDocList');
            list.innerHTML = '';
            
            const docs = [
                { name: 'PSA Birth Certificate', path: psa, icon: 'fa-id-card', color: '#eef2ff', text: '#1648bc' },
                { name: 'Form 138 (Report Card)', path: f138, icon: 'fa-file-pdf', color: '#fee2e2', text: '#ef4444' },
                { name: 'Form 137 (TOR)', path: f137, icon: 'fa-scroll', color: '#f0fdf4', text: '#16a34a' },
                { name: 'Good Moral Certificate', path: moral, icon: 'fa-certificate', color: '#fff7ed', text: '#ea580c' },
                { name: 'Barangay Clearance', path: brgy, icon: 'fa-map-marker-alt', color: '#f0f9ff', text: '#0369a1' },
                { name: 'Passport Size ID', path: idpic, icon: 'fa-id-badge', color: '#f5f3ff', text: '#7c3aed' }
            ];

            let docFound = false;
            docs.forEach(doc => {
                if (doc.path) {
                    docFound = true;
                    const item = document.createElement('div');
                    item.className = 'doc-item';
                    const fullPath = '../../' + doc.path;
                    item.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="width: 48px; height: 48px; background: ${doc.color}; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: ${doc.text};">
                                <i class="fas ${doc.icon}"></i>
                            </div>
                            <div>
                                <p style="font-weight: 600; font-size: 0.9rem; color: #1e293b;">${doc.name}</p>
                                <p style="font-size: 0.75rem; color: #64748b;">Uploaded File</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <a href="${fullPath}" target="_blank" style="color: var(--primary-blue); background: #f1f5f9; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    `;
                    list.appendChild(item);
                }
            });

            if (!docFound) {
                list.innerHTML = '<div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 0.9rem;">No documents uploaded.</div>';
            }

            const modal = document.getElementById('reviewModal');
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
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