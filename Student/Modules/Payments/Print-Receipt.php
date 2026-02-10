<?php
session_start();
require_once '../../../Database/config.php';

if (!isset($_GET['id'])) {
    die("Transaction ID missing.");
}

$transaction_id = $_GET['id'];
$student_email = $_SESSION['email'] ?? '';

try {
    $stmt = $pdo->prepare("
        SELECT p.*, e.first_name, e.last_name, e.year_level,
               IFNULL(s.student_id, e.reference_code) as student_id,
               c.course_name as course
        FROM payments p 
        JOIN enrollments e ON p.enrollment_id = e.enrollmentId 
        LEFT JOIN students s ON e.email = s.email
        LEFT JOIN courses c ON e.course_id = c.courseId
        WHERE p.transaction_id = ? AND e.email = ?
    ");
    $stmt->execute([$transaction_id, $student_email]);
    $payment = $stmt->fetch();

    if (!$payment) {
        die("Transaction not found or unauthorized access.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - <?php echo $transaction_id; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f1f5f9; padding: 40px; display: flex; justify-content: center; }
        
        .receipt-card {
            background: white;
            width: 100%;
            max-width: 600px;
            padding: 50px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            position: relative;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px dashed #e2e8f0;
            padding-bottom: 30px;
        }

        .school-logo { width: 80px; margin-bottom: 15px; }
        .school-name { font-size: 1.5rem; font-weight: 800; color: #1648bc; margin-bottom: 5px; }
        .receipt-title { font-size: 0.9rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 2px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px; }
        .info-item { display: flex; flex-direction: column; gap: 5px; }
        .label { font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase; }
        .value { font-size: 1rem; color: #1e293b; font-weight: 600; }

        .payment-summary {
            background: #f8fafc;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 40px;
        }

        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .summary-row:last-child { margin-bottom: 0; padding-top: 15px; border-top: 1px solid #e2e8f0; }

        .total-label { font-size: 1.1rem; font-weight: 800; color: #1e293b; }
        .total-amount { font-size: 1.4rem; font-weight: 800; color: #1648bc; }

        .status-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 5rem;
            font-weight: 900;
            opacity: 0.05;
            pointer-events: none;
            text-transform: uppercase;
        }

        .footer { text-align: center; color: #94a3b8; font-size: 0.8rem; margin-top: 40px; }

        @media print {
            body { background: white; padding: 0; }
            .receipt-card { box-shadow: none; max-width: 100%; width: 100%; border: none; }
            .no-print { display: none; }
        }

        .btn-print {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #1648bc;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 30px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(22, 72, 188, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>

    <div class="receipt-card">
        <div class="status-watermark"><?php echo $payment->status; ?></div>
        
        <div class="receipt-header">
            <img src="/sms/Assets/image/logo.png" alt="Logo" class="school-logo">
            <div class="school-name">SMS ACADEMY</div>
            <div class="receipt-title">Official Electronic Receipt</div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <span class="label">Date Issued</span>
                <span class="value"><?php echo date('M d, Y h:i A'); ?></span>
            </div>
            <div class="info-item" style="text-align: right;">
                <span class="label">Transaction No.</span>
                <span class="value"><?php echo $transaction_id; ?></span>
            </div>
        </div>

        <div class="info-item" style="margin-bottom: 30px;">
            <span class="label">Payor Name</span>
            <span class="value" style="font-size: 1.2rem;"><?php echo $payment->first_name . " " . $payment->last_name; ?></span>
            <span style="font-size: 0.85rem; color: #64748b;"><?php echo $payment->student_id; ?> | <?php echo $payment->course; ?></span>
        </div>

        <div class="payment-summary">
            <div class="summary-row">
                <span class="label">Payment Description</span>
                <span class="value"><?php echo $payment->description ?: 'Fee Payment'; ?></span>
            </div>
            <div class="summary-row">
                <span class="label">Semester / Term</span>
                <span class="value"><?php echo $payment->semester ?: 'N/A'; ?></span>
            </div>
            <div class="summary-row">
                <span class="label">Payment Method</span>
                <span class="value"><?php echo $payment->payment_method; ?></span>
            </div>
            <div class="summary-row">
                <span class="label">Ref / Check No.</span>
                <span class="value"><?php echo $payment->transaction_id; ?></span>
            </div>
            <div class="summary-row">
                <span class="total-label">Amount Paid</span>
                <span class="total-amount">₱<?php echo number_format($payment->amount, 2); ?></span>
            </div>
        </div>

        <div class="footer">
            <p>This is a computer-generated receipt. No signature required.</p>
            <p>Date of Transaction: <?php echo date('M d, Y', strtotime($payment->payment_date ?: $payment->created_at)); ?></p>
        </div>
    </div>

    <button class="btn-print no-print" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Print Receipt
    </button>

</body>
</html>
