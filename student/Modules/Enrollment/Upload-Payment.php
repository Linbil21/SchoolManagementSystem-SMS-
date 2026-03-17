<?php
session_start();
require_once '../../../Database/config.php';
require_once '../../../auth/Security.php';
checkRole(['student']);

// Robust absolute-relative path logic
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/student/', '/Super-admin/', '/Admin/', '/Cashier/', '/Admission/', '/auth/', '/modules/'];
$project_base = '';
foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        $project_base = rtrim(substr($script_name, 0, $pos), '/');
        break;
    }
}
$root = $project_base . '/';

// Admission Approval Check
$enrollment_status = $_SESSION['enrollment_status'] ?? 'Pending';
$allowed_payment_statuses = ['Pending Payment', 'Validation', 'Enrolled'];
if (!in_array($enrollment_status, $allowed_payment_statuses)) {
    header("Location: " . $root . "student/Modules/Admission/Result.php");
    exit();
}

$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['proof'])) {
    try {
        $channel = $_POST['channel'];
        $reference = $_POST['reference'];
        $amount = $_POST['amount'];
        $date = $_POST['payment_date'];
        
        // File Upload
        $target_dir = "../../../Assets/image/uploads/payments/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES["proof"]["name"], PATHINFO_EXTENSION);
        $filename = "PAY_" . $_SESSION['student_id'] . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["proof"]["tmp_name"], $target_file)) {
            $proof_path = "Assets/image/uploads/payments/" . $filename;
            
            $pdo->beginTransaction();
            
            // 1. Insert into payments table
            $stmt = $pdo->prepare("INSERT INTO payments (enrollment_id, amount, payment_method, status, transaction_id, proof_of_payment) 
                                 SELECT enrollmentId, ?, ?, 'Pending', ?, ? FROM enrollments WHERE email = ? LIMIT 1");
            $stmt->execute([$amount, $channel, $reference, $proof_path, $_SESSION['email']]);
            
            // 2. Update enrollment status to 'Validation'
            $stmt = $pdo->prepare("UPDATE enrollments SET status = 'Validation' WHERE email = ?");
            $stmt->execute([$_SESSION['email']]);
            
            $pdo->commit();
            
            // Notification for Cashier
            $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, icon, icon_bg, icon_color, link) VALUES (NULL, 'online_payment', 'New Online Payment', ?, 'fa-file-invoice-dollar', '#fef2f2', '#ef4444', ?)");
            $notif_link = $root . 'Cashier/Modules/Online-Payments.php';
            $notif_stmt->execute([$_SESSION['fullname'] . " has uploaded a payment proof for validation.", $notif_link]);
            
            header("Location: " . $root . "student/Modules/Enrollment/Enrollment-Status.php?status=payment_uploaded");
            exit();
        } else {
            throw new Exception("Failed to upload file.");
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $status = 'error';
    }
}
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
                        <h3 class="card-title">Payment Accounts</h3>
                    </div>
                    <div class="accounts-list">
                        <!-- Hello Money -->
                        <div class="account-item">
                            <div class="bank-logo" style="color: #ea580c;">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="bank-details">
                                <h4>AUB - Hello Money</h4>
                                <p>Account Name: School Admin</p>
                                <p>Account No: <strong>055-12-000123-4</strong></p>
                            </div>
                        </div>
                        <!-- GCash -->
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
                        <!-- BDO -->
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
                    </div>
                </div>

                <!-- Right: Upload Form -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Payment Details</h3>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="form-label">Payment Channel</label>
                            <select name="channel" class="form-select" required>
                                <option value="">Select Channel</option>
                                <option value="HelloMoney" <?php echo $selected_method == 'HelloMoney' ? 'selected' : ''; ?>>Hello Money (AUB)</option>
                                <option value="GCash" <?php echo $selected_method == 'GCash' ? 'selected' : ''; ?>>GCash</option>
                                <option value="BankTransfer" <?php echo $selected_method == 'BankTransfer' ? 'selected' : ''; ?>>Bank Transfer (BDO/BPI)</option>
                                <option value="Others" <?php echo $selected_method == 'Others' ? 'selected' : ''; ?>>Others</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference" class="form-input" placeholder="e.g. 123456789" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Amount Paid</label>
                            <input type="number" name="amount" class="form-input" placeholder="0.00" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date of Payment</label>
                            <input type="date" name="payment_date" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Proof of Payment</label>
                            <div class="upload-box">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #94a3b8; margin-bottom: 10px;"></i>
                                <p style="font-size: 0.9rem; color: #64748b;">Click to upload image or PDF</p>
                                <input type="file" name="proof" required accept="image/*,.pdf">
                            </div>
                        </div>
                        <button type="submit" class="submit-btn">Submit Payment Details</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
