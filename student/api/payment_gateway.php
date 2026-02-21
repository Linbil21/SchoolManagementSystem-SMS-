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

    // 4. Fetch Active/Pending Enrollment
    $stmt = $pdo->prepare("SELECT enrollmentId, balance, first_name, last_name, status FROM enrollments WHERE email = ? AND status IN ('Enrolled', 'Validated', 'Pending Review', 'Pending Payment') LIMIT 1");
    $stmt->execute([$student_email]);
    $student = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$student) {
        $stmt = $pdo->prepare("SELECT enrollmentId, balance, first_name, last_name, status FROM enrollments WHERE email = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$student_email]);
        $student = $stmt->fetch(PDO::FETCH_OBJ);
    }

    if (!$student) {
        throw new Exception("Active enrollment record not found.");
    }


    // 5. Simulate External Gateway Processing (Mock 500ms delay)
    // usleep(500000); 

    // 6. Record the Successful Transaction
    $transaction_id = "GATEWAY-" . strtoupper(bin2hex(random_bytes(6)));
    
    $insert = $pdo->prepare("INSERT INTO payments (enrollment_id, amount, payment_method, status, transaction_id, description, created_at, payment_date) VALUES (?, ?, ?, 'Completed', ?, ?, NOW(), CURDATE())");
    $insert->execute([
        $student->enrollmentId,
        $amount,
        $method,
        $transaction_id,
        $description
    ]);

    // 7. Update Student Balance
    $update = $pdo->prepare("UPDATE enrollments SET balance = balance - ? WHERE enrollmentId = ?");
    $update->execute([$amount, $student->enrollmentId]);

    // 8. Create Success Notification
    $notif_msg = "Gateway payment of ₱" . number_format($amount, 2) . " via $method was successful. Ref: $transaction_id";
    $notif = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, icon, icon_bg, icon_color) SELECT userId, 'payment', 'Payment Successful', ?, 'fa-check-circle', '#dcfce7', '#16a34a' FROM users WHERE email = ?");
    $notif->execute([$notif_msg, $student_email]);

    $pdo->commit();

    // 9. Respond with Gateway Receipt Data
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
            'name' => $student->first_name . ' ' . $student->last_name,
            'new_balance' => $student->balance - $amount
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
