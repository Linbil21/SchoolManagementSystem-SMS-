<?php
session_start();
// Security check
require_once '../../../auth/Security.php';
checkRole(['student']);
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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 class="page-title">Payment History</h1>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <button onclick="openGatewayModal()" style="background: linear-gradient(135deg, #1648bc 0%, #2563eb 100%); color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(22, 72, 188, 0.25);">
                        <i class="fas fa-credit-card"></i> Pay via Gateway
                    </button>
                    <div class="search-box" style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="text" id="paymentSearch" onkeyup="filterTable('paymentSearch', 'paymentTable')" placeholder="Search payments..." 
                            style="padding: 10px 15px 10px 40px; border-radius: 12px; border: 1px solid #e2e8f0; outline: none; width: 280px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    </div>
                </div>
            </div>

            <div class="table-card">
                <table id="paymentTable">
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
                                            <button class='view-btn' onclick=\"openModal('" . htmlspecialchars($payment->transaction_id) . "', '" . htmlspecialchars($payment->payment_method) . "', '" . number_format($payment->amount, 2) . "', '" . htmlspecialchars($date) . "', '" . htmlspecialchars($payment->status) . "', '/" . htmlspecialchars($payment->proof_of_payment) . "')\">View</button>
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

    <!-- Gateway Modal -->
    <div class="modal" id="gatewayModal">
        <div class="modal-content" style="max-width: 450px;">
            <div class="modal-header">
                <h3 class="modal-title">Secure Payment Gateway</h3>
                <button class="close-btn" onclick="closeGatewayModal()">&times;</button>
            </div>
            <div id="gatewayForm">
                <div style="margin-bottom: 20px; text-align: center;">
                    <div style="display: inline-flex; background: #eff6ff; color: #2563eb; padding: 15px; border-radius: 50%; font-size: 1.5rem; margin-bottom: 15px;">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <p style="font-size: 0.9rem; color: #64748b;">Enter the amount you wish to pay. This transaction is secured by SMS Gateway.</p>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">PAYMENT AMOUNT (PHP)</label>
                    <input type="number" id="payAmount" value="5000" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 1.1rem; font-weight: 700;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 5px;">PAYMENT METHOD</label>
                    <select id="payMethod" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; font-weight: 600;">
                        <option value="GCash">GCash</option>
                        <option value="Maya">Maya</option>
                        <option value="Credit/Debit Card">Credit/Debit Card</option>
                        <option value="Online Banking">Online Banking</option>
                    </select>
                </div>
                <button onclick="processGatewayPayment()" id="processBtn" style="width: 100%; background: #1648bc; color: white; border: none; padding: 15px; border-radius: 12px; font-weight: 700; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.2s;">
                    Process Payment <i class="fas fa-lock"></i>
                </button>
            </div>
            <div id="gatewayProcessing" style="display: none; text-align: center; padding: 30px 0;">
                <div class="loader" style="border: 4px solid #f3f3f3; border-top: 4px solid #1648bc; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
                <p style="font-weight: 600; color: #1e293b;">Verifying Transaction...</p>
                <p style="font-size: 0.85rem; color: #64748b;">Please do not close this window.</p>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>

    <script>
        function openGatewayModal() {
            document.getElementById('gatewayModal').classList.add('active');
            document.getElementById('gatewayForm').style.display = 'block';
            document.getElementById('gatewayProcessing').style.display = 'none';
        }

        function closeGatewayModal() {
            document.getElementById('gatewayModal').classList.remove('active');
        }

        async function processGatewayPayment() {
            const amount = document.getElementById('payAmount').value;
            const method = document.getElementById('payMethod').value;
            const btn = document.getElementById('processBtn');
            const form = document.getElementById('gatewayForm');
            const processing = document.getElementById('gatewayProcessing');

            if (amount <= 0) {
                alert("Please enter a valid amount.");
                return;
            }

            form.style.display = 'none';
            processing.style.display = 'block';

            try {
                const response = await fetch('../../api/payment_gateway.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ amount, method, description: "Tuition Fee via Gateway" })
                });

                const result = await response.json();

                if (result.success) {
                    processing.innerHTML = `
                        <div style="color: #16a34a; font-size: 3.5rem; margin-bottom: 15px;"><i class="fas fa-check-circle"></i></div>
                        <h4 style="margin-bottom: 10px; font-size: 1.2rem;">Payment Approved!</h4>
                        <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 20px;">Ref: ${result.transaction.id}</p>
                        <button onclick="location.reload()" style="background: #1648bc; color: white; border: none; padding: 10px 30px; border-radius: 10px; font-weight: 700; cursor: pointer;">View Receipt</button>
                    `;
                } else {
                    alert("Gateway Error: " + result.message);
                    form.style.display = 'block';
                    processing.style.display = 'none';
                }
            } catch (error) {
                console.error(error);
                alert("An error occurred during processing.");
                form.style.display = 'block';
                processing.style.display = 'none';
            }
        }

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

        const modal = document.getElementById('receiptModal');

        function openModal(ref, channel, amount, date, status, image) {
            document.getElementById('modalRef').textContent = ref;
            document.getElementById('modalChannel').textContent = channel;
            document.getElementById('modalAmount').textContent = amount;
            document.getElementById('modalDate').textContent = date;
            document.getElementById('modalStatus').textContent = status;
            document.getElementById('modalPrintBtn').href = 'Print-Receipt.php?id=' + ref;
            
            const preview = document.querySelector('.receipt-preview');
            if (image && image !== '/') {
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
            const rModal = document.getElementById('receiptModal');
            const gModal = document.getElementById('gatewayModal');
            if (event.target == rModal) {
                closeModal();
            }
            if (event.target == gModal) {
                closeGatewayModal();
            }
        }
    </script>
</body>

</html>
