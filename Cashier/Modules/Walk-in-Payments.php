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
    <title>Walk-in Payments - Cashier</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1648bc;
            --bg: #f8fafc;
            --text-dark: #1e293b;
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
        }

        .content-area {
            padding: 40px;
        }

        .search-box {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
        }

        input[type="text"] {
            flex: 1;
            padding: 12px 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            outline: none;
        }

        .btn-search {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0 25px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .results-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .btn-pay {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
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

        .modal-header {
            padding: 25px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            padding: 20px;
            background: #f8fafc;
            display: flex;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            outline: none;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 25px; color: var(--text-dark);">Walk-in Payments</h1>
            <div class="search-box">
                <input type="text" placeholder="Search by Student ID or Name...">
                <button class="btn-search">Search Student</button>
            </div>

            <div class="results-card">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">
                            <th style="padding: 15px; border-bottom: 2px solid #f1f5f9;">Student Info</th>
                            <th style="padding: 15px; border-bottom: 2px solid #f1f5f9;">Remaining Balance</th>
                            <th style="padding: 15px; border-bottom: 2px solid #f1f5f9;">Last Payment</th>
                            <th style="padding: 15px; border-bottom: 2px solid #f1f5f9;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 20px 15px; border-bottom: 1px solid #f1f5f9;">
                                <p style="font-weight: 700;">Sarah Johnson</p>
                                <p style="font-size: 0.8rem; color: #64748b;">ID: 2024-5501</p>
                            </td>
                            <td
                                style="padding: 20px 15px; border-bottom: 1px solid #f1f5f9; font-weight: 800; color: #ef4444;">
                                ₱28,400.00</td>
                            <td style="padding: 20px 15px; border-bottom: 1px solid #f1f5f9;">Dec 15, 2023</td>
                            <td style="padding: 20px 15px; border-bottom: 1px solid #f1f5f9;"><button class="btn-pay"
                                    onclick="openPaymentModal('Sarah Johnson', '₱28,400.00')">Process Payment</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Processing Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-weight: 800;">Process Payment</h2>
                <i class="fas fa-times" style="cursor: pointer; color: #64748b;" onclick="closeModal()"></i>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 20px; font-size: 0.9rem; color: #64748b;">Processing payment for <strong
                        id="studentName" style="color: var(--text-dark);"></strong></p>
                <div class="form-group">
                    <label>Amount to Pay</label>
                    <input type="number" placeholder="0.00" value="5000">
                </div>
                <div class="form-group">
                    <label>Payment Mode</label>
                    <select>
                        <option>Cash</option>
                        <option>Cheque</option>
                        <option>Credit/Debit Card</option>
                        <option>Bank Transfer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Reference # (if applicable)</label>
                    <input type="text" placeholder="e.g. Check number or Bank ref">
                </div>
            </div>
            <div class="modal-footer">
                <button
                    style="flex: 1; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; font-weight: 600; cursor: pointer;"
                    onclick="closeModal()">Cancel</button>
                <button
                    style="flex: 1; padding: 12px; border-radius: 12px; background: #10b981; color: white; border: none; font-weight: 700; cursor: pointer;"
                    onclick="processPayment()">Post Payment</button>
            </div>
        </div>
    </div>

    <script>
        function openPaymentModal(name, balance) {
            document.getElementById('studentName').textContent = name;
            document.getElementById('paymentModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            document.getElementById('paymentModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        function processPayment() {
            alert('Payment posted successfully! Generating receipt...');
            closeModal();
        }
    </script>
</body>

</html>