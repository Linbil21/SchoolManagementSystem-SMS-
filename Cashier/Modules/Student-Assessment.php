<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'cashier' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: ../../auth/Login.php");
    exit();
}
require_once '../../Database/config.php';

// Lazy Migration / Column Check
try {
    $pdo->exec("ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS tuition_fee DECIMAL(10,2) DEFAULT 0.00");
    $pdo->exec("ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS misc_fee DECIMAL(10,2) DEFAULT 0.00");
    $pdo->exec("ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS lab_fee DECIMAL(10,2) DEFAULT 0.00");
    $pdo->exec("ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS total_fee DECIMAL(10,2) DEFAULT 0.00");
    $pdo->exec("ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS balance DECIMAL(10,2) DEFAULT 0.00");
} catch (PDOException $e) { /* Already exists */ }

$student = null;
$enrollment = null;
$message = '';
$error = '';

// Handle POST Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_balance') {
    try {
        $enrollment_id = $_POST['enrollment_id'];
        $tuition = floatval($_POST['tuition']);
        $misc = floatval($_POST['misc']);
        $lab = floatval($_POST['lab']);
        $total_fee = $tuition + $misc + $lab;
        
        // If they explicitly edited the balance, use it, otherwise calculate from total
        // In a real system, balance is often (Total - Payments).
        // For simplicity here, we allow manual balance override but suggest the calculation.
        $new_balance = floatval($_POST['new_balance']);
        $student_id_val = $_POST['student_id'];

        $sql = "UPDATE enrollments SET 
                tuition_fee = ?, 
                misc_fee = ?, 
                lab_fee = ?, 
                total_fee = ?, 
                balance = ? 
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tuition, $misc, $lab, $total_fee, $new_balance, $enrollment_id]);
        
        $message = "Assessment updated successfully for Student ID: " . htmlspecialchars($student_id_val);
        // Persist search
        $_GET['search'] = $student_id_val;
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Handle Search
$search = trim($_GET['search'] ?? '');
if ($search) {
    try {
        $sql = "SELECT s.*, e.id as enrollment_id, e.reference_code, e.course_id, e.year_level as enr_year, e.status as enr_status, 
                e.tuition_fee, e.misc_fee, e.lab_fee, e.balance, e.total_fee, e.created_at as assessment_date 
                FROM students s 
                LEFT JOIN enrollments e ON s.email = e.email 
                WHERE s.student_id LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ? 
                ORDER BY e.created_at DESC LIMIT 1";
        
        $term = "%$search%";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$term, $term, $term]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $student = $data;
            if (!$student['enrollment_id']) {
                $error = "Student found but no enrollment record exists.";
            }
        } else {
            $error = "Student not found.";
        }
    } catch (PDOException $e) {
        $error = "Search error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Assessment - Cashier</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #1648bc; --bg: #f8fafc; --text-dark: #1e293b; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg); display: flex; min-height: 100vh; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        .content-area { padding: 40px; }
        .card { background: white; border-radius: 24px; padding: 30px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); }
        .assessment-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #f1f5f9; }
        .fee-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed #e2e8f0; }
        .fee-item:last-child { border-bottom: 2px solid #1648bc; margin-bottom: 15px; }
        .total-row { display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 800; color: #1648bc; }
        .btn-action { padding: 12px 25px; border-radius: 12px; font-weight: 700; cursor: pointer; border: none; transition: 0.3s; }
        .btn-print { background: #eef2ff; color: var(--primary); }
        .btn-assess { background: var(--primary); color: white; margin-left: 10px; }
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); }
        .modal-content { background: white; margin: 10vh auto; width: 500px; border-radius: 24px; overflow: hidden; animation: modalSlide 0.3s ease-out; }
        @keyframes modalSlide { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { padding: 25px; border-bottom: 1px solid #f1f5f9; }
        .modal-body { padding: 25px; }
        .modal-footer { padding: 20px; background: #f8fafc; display: flex; justify-content: flex-end; gap: 10px; }
        
        .search-container { margin-bottom: 30px; display: flex; gap: 10px; }
        .search-input { flex: 1; padding: 15px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 1rem; outline: none; }
        .search-btn { padding: 0 25px; background: var(--primary); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; }
        
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 25px;">Student Assessment Console</h1>

            <div class="search-container">
                <form action="" method="GET" style="display: flex; width: 100%; gap: 10px;">
                    <input type="text" name="search" class="search-input" placeholder="Enter Student ID or Name..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="search-btn"><i class="fas fa-search"></i> Search</button>
                </form>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($student && isset($student['enrollment_id'])): ?>
            <div class="card">
                <div class="assessment-header">
                    <div>
                        <h2 style="color: var(--primary);"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>
                        <p style="color: #64748b;">ID: <?php echo htmlspecialchars($student['student_id']); ?> | Course: <?php echo htmlspecialchars($student['course']); ?></p>
                        <p style="color: #64748b;">Registration Status: <span style="color: #10b981; font-weight: 600;"><?php echo htmlspecialchars($student['enr_status'] ?? 'Active'); ?></span></p>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 0.8rem; color: #64748b;">Assessment Date</p>
                        <p style="font-weight: 600;"><?php echo date('M d, Y', strtotime($student['assessment_date'])); ?></p>
                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <h3 style="margin-bottom: 15px; font-size: 1rem; color: #64748b;">CURRENT BALANCE DETAILS</h3>
                    
                    <div class="fee-item">
                        <span>Tuition Fee</span>
                        <span style="font-weight: 600;">₱<?php echo number_format($student['tuition_fee'] ?? 0, 2); ?></span>
                    </div>
                    <div class="fee-item">
                        <span>Miscellaneous Fees</span>
                        <span style="font-weight: 600;">₱<?php echo number_format($student['misc_fee'] ?? 0, 2); ?></span>
                    </div>
                    <div class="fee-item">
                        <span>Laboratory Fees</span>
                        <span style="font-weight: 600;">₱<?php echo number_format($student['lab_fee'] ?? 0, 2); ?></span>
                    </div>
                    
                    <div class="fee-item" style="border-top: 1px solid #1648bc; margin-top: 5px;">
                        <span style="font-weight: 700;">TOTAL ASSESSMENT</span>
                        <span style="font-weight: 700; color: var(--primary);">₱<?php echo number_format($student['total_fee'], 2); ?></span>
                    </div>

                    <div class="total-row" style="margin-top: 20px;">
                        <span>OUTSTANDING BALANCE</span>
                        <span style="font-size: 1.5rem;">₱<?php echo number_format($student['balance'], 2); ?></span>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button class="btn-action btn-print"><i class="fas fa-print"></i> Print Statement</button>
                    <button class="btn-action btn-assess" onclick="openAdjustmentModal()">
                        <i class="fas fa-edit"></i> Update Fees
                    </button>
                </div>
            </div>

            <!-- Edit Balance Modal -->
            <div id="adjModal" class="modal">
                <div class="modal-content">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_balance">
                        <input type="hidden" name="enrollment_id" value="<?php echo $student['enrollment_id']; ?>">
                        <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                        
                        <div class="modal-header">
                            <h2 style="font-weight: 800;">Update Assessment Fees</h2>
                            <p style="font-size: 0.85rem; color: #64748b;">Set specific fee amounts for the student.</p>
                        </div>
                        <div class="modal-body">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                <div>
                                    <label style="display: block; font-size: 0.85rem; color: #64748b; margin-bottom: 5px;">Tuition Fee (₱)</label>
                                    <input type="number" step="0.01" name="tuition" id="f_tuition" value="<?php echo $student['tuition_fee'] ?? 0; ?>" oninput="calcTotal()"
                                        style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; font-weight: 600;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.85rem; color: #64748b; margin-bottom: 5px;">Miscellaneous (₱)</label>
                                    <input type="number" step="0.01" name="misc" id="f_misc" value="<?php echo $student['misc_fee'] ?? 0; ?>" oninput="calcTotal()"
                                        style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; font-weight: 600;">
                                </div>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; font-size: 0.85rem; color: #64748b; margin-bottom: 5px;">Laboratory Fees (₱)</label>
                                <input type="number" step="0.01" name="lab" id="f_lab" value="<?php echo $student['lab_fee'] ?? 0; ?>" oninput="calcTotal()"
                                    style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; font-weight: 600;">
                            </div>
                            
                            <div style="background: #eef2ff; padding: 15px; border-radius: 15px; margin-bottom: 20px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span style="font-size: 0.9rem; font-weight: 600;">Calculated Total Fee:</span>
                                    <span style="font-weight: 800; color: #1648bc;">₱<span id="total_display"><?php echo number_format($student['total_fee'], 2); ?></span></span>
                                </div>
                                <p style="font-size: 0.75rem; color: #64748b;">(Total = Tuition + Misc + Lab)</p>
                            </div>

                            <div>
                                <label style="display: block; font-size: 0.85rem; color: #64748b; margin-bottom: 5px;">Outstanding Balance (₱)</label>
                                <input type="number" step="0.01" name="new_balance" value="<?php echo $student['balance']; ?>"
                                    style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #1648bc;">
                                <p style="font-size: 0.75rem; color: #64748b; margin-top: 5px;">* Balance should be adjusted manually if payments were made.</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" 
                                style="padding: 12px 24px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; font-weight: 600; cursor: pointer;"
                                onclick="closeModal()">Cancel</button>
                            <button type="submit"
                                style="padding: 12px 24px; border-radius: 12px; background: var(--primary); color: white; border: none; font-weight: 700; cursor: pointer;">
                                Save Assessment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php elseif($search && !$student): ?>
               <!-- Error already shown above -->
            <?php else: ?>
                <div style="text-align: center; padding: 50px; color: #64748b;">
                    <i class="fas fa-search-dollar" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
                    <p>Enter a Student ID or Name to view and manage assessment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function openAdjustmentModal() {
            document.getElementById('adjModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
            calcTotal();
        }
        function closeModal() {
            document.getElementById('adjModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        function calcTotal() {
            const t = parseFloat(document.getElementById('f_tuition').value) || 0;
            const m = parseFloat(document.getElementById('f_misc').value) || 0;
            const l = parseFloat(document.getElementById('f_lab').value) || 0;
            const total = t + m + l;
            document.getElementById('total_display').innerText = total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    </script>
</body>
</html>