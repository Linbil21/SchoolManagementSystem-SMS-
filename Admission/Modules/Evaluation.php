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

                // Improved Course Resolution
                $c_stmt = $pdo->prepare("SELECT courseId FROM courses WHERE course_name = ? OR CAST(courseId AS CHAR) = ?");
                $c_stmt->execute([$app->course_display_name, $app->course_display_name]);
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
                   LEFT JOIN students s ON a.email = s.email
                   LEFT JOIN enrollments e ON a.email = e.email
                   LEFT JOIN courses c ON (TRIM(a.preferred_course_1) = CAST(c.courseId AS CHAR) OR a.preferred_course_1 = c.course_name)
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
            --primary-dark: #0f172a;
            --bg-light: #f8fafc;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --border-soft: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
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
            overflow-x: hidden;
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

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: var(--shadow-premium);
            display: flex;
            align-items: center;
            gap: 20px;
            border: 1px solid var(--border-soft);
            transition: transform 0.3s ease;
        }

        .stat-card:hover { transform: translateY(-5px); }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Table Section */
        .evaluation-table-card {
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
            background: #ffffff;
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

        .eval-row { transition: all 0.2s; cursor: pointer; }
        .eval-row:hover { background: #f8faff; }

        /* Badges */
        .badge {
            padding: 6px 14px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-pending { background: #fff7ed; color: #c2410c; }
        .badge-processing { background: #eff6ff; color: #1d4ed8; }
        .badge-verified { background: #f0fdf4; color: #15803d; }
        .badge-unverified { background: #fef2f2; color: #b91c1c; }

        .btn-evaluate-action {
            background: var(--primary-blue);
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.85rem;
            box-shadow: 0 4px 12px rgba(22, 72, 188, 0.2);
        }

        .btn-evaluate-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(22, 72, 188, 0.3);
            background: #1a3a8a;
        }

        /* MODAL REDESIGN */
        .eval-modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(12px);
            z-index: 99999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .eval-modal-container {
            background: white;
            width: 100%;
            max-width: 1100px;
            height: 90vh;
            border-radius: 35px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.3);
            animation: modalScale 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes modalScale {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .eval-modal-header-pro {
            padding: 30px 45px;
            background: linear-gradient(135deg, #1648bc 0%, #1e3a8a 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .eval-student-info h2 { font-weight: 800; font-size: 1.6rem; letter-spacing: -0.02em; }
        .eval-student-info p { opacity: 0.8; font-size: 0.9rem; font-weight: 500; }

        .eval-close-btn {
            width: 45px; height: 45px; border-radius: 15px; border: none;
            background: rgba(255, 255, 255, 0.15); color: white;
            cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center;
        }
        .eval-close-btn:hover { background: rgba(255, 255, 255, 0.3); transform: rotate(90deg); }

        .eval-modal-body-split {
            flex: 1;
            display: grid;
            grid-template-columns: 460px 1fr;
            overflow: hidden;
        }

        .eval-left-form {
            padding: 35px 45px;
            background: #fcfdfe;
            border-right: 1px solid var(--border-soft);
            overflow-y: auto;
            scrollbar-width: none;
        }
        .eval-left-form::-webkit-scrollbar { display: none; }

        .eval-right-preview {
            background: #f1f5f9;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .section-title-mod {
            font-size: 0.7rem;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title-mod::after { content: ''; flex: 1; height: 1px; background: var(--border-soft); }

        .doc-item-pro {
            background: white;
            padding: 16px 20px;
            border-radius: 18px;
            border: 2px solid #f1f5f9;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: 0.3s;
        }
        .doc-item-pro:hover { border-color: var(--primary-blue); transform: translateX(5px); background: #f8faff; }
        .doc-item-pro.active { border-color: var(--primary-blue); background: #eef2ff; }

        .doc-icon-box {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
        }

        .eval-input-group { margin-bottom: 25px; }
        .eval-input-group label { display: block; font-weight: 700; color: var(--primary-dark); font-size: 0.9rem; margin-bottom: 10px; }
        
        .eval-select, .eval-textarea {
            width: 100%; border-radius: 16px; border: 2px solid var(--border-soft);
            padding: 14px 18px; font-family: inherit; font-size: 0.95rem; font-weight: 600;
            color: var(--primary-dark); outline: none; transition: 0.3s;
        }
        .eval-select:focus, .eval-textarea:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 4px rgba(22, 72, 188, 0.05); }

        .eval-textarea { height: 120px; resize: none; }

        .eval-modal-footer-pro {
            padding: 25px 45px;
            background: #ffffff;
            border-top: 1px solid var(--border-soft);
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .btn-cancel-eval {
            padding: 14px 28px; border-radius: 16px; border: 2px solid var(--border-soft);
            background: white; color: var(--text-muted); font-weight: 700; cursor: pointer; transition: 0.3s;
        }
        .btn-cancel-eval:hover { background: #f8fafc; border-color: var(--text-muted); color: var(--primary-dark); }

        .btn-save-eval {
            padding: 14px 35px; border-radius: 16px; border: none;
            background: var(--primary-blue); color: white; font-weight: 700; cursor: pointer;
            transition: all 0.3s; box-shadow: 0 8px 16px rgba(22, 72, 188, 0.25);
        }
        .btn-save-eval:hover { transform: translateY(-3px); box-shadow: 0 12px 24px rgba(22, 72, 188, 0.35); }

        /* Preview Area */
        #previewPlaceholder {
            height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;
            color: var(--text-muted); text-align: center; padding: 40px;
        }
        #previewPlaceholder i { font-size: 4rem; margin-bottom: 20px; opacity: 0.2; color: var(--primary-blue); }

        #previewContent { height: 100%; width: 100%; background: #f8fafc; overflow: hidden; }

        @media (max-width: 1000px) {
            .eval-modal-body-split { grid-template-columns: 1fr; }
            .eval-right-preview { display: none; }
            .eval-modal-container { max-width: 600px; height: auto; max-height: 95vh; }
        }
    </style>
</head>

<body>
    <!-- Premium Evaluation Modal -->
    <div id="reviewModal" class="eval-modal-overlay">
        <div class="eval-modal-container">
            <div class="eval-modal-header-pro">
                <div class="eval-student-info">
                    <h2 id="modalStudentName">Student Name</h2>
                    <p id="modalStudentCourse">BS Computer Science | Freshman Applicant</p>
                </div>
                <button class="eval-close-btn" onclick="closeReviewModal()">
                    <i class="fas fa-times fa-lg"></i>
                </button>
            </div>
            
            <div class="eval-modal-body-split">
                <!-- Data & Form Panel -->
                <div class="eval-left-form">
                    <div class="section-title-mod">
                        <i class="fas fa-folder-open"></i> DOCUMENTS REVIEW
                    </div>
                    <div id="modalDocList" style="margin-bottom: 35px;">
                        <!-- Injected by JS -->
                    </div>

                    <form id="evaluationForm" method="POST">
                        <input type="hidden" name="action" value="submit_evaluation">
                        <input type="hidden" name="application_id" id="modalAppId">
                        
                        <div class="section-title-mod">
                            <i class="fas fa-clipboard-check"></i> FINAL DECISION
                        </div>
                        
                        <div class="eval-input-group">
                            <label>Application Status</label>
                            <select name="status" id="modalEvalStatus" class="eval-select">
                                <option value="Approved">Approve & Generate Payment Link</option>
                                <option value="Processing">Keep for Further Review</option>
                                <option value="Rejected">Decline Application</option>
                            </select>
                        </div>

                        <div class="eval-input-group">
                            <label>Evaluation Notes (Internal)</label>
                            <textarea name="notes" placeholder="Specify reasons for approval or rejection..." class="eval-textarea"></textarea>
                        </div>
                    </form>
                </div>

                <!-- Preview Panel -->
                <div class="eval-right-preview">
                    <div id="previewPlaceholder">
                        <i class="fas fa-file-invoice"></i>
                        <h3 style="color: var(--primary-dark); margin-bottom: 10px; font-weight: 800;">Interactive Preview</h3>
                        <p>Select any document on the left to inspect credentials<br>directly within this workspace.</p>
                    </div>
                    <div id="previewContent" style="display: none;">
                        <!-- Injected by JS -->
                    </div>
                </div>
            </div>

            <div class="eval-modal-footer-pro">
                <button class="btn-cancel-eval" onclick="closeReviewModal()">Discard Changes</button>
                <button class="btn-save-eval" onclick="saveEvaluation()">Finalize Evaluation</button>
            </div>
        </div>
    </div>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="header-section">
                <div>
                    <h1>Evaluation Workspace</h1>
                    <p>Review and verify submitted student documents and credentials.</p>
                </div>
                <button class="btn-evaluate-action" onclick="toggleFilter()"><i class="fas fa-filter"></i> Refine Search</button>
            </div>

            <?php if ($message): ?>
                <div style="background: #f0fdf4; color: #15803d; padding: 20px; border-radius: 20px; margin-bottom: 30px; border: 1px solid #bbf7d0; font-weight: 600; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <i class="fas fa-check-circle fa-lg"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div style="background: #fef2f2; color: #b91c1c; padding: 20px; border-radius: 20px; margin-bottom: 30px; border: 1px solid #fecaca; font-weight: 600; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <i class="fas fa-exclamation-circle fa-lg"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #eff6ff; color: #1d4ed8;"><i class="fas fa-clock"></i></div>
                    <div>
                        <p style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Pending Review</p>
                        <h3 style="font-size: 1.5rem; font-weight: 800;"><?php echo $pending_count; ?></h3>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #f0fdf4; color: #10b981;"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <p style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Approved Today</p>
                        <h3 style="font-size: 1.5rem; font-weight: 800;"><?php echo $approved_today; ?></h3>
                    </div>
                </div>
            </div>

            <div class="evaluation-table-card">
                <div class="table-header">
                    <h3>Recent Submissions</h3>
                    <div style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem;"></i>
                        <input type="text" id="evalSearch" onkeyup="filterTable('evalSearch', 'evalTable')" placeholder="Search by name or email..." 
                            style="padding: 14px 20px 14px 45px; border-radius: 16px; border: 2px solid #f1f5f9; outline: none; width: 320px; font-size: 0.9rem; font-weight: 600; transition: 0.3s;"
                            onfocus="this.style.borderColor='#1648bc'">
                    </div>
                </div>
                <table id="evalTable">
                    <thead>
                        <tr>
                            <th>Student Information</th>
                            <th>Verification</th>
                            <th>Target Course</th>
                            <th>Documents</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($apps as $app): ?>
                            <tr class="eval-row" 
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
                                <td>
                                    <div style="font-weight: 800; color: var(--primary-dark);"><?php echo htmlspecialchars($app->first_name . ' ' . $app->last_name); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;"><?php echo htmlspecialchars($app->email); ?></div>
                                </td>
                                <td>
                                    <?php if ($app->is_verified): ?>
                                        <span class="badge badge-verified">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-unverified">
                                            <i class="fas fa-times-circle"></i> Unverified
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 700; color: #475569; font-size: 0.85rem;"><?php echo htmlspecialchars($app->course_display_name); ?></td>
                                <td>
                                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                        <?php 
                                        $docs = [
                                            'PSA' => $app->birth_cert,
                                            'F-138' => $app->form_138,
                                            'F-137' => $app->form_137,
                                            'Moral' => $app->good_moral,
                                            'Brgy' => $app->barangay_clearance,
                                            'ID' => $app->id_picture
                                        ];
                                        foreach ($docs as $label => $exists) {
                                            if ($exists) echo "<span style='padding: 2px 6px; background: #f1f5f9; border-radius: 6px; font-size: 0.65rem; font-weight: 800; color: #64748b; border: 1px solid #e2e8f0;'>$label</span>";
                                        }
                                        if (!array_filter($docs)) echo "<span style='color: #94a3b8; font-size: 0.8rem;'>No uploads</span>";
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?php echo ($app->status == 'Pending') ? 'badge-pending' : 'badge-processing'; ?>">
                                        <i class="fas <?php echo ($app->status == 'Pending') ? 'fa-hourglass-start' : 'fa-spinner fa-spin'; ?>" style="font-size: 0.7rem;"></i>
                                        <?php echo htmlspecialchars($app->status); ?>
                                    </span>
                                </td>
                                <td><button class="btn-evaluate-action">Review</button></td>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        function toggleFilter() {
            Swal.fire({
                title: 'Refine Evaluation List',
                text: 'Advanced filtering options are coming soon.',
                icon: 'info',
                confirmButtonColor: '#1648bc'
            });
        }

        function openReviewModal(id, name, course, psa, f138, f137, moral, brgy, idpic) {
            document.getElementById('modalAppId').value = id;
            document.getElementById('modalStudentName').textContent = name;
            document.getElementById('modalStudentCourse').textContent = course + ' | Official Applicant';
            
            const list = document.getElementById('modalDocList');
            list.innerHTML = '';
            
            const docs = [
                { name: 'PSA Birth Certificate', path: psa, icon: 'fa-id-card', color: '#eef2ff', text: '#1d4ed8' },
                { name: 'Report Card (F-138)', path: f138, icon: 'fa-file-pdf', color: '#fef2f2', text: '#b91c1c' },
                { name: 'Form 137 (TOR)', path: f137, icon: 'fa-scroll', color: '#f0fdf4', text: '#15803d' },
                { name: 'Good Moral Cert.', path: moral, icon: 'fa-certificate', color: '#fff7ed', text: '#c2410c' },
                { name: 'Brgy Clearance', path: brgy, icon: 'fa-map-marker-alt', color: '#f0f9ff', text: '#0369a1' },
                { name: 'Passport Size ID', path: idpic, icon: 'fa-user-circle', color: '#f5f3ff', text: '#6d28d9' }
            ];

            let docFound = false;
            docs.forEach(doc => {
                if (doc.path) {
                    docFound = true;
                    const item = document.createElement('div');
                    item.className = 'doc-item-pro';
                    const fullPath = '../../' + doc.path;
                    item.innerHTML = `
                        <div class="doc-icon-box" style="background: ${doc.color}; color: ${doc.text};">
                            <i class="fas ${doc.icon}"></i>
                        </div>
                        <div style="flex: 1;">
                            <p style="font-weight: 700; font-size: 0.85rem; color: #1e293b; margin-bottom: 2px;">${doc.name}</p>
                            <p style="font-size: 0.7rem; color: var(--text-muted); font-weight: 500;">Review Credentials</p>
                        </div>
                        <i class="fas fa-chevron-right" style="font-size: 0.7rem; color: #cbd5e1;"></i>
                    `;
                    item.onclick = function() { previewDoc(fullPath, this); };
                    list.appendChild(item);
                }
            });

            if (!docFound) {
                list.innerHTML = `
                    <div style="background: #f8fafc; border-radius: 20px; padding: 30px; text-align: center; border: 2px dashed #e2e8f0;">
                        <i class="fas fa-file-excel fa-2x" style="color: #94a3b8; margin-bottom: 15px;"></i>
                        <p style="color: #64748b; font-weight: 600; font-size: 0.9rem;">No documents found for this applicant.</p>
                    </div>`;
            }

            document.getElementById('reviewModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Reset Preview
            document.getElementById('previewContent').style.display = 'none';
            document.getElementById('previewPlaceholder').style.display = 'flex';
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function previewDoc(path, element) {
            document.querySelectorAll('.doc-item-pro').forEach(i => i.classList.remove('active'));
            element.classList.add('active');

            const placeholder = document.getElementById('previewPlaceholder');
            const container = document.getElementById('previewContent');
            
            placeholder.style.display = 'none';
            container.style.display = 'block';
            container.innerHTML = `
                <div style="height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; background: #eaeff5;">
                    <i class="fas fa-circle-notch fa-spin fa-3x" style="color: var(--primary-blue); margin-bottom: 20px;"></i>
                    <p style="font-weight: 700; color: var(--primary-dark);">Loading Document Content...</p>
                </div>`;
            
            const ext = path.split('.').pop().toLowerCase();
            
            setTimeout(() => {
                if(['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                    container.innerHTML = `<img src="${path}" style="width:100%; height:100%; object-fit:contain; padding: 20px; animation: fadeIn 0.5s ease;">`;
                } else if(ext === 'pdf') {
                    container.innerHTML = `<iframe src="${path}" style="width:100%; height:100%; border:none;"></iframe>`;
                } else {
                    container.innerHTML = `
                        <div style="padding:100px 40px; text-align:center;">
                            <i class="fas fa-file-alt fa-4x" style="color: #cbd5e1; margin-bottom: 25px;"></i>
                            <h3 style="margin-bottom: 10px;">Preview Unavailable</h3>
                            <p style="color: var(--text-muted); margin-bottom: 20px;">This file type cannot be rendered directly.</p>
                            <a href="${path}" target="_blank" class="btn-evaluate-action" style="text-decoration:none; display:inline-block;">Download File Instead</a>
                        </div>`;
                }
            }, 600);
        }

        function saveEvaluation() {
            const status = document.getElementById('modalEvalStatus').value;
            let title = 'Finalize Review?';
            let text = 'Save current evaluation status?';
            let icon = 'question';

            if (status === 'Approved') {
                title = 'Approve Application?';
                text = 'This will officially Enroll the student, generate fees, and send payment instructions via email.';
                icon = 'success';
            } else if (status === 'Rejected') {
                title = 'Decline Applicant?';
                text = 'Are you sure you want to reject this application? This action is permanent.';
                icon = 'warning';
            }

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: status === 'Approved' ? '#10b981' : '#1648bc',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Yes, Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Updating records and sending notifications...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    document.getElementById('evaluationForm').submit();
                }
            });
        }

        window.onclick = function (event) {
            const modal = document.getElementById('reviewModal');
            if (event.target == modal) closeReviewModal();
        }
    </script>
</body>
</html>