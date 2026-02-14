<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cashier') {
    header("Location: ../../auth/Login.php");
    exit();
}
require_once '../../Database/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt History - Cashier</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1648bc;
            --bg: #f8fafc;
            --white: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
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
            color: var(--text-main);
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .content-area {
            padding: 40px;
        }

        .search-bar {
            background: var(--white);
            padding: 25px;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
        }

        .search-bar input {
            flex: 1;
            padding: 12px 20px;
            border-radius: 12px;
            border: 1.5px solid #edf2f7;
            outline: none;
        }

        .btn-filter {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0 25px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .history-card {
            background: var(--white);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            border-bottom: 2px solid #f1f5f9;
        }

        td {
            padding: 20px 15px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.92rem;
        }

        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-success {
            background: #f0fdf4;
            color: #16a34a;
        }

        .badge-void {
            background: #fff1f2;
            color: #e11d48;
        }

        .btn-view {
            padding: 8px 16px;
            border-radius: 10px;
            border: none;
            background: #eef2ff;
            color: var(--primary);
            font-weight: 700;
            cursor: pointer;
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
            margin: 10vh auto;
            width: 450px;
            border-radius: 24px;
            overflow: hidden;
            animation: modalSlide 0.3s ease-out;
        }

        @keyframes modalSlide {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .modal-body {
            padding: 30px;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 30px;">Receipt History</h1>

            <div class="search-bar">
                <input type="text" id="receiptSearch" onkeyup="filterTable('receiptSearch', 'receiptTable')" placeholder="Search by OR Number, Student Name, or Date...">
                <button class="btn-filter" onclick="filterTable('receiptSearch', 'receiptTable')"><i class="fas fa-search"></i> Search</button>
            </div>

            <div class="history-card">
                <table id="receiptTable">
                    <thead>
                        <tr>
                            <th>OR Number</th>
                            <th>Student</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $stmt = $pdo->query("
                                SELECT p.*, e.first_name, e.last_name 
                                FROM payments p 
                                JOIN enrollments e ON p.enrollment_id = e.enrollmentId 
                                WHERE p.status IN ('Completed', 'Voided')
                                ORDER BY p.created_at DESC
                            ");
                            $history = $stmt->fetchAll();
                            
                            foreach ($history as $row) {
                                $name = htmlspecialchars($row->first_name . " " . $row->last_name);
                                $ref = htmlspecialchars($row->transaction_id);
                                $date = date('M d, Y', strtotime($row->created_at));
                                $amount = number_format($row->amount, 2);
                                $status = $row->status;
                                $badge_class = ($status === 'Voided') ? 'badge-void' : 'badge-success';
                                $display_status = ($status === 'Voided') ? 'Voided' : 'Active';

                                echo "<tr>
                                        <td style='font-family: monospace; font-weight: 700; color: var(--primary);'>$ref</td>
                                        <td>$name</td>
                                        <td>$date</td>
                                        <td style='font-weight: 700;'>₱$amount</td>
                                        <td><span class='badge $badge_class'>$display_status</span></td>
                                        <td><button class='btn-view' onclick=\"openModal('$ref')\">View Details</button></td>
                                    </tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='6' style='text-align: center;'>Error fetching data</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Placeholder -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-body">
                <div style="font-size: 3rem; color: var(--primary); margin-bottom: 20px;"><i
                        class="fas fa-file-invoice"></i></div>
                <h2 id="modalTitle">Receipt Details</h2>
                <p style="color: var(--text-muted); margin-top: 10px;">The system is generating a digital copy for
                    <strong id="orVal"></strong>...</p>
                <div style="margin-top: 30px; display: flex; gap: 10px;">
                    <button
                        style="flex: 1; padding: 12px; border-radius: 12px; border: 1.5px solid #edf2f7; background: white; font-weight: 600; cursor: pointer;"
                        onclick="closeModal()">Close</button>
                    <button
                        style="flex: 1; padding: 12px; border-radius: 12px; background: var(--primary); color: white; border: none; font-weight: 700; cursor: pointer;"><i
                            class="fas fa-download"></i> Download</button>
                </div>
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

        function openModal(or) {
            document.getElementById('orVal').textContent = or;
            document.getElementById('viewModal').style.display = 'block';
        }
        function closeModal() {
            document.getElementById('viewModal').style.display = 'none';
        }
    </script>
</body>

</html>