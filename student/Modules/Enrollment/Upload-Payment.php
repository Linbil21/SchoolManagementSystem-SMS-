<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Payment</title>
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

        .upload-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .card-header {
            margin-bottom: 25px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 15px;
        }
        
        .card-title {
            font-weight: 700;
            color: #1e293b;
            font-size: 1.1rem;
        }

        .accounts-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .account-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }
        
        .bank-logo {
            width: 50px;
            height: 50px;
            background: #f8fafc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #64748b;
        }
        
        .bank-details h4 {
            font-size: 0.95rem;
            color: #1e293b;
            margin-bottom: 2px;
        }
        
        .bank-details p {
            font-size: 0.85rem;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
        }
        
        .upload-box {
            border: 2px dashed #cbd5e1;
            padding: 30px;
            text-align: center;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        
        .upload-box:hover {
            border-color: var(--primary);
            background: #f8fafc;
        }
        
        .upload-box input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .submit-btn {
            background: var(--primary);
            color: white;
            border: none;
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            font-size: 1rem;
        }

        @media (max-width: 900px) {
            .upload-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Upload Payment</h1>
                <p style="color: #64748b;">Upload your proof of payment to validate your enrollment.</p>
            </div>

            <div class="upload-container">
                <!-- Left: Bank Accounts -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Bank Accounts</h3>
                    </div>
                    <div class="accounts-list">
                        <div class="account-item">
                            <div class="bank-logo" style="color: #0056b3;">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="bank-details">
                                <h4>BDO Unibank</h4>
                                <p>Account Name: School Management System</p>
                                <p>Account No: <strong>0012-3456-7890</strong></p>
                            </div>
                        </div>
                        <div class="account-item">
                            <div class="bank-logo" style="color: #007bff;">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="bank-details">
                                <h4>GCash</h4>
                                <p>Account Name: School Finance</p>
                                <p>Account No: <strong>0917-123-4567</strong></p>
                            </div>
                        </div>
                        <div class="account-item">
                            <div class="bank-logo" style="color: #dc3545;">
                                <i class="fas fa-piggy-bank"></i>
                            </div>
                            <div class="bank-details">
                                <h4>BPI Family</h4>
                                <p>Account Name: School Management System</p>
                                <p>Account No: <strong>1029-3847-56</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Upload Form -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Payment Details</h3>
                    </div>
                    <form action="Enrollment-Status.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="form-label">Payment Channel</label>
                            <select class="form-select" required>
                                <option value="">Select Channel</option>
                                <option>BDO Unibank</option>
                                <option>GCash</option>
                                <option>BPI Family</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Reference Number</label>
                            <input type="text" class="form-input" placeholder="e.g. 123456789" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Amount Paid</label>
                            <input type="number" class="form-input" placeholder="0.00" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date of Payment</label>
                            <input type="date" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Proof of Payment</label>
                            <div class="upload-box">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #94a3b8; margin-bottom: 10px;"></i>
                                <p style="font-size: 0.9rem; color: #64748b;">Click to upload image or PDF</p>
                                <input type="file" required accept="image/*,.pdf">
                            </div>
                        </div>
                        <button type="submit" class="submit-btn">Submit Payment</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
