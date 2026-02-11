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
    <title>Payment Status - Cashier Submodule</title>
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

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--white);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .stat-card h3 {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .stat-card .value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
        }

        .stat-card .trend {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 12px;
            align-self: flex-start;
        }

        .trend.up {
            background: #f0fdf4;
            color: #16a34a;
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

        .status-pill {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-paid {
            background: #f0fdf4;
            color: #16a34a;
        }

        .status-partial {
            background: #fef9c3;
            color: #a16207;
        }

        .status-overdue {
            background: #fef2f2;
            color: #ef4444;
        }

        .search-area {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .search-area input {
            flex: 1;
            padding: 12px 20px;
            border-radius: 12px;
            border: 1.5px solid #edf2f7;
            outline: none;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 30px;">Payment Status Overview</h1>

            <div class="status-grid">
                <div class="stat-card">
                    <h3>Fully Paid Students</h3>
                    <div class="value">1,245</div>
                    <div class="trend up">+12 today</div>
                </div>
                <div class="stat-card">
                    <h3>Partial Payments</h3>
                    <div class="value">458</div>
                    <div class="trend" style="background: #fff7ed; color: #c2410c;">In Progress</div>
                </div>
                <div class="stat-card">
                    <h3>Unpaid / Locked</h3>
                    <div class="value">82</div>
                    <div class="trend" style="background: #fef2f2; color: #ef4444;">Needs Action</div>
                </div>
            </div>

            <div class="data-card">
                <div class="search-area">
                    <input type="text" placeholder="Search by Student ID, Name or Department...">
                    <button
                        style="background: var(--primary); color: white; border: none; padding: 0 25px; border-radius: 12px; font-weight: 600; cursor: pointer;"><i
                            class="fas fa-filter"></i> Filter</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Registration</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Visibility</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="font-weight: 700;">Alice Wonderland</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">ID: 2024-0010</div>
                            </td>
                            <td>Regular</td>
                            <td style="font-weight: 700;">₱0.00</td>
                            <td><span class="status-pill status-paid">Fully Paid</span></td>
                            <td><i class="fas fa-eye" style="color: #10b981;"></i> All Clear</td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 700;">Bob Builder</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">ID: 2024-0011</div>
                            </td>
                            <td>Irregular</td>
                            <td style="font-weight: 700;">₱8,400.00</td>
                            <td><span class="status-pill status-partial">Partial</span></td>
                            <td><i class="fas fa-eye-slash" style="color: #f59e0b;"></i> Exam Restricted</td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 700;">Charlie Brown</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">ID: 2024-0012</div>
                            </td>
                            <td>New Student</td>
                            <td style="font-weight: 700;">₱25,000.00</td>
                            <td><span class="status-pill status-overdue">Unpaid</span></td>
                            <td><i class="fas fa-lock" style="color: #ef4444;"></i> Portal Locked</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>