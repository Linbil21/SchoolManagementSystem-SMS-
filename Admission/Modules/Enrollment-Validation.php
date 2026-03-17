<?php
/**
 * Enrollment Validation Module
 */
require_once __DIR__ . '/../../auth/Security.php';
require_once __DIR__ . '/../../Database/config.php';
checkRole(['admission', 'admin', 'superadmin']);

$message = '';
$error = '';

// Handle Official Enrollment Action (Now handles POST via AJAX for better UX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'enroll_student') {
    header('Content-Type: application/json');
    try {
        $enrollment_id = $_POST['enrollment_id'];
        
        $pdo->beginTransaction();

        // 1. Get student & enrollment info
        $stmt = $pdo->prepare("SELECT s.id, s.email, s.first_name, s.last_name, s.student_id 
                               FROM students s 
                               JOIN enrollments e ON s.email = e.email 
                               WHERE e.enrollmentId = ?");
        $stmt->execute([$enrollment_id]);
        $student = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$student) throw new Exception("Student record not found linked to this enrollment.");

        // 2. Update Enrollment Status to 'Enrolled'
        $stmt = $pdo->prepare("UPDATE enrollments SET status = 'Enrolled' WHERE enrollmentId = ?");
        $stmt->execute([$enrollment_id]);

        // 3. Update Student Record and Generate ID if needed
        $year = date('Y');
        $official_id = $student->student_id;
        
        if (strpos($official_id, 'ENR') === 0 || empty($official_id) || $official_id === 'PENDING') {
            $stmt = $pdo->query("SELECT MAX(id) as last_id FROM students");
            $last_res = $stmt->fetch(PDO::FETCH_OBJ);
            $last_id = $last_res->last_id ?? 0;
            $new_id_num = str_pad($last_id + 1, 4, '0', STR_PAD_LEFT);
            $official_id = "$year-$new_id_num";

            $stmt = $pdo->prepare("UPDATE students SET student_id = ? WHERE id = ?");
            $stmt->execute([$official_id, $student->id]);
        }

        // 4. Notify Student
        $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, icon, icon_bg, icon_color, link) VALUES (?, 'enrollment_success', 'Enrollment Complete!', 'Congratulations! You are now officially enrolled.', 'fa-graduation-cap', '#dcfce7', '#16a34a', '/student/Dashboard.php')");
        $notif_stmt->execute([$student->id]);

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => "Student {$student->first_name} {$student->last_name} enrolled successfully with ID: {$official_id}"]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Fetch students pending final validation (Status: Validation) who have made at least one payment
try {
    $sql = "SELECT e.*, s.student_id as current_id, s.is_verified, 
            (SELECT SUM(amount) FROM payments WHERE enrollment_id = e.enrollmentId AND status IN ('Completed', 'Verified')) as total_paid,
            (SELECT COUNT(*) FROM payments WHERE enrollment_id = e.enrollmentId AND status IN ('Completed', 'Verified')) as payment_count
            FROM enrollments e 
            LEFT JOIN students s ON e.email = s.email 
            WHERE e.status = 'Validation' 
            HAVING payment_count > 0
            ORDER BY e.created_at DESC";
    $validation_list = $pdo->query($sql)->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    $validation_list = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Validation - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1648bc;
            --primary-dark: #0f172a;
            --bg-light: #f8fafc;
            --surface: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            --success: #10b981;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; color: var(--text-main); }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; width: 100%; }
        .content-area { padding: 40px; max-width: 1400px; margin: 0 auto; width: 100%; }

        .header-section { margin-bottom: 35px; }
        .header-section h1 { font-size: 2.2rem; font-weight: 800; color: var(--primary-dark); letter-spacing: -0.02em; }
        .header-section p { color: var(--text-muted); font-size: 1rem; }

        .table-card {
            background: var(--surface);
            border-radius: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .table-header {
            padding: 30px 40px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h3 { font-weight: 800; color: var(--primary-dark); font-size: 1.2rem; }

        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left; padding: 20px 40px; background: #f8fafc;
            color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;
            letter-spacing: 0.05em; font-weight: 700;
        }
        td { padding: 25px 40px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; vertical-align: middle; }

        .student-meta { display: flex; flex-direction: column; }
        .student-name { font-weight: 700; color: var(--primary-dark); }
        .student-sub { font-size: 0.8rem; color: var(--text-muted); }

        .badge {
            padding: 6px 14px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .badge-verified { background: #ecfdf5; color: #10b981; }
        .badge-unverified { background: #fff1f2; color: #ef4444; }

        .financial-row { display: flex; flex-direction: column; gap: 4px; }
        .total-amount { font-weight: 500; color: var(--text-muted); font-size: 0.8rem; }
        .paid-amount { font-weight: 700; color: var(--success); font-size: 1rem; }
        .balance-amount { font-weight: 600; color: #f59e0b; font-size: 0.8rem; }

        .btn-confirm {
            background: var(--primary); color: white; border: none; padding: 12px 24px;
            border-radius: 14px; font-weight: 700; cursor: pointer; transition: 0.3s;
            display: flex; align-items: center; gap: 10px; font-size: 0.85rem;
            box-shadow: 0 4px 12px rgba(22, 72, 188, 0.2);
        }
        .btn-confirm:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(22, 72, 188, 0.3); background: #1a3a8a; }

        .empty-state { padding: 100px 40px; text-align: center; color: var(--text-muted); }
        .empty-state i { font-size: 4.5rem; opacity: 0.15; margin-bottom: 25px; color: var(--primary); }
    </style>
</head>
<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="header-section">
                <h1>Enrollment Validation</h1>
                <p>Final review stage. Confirm official enrollment after verifying payments and credentials.</p>
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h3>Waiting for Validation (<?php echo count($validation_list); ?>)</h3>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Student Information</th>
                            <th>Identity Verification</th>
                            <th>Enrollment Type</th>
                            <th>Financial Settlement</th>
                            <th style="text-align: right;">Final Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($validation_list)): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-clipboard-check"></i>
                                        <h3>Cleanup Complete</h3>
                                        <p>No students are currently in the validation queue.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($validation_list as $row): ?>
                                <tr>
                                    <td>
                                        <div class="student-meta">
                                            <span class="student-name"><?php echo htmlspecialchars($row->first_name . ' ' . $row->last_name); ?></span>
                                            <span class="student-sub"><?php echo htmlspecialchars($row->email); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($row->is_verified): ?>
                                            <span class="badge badge-verified"><i class="fas fa-check-circle"></i> Identity Verified</span>
                                        <?php else: ?>
                                            <span class="badge badge-unverified"><i class="fas fa-exclamation-triangle"></i> Needs ID Check</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--primary-dark);"><?php echo htmlspecialchars($row->year_level); ?></div>
                                        <div class="student-sub">Ref: <?php echo htmlspecialchars($row->reference_code); ?></div>
                                    </td>
                                    <td>
                                        <div class="financial-row">
                                            <span class="total-amount">Total Fee: ₱<?php echo number_format($row->total_fee, 2); ?></span>
                                            <span class="paid-amount">Settled: ₱<?php echo number_format($row->total_fee - $row->balance, 2); ?></span>
                                            <?php if ($row->balance > 0): ?>
                                                <span class="balance-amount" style="color: #ef4444; font-weight: 800;">
                                                    <i class="fas fa-exclamation-triangle"></i> Unpaid: ₱<?php echo number_format($row->balance, 2); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-verified" style="margin-top: 5px;">
                                                    <i class="fas fa-check-double"></i> Fully Settled
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        <?php 
                                        $is_admin = isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'superadmin']);
                                        if ($row->balance <= 0 || $is_admin): 
                                        ?>
                                            <button class="btn-confirm" onclick="confirmEnrollment('<?php echo $row->enrollmentId; ?>', '<?php echo addslashes($row->first_name . ' ' . $row->last_name); ?>')">
                                                <i class="fas fa-user-check"></i> Validate Enrollment
                                                <?php if ($row->balance > 0 && $is_admin): ?>
                                                    <span style="font-size: 0.7rem; opacity: 0.8;">(Admin Bypass)</span>
                                                <?php endif; ?>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn-confirm" style="background: #cbd5e1; cursor: not-allowed; box-shadow: none;" disabled title="Student has pending balance">
                                                <i class="fas fa-lock"></i> Payment Pending
                                            </button>
                                        <?php endif; ?>
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
        async function confirmEnrollment(id, name) {
            const { isConfirmed } = await Swal.fire({
                title: 'Confirm Enrollment?',
                text: `You are about to officially enroll ${name} and generate their digital ID.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1648bc',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Enroll Student'
            });

            if (isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we finalize the enrollment.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                const formData = new FormData();
                formData.append('action', 'enroll_student');
                formData.append('enrollment_id', id);

                try {
                    const response = await fetch('Enrollment-Validation.php', {
                        method: 'POST',
                        body: formData
                    });
                    const res = await response.json();

                    if (res.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: res.message,
                            icon: 'success',
                            confirmButtonColor: '#1648bc'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Server communication failure.', 'error');
                }
            }
        }
    </script>
</body>
</html>
