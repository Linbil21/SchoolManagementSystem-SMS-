<?php
/**
 * SMS PAYMENT GATEWAY API (MOCK)
 * Simulates a real-world payment gateway integration (Stripe/PayPal/Maya styles)
 * 
 * Endpoint: student/api/payment_gateway.php
 * Methods: POST (JSON)
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

session_start();
require_once '../../Database/config.php';

// Fallback for getallheaders() if not on Apache
if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

// Mock API Key Check (for simulated security)
$headers = getallheaders();
$api_key = $headers['X-Gateway-Key'] ?? 'demo_key_123';

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 1. Validate Session / Identity
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'error' => 'UNAUTHORIZED', 'message' => 'Valid student session required.']);
    exit();
}

$student_email = $_SESSION['email'];

// 2. Parse Input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    // Fallback for form-data if needed
    $input = $_POST;
}

$amount = floatval($input['amount'] ?? 0);
$method = $input['method'] ?? 'Digital Wallet';
$description = $input['description'] ?? 'Tuition Fee Payment';
$nonce = $input['nonce'] ?? uniqid('tok_'); // Mock gateway token

// 3. Validation
if ($amount <= 0) {
    echo json_encode(['success' => false, 'error' => 'INVALID_AMOUNT', 'message' => 'Payment amount must be greater than zero.']);
    exit();
}

try {
    $pdo->beginTransaction();

    $student_id = $_SESSION['student_id'] ?? '';
    
    // 4. Fetch Active/Pending Enrollment (Robust lookup by email or reference code)
    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE (TRIM(email) = TRIM(?) AND email != '') OR (reference_code = ? AND reference_code != '') ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$student_email, $student_id]);
    $student = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$student) {
        $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$student_email]);
        $student = $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    // 5. Ultimate Fallback: Match by Student Name if emails got disjointed
    if (!$student) {
        $nameStmt = $pdo->prepare("SELECT first_name, last_name FROM students WHERE email = ? LIMIT 1");
        $nameStmt->execute([$student_email]);
        $sName = $nameStmt->fetch(PDO::FETCH_OBJ);
        
        if ($sName) {
            $fallback = $pdo->prepare("SELECT * FROM enrollments WHERE first_name = ? AND last_name = ? ORDER BY created_at DESC LIMIT 1");
            $fallback->execute([$sName->first_name, $sName->last_name]);
            $student = $fallback->fetch(PDO::FETCH_OBJ);
        }
    }
    // 6. Record the student's name in the description as a professional receipt anchor
    $enrollment_id = $student ? $student->enrollmentId : null;
    $student_full_name = $student ? (trim($student->first_name . " " . $student->last_name)) : $student_email;
    $description = $description . " [Paid by: " . $student_full_name . "]";

    // 5. Simulate External Gateway Processing (Mock 500ms delay)
    // usleep(500000); 

    // 6. Record the Successful Transaction
    $transaction_id = "GATEWAY-" . strtoupper(bin2hex(random_bytes(6)));
    
    $insert = $pdo->prepare("INSERT INTO payments (enrollment_id, amount, payment_method, status, transaction_id, description, created_at) VALUES (?, ?, ?, 'Completed', ?, ?, NOW())");
    $insert->execute([
        $enrollment_id,
        $amount,
        $method,
        $transaction_id,
        $description
    ]);

    // 7. Update Student Balance (Wrapped safely in case 'balance' column doesn't exist yet on live DB)
    try {
        if ($enrollment_id) {
            $update = $pdo->prepare("UPDATE enrollments SET balance = balance - ? WHERE enrollmentId = ?");
            $update->execute([$amount, $enrollment_id]);
        } else {
            // Aggressive fallback: deduct balance by their email if the enrollment ID didn't perfectly map
            $update = $pdo->prepare("UPDATE enrollments SET balance = balance - ? WHERE email = ?");
            $update->execute([$amount, $student_email]);
        }
    } catch (PDOException $ex) {
        // Silently ignore if balance column doesn't exist
    }

    // 8. Create Success Notification
    $notif_msg = "Gateway payment of ₱" . number_format($amount, 2) . " via $method was successful. Ref: $transaction_id";
    $notif = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, icon, icon_bg, icon_color) SELECT userId, 'payment', 'Payment Successful', ?, 'fa-check-circle', '#dcfce7', '#16a34a' FROM users WHERE email = ?");
    $notif->execute([$notif_msg, $student_email]);

    $pdo->commit();

    // 9. Respond with Gateway Receipt Data
    $fname = $student ? $student->first_name : 'Guest';
    $lname = $student ? $student->last_name : 'Student';
    $bal = $student ? (($student->balance ?? 0) - $amount) : 0;
    
    echo json_encode([
        'success' => true,
        'gateway_status' => 'APPROVED',
        'auth_code' => rand(100000, 999999),
        'transaction' => [
            'id' => $transaction_id,
            'amount' => $amount,
            'currency' => 'PHP',
            'method' => $method,
            'timestamp' => date('Y-m-d H:i:s'),
            'description' => $description
        ],
        'student' => [
            'name' => $fname . ' ' . $lname,
            'new_balance' => $bal
        ]
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'error' => 'PROCESSING_FAILED',
        'message' => $e->getMessage()
    ]);
}
