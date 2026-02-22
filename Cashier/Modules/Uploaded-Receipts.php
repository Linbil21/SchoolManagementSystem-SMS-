<?php
session_start();
require_once '../../auth/Security.php';
checkRole(['cashier', 'superadmin']);
$role = $_SESSION['role'];

// Robust absolute-relative path logic
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/Super-admin/', '/Admin/', '/Cashier/', '/Admission/', '/auth/', '/student/', '/modules/'];
$project_base = '';

foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        $project_base = rtrim(substr($script_name, 0, $pos), '/');
        break;
    }
}
$root_path = $project_base . '/';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Receipts - Cashier</title>
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
            max-width: 800px;
            border-radius: 24px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: modalSlide 0.3s ease-out;
        }

        @keyframes modalSlide {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 25px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .receipt-preview {
            background: #f1f5f9;
            border-radius: 15px;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed #cbd5e1;
        }

        .receipt-preview i {
            font-size: 3rem;
            color: #94a3b8;
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-group label {
            display: block;
            font-size: 0.8rem;
            color: var(--text-gray);
            margin-bottom: 5px;
        }

        .info-group p {
            font-weight: 700;
            color: var(--text-dark);
        }

        .modal-footer {
            padding: 20px;
            background: #f8fafc;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-approve {
            background: #10b981;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-reject {
            background: #ef4444;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="header-box" style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <h1>Uploaded Receipts</h1>
                    <p style="color: var(--text-gray);">Verify online payment submissions from students.</p>
                </div>
                <div class="search-box" style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    <input type="text" id="receiptSearch" onkeyup="filterTable('receiptSearch', 'receiptTable')" placeholder="Search receipts..." 
                        style="padding: 10px 15px 10px 40px; border-radius: 12px; border: 1px solid #e2e8f0; outline: none; width: 280px;">
                </div>
            </div>

            <div class="card">
                <table id="receiptTable">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Reference No.</th>
                            <th>Amount</th>
                            <th>Date Uploaded</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $stmt = $pdo->prepare("
                                SELECT p.*, e.first_name, e.last_name, IFNULL(s.student_id, e.reference_code) as student_id 
                                FROM payments p 
                                JOIN enrollments e ON p.enrollment_id = e.enrollmentId 
                                LEFT JOIN students s ON e.email = s.email
                                WHERE p.proof_of_payment IS NOT NULL AND p.status = 'Pending'
                                ORDER BY p.created_at DESC
                            ");
                            $stmt->execute();
                            $uploaded = $stmt->fetchAll();
                            
                            if (empty($uploaded)) {
                                echo '<tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--text-gray);">No pending receipts to verify.</td></tr>';
                            } else {
                                foreach ($uploaded as $row) {
                                    $name = htmlspecialchars($row->first_name . " " . $row->last_name);
                                    $student_id = htmlspecialchars($row->student_id);
                                    $ref = htmlspecialchars($row->transaction_id);
                                    $amount = number_format($row->amount, 2);
                                    $date = date('M d, Y', strtotime($row->created_at));
                                    $method = htmlspecialchars($row->payment_method);
                                    $img = "<?php echo $root_path; ?>" . htmlspecialchars($row->proof_of_payment);
                                    $purpose = htmlspecialchars($row->purpose ?? "");
                                    echo "<tr>
                                            <td>
                                                <div style='display: flex; align-items: center; gap: 12px;'>
                                                    <div style='width: 42px; height: 42px; border-radius: 12px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-weight: 800; border: 1px solid #c7d2fe;'>
                                                        " . substr($row->first_name, 0, 1) . "
                                                    </div>
                                                    <div>
                                                        <p style='font-weight: 700; color: #1e293b; margin: 0;'>$name</p>
                                                        <p style='font-size: 0.75rem; color: #64748b; margin: 0;'>$student_id</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span style='font-family: monospace; font-weight: 700; color: #475569; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; border: 1px solid #e2e8f0;'>$ref</span>
                                            </td>
                                            <td style='font-weight: 800; color: #059669; font-size: 1.05rem;'>₱$amount</td>
                                            <td style='color: #64748b; font-weight: 500;'>$date</td>
                                            <td>
                                                <button class='btn-view' style='background: #1648bc; color: white; border-radius: 12px; padding: 10px 18px; font-weight: 700; box-shadow: 0 4px 12px rgba(22, 72, 188, 0.2); border: none; cursor: pointer; transition: 0.3s;' 
                                                    onclick=\"openVerifyModal('$name', '$ref', '₱$amount', '$method', '$img', '{$row->payment_id}', '$purpose')\">
                                                    <i class='fas fa-eye' style='margin-right: 8px;'></i><?php echo $role === 'superadmin' ? 'View Details' : 'Verify'; ?>
                                                </button>
                                            </td>
                                        </tr>";
                                }
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='5' style='text-align: center; padding: 40px;'>
                                    <div style='color: #ef4444; background: #fee2e2; padding: 20px; border-radius: 16px; display: inline-block; border: 1px solid #fecaca;'>
                                        <i class='fas fa-exclamation-circle' style='margin-right: 8px;'></i>
                                        <strong>Database Error:</strong> " . $e->getMessage() . "
                                    </div>
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Verification Modal -->
    <div id="verifyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-weight: 800;">Verify Transaction</h2>
                <i class="fas fa-times" style="cursor: pointer; color: var(--text-gray);"
                    onclick="closeVerifyModal()"></i>
            </div>
            <div class="modal-body">
                <div class="receipt-preview" id="modalPreview">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <p style="position: absolute; margin-top: 60px; color: #94a3b8; font-weight: 500;">Proof of Payment
                        Preview</p>
                </div>
                <div>
                    <div class="info-group">
                        <label>Student Name</label>
                        <p id="modalName">Mark Lee</p>
                    </div>
                    <div class="info-group">
                        <label>Reference Number</label>
                        <p id="modalRef">REF-9921004</p>
                    </div>
                    <div class="info-group">
                        <label>Amount Submitted</label>
                        <p id="modalAmount">₱12,500.00</p>
                    </div>
                    <div class="info-group">
                        <label>Payment Method</label>
                        <p id="modalMethod">GCash</p>
                    </div>
                    <div class="info-group">
                        <label>Specific Purpose / Remarks</label>
                        <p id="modalPurpose">---</p>
                    </div>

                    <div style="margin-top: 20px;">
                        <label
                            style="display: block; font-size: 0.8rem; color: var(--text-gray); margin-bottom: 10px;">Internal
                            Note</label>
                        <textarea
                            style="width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #e2e8f0; height: 80px; outline: none; font-family: inherit;"
                            placeholder="Add remarks..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php if ($role === 'cashier'): ?>
                <button class="btn-reject" onclick="processStatus('Rejected')">Reject Payment</button>
                <button class="btn-approve" onclick="processStatus('Verified')">Approve & Post</button>
                <?php else: ?>
                <button class="btn-view" style="background: #64748b; color: white;" onclick="closeVerifyModal()">Close Review</button>
                <?php endif; ?>
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

        let currentPaymentId = null;

        function openVerifyModal(name, ref, amount, method, img, id, purpose) {
            currentPaymentId = id;
            document.getElementById('modalName').textContent = name;
            document.getElementById('modalRef').textContent = ref;
            document.getElementById('modalAmount').textContent = amount;
            document.getElementById('modalMethod').textContent = method;
            document.getElementById('modalPurpose').textContent = purpose || 'None provided';
            
            if (img && img !== '/') {
                document.getElementById('modalPreview').innerHTML = `<img src="${img}" style="max-width: 100%; max-height: 100%; object-fit: contain;">`;
            } else {
                document.getElementById('modalPreview').innerHTML = `<i class="fas fa-file-invoice-dollar"></i><p style="position: absolute; margin-top: 60px; color: #94a3b8; font-weight: 500;">No Preview Available</p>`;
            }

            document.getElementById('verifyModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeVerifyModal() {
            document.getElementById('verifyModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function processStatus(status) {
            const remarks = document.querySelector('textarea[placeholder="Add remarks..."]').value;
            
            Swal.fire({
                title: 'Confirm ' + status,
                text: "Are you sure you want to mark this receipt as " + status + "?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: status === 'Verified' ? '#10b981' : '#ef4444'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

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
                            Swal.fire('Success!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
                }
            });
        }

        window.onclick = function (event) {
            if (event.target == document.getElementById('verifyModal')) {
                closeVerifyModal();
            }
        }
    </script>
</body>

</html>