<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'cashier' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: ../../auth/Login.php");
    exit();
}
require_once '../../Database/config.php';

$search = trim($_GET['search'] ?? '');
$results = [];
$message = '';
$error = '';

// Handle Payment Posting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'post_payment') {
    try {
        $enrollment_id = $_POST['enrollment_id'];
        $amount = floatval($_POST['amount']);
        $method = $_POST['method'];
        $ref = $_POST['reference'];

        $pdo->beginTransaction();

        // 1. Insert into payments table
        $stmt = $pdo->prepare("INSERT INTO payments (enrollment_id, amount, payment_method, transaction_id, status) VALUES (?, ?, ?, ?, 'Completed')");
        $stmt->execute([$enrollment_id, $amount, $method, $ref ?: 'WALKIN-'.time()]);

        // 2. Update balance and status in enrollments table
        $stmt = $pdo->prepare("UPDATE enrollments SET balance = balance - ?, status = 'Validation' WHERE enrollmentId = ?");
        $stmt->execute([$amount, $enrollment_id]);

        $pdo->commit();
        $message = "Payment of ₱" . number_format($amount, 2) . " successfully posted!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Payment error: " . $e->getMessage();
    }
}

// Search Logic
if ($search) {
    try {
        $stmt = $pdo->prepare("SELECT s.first_name, s.last_name, s.student_id, e.enrollmentId as enrollment_id, e.balance 
                               FROM students s 
                               JOIN enrollments e ON s.email = e.email 
                               WHERE (s.student_id LIKE ? OR s.last_name LIKE ? OR s.first_name LIKE ?)
                               AND e.status IN ('Pending Payment', 'Pending Walk-in', 'Validation', 'Enrolled')");
        $term = "%$search%";
        $stmt->execute([$term, $term, $term]);
        $results = $stmt->fetchAll();
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
    <title>Walk-in Payments - Cashier</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #1648bc; --bg: #f8fafc; --text-dark: #1e293b; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg); display: flex; min-height: 100vh; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        .content-area { padding: 40px; }
        .search-box { background: white; padding: 25px; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin-bottom: 30px; display: flex; gap: 15px; }
        input[type="text"], input[type="number"], select { flex: 1; padding: 12px 20px; border-radius: 12px; border: 1px solid #e2e8f0; outline: none; }
        .btn-search { background: var(--primary); color: white; border: none; padding: 0 25px; border-radius: 12px; font-weight: 600; cursor: pointer; }
        .results-card { background: white; border-radius: 24px; padding: 30px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); }
        .btn-pay { background: var(--primary); color: white; border: none; padding: 8px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; }
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); }
        .modal-content { background: white; margin: 10vh auto; width: 450px; border-radius: 24px; overflow: hidden; animation: modalSlide 0.3s ease-out; }
        @keyframes modalSlide { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .modal-header { padding: 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; }
        .modal-body { padding: 25px; }
        .modal-footer { padding: 20px; background: #f8fafc; display: flex; gap: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.85rem; color: #64748b; margin-bottom: 5px; font-weight: 500; }
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 25px; color: var(--text-dark);">Walk-in Payments</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form class="search-box" method="GET">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by Student ID or Name...">
                <button type="submit" class="btn-search">Search Student</button>
            </form>

            <div class="results-card">
                <?php if ($results): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">
                            <th style="padding: 15px; border-bottom: 2px solid #f1f5f9;">Student Info</th>
                            <th style="padding: 15px; border-bottom: 2px solid #f1f5f9;">Remaining Balance</th>
                            <th style="padding: 15px; border-bottom: 2px solid #f1f5f9;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $s): ?>
                        <tr>
                            <td style="padding: 20px 15px; border-bottom: 1px solid #f1f5f9;">
                                <p style="font-weight: 700;"><?php echo htmlspecialchars($s->first_name . ' ' . $s->last_name); ?></p>
                                <p style="font-size: 0.8rem; color: #64748b;">ID: <?php echo htmlspecialchars($s->student_id); ?></p>
                            </td>
                            <td style="padding: 20px 15px; border-bottom: 1px solid #f1f5f9; font-weight: 800; color: #ef4444;">
                                ₱<?php echo number_format($s->balance, 2); ?>
                            </td>
                            <td style="padding: 20px 15px; border-bottom: 1px solid #f1f5f9;">
                                <button class="btn-pay" onclick="openPaymentModal('<?php echo addslashes($s->first_name . ' ' . $s->last_name); ?>', <?php echo $s->balance; ?>, <?php echo $s->enrollment_id; ?>)">Process Payment</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <p style="text-align: center; color: #64748b; padding: 20px;">No students found. Please try a different search term.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Payment Processing Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="post_payment">
                <input type="hidden" name="enrollment_id" id="modal_eid">
                
                <div class="modal-header">
                    <h2 style="font-weight: 800;">Process Payment</h2>
                    <i class="fas fa-times" style="cursor: pointer; color: #64748b;" onclick="closeModal()"></i>
                </div>
                <div class="modal-body">
                    <p style="margin-bottom: 20px; font-size: 0.9rem; color: #64748b;">Processing payment for <strong id="studentName" style="color: var(--text-dark);"></strong></p>
                    <div class="form-group">
                        <label>Amount to Pay (₱)</label>
                        <input type="number" name="amount" step="0.01" id="modal_amount" required>
                        <p style="font-size: 0.7rem; color: #64748b; margin-top: 5px;">Current Balance: <span id="modal_balance"></span></p>
                    </div>
                    <div class="form-group">
                        <label>Payment Mode</label>
                        <select name="method">
                            <option value="Cash">Cash</option>
                            <option value="Check">Check</option>
                            <option value="Card">Credit/Debit Card</option>
                            <option value="Bank">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Reference # (Optional)</label>
                        <input type="text" name="reference" placeholder="e.g. OR # or Check #">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" style="flex: 1; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; font-weight: 600; cursor: pointer;" onclick="closeModal()">Cancel</button>
                    <button type="submit" style="flex: 1; padding: 12px; border-radius: 12px; background: #10b981; color: white; border: none; font-weight: 700; cursor: pointer;">Post Payment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPaymentModal(name, balance, eid) {
            document.getElementById('studentName').textContent = name;
            document.getElementById('modal_balance').textContent = '₱' + balance.toLocaleString();
            document.getElementById('modal_amount').value = balance;
            document.getElementById('modal_eid').value = eid;
            document.getElementById('paymentModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            document.getElementById('paymentModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    </script>
</body>
</html>