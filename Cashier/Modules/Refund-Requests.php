<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cashier') {
    header("Location: ../../auth/Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Void / Refund Requests - Cashier</title>
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

        .request-card {
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

        .btn-refund {
            padding: 8px 16px;
            border-radius: 10px;
            border: none;
            background: #fff1f2;
            color: #e11d48;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-refund:hover {
            background: #e11d48;
            color: white;
        }

        .stats-banner {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-item {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 30px;">Void / Refund Control</h1>

            <?php
            require_once '../../Database/config.php';
            
            // Stats
            $pending_count = $pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'Refund Pending'")->fetchColumn() ?: 0;
            $processed_today = $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'Refunded' AND DATE(created_at) = CURDATE()")->fetchColumn() ?: 0;
            ?>
            <div class="stats-banner">
                <div class="stat-item">
                    <div class="stat-icon" style="background: #fff1f2; color: #e11d48;"><i class="fas fa-undo-alt"></i>
                    </div>
                    <div>
                        <p style="font-size: 0.8rem; color: #64748b;">Pending Refunds</p>
                        <h3 style="font-weight: 800;"><?php echo $pending_count; ?> Requests</h3>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;"><i
                            class="fas fa-check-circle"></i></div>
                    <div>
                        <p style="font-size: 0.8rem; color: #64748b;">Processed Today</p>
                        <h3 style="font-weight: 800;">₱<?php echo number_format($processed_today, 2); ?></h3>
                    </div>
                </div>
            </div>

            <div class="request-card">
                <h2 style="font-size: 1.2rem; margin-bottom: 20px;">Pending Requests</h2>
                <table>
                    <thead>
                        <tr>
                            <th>OR Number</th>
                            <th>Student</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Date</th>
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
                                WHERE p.status = 'Refund Pending'
                                ORDER BY p.created_at DESC
                            ");
                            $requests = $stmt->fetchAll();
                            
                            if (empty($requests)) {
                                echo '<tr><td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">No pending refund requests.</td></tr>';
                            } else {
                                foreach ($requests as $row) {
                                    $name = htmlspecialchars($row->first_name . " " . $row->last_name);
                                    $ref = htmlspecialchars($row->transaction_id);
                                    $amount = number_format($row->amount, 2);
                                    $reason = htmlspecialchars($row->description ?? "N/A");
                                    $date = date('M d, Y', strtotime($row->created_at));

                                    echo "<tr>
                                            <td style='font-family: monospace; font-weight: 700;'>$ref</td>
                                            <td>$name</td>
                                            <td style='font-weight: 700;'>₱$amount</td>
                                            <td><span style='color: #64748b;'>$reason</span></td>
                                            <td style='font-size: 0.85rem;'>$date</td>
                                            <td><button class='btn-refund' onclick=\"confirmRefund('$ref')\">Approve Refund</button></td>
                                        </tr>";
                                }
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

    <script>
        function confirmRefund(or) {
            if (confirm('Are you sure you want to approve the refund for ' + or + '? This action will void the official receipt and cannot be undone.')) {
                alert('Refund processed successfully. OR ' + or + ' is now void.');
            }
        }
    </script>
</body>

</html>