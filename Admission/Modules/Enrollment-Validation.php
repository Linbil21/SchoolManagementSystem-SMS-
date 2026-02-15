<?php
session_start();
require_once '../../Database/config.php';
require_once '../../auth/Security.php';
checkRole(['admission', 'superadmin']);

$message = '';
$error = '';

// Handle Official Enrollment Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'enroll_student') {
    try {
        $enrollment_id = $_POST['enrollment_id'];
        
        $pdo->beginTransaction();

        // 1. Get student info
        $stmt = $pdo->prepare("SELECT id, email, first_name, last_name, student_id FROM students WHERE email = (SELECT email FROM enrollments WHERE enrollmentId = ?)");
        $stmt->execute([$enrollment_id]);
        $student = $stmt->fetch();

        if (!$student) throw new Exception("Student record not found.");

        // 2. Update Enrollment Status to 'Enrolled'
        $stmt = $pdo->prepare("UPDATE enrollments SET status = 'Enrolled' WHERE enrollmentId = ?");
        $stmt->execute([$enrollment_id]);

        // 3. Update Student Record and Generate ID if needed
        $year = date('Y');
        $official_id = $student->student_id;
        
        if (strpos($official_id, 'ENR') === 0 || empty($official_id) || $official_id === 'PENDING') {
            $stmt = $pdo->query("SELECT MAX(id) as last_id FROM students");
            $last_id = $stmt->fetch()->last_id ?? 0;
            $new_id_num = str_pad($last_id + 1, 4, '0', STR_PAD_LEFT);
            $official_id = "$year-$new_id_num";

            $stmt = $pdo->prepare("UPDATE students SET student_id = ? WHERE id = ?");
            $stmt->execute([$official_id, $student->id]);
        }

        // 4. Notify Student
        $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, icon, icon_bg, icon_color, link) VALUES (?, 'enrollment_success', 'Enrollment Complete!', 'Congratulations! You are now officially enrolled.', 'fa-graduation-cap', '#dcfce7', '#16a34a', '/student/Dashboard.php')");
        $notif_stmt->execute([$student->id]);

        $pdo->commit();
        $message = "Student " . $student->first_name . " " . $student->last_name . " has been officially enrolled with ID: " . $official_id;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Enrollment error: " . $e->getMessage();
    }
}

// Fetch students pending final validation (Status: Validation)
$sql = "SELECT e.*, s.student_id as current_id, (SELECT amount FROM payments WHERE enrollment_id = e.enrollmentId ORDER BY created_at DESC LIMIT 1) as last_payment 
        FROM enrollments e 
        LEFT JOIN students s ON e.email = s.email 
        WHERE e.status = 'Validation' 
        ORDER BY e.created_at DESC";
$validation_list = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Enrollment Validation - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #1648bc; --bg: #f8fafc; --text-dark: #1e293b; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg); display: flex; min-height: 100vh; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; width: 100%; }
        .content-area { padding: 40px; }
        .page-header { margin-bottom: 30px; }
        .page-title { font-size: 1.8rem; font-weight: 800; color: var(--text-dark); }
        .card { background: white; border-radius: 24px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase; border-bottom: 2px solid #f1f5f9; }
        td { padding: 20px 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .btn-enroll { background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-enroll:hover { background: #059669; transform: translateY(-2px); }
        .alert { padding: 15px; border-radius: 12px; margin-bottom: 25px; font-size: 0.95rem; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .payment-info { font-weight: 700; color: #16a34a; }
    </style>
</head>
<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="contentArea">
            <div class="content-area">
                <div class="page-header">
                    <h1 class="page-title">Final Enrollment Validation</h1>
                    <p style="color: #64748b;">Verify payments and documents for final enrollment confirmation.</p>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card">
                    <?php if ($validation_list): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Course & Level</th>
                                    <th>Financial Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($validation_list as $row): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700; color: #1e293b;"><?php echo htmlspecialchars($row->first_name . ' ' . $row->last_name); ?></div>
                                            <div style="font-size: 0.8rem; color: #64748b;"><?php echo htmlspecialchars($row->email); ?></div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;"><?php echo htmlspecialchars($row->year_level); ?></div>
                                            <div style="font-size: 0.8rem; color: #64748b;">Applicant ID: <?php echo htmlspecialchars($row->reference_code); ?></div>
                                        </td>
                                        <td>
                                            <div>Total: ₱<?php echo number_format($row->total_fee, 2); ?></div>
                                            <div class="payment-info">Paid: ₱<?php echo number_format($row->total_fee - $row->balance, 2); ?></div>
                                            <div style="font-size: 0.75rem; color: #64748b;">Rem. Balance: ₱<?php echo number_format($row->balance, 2); ?></div>
                                        </td>
                                        <td>
                                            <form method="POST" onsubmit="return confirm('Confirm official enrollment for this student?');">
                                                <input type="hidden" name="action" value="enroll_student">
                                                <input type="hidden" name="enrollment_id" value="<?php echo $row->enrollmentId; ?>">
                                                <button type="submit" class="btn-enroll">
                                                    <i class="fas fa-user-check" style="margin-right: 8px;"></i>Confirm Enrollment
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px; color: #64748b;">
                            <i class="fas fa-clipboard-check" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                            <p>No students currently waiting for final validation.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
