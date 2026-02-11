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
    <title>Payment History - Cashier Submodule</title>
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

        .filters-strip {
            background: var(--white);
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .filters-strip input,
        .filters-strip select {
            padding: 10px 15px;
            border-radius: 10px;
            border: 1.5px solid #edf2f7;
            outline: none;
        }

        .data-card {
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

        .method-tag {
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .method-cash {
            background: #f0fdf4;
            color: #16a34a;
        }

        .method-bank {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .method-gcash {
            background: #fdf2f8;
            color: #be185d;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 30px;">Global Payment History</h1>

            <div class="filters-strip">
                <i class="fas fa-filter" style="color: var(--text-muted);"></i>
                <input type="text" placeholder="Transaction Ref..." style="flex: 1;">
                <select>
                    <option>All Methods</option>
                    <option>Cash</option>
                    <option>Online</option>
                </select>
                <input type="date">
                <button
                    style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; cursor: pointer;">Export
                    CSV</button>
            </div>

            <div class="data-card">
                <table>
                    <thead>
                        <tr>
                            <th>Ref Number</th>
                            <th>Student</th>
                            <th>Date & Time</th>
                            <th>Method</th>
                            <th>Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-family: monospace; font-weight: 700;">#TX-2024-9901</td>
                            <td><strong>Mark Anthony</strong></td>
                            <td>Jan 11, 2024 | 09:15 AM</td>
                            <td><span class="method-tag method-cash">CASH</span></td>
                            <td style="font-weight: 800; color: #10b981;">₱15,000.00</td>
                        </tr>
                        <tr>
                            <td style="font-family: monospace; font-weight: 700;">#TX-2024-9902</td>
                            <td><strong>Sarah Jane</strong></td>
                            <td>Jan 10, 2024 | 02:30 PM</td>
                            <td><span class="method-tag method-gcash">GCASH</span></td>
                            <td style="font-weight: 800; color: #10b981;">₱8,500.00</td>
                        </tr>
                        <tr>
                            <td style="font-family: monospace; font-weight: 700;">#TX-2024-9903</td>
                            <td><strong>Elon Musk</strong></td>
                            <td>Jan 09, 2024 | 11:00 AM</td>
                            <td><span class="method-tag method-bank">BANK</span></td>
                            <td style="font-weight: 800; color: #10b981;">₱42,000.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>