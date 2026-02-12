<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Payee | Student Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #64748b;
            --bg: #f1f5f9;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --receipt-bg: #fff;
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
            grid-template-columns: 1fr 1.5fr;
            gap: 30px;
        }

        .info-card {
            background: #1e293b;
            color: white;
            border-radius: 20px;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .info-card h3 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
        }

        .bank-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 12px;
        }

        .bank-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
            font-size: 1.2rem;
        }

        .bank-text p {
            margin: 0;
            font-size: 0.85rem;
            opacity: 0.8;
        }

        .bank-text h4 {
            margin: 0;
            font-size: 1rem;
            letter-spacing: 0.5px;
        }

        .form-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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

        .form-input,
        .form-select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.95rem;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .upload-area {
            border: 2px dashed #cbd5e1;
            padding: 40px;
            text-align: center;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .upload-area:hover {
            border-color: var(--primary);
            background: #f8fafc;
        }

        .upload-area input {
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
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
        }

        .submit-btn:hover {
            background: #1d4ed8;
        }

        .blur-text {
            filter: blur(6px);
            transition: all 0.3s ease;
            user-select: none;
        }

        .blur-text.active {
            filter: blur(0);
            user-select: auto;
        }

        .security-toggle {
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            background: rgba(255,255,255,0.1);
        }

        .security-toggle:hover {
            background: rgba(255,255,255,0.2);
        }

        /* Live Receipt Preview Styles */
        .receipt-live-preview {
            background: var(--receipt-bg);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.1);
            position: sticky;
            top: 40px;
            border: 1px solid #e2e8f0;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .receipt-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .receipt-label {
            font-size: 0.65rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
        }

        .receipt-value {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .receipt-total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #1e293b;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 4rem;
            font-weight: 900;
            color: rgba(0, 0, 0, 0.03);
            pointer-events: none;
            text-transform: uppercase;
        }

        .receipt-blur-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(8px);
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 24px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .receipt-blur-overlay:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .receipt-blur-overlay.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .unlock-icon {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            color: var(--primary);
            font-size: 1.5rem;
        }

        .receipt-blur-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(8px);
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 24px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .receipt-blur-overlay:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .receipt-blur-overlay.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .unlock-icon {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            color: var(--primary);
            font-size: 1.5rem;
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <h1 class="page-title">Payment Payee</h1>
            <p style="color: #64748b; margin-bottom: 30px;">Verify payee details and provide transaction information accurately.</p>

            <div class="upload-container" style="grid-template-columns: 1fr 1.2fr 1fr;">
                <div class="info-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 15px;">
                        <h3 style="margin: 0; border: none; padding: 0;">Bank Details</h3>
                        <div class="security-toggle" onclick="toggleSecurity()" title="Show/Hide Details">
                            <i class="fas fa-eye-slash" id="securityIcon"></i>
                        </div>
                    </div>

                    <div class="bank-item">
                        <div class="bank-icon"><i class="fas fa-university"></i></div>
                        <div class="bank-text">
                            <p>BDO Unibank</p>
                            <h4 class="blur-text">0012-3456-7890</h4>
                        </div>
                    </div>

                    <div class="bank-item">
                        <div class="bank-icon"><i class="fas fa-mobile-alt"></i></div>
                        <div class="bank-text">
                            <p>GCash</p>
                            <h4 class="blur-text">0917-123-4567</h4>
                        </div>
                    </div>

                    <div class="bank-item">
                        <div class="bank-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="bank-text">
                            <p>Landbank</p>
                            <h4 class="blur-text">1234-5678-9012</h4>
                        </div>
                    </div>

                    <div style="margin-top: 30px; font-size: 0.85rem; opacity: 0.7;">
                        Note: Please ensure the account number is correct before transferring. Keep your receipt for
                        verification purposes.
                    </div>
                </div>

                <div class="form-card">
                    <?php
                    require_once '../../../Database/config.php';
                    $student_email = $_SESSION['email'];
                    $stmt = $pdo->prepare("SELECT IFNULL(s.student_id, e.reference_code) as student_id, e.first_name, e.last_name 
                                         FROM enrollments e 
                                         LEFT JOIN students s ON e.email = s.email
                                         WHERE e.email = ? 
                                         ORDER BY e.created_at DESC LIMIT 1");
                    $stmt->execute([$student_email]);
                    $student_info = $stmt->fetch();
                    
                    $student_name = $student_info ? $student_info->first_name . " " . $student_info->last_name : "N/A";
                    $student_id_val = $student_info ? $student_info->student_id : "N/A";
                    $auto_ref_no = "TRX-" . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
                    ?>
                    <form id="paymentForm">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Student Name</label>
                                <input type="text" id="inputName" class="form-input" value="<?php echo htmlspecialchars($student_name); ?>" readonly style="background: #f8fafc;">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Student ID</label>
                                <input type="text" id="inputId" class="form-input" value="<?php echo htmlspecialchars($student_id_val); ?>" readonly style="background: #f8fafc;">
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Amount Paid</label>
                                <input type="number" name="amount" id="inputAmount" class="form-input" placeholder="0.00" step="0.01" required oninput="updateReceipt()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Payment Date</label>
                                <input type="date" name="payment_date" id="inputDate" class="form-input" value="<?php echo date('Y-m-d'); ?>" required oninput="updateReceipt()">
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Payment Description</label>
                                <select name="description" id="inputDesc" class="form-select" required onchange="updateReceipt()">
                                    <option value="">Select Purpose...</option>
                                    <option value="Tuition Fee">Tuition Fee</option>
                                    <option value="Entrance Fee">Entrance Fee</option>
                                    <option value="Miscellaneous Fee">Miscellaneous Fee</option>
                                    <option value="Examination Fee">Examination Fee</option>
                                    <option value="Books">Books</option>
                                    <option value="Uniform">Uniform</option>
                                    <option value="Graduation Fee">Graduation Fee</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Semester / Term</label>
                                <select name="semester" id="inputSem" class="form-select" required onchange="updateReceipt()">
                                    <option value="1st Semester">1st Semester</option>
                                    <option value="2nd Semester">2nd Semester</option>
                                    <option value="Summer Class">Summer Class</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Reference Number (Auto-generated)</label>
                            <input type="text" name="reference_number" id="inputRef" class="form-input" value="<?php echo $auto_ref_no; ?>" readonly style="background: #f8fafc; font-family: monospace; font-weight: 700; color: var(--primary);">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" id="inputMethod" class="form-select" required onchange="updateReceipt()">
                                <option value="">Select Channel</option>
                                <option value="Online Banking">Online Banking</option>
                                <option value="Over-the-Counter">Over-the-Counter</option>
                                <option value="E-Wallet (GCash/Maya)">E-Wallet (GCash/Maya)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Specific Purpose / Remarks</label>
                            <textarea name="purpose" id="inputPurpose" class="form-input" placeholder="e.g. For Prelim Exam, Balance for Books, etc." style="height: 80px; resize: none;" oninput="updateReceipt()"></textarea>
                        </div>

                        <button type="submit" class="submit-btn" id="submitBtn">Submit Payment Report</button>
                    </form>
                </div>

                <!-- Live Receipt Preview Section -->
                <div class="receipt-live-preview" id="receiptPreview">
                    <div class="receipt-blur-overlay" id="receiptOverlay" onclick="toggleReceiptPrivacy()">
                        <div class="unlock-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                    <div class="watermark">PREVIEW</div>
                    <div style="text-align: center; margin-bottom: 25px;">
                        <img src="/Assets/image/logo.png" style="width: 50px; margin-bottom: 10px;">
                        <h4 style="color: #1648bc; text-transform: uppercase; font-weight: 800; letter-spacing: 1px;">SMS Academy</h4>
                        <p style="font-size: 0.65rem; color: #64748b;">OFFICIAL E-RECEIPT PREVIEW</p>
                    </div>

                    <div class="receipt-line">
                        <span class="receipt-label">Student Name</span>
                        <span class="receipt-value" id="viewName"><?php echo htmlspecialchars($student_name); ?></span>
                    </div>
                    <div class="receipt-line">
                        <span class="receipt-label">Student ID</span>
                        <span class="receipt-value" id="viewId"><?php echo htmlspecialchars($student_id_val); ?></span>
                    </div>
                    <div class="receipt-line">
                        <span class="receipt-label">Date</span>
                        <span class="receipt-value" id="viewDate"><?php echo date('M d, Y'); ?></span>
                    </div>
                    <div class="receipt-line">
                        <span class="receipt-label">Payment Purpose</span>
                        <span class="receipt-value" id="viewDesc">---</span>
                    </div>
                    <div class="receipt-line">
                        <span class="receipt-label">Semester</span>
                        <span class="receipt-value" id="viewSem">1st Semester</span>
                    </div>
                    <div class="receipt-line">
                        <span class="receipt-label">Method</span>
                        <span class="receipt-value" id="viewMethod">---</span>
                    </div>
                    <div class="receipt-line">
                        <span class="receipt-label">Reference No.</span>
                        <span class="receipt-value" id="viewRef" style="font-family: monospace;">---</span>
                    </div>
                    <div class="receipt-line">
                        <span class="receipt-label">Purpose Details</span>
                        <span class="receipt-value" id="viewPurpose" style="font-size: 10px;">---</span>
                    </div>

                    <div class="receipt-total">
                        <div>
                            <p style="font-size: 0.7rem; color: #64748b; font-weight: 700;">TOTAL AMOUNT</p>
                            <h2 style="color: #1648bc; font-weight: 800;" id="viewAmount">₱0.00</h2>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 0.6rem; padding: 4px 8px; background: #dcfce7; color: #166534; border-radius: 20px; font-weight: 700;">PROVISIONAL</span>
                        </div>
                    </div>

                    <div style="margin-top: 30px; text-align: center;">
                        <p style="font-size: 0.6rem; color: #94a3b8; line-height: 1.4;">
                            This is a live preview of your payment submission. Data will be recorded once you click submit.
                        </p>
                    </div>
                </div>
            </div>
        </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function updateReceipt() {
            const amount = document.getElementById('inputAmount').value;
            const date = document.getElementById('inputDate').value;
            const desc = document.getElementById('inputDesc').value;
            const sem = document.getElementById('inputSem').value;
            const ref = document.getElementById('inputRef').value;
            const method = document.getElementById('inputMethod').value;
            const purpose = document.getElementById('inputPurpose').value;

            document.getElementById('viewAmount').textContent = amount ? '₱' + parseFloat(amount).toLocaleString(undefined, {minimumFractionDigits: 2}) : '₱0.00';
            document.getElementById('viewDate').textContent = date ? new Date(date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) : '---';
            document.getElementById('viewDesc').textContent = desc || '---';
            document.getElementById('viewSem').textContent = sem;
            document.getElementById('viewRef').textContent = ref; 
            document.getElementById('viewMethod').textContent = method || '---';
            document.getElementById('viewPurpose').textContent = purpose || '---';
        }

        window.addEventListener('load', updateReceipt);

        function toggleSecurity() {
            const items = document.querySelectorAll('.blur-text');
            const icon = document.getElementById('securityIcon');
            
            items.forEach(item => item.classList.toggle('active'));
            
            if (icon.classList.contains('fa-eye-slash')) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }

        function toggleReceiptPrivacy() {
            document.getElementById('receiptOverlay').classList.add('hidden');
        }

        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

            const formData = new FormData(this);

            fetch('../../api/submit_payment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        window.location.href = 'History.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message,
                        confirmButtonColor: '#2563eb'
                    });
                    btn.disabled = false;
                    btn.textContent = 'Submit Payment';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred. Please try again.',
                    confirmButtonColor: '#2563eb'
                });
                btn.disabled = false;
                btn.textContent = 'Submit Payment';
            });
        });
    </script>
</body>

</html>