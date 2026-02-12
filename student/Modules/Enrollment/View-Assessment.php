<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assessment</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
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
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .invoice-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 40px;
            position: relative;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 30px;
            margin-bottom: 30px;
        }

        .school-info h2 {
            font-size: 1.5rem;
            color: var(--primary);
            font-weight: 800;
        }

        .school-info p {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-meta h4 {
            color: var(--text-main);
            margin-bottom: 5px;
        }

        .invoice-meta p {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .fees-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        .fees-table th {
            text-align: left;
            padding: 15px 0;
            color: #64748b;
            font-size: 0.9rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .fees-table td {
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }

        .fees-table td.amount {
            text-align: right;
            font-weight: 600;
        }

        .fees-table th.amount {
            text-align: right;
        }

        .total-section {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-label {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
        }

        .payment-options {
            margin-top: 30px;
            border-top: 2px solid #f1f5f9;
            padding-top: 30px;
        }

        .payment-title {
            font-weight: 700;
            margin-bottom: 15px;
            color: #1e293b;
        }

        .payment-grid {
            display: flex;
            gap: 15px;
        }

        .payment-box {
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 12px;
            flex: 1;
            text-align: center;
        }

        .payment-box h5 {
            margin-bottom: 5px;
            color: #1e293b;
        }

        .payment-box p {
            font-size: 0.85rem;
            color: #64748b;
        }

        .note {
            margin-top: 30px;
            font-size: 0.85rem;
            color: #94a3b8;
            font-style: italic;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Assessment of Fees</h1>
            </div>

            <div class="invoice-card">
                <div class="invoice-header">
                    <div class="school-info">
                        <h2>School Management System</h2>
                        <p>123 University Avenue, Academic City</p>
                        <p>Tel: (02) 123-4567</p>
                    </div>
                    <div class="invoice-meta">
                        <h4>ASSESSMENT FORM</h4>
                        <p>Date:
                            <?php echo date("M d, Y"); ?>
                        </p>
                        <p>Student ID: 2023-0001</p>
                    </div>
                </div>

                <table class="fees-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="amount">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Tuition Fee (17 Units x ₱500.00)</td>
                            <td class="amount">₱8,500.00</td>
                        </tr>
                        <tr>
                            <td>Miscellaneous Fee</td>
                            <td class="amount">₱2,500.00</td>
                        </tr>
                        <tr>
                            <td>Laboratory Fee</td>
                            <td class="amount">₱1,500.00</td>
                        </tr>
                        <tr>
                            <td>Library Fee</td>
                            <td class="amount">₱500.00</td>
                        </tr>
                        <tr>
                            <td>Student Council Fee</td>
                            <td class="amount">₱200.00</td>
                        </tr>
                    </tbody>
                </table>

                <div class="total-section">
                    <span class="total-label">TOTAL ASSESSMENT</span>
                    <span class="total-amount">₱13,200.00</span>
                </div>

                <div class="payment-options">
                    <h4 class="payment-title">Payment Schedule</h4>
                    <div class="payment-grid">
                        <div class="payment-box">
                            <h5>Upon Enrollment (Downpayment)</h5>
                            <p>₱4,000.00</p>
                        </div>
                        <div class="payment-box">
                            <h5>Prelim</h5>
                            <p>₱3,066.00</p>
                        </div>
                        <div class="payment-box">
                            <h5>Midterm</h5>
                            <p>₱3,066.00</p>
                        </div>
                        <div class="payment-box">
                            <h5>Finals</h5>
                            <p>₱3,068.00</p>
                        </div>
                    </div>
                </div>

                <p class="note">This assessment is valid for 24 hours. Please proceed to payment to finalize your
                    enrollment.</p>

                <div style="margin-top: 30px; text-align: right;">
                    <button onclick="window.print()"
                        style="background: white; border: 1px solid #cbd5e1; padding: 12px 25px; border-radius: 10px; cursor: pointer; color: #475569; font-weight: 600; margin-right: 10px;">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <a href="Upload-Payment.php"
                        style="background: var(--primary); padding: 12px 30px; border-radius: 10px; color: white; text-decoration: none; font-weight: 600; display: inline-block;">
                        Proceed to Payment <i class="fas fa-credit-card" style="margin-left: 8px;"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</body>

</html>