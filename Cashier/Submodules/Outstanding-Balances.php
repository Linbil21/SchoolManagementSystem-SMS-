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
    <title>Outstanding Balances - Cashier Submodule</title>
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

        .alert-banner {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            padding: 20px;
            border-radius: 20px;
            color: #b91c1c;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
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

        .btn-notice {
            background: #fff1f2;
            color: #e11d48;
            border: none;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-notice:hover {
            background: #e11d48;
            color: white;
        }

        .balance-badge {
            font-weight: 800;
            color: #ef4444;
            background: #fff1f2;
            padding: 4px 10px;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 30px;">Outstanding Balances Registry</h1>

            <div class="alert-banner">
                <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem;"></i>
                <div>
                    <h4 style="font-weight: 800;">Debt Collection Alert</h4>
                    <p style="font-size: 0.85rem;">Total outstanding debt for this semester reached
                        <strong>₱2,450,000.00</strong>. Consider sending automated reminders.</p>
                </div>
            </div>

            <div class="data-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <h2 style="font-size: 1.25rem; font-weight: 800;">Student Debtors List</h2>
                    <button
                        style="background: var(--primary); color: white; border: none; padding: 12px 20px; border-radius: 12px; font-weight: 700; cursor: pointer;"><i
                            class="fas fa-paper-plane"></i> Send Mass Notice</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Course / Year</th>
                            <th>Total Assessment</th>
                            <th>Amount Owed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>David Miller</strong><br><small>ID: 2024-5001</small></td>
                            <td>BS Architecture - 3rd</td>
                            <td>₱45,000.00</td>
                            <td><span class="balance-badge">₱22,500.00</span></td>
                            <td><button class="btn-notice"><i class="fas fa-bell"></i> Notify</button></td>
                        </tr>
                        <tr>
                            <td><strong>Sophia Loren</strong><br><small>ID: 2024-5002</small></td>
                            <td>BS Nursing - 1st</td>
                            <td>₱62,000.00</td>
                            <td><span class="balance-badge">₱15,000.00</span></td>
                            <td><button class="btn-notice"><i class="fas fa-bell"></i> Notify</button></td>
                        </tr>
                        <tr>
                            <td><strong>Robert Downy</strong><br><small>ID: 2024-5003</small></td>
                            <td>BS Engineering - 4th</td>
                            <td>₱55,000.00</td>
                            <td><span class="balance-badge">₱45,000.00</span></td>
                            <td><button class="btn-notice"><i class="fas fa-bell"></i> Notify</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>