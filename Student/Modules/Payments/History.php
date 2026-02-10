<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--bg);
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
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .table-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 18px 25px;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            background: #f8fafc;
            text-transform: uppercase;
        }

        td {
            padding: 18px 25px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            font-size: 0.95rem;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-verified {
            background: #dcfce7;
            color: #16a34a;
        }

        .status-pending {
            background: #fff7ed;
            color: #ea580c;
        }

        .status-rejected {
            background: #fee2e2;
            color: #ef4444;
        }

        .view-btn {
            background: transparent;
            border: 1px solid #e2e8f0;
            color: #64748b;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .view-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #eff6ff;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 500px;
            padding: 30px;
            position: relative;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .modal-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #94a3b8;
            cursor: pointer;
        }

        .receipt-preview {
            width: 100%;
            height: 250px;
            background: #f1f5f9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .receipt-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        .detail-label {
            color: #64748b;
        }

        .detail-value {
            font-weight: 600;
            color: #1e293b;
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <h1 class="page-title">Payment History</h1>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference No.</th>
                            <th>Payment Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require_once '../../../Database/config.php';
                        $student_email = $_SESSION['email'];
                        
                        try {
                            $stmt = $pdo->prepare("SELECT p.* FROM payments p JOIN enrollments e ON p.enrollment_id = e.enrollmentId WHERE e.email = ? ORDER BY p.created_at DESC");
                            $stmt->execute([$student_email]);
                            $payments = $stmt->fetchAll();

                            if (count($payments) > 0) {
                                foreach ($payments as $payment) {
                                    $date = date('M d, Y', strtotime($payment->payment_date ?: $payment->created_at));
                                    $status_class = '';
                                    if ($payment->status === 'Completed' || $payment->status === 'Verified') {
                                        $status_class = 'status-verified';
                                    } elseif ($payment->status === 'Rejected') {
                                        $status_class = 'status-rejected';
                                    } else {
                                        $status_class = 'status-pending';
                                    }
                                    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($date) . "</td>";
                                    echo "<td>" . htmlspecialchars($payment->transaction_id) . "</td>";
                                    echo "<td>" . htmlspecialchars($payment->payment_method) . "</td>";
                                    echo "<td style='font-weight: 600;'>₱" . number_format($payment->amount, 2) . "</td>";
                                    echo "<td><span class='status-badge {$status_class}'>" . htmlspecialchars($payment->status) . "</span></td>";
                                    echo "<td>
                                        <div style='display: flex; gap: 8px;'>
                                            <button class='view-btn' onclick=\"openModal('" . htmlspecialchars($payment->transaction_id) . "', '" . htmlspecialchars($payment->payment_method) . "', '" . number_format($payment->amount, 2) . "', '" . htmlspecialchars($date) . "', '" . htmlspecialchars($payment->status) . "', '/sms/" . htmlspecialchars($payment->proof_of_payment) . "')\">View</button>
                                            <a href='Print-Receipt.php?id=" . htmlspecialchars($payment->transaction_id) . "' target='_blank' class='view-btn' style='text-decoration: none; display: flex; align-items: center; gap: 5px; background: #f8fafc; border-color: #2563eb; color: #2563eb;'>
                                                <i class='fas fa-print'></i> Receipt
                                            </a>
                                        </div>
                                    </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align:center; padding: 50px;'>No payment history found.</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='6' style='text-align:center; color:red;'>Error fetching data.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="receiptModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Transaction Details</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>

            <div class="receipt-preview">
                <i class="fas fa-image" style="font-size: 3rem;"></i>
                <!-- <img src="..." alt="Receipt"> -->
            </div>

            <div class="detail-row">
                <span class="detail-label">Reference No.</span>
                <span class="detail-value" id="modalRef">--</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date</span>
                <span class="detail-value" id="modalDate">--</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Channel</span>
                <span class="detail-value" id="modalChannel">--</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Amount Paid</span>
                <span class="detail-value" style="color: var(--primary); font-size: 1.1rem;">₱<span
                        id="modalAmount">--</span></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value" id="modalStatus">--</span>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button style="flex: 1; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 12px; border-radius: 10px; font-weight: 600; cursor: pointer;" onclick="closeModal()">Close</button>
                <a id="modalPrintBtn" href="#" target="_blank" style="flex: 1; background: #1648bc; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; cursor: pointer; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fas fa-print"></i> Print Receipt
                </a>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('receiptModal');

        function openModal(ref, channel, amount, date, status, image) {
            document.getElementById('modalRef').textContent = ref;
            document.getElementById('modalChannel').textContent = channel;
            document.getElementById('modalAmount').textContent = amount;
            document.getElementById('modalDate').textContent = date;
            document.getElementById('modalStatus').textContent = status;
            document.getElementById('modalPrintBtn').href = 'Print-Receipt.php?id=' + ref;
            
            const preview = document.querySelector('.receipt-preview');
            if (image && image !== '/sms/') {
                preview.innerHTML = `<img src="${image}" alt="Receipt" style="width: 100%; height: 100%; object-fit: contain; cursor: pointer;" onclick="window.open('${image}', '_blank')">`;
            } else {
                preview.innerHTML = `<i class="fas fa-image" style="font-size: 3rem;"></i>`;
            }

            modal.classList.add('active');
        }

        function closeModal() {
            modal.classList.remove('active');
        }

        // Close on outside click
        window.onclick = function (event) {
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>