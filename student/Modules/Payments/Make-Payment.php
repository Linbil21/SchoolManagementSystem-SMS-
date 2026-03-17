<?php
session_start();

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

require_once '../../../auth/Security.php';
checkRole(['student']);
require_once '../../../Database/config.php';

$email = $_SESSION['email'] ?? '';
$student_name = "Guest Student";
$course_name = "N/A";
$student_id = "N/A";

if ($email) {
    try {
        // Try enrollment record first (for students with active enrollment)
        $stmt = $pdo->prepare("SELECT e.first_name, e.last_name, e.reference_code, c.course_name, s.student_id as real_id 
                               FROM enrollments e 
                               LEFT JOIN courses c ON (e.course_id = c.courseId OR e.preferred_course_1 = CAST(c.courseId AS CHAR))
                               LEFT JOIN students s ON e.email = s.email
                               WHERE e.email = ? LIMIT 1");
        $stmt->execute([$email]);
        $student = $stmt->fetch(PDO::FETCH_OBJ);
        if ($student) {
            $student_name = trim($student->first_name . " " . $student->last_name);
            $course_name = $student->course_name ?? 'N/A';
            $student_id = $student->real_id ?? $student->reference_code ?? 'N/A';
        } else {
            // Fallback: pre-enrolled student (no enrollment record yet) — get from students table
            $s2 = $pdo->prepare("SELECT first_name, last_name, student_id, course FROM students WHERE email = ? LIMIT 1");
            $s2->execute([$email]);
            $sdata = $s2->fetch(PDO::FETCH_OBJ);
            if ($sdata) {
                $student_name = trim($sdata->first_name . " " . $sdata->last_name);
                $course_name = $sdata->course ?? 'N/A';
                $student_id = $sdata->student_id ?? 'PENDING';
            }
        }
    } catch (PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Online Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
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
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .receipt-container {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
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
            padding: 14px 18px;
            border-radius: 14px;
            border: 1.5px solid #edf2f7;
            background: #f8fafc;
            outline: none;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .form-group input[readonly] {
            background: #f1f5f9;
            color: #475569;
            cursor: not-allowed;
        }

        /* Receipt Preview Styling */
        .receipt-preview {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            position: relative;
            min-height: 500px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 0 60px rgba(37, 99, 235, 0.08);
            transition: all 0.3s ease;
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
            width: 70px;
            margin-bottom: 12px;
        }

        .receipt-header h2 {
            font-size: 1.25rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #1e293b;
            letter-spacing: -0.5px;
        }

        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            font-size: 0.85rem;
            background: #f8fafc;
            padding: 15px;
            border-radius: 12px;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .receipt-table th {
            text-align: left;
            padding: 10px 0;
            border-bottom: 2px solid #e2e8f0;
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .receipt-table td {
            padding: 16px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .receipt-total {
            border-top: 2px solid var(--primary);
            padding-top: 20px;
            margin-top: 10px;
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.95rem;
            color: #475569;
        }

        .total-item.grand-total {
            font-weight: 800;
            color: var(--primary);
            font-size: 1.35rem;
            margin-top: 15px;
        }

        .btn-issue {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 16px;
            border-radius: 14px;
            border: none;
            width: 100%;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 30px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.25);
        }

        .btn-issue:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(37, 99, 235, 0.35);
        }

        .payment-methods {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }

        .method-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .method-card:hover {
            border-color: #93c5fd;
            background: #eff6ff;
        }

        .method-card.active {
            border-color: #2563eb;
            background: #eff6ff;
            color: #2563eb;
        }

        .method-card i {
            font-size: 1.5rem;
            color: #64748b;
        }
        
        .method-card.active i {
            color: #2563eb;
        }

        .method-card span {
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Blur Overlay */
        .blur-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 10;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.3s ease;
        }

        .btn-reveal {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
            padding: 12px 25px;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .btn-reveal:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.3);
        }
        
        /* Modal */
        .processing-modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(8px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white; padding: 40px; border-radius: 24px; text-align: center; max-width: 400px; width: 100%; box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        .loader {
            border: 4px solid #f3f3f3; border-top: 4px solid #2563eb; border-radius: 50%;
            width: 50px; height: 50px; animation: spin 1s linear infinite; margin: 0 auto 20px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div>
                <h1 style="font-weight: 800; font-size: 1.8rem; color: #1e293b;">Secure Online Payment</h1>
                <p style="color: #64748b; margin-bottom: 30px; margin-top: 5px;">Process your payment directly via SMS gateway and instantly secure an e-receipt.</p>
            </div>

            <div class="receipt-container">
                <!-- Transaction Details Form -->
                <div class="card">
                    <div class="card-title"><i class="fas fa-shield-alt"></i> Gateway Inputs</div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Payer Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($student_name); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Student ID</label>
                            <input type="text" value="<?php echo htmlspecialchars($student_id); ?>" readonly>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #64748b; margin-bottom: 12px;">Select Payment Gateway</label>
                        <div class="payment-methods">
                            <div class="method-card active" onclick="setMethod(this, 'GCash')">
                                <i class="fas fa-mobile-alt"></i>
                                <span>GCash E-Wallet</span>
                            </div>
                            <div class="method-card" onclick="setMethod(this, 'Maya')">
                                <i class="fas fa-wallet"></i>
                                <span>Maya E-Wallet</span>
                            </div>
                            <div class="method-card" onclick="setMethod(this, 'Credit/Debit Card')">
                                <i class="fas fa-credit-card"></i>
                                <span>Credit / Debit Card</span>
                            </div>
                            <div class="method-card" onclick="setMethod(this, 'Online Banking')">
                                <i class="fas fa-university"></i>
                                <span>Online Banking</span>
                            </div>
                        </div>
                        <input type="hidden" id="selectedMethod" value="GCash">
                    </div>

                    <div style="padding: 24px; background: #f8fafc; border-radius: 16px; border: 1.5px solid #edf2f7;">
                        <div class="form-row" style="grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 0;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Payment Description</label>
                                <select id="inputDesc" onchange="updatePreview()">
                                    <option value="Enrollment Downpayment">Enrollment Downpayment</option>
                                    <option value="Partial Tuition Fee">Partial Tuition Fee</option>
                                    <option value="Full Tuition Fee">Full Tuition Fee</option>
                                    <option value="Miscellaneous Fees">Miscellaneous Fees</option>
                                    <option value="Laboratory Fees">Laboratory Fees</option>
                                    <option value="Uniforms / PE">Uniforms / PE Requirements</option>
                                    <option value="Books and Modules">Books and Modules</option>
                                    <option value="Clearance / Graduation Fee">Clearance / Graduation Fee</option>
                                    <option value="Other Fees">Other Fees / Back Account</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Amount (PHP)</label>
                                <input type="number" id="inputAmt" value="5000" onkeyup="updatePreview()" onchange="updatePreview()" style="font-weight: 800; color: #2563eb;">
                            </div>
                        </div>
                    </div>

                    <button class="btn-issue" id="processBtn" onclick="processPayment()"><i class="fas fa-lock"></i> Authorize & Make Payment</button>
                    <p style="text-align:center; font-size:0.75rem; color:#94a3b8; margin-top:15px;"><i class="fas fa-info-circle"></i> Transactions are completely secured by SMS Gateway.</p>
                </div>

                <!-- Live Preview -->
                <div>
                    <div class="receipt-preview" id="receiptCard">
                        <!-- Blur Overlay Layer -->
                        <div class="blur-overlay" id="blurOverlay">
                            <button onclick="revealReceipt()" class="btn-reveal">
                                <i class="fas fa-eye"></i> View Live Preview
                            </button>
                        </div>
                        
                        <!-- Actual Content inside a container to be blurred -->
                        <div id="receiptContent" style="filter: blur(6px); user-select: none; pointer-events: none; transition: filter 0.4s ease;">
                            <div class="receipt-header">
                                <img src="<?php echo $root; ?>Assets/image/logo.png" alt="Logo">
                                <h2>SMS School Management</h2>
                                <p style="font-size: 0.75rem; color: var(--text-muted); letter-spacing: 0.5px;">Live E-Receipt Preview</p>
                            </div>

                        <div class="receipt-info">
                            <div>
                                <p style="color: var(--text-muted); font-size: 0.75rem; text-transform:uppercase;">Billed To</p>
                                <strong style="color: #1e293b; font-size: 0.95rem;"><?php echo htmlspecialchars($student_name); ?></strong>
                                <p style="color: #475569; margin-top: 2px;"><?php echo htmlspecialchars($course_name); ?></p>
                            </div>
                            <div style="text-align: right;">
                                <p style="color: var(--text-muted); font-size: 0.75rem; text-transform:uppercase;">Date</p>
                                <strong style="color: #1e293b; font-size: 0.95rem;"><?php echo date('M d, Y'); ?></strong>
                                <p style="color: var(--primary); font-weight: 700; margin-top: 2px; font-size:0.8rem;" id="previewMethod">Via GCash</p>
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
                                    <td id="previewDesc">Enrollment Downpayment</td>
                                    <td style="text-align: right;" id="previewAmt">₱5,000.00</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="receipt-total">
                            <div class="total-item">
                                <span>Subtotal</span>
                                <span id="previewSub">₱5,000.00</span>
                            </div>
                            <div class="total-item">
                                <span>Gateway Fee</span>
                                <span style="color: #22c55e;">FREE</span>
                            </div>
                            <div class="total-item grand-total">
                                <span>TOTAL PAYMENT</span>
                                <span id="previewTotal">₱5,000.00</span>
                            </div>
                        </div>

                        <div style="margin-top: 40px; text-align: center; font-size: 0.8rem; color: var(--text-muted);">
                            <p style="margin-top: 10px;">This e-receipt will be finalized upon successful transaction.</p>
                        </div>
                        </div> <!-- End receiptContent -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Processing Modal -->
    <div class="processing-modal" id="procModal">
        <div class="modal-content">
            <div id="loaderArea">
                <div class="loader"></div>
                <h3 style="color: #1e293b; margin-bottom: 5px;">Processing Secure Payment...</h3>
                <p style="color: #64748b; font-size: 0.9rem;">Please do not refresh or close this window.</p>
            </div>
            <div id="successArea" style="display: none;">
                <div style="color: #22c55e; font-size: 4rem; margin-bottom: 15px;"><i class="fas fa-check-circle"></i></div>
                <h3 style="color: #1e293b; margin-bottom: 10px;">Payment Successful!</h3>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 25px;">Your official e-receipt has been generated and recorded.</p>
                <a href="<?php echo $root; ?>student/Modules/Payments/History.php" style="background: #2563eb; color: white; padding: 12px 25px; border-radius: 12px; text-decoration: none; font-weight: 700; display: inline-block;">View Official Receipt</a>
            </div>
        </div>
    </div>

    <script>
        function setMethod(elem, method) {
            document.querySelectorAll('.method-card').forEach(el => el.classList.remove('active'));
            elem.classList.add('active');
            document.getElementById('selectedMethod').value = method;
            document.getElementById('previewMethod').textContent = 'Via ' + method;
        }

        function updatePreview() {
            const desc = document.getElementById('inputDesc').value;
            let amt = parseFloat(document.getElementById('inputAmt').value) || 0;
            
            // Format amount safely
            const formattedAmt = '₱' + amt.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            document.getElementById('previewDesc').textContent = desc;
            document.getElementById('previewAmt').textContent = formattedAmt;
            document.getElementById('previewSub').textContent = formattedAmt;
            document.getElementById('previewTotal').textContent = formattedAmt;
        }

        async function processPayment() {
            const amount = parseFloat(document.getElementById('inputAmt').value);
            const method = document.getElementById('selectedMethod').value;
            const desc = document.getElementById('inputDesc').value;

            if (!amount || amount <= 0) {
                alert("Please enter a valid amount.");
                return;
            }

            // Show modal
            const modal = document.getElementById('procModal');
            const loader = document.getElementById('loaderArea');
            const success = document.getElementById('successArea');
            
            modal.style.display = 'flex';
            loader.style.display = 'block';
            success.style.display = 'none';

            // Send actual API request
            try {
                const response = await fetch('<?php echo $root; ?>student/api/payment_gateway.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ amount: amount, method: method, description: desc })
                });

                const result = await response.json();

                if (result.success) {
                    setTimeout(() => {
                        loader.style.display = 'none';
                        success.style.display = 'block';
                    }, 500); // UI delay for realism
                } else {
                    alert("Payment Error: " + result.message);
                    modal.style.display = 'none';
                }
            } catch (error) {
                console.error(error);
                alert("Connection error. Payment could not be processed.");
                modal.style.display = 'none';
            }
        }
        
        function revealReceipt() {
            const overlay = document.getElementById('blurOverlay');
            const content = document.getElementById('receiptContent');
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 300);
            content.style.filter = 'blur(0)';
            content.style.userSelect = 'auto';
            content.style.pointerEvents = 'auto';
        }

        // Init preview on load
        window.onload = updatePreview;
    </script>
</body>

</html>
