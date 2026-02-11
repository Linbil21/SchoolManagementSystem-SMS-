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

            <div class="stats-banner">
                <div class="stat-item">
                    <div class="stat-icon" style="background: #fff1f2; color: #e11d48;"><i class="fas fa-undo-alt"></i>
                    </div>
                    <div>
                        <p style="font-size: 0.8rem; color: #64748b;">Pending Refunds</p>
                        <h3 style="font-weight: 800;">4 Requests</h3>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;"><i
                            class="fas fa-check-circle"></i></div>
                    <div>
                        <p style="font-size: 0.8rem; color: #64748b;">Processed Today</p>
                        <h3 style="font-weight: 800;">₱12,450.00</h3>
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
                            <th>Requested By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-family: monospace; font-weight: 700;">OR-882905</td>
                            <td>James Wilson</td>
                            <td style="font-weight: 700;">₱4,200.00</td>
                            <td><span style="color: #64748b;">Duplicate Payment</span></td>
                            <td style="font-size: 0.85rem;">Admin Staff</td>
                            <td><button class="btn-refund" onclick="confirmRefund('OR-882905')">Approve Refund</button>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family: monospace; font-weight: 700;">OR-882901</td>
                            <td>Mia Thorne</td>
                            <td style="font-weight: 700;">₱15,000.00</td>
                            <td><span style="color: #64748b;">Wrong Assessment</span></td>
                            <td style="font-size: 0.85rem;">Registrar</td>
                            <td><button class="btn-refund" onclick="confirmRefund('OR-882901')">Approve Refund</button>
                            </td>
                        </tr>
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