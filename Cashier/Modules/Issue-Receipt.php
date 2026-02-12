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
    <title>Issue Official Receipt - Cashier</title>
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

        .receipt-container {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 30px;
        }

        .card {
            background: var(--white);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 25px;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 18px;
            border-radius: 12px;
            border: 1.5px solid #edf2f7;
            background: #f8fafc;
            outline: none;
        }

        .form-group input:focus {
            border-color: var(--primary);
            background: #fff;
        }

        /* Receipt Preview Styling */
        .receipt-preview {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            border: 1px solid #e2e8f0;
            position: relative;
            min-height: 500px;
        }

        .receipt-preview::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: var(--primary);
            border-radius: 20px 20px 0 0;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .receipt-header img {
            width: 60px;
            margin-bottom: 10px;
        }

        .receipt-header h2 {
            font-size: 1.1rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            font-size: 0.85rem;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .receipt-table th {
            text-align: left;
            padding: 10px 0;
            border-bottom: 2px solid #f1f5f9;
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .receipt-table td {
            padding: 12px 0;
            border-bottom: 1px solid #f8fafc;
            font-size: 0.9rem;
        }

        .receipt-total {
            border-top: 2px solid var(--primary);
            padding-top: 15px;
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total-item.grand-total {
            font-weight: 800;
            color: var(--primary);
            font-size: 1.2rem;
            margin-top: 10px;
        }

        .btn-issue {
            background: var(--primary);
            color: white;
            padding: 15px;
            border-radius: 15px;
            border: none;
            width: 100%;
            font-weight: 800;
            cursor: pointer;
            margin-top: 20px;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-issue:hover {
            background: #123896;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 30px; letter-spacing: -0.5px;">Issue Official Receipt</h1>

            <div class="receipt-container">
                <!-- Transaction Details -->
                <div class="card">
                    <div class="card-title"><i class="fas fa-edit"></i> Transaction Details</div>
                    <div class="form-group">
                        <label>Search Student (ID or Name)</label>
                        <input type="text" placeholder="Enter Student ID..." value="2024-1005">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>OR Number</label>
                            <input type="text" value="OR-882910" style="font-family: monospace; font-weight: 700;">
                        </div>
                        <div class="form-group">
                            <label>Payment Date</label>
                            <input type="date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div style="margin-top: 25px; padding: 20px; background: #f8fafc; border-radius: 18px; border: 1.5px solid #edf2f7;">
                        <div class="card-title" style="font-size: 1rem; margin-bottom: 15px;"><i class="fas fa-list-ul"></i> Particulars</div>
                        <div class="form-row" style="grid-template-columns: 2fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="font-size: 0.75rem;">Description</label>
                                <input type="text" placeholder="e.g. Tuition Fee" value="Partial Tuition Fee" style="background: white; border-color: #e2e8f0;">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="font-size: 0.75rem;">Amount</label>
                                <input type="number" placeholder="0.00" value="5000" style="background: white; border-color: #e2e8f0; font-weight: 700; color: var(--primary);">
                            </div>
                        </div>
                        <button
                            style="background: white; color: var(--primary); border: 1.5px solid #e2e8f0; padding: 12px; border-radius: 12px; font-weight: 700; cursor: pointer; width: 100%; font-size: 0.85rem; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <i class="fas fa-plus-circle"></i> Add Line Item
                        </button>
                    </div>

                    <div class="form-group" style="margin-top: 25px;">
                        <label>Payment Mode</label>
                        <select>
                            <option>Cash</option>
                            <option>GCash / E-Wallet</option>
                            <option>Bank Transfer</option>
                            <option>Cheque</option>
                        </select>
                    </div>

                    <button class="btn-issue"><i class="fas fa-print"></i> Generate & Issue Receipt</button>
                </div>

                <!-- Live Preview -->
                <div>
                    <div class="receipt-preview">
                        <div class="receipt-header">
                            <img src="/Assets/image/logo.png" alt="Logo">
                            <h2>SMS School Management</h2>
                            <p style="font-size: 0.75rem; color: var(--text-muted);">Official Receipt of Payment</p>
                        </div>

                        <div class="receipt-info">
                            <div>
                                <p style="color: var(--text-muted);">Payer</p>
                                <strong>John Michael Doe</strong>
                                <p>BS Computer Science (2nd Year)</p>
                            </div>
                            <div style="text-align: right;">
                                <p style="color: var(--text-muted);">OR #</p>
                                <strong style="color: var(--primary);">OR-882910</strong>
                                <p>
                                    <?php echo date('M d, Y'); ?>
                                </p>
                            </div>
                        </div>

                        <table class="receipt-table">
                            <thead>
                                <tr>
                                    <th>Particulars</th>
                                    <th style="text-align: right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Partial Tuition Fee - 2nd Sem</td>
                                    <td style="text-align: right;">₱5,000.00</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="receipt-total">
                            <div class="total-item">
                                <span style="color: var(--text-muted);">Subtotal</span>
                                <span>₱5,000.00</span>
                            </div>
                            <div class="total-item grand-total">
                                <span>TOTAL PAID</span>
                                <span>₱5,000.00</span>
                            </div>
                        </div>

                        <div style="margin-top: 50px; text-align: center; font-size: 0.7rem; color: var(--text-muted);">
                            <p>Cashier: <strong>MARIA CLARA</strong></p>
                            <p style="margin-top: 10px;">This is a computer-generated receipt.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>