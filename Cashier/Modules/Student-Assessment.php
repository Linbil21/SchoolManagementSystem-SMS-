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
    <title>Student Assessment - Cashier</title>
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

        .card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .assessment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
        }

        .fee-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .fee-item:last-child {
            border-bottom: 2px solid #1648bc;
            margin-bottom: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 1.2rem;
            font-weight: 800;
            color: #1648bc;
        }

        .btn-action {
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: 0.3s;
        }

        .btn-print {
            background: #eef2ff;
            color: var(--primary);
        }

        .btn-assess {
            background: var(--primary);
            color: white;
            margin-left: 10px;
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
            margin: 5vh auto;
            width: 600px;
            border-radius: 24px;
            overflow: hidden;
            animation: modalSlide 0.3s ease-out;
        }

        @keyframes modalSlide {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 25px;
            border-bottom: 1px solid #f1f5f9;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            padding: 20px;
            background: #f8fafc;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <h1 style="font-weight: 800; margin-bottom: 25px;">Student Assessment</h1>

            <div class="card">
                <div class="assessment-header">
                    <div>
                        <h2 style="color: var(--primary);">Emily Davis</h2>
                        <p style="color: #64748b;">Course: BS Information Technology | Year: 2nd Year</p>
                        <p style="color: #64748b;">Registration Status: <span
                                style="color: #10b981; font-weight: 600;">Enrolled</span></p>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 0.8rem; color: #64748b;">Assessment Date</p>
                        <p style="font-weight: 600;">Jan 11, 2024</p>
                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <h3 style="margin-bottom: 15px; font-size: 1rem; color: #64748b;">TUITION & MISCELLANEOUS FEES</h3>
                    <div class="fee-item"><span>Tuition Fee (21 Units)</span><span>₱15,750.00</span></div>
                    <div class="fee-item"><span>Registration Fee</span><span>₱500.00</span></div>
                    <div class="fee-item"><span>Laboratory Fees</span><span>₱2,500.00</span></div>
                    <div class="fee-item"><span>Library Fee</span><span>₱300.00</span></div>
                    <div class="fee-item"><span>Athletic Fee</span><span>₱200.00</span></div>
                    <div class="fee-item"><span>Medical/Dental Fee</span><span>₱150.00</span></div>

                    <div class="total-row" style="margin-top: 20px;">
                        <span>GROSS TOTAL</span>
                        <span>₱19,400.00</span>
                    </div>
                    <div class="total-row" style="color: #ef4444; font-size: 1rem; margin-top: 5px;">
                        <span>Less: Academic Scholarship (20%)</span>
                        <span>-₱3,880.00</span>
                    </div>
                    <div class="total-row" style="margin-top: 15px; border-top: 3px solid #1648bc; padding-top: 10px;">
                        <span>NET PAYABLE</span>
                        <span>₱15,520.00</span>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button class="btn-action btn-print"><i class="fas fa-print"></i> Print Assessment</button>
                    <button class="btn-action btn-assess" onclick="openAdjustmentModal()">Adjust Assessment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Adjustment Modal -->
    <div id="adjModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-weight: 800;">Adjust Student Assessment</h2>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 0.85rem; color: #64748b; margin-bottom: 5px;">Add
                        Fee/Discount Item</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" placeholder="Description"
                            style="flex: 2; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0;">
                        <input type="number" placeholder="Amount"
                            style="flex: 1; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0;">
                        <button
                            style="background: var(--primary); color: white; border: none; padding: 0 15px; border-radius: 10px;"><i
                                class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div style="margin-top: 20px;">
                    <label style="display: block; font-size: 0.85rem; color: #64748b; margin-bottom: 10px;">Reason for
                        Adjustment</label>
                    <textarea
                        style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; height: 80px; font-family: inherit;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button
                    style="padding: 12px 24px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; font-weight: 600; cursor: pointer;"
                    onclick="closeModal()">Cancel</button>
                <button
                    style="padding: 12px 24px; border-radius: 12px; background: var(--primary); color: white; border: none; font-weight: 700; cursor: pointer;"
                    onclick="closeModal()">Apply Adjustments</button>
            </div>
        </div>
    </div>

    <script>
        function openAdjustmentModal() {
            document.getElementById('adjModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            document.getElementById('adjModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    </script>
</body>

</html>