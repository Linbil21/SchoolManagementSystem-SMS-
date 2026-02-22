<?php
session_start();
require_once '../../Database/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cashier') {
    header("Location: ../../auth/Login.php");
    exit();
}

// Fetch Online Payments from the database
try {
    $stmt = $pdo->prepare("
        SELECT p.*, 
               IFNULL(e.first_name, IFNULL(s.first_name, 'Guest')) as first_name, 
               IFNULL(e.last_name, IFNULL(s.last_name, 'Student')) as last_name, 
               IFNULL(s.student_id, IFNULL(e.reference_code, p.paymentId)) as student_id 
        FROM payments p 
        LEFT JOIN enrollments e ON p.enrollment_id = e.enrollmentId 
        LEFT JOIN students s ON (s.email = e.email OR (e.email IS NULL AND p.description LIKE CONCAT('%Paid by: ', s.email, '%')))
        WHERE p.payment_method IN ('Online Banking', 'E-Wallet (GCash/Maya)', 'GCash', 'Maya', 'Credit/Debit Card', 'Digital Wallet')
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $payments = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = $e->getMessage();
    $payments = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Payments - Cashier</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1648bc;
            --bg: #f8fafc;
            --text-dark: #1e293b;
            --text-gray: #64748b;
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
        }

        .header-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-box h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-dark);
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            color: var(--text-gray);
            font-size: 0.85rem;
            text-transform: uppercase;
            border-bottom: 2px solid #f1f5f9;
        }

        td {
            padding: 20px 15px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.95rem;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-pending { background: #fff7ed; color: #ea580c; }
        .status-completed { background: #dcfce7; color: #16a34a; }
        .status-rejected { background: #fee2e2; color: #ef4444; }

        .btn-view {
            padding: 8px 16px;
            border-radius: 10px;
            border: none;
            background: #eef2ff;
            color: var(--primary);
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-view:hover {
            background: var(--primary);
            color: white;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 5vh auto;
            width: 90%;
            max-width: 900px;
            border-radius: 24px;
            overflow: hidden;
            animation: modalSlide 0.3s ease-out;
        }

        @keyframes modalSlide {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            padding: 0;
        }

        .receipt-view {
            background: #f1f5f9;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 500px;
        }

        .receipt-view img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .details-view {
            padding: 40px;
        }

        .info-item {
            margin-bottom: 20px;
        }

        .info-item label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-gray);
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-item p {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .btn-action {
            flex: 1;
            padding: 14px;
            border-radius: 12px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-approve { background: #10b981; color: white; }
        .btn-reject { background: #ef4444; color: white; }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="header-box">
                <div>
                    <h1>Online Payments</h1>
                    <p style="color: var(--text-gray);">Monitor and verify transactions via GCash, Maya, and Online Banking.</p>
                </div>
                <div style="background: white; padding: 10px 20px; border-radius: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <span style="font-weight: 700; font-size: 1.2rem; color: var(--primary);">
                        <?php echo count($payments); ?>
                    </span>
                    <span style="font-size: 0.85rem; color: var(--text-gray); margin-left: 5px;">Total Online Submissions</span>
                </div>
            </div>

            <div class="card">
                <?php if (isset($error)): ?>
                    <div style="padding: 20px; background: #fee2e2; color: #ef4444; border-radius: 12px; margin-bottom: 20px;">
                        Error: <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div style="margin-bottom: 20px; position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    <input type="text" id="paymentSearch" onkeyup="filterTable('paymentSearch', 'paymentsTable')" placeholder="Search payments..." 
                        style="width: 100%; padding: 12px 15px 12px 45px; border-radius: 12px; border: 1px solid #e2e8f0; outline: none; font-size: 0.95rem;">
                </div>

                <table id="paymentsTable">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payments)): ?>
                            <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-gray);">No online payments recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($payments as $p): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div style="width: 42px; height: 42px; border-radius: 12px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center; font-weight: 800; border: 1px solid #bbf7d0;">
                                                <?php echo substr($p->first_name, 0, 1); ?>
                                            </div>
                                            <div>
                                                <p style="font-weight: 700; color: #1e293b; margin: 0;"><?php echo htmlspecialchars($p->first_name . " " . $p->last_name); ?></p>
                                                <p style="font-size: 0.75rem; color: #64748b; margin: 0;"><?php echo htmlspecialchars($p->student_id); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.85rem; font-weight: 500; color: #475569;">
                                            <?php echo htmlspecialchars($p->description ?: 'Fee Payment'); ?>
                                            <span style="display: block; font-size: 0.75rem; color: #94a3b8;"><?php echo htmlspecialchars($p->semester ?: ''); ?></span>
                                        </div>
                                    </td>
                                    <td style="font-weight: 800; color: #059669; font-size: 1.05rem;">₱<?php echo number_format($p->amount, 2); ?></td>
                                    <td>
                                        <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; border: 1px solid #e2e8f0;">
                                            <?php echo htmlspecialchars($p->payment_method); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($p->status); ?>">
                                            <?php echo htmlspecialchars($p->status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-view" style="background: #1648bc; color: white; border-radius: 12px; padding: 10px 18px; font-weight: 700; box-shadow: 0 4px 12px rgba(22, 72, 188, 0.2); border: none; cursor: pointer; transition: 0.3s;"
                                            onclick="openModal(<?php echo htmlspecialchars(json_encode($p)); ?>)">
                                            <i class="fas fa-search-dollar" style="margin-right: 8px;"></i>Details
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-body">
                <div class="receipt-view">
                    <img id="modalImg" src="" alt="Proof of Payment">
                </div>
                <div class="details-view">
                    <div style="display:flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
                        <h2 style="font-weight: 800;">Verify Payment</h2>
                        <i class="fas fa-times" onclick="closeModal()" style="cursor: pointer; color: var(--text-gray); font-size: 1.2rem;"></i>
                    </div>

                    <div class="info-item">
                        <label>Student Information</label>
                        <p id="modalStudentName"></p>
                        <p id="modalStudentID" style="font-size: 0.85rem; color: var(--text-gray);"></p>
                    </div>

                    <div class="info-item">
                        <label>Payment Purpose</label>
                        <p id="modalPurpose"></p>
                    </div>

                    <div class="info-item">
                        <label>Specific Remarks / Purpose Details</label>
                        <p id="modalPurposeDetails"></p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="info-item">
                            <label>Amount Paid</label>
                            <p id="modalAmount" style="color: var(--primary); font-size: 1.2rem;"></p>
                        </div>
                        <div class="info-item">
                            <label>Ref Number</label>
                            <p id="modalRef" style="font-family: monospace;"></p>
                        </div>
                    </div>

                    <div class="modal-actions" id="modalActionButtons">
                        <button class="btn-action btn-reject" onclick="updateStatus('Rejected')">Reject</button>
                        <button class="btn-action btn-approve" onclick="updateStatus('Verified')">Verify & Post</button>
                    </div>
                </div>
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

        let currentPaymentId = null;

        function openModal(data) {
            currentPaymentId = data.payment_id;
            document.getElementById('modalPurposeDetails').textContent = data.purpose || 'None provided';
            document.getElementById('modalStudentName').textContent = data.first_name + ' ' + data.last_name;
            document.getElementById('modalStudentID').textContent = 'ID: ' + data.student_id;
            document.getElementById('modalPurpose').textContent = (data.description || 'Fee Payment') + ' - ' + (data.semester || '');
            document.getElementById('modalAmount').textContent = '₱' + parseFloat(data.amount).toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('modalRef').textContent = data.transaction_id;
            
            const img = document.getElementById('modalImg');
            if (data.proof_of_payment) {
                img.src = '/' + data.proof_of_payment;
                img.style.display = 'block';
            } else {
                img.style.display = 'none';
            }

            const actions = document.getElementById('modalActionButtons');
            if (data.status === 'Pending') {
                actions.style.display = 'flex';
            } else {
                actions.style.display = 'none';
            }

            document.getElementById('paymentModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }

        function updateStatus(status) {
            const remarks = document.querySelector('textarea[placeholder="Add remarks..."]').value;
            
            Swal.fire({
                title: 'Confirm ' + status,
                text: "Are you sure you want to mark this payment as " + status + "?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: status === 'Verified' ? '#10b981' : '#ef4444',
                confirmButtonText: 'Yes, proceed'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    const formData = new FormData();
                    formData.append('payment_id', currentPaymentId);
                    formData.append('status', status);
                    formData.append('remarks', remarks);

                    fetch('../api/verify_payment.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success!', data.message, 'success')
                            .then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                    });
                }
            });
        }

        window.onclick = function(e) {
            if (e.target.id === 'paymentModal') closeModal();
        }
    </script>
</body>

</html>
