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
    <title>Outstanding Report - Cashier</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1648bc;
            --danger: #ef4444;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-sub: #64748b;
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
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            animation: fadeIn 0.5s ease-out;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .alert-card {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.2);
        }

        .alert-info h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .alert-info p {
            opacity: 0.9;
            font-size: 1rem;
        }

        .table-card {
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            color: var(--text-sub);
            font-size: 0.85rem;
            font-weight: 600;
            border-bottom: 2px solid #f1f5f9;
        }

        td {
            padding: 20px 15px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--text-main);
        }

        .student-flex {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #cbd5e1;
        }

        .action-btn {
            background: #eef2ff;
            color: var(--primary);
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .action-btn:hover {
            background: #e0e7ff;
        }

        .status-badge {
            background: #fee2e2;
            color: #ef4444;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 5px;
            border: 2px solid #f1f5f9;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Outstanding Balances</h1>
                    <p style="color: var(--text-sub);">Track uncollected fees and student debts</p>
                </div>
                <button
                    style="background: var(--primary); color: white; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-file-export" style="margin-right: 8px;"></i> Export List
                </button>
            </div>

            <div class="alert-card">
                <div class="alert-info">
                    <p>Total Receivable Amount</p>
                    <h2>₱842,500.00</h2>
                    <p style="font-size: 0.9rem; margin-top: 10px;"><i class="fas fa-exclamation-circle"></i> 142
                        Accounts Past Due</p>
                </div>
                <div
                    style="background: rgba(255,255,255,0.2); width: 80px; height: 80px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>

            <div class="table-card">
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                    <h3 style="font-weight: 700; color: var(--text-main);">High Priority Accounts</h3>
                    <input type="text" placeholder="Search student..."
                        style="padding: 10px 15px; border-radius: 10px; border: 1px solid #e2e8f0; width: 250px;">
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Grade/Level</th>
                            <th>Last Payment</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-family: monospace; color: var(--text-sub);">2023-0105</td>
                            <td>
                                <div class="student-flex">
                                    <img src="https://ui-avatars.com/api/?name=Mark+Ryder&background=random"
                                        class="avatar" alt="">
                                    <div>
                                        <strong>Mark Ryder</strong>
                                        <div style="font-size: 0.8rem; color: var(--text-sub);">BSIT - 3rd Year</div>
                                    </div>
                                </div>
                            </td>
                            <td>Collge</td>
                            <td>Oc 15, 2025</td>
                            <td style="font-weight: 700; color: var(--danger);">₱24,500.00</td>
                            <td><span class="status-badge">Overdue</span></td>
                            <td><button class="action-btn">Remind</button></td>
                        </tr>
                        <tr>
                            <td style="font-family: monospace; color: var(--text-sub);">2023-0089</td>
                            <td>
                                <div class="student-flex">
                                    <img src="https://ui-avatars.com/api/?name=Sarah+Lee&background=random"
                                        class="avatar" alt="">
                                    <div>
                                        <strong>Sarah Lee</strong>
                                        <div style="font-size: 0.8rem; color: var(--text-sub);">Grade 12</div>
                                    </div>
                                </div>
                            </td>
                            <td>SHS</td>
                            <td>Nov 02, 2025</td>
                            <td style="font-weight: 700; color: var(--danger);">₱18,200.00</td>
                            <td><span class="status-badge">Overdue</span></td>
                            <td><button class="action-btn">Remind</button></td>
                        </tr>
                        <tr>
                            <td style="font-family: monospace; color: var(--text-sub);">2024-0012</td>
                            <td>
                                <div class="student-flex">
                                    <img src="https://ui-avatars.com/api/?name=David+Kim&background=random"
                                        class="avatar" alt="">
                                    <div>
                                        <strong>David Kim</strong>
                                        <div style="font-size: 0.8rem; color: var(--text-sub);">Grade 10</div>
                                    </div>
                                </div>
                            </td>
                            <td>JHS</td>
                            <td>Dec 01, 2025</td>
                            <td style="font-weight: 700; color: #f59e0b;">₱8,500.00</td>
                            <td><span class="status-badge" style="background: #fef3c7; color: #d97706;">Pending</span>
                            </td>
                            <td><button class="action-btn">View</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>