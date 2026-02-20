<?php
header('Content-Type: application/json');
require_once '../Database/config.php';
require_once '../auth/OcrProcessor.php';

/**
 * Documents Integration API
 * Handles multi-file validation and OCR verification
 * Also provides a listing of document statuses for external monitoring/integration
 */

$method = $_SERVER['REQUEST_METHOD'];
$action = ($method === 'POST') ? ($_POST['action'] ?? 'validate_step') : ($_GET['action'] ?? 'list_documents');

try {
    if ($method === 'GET') {
        // Handle Listings (Similar to student_api.php requested by user)
        switch ($action) {
            case 'list_documents':
                $stmt = $pdo->query("SELECT 
                                        e.enrollmentId,
                                        e.reference_code,
                                        e.first_name,
                                        e.last_name,
                                        e.status,
                                        e.admission_type,
                                        e.year_level,
                                        e.email,
                                        c.course_name,
                                        e.id_picture,
                                        e.birth_cert,
                                        e.form_138,
                                        e.form_137,
                                        e.good_moral,
                                        e.barangay_clearance,
                                        e.created_at
                                     FROM enrollments e 
                                     LEFT JOIN courses c ON e.course_id = c.courseId 
                                     ORDER BY e.created_at DESC");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'count' => count($data),
                    'list' => $data
                ]);
                break;

            case 'get_document_status':
                $ref = $_GET['ref'] ?? '';
                if (!$ref) {
                    throw new Exception('Reference code is required.');
                }
                
                $stmt = $pdo->prepare("SELECT 
                                        reference_code, first_name, last_name,
                                        id_picture, birth_cert, form_138, form_137, good_moral, barangay_clearance
                                      FROM enrollments WHERE reference_code = ?");
                $stmt->execute([$ref]);
                $docs = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($docs) {
                    echo json_encode([
                        'success' => true,
                        'data' => $docs
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Record not found.']);
                }
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid GET action.']);
        }
    } 
    elseif ($method === 'POST') {
        $ocr = new OcrProcessor();
        
        if ($action === 'validate_step') {
            $step = $_POST['step'] ?? '';
            
            switch ($step) {
                case 'primary_docs':
                    $detected = [];
                    $errors = [];
                    
                    if (isset($_FILES['id_picture']) && $_FILES['id_picture']['error'] === 0) {
                        $res = $ocr->scanDocument($_FILES['id_picture']['tmp_name'], 'id_picture', $_FILES['id_picture']['name']);
                        if (isset($res['error'])) {
                            $errors[] = "ID Photo: " . $res['error'];
                        } else {
                            $detected[] = "Passport ID (" . ($res['confidence'] ?? '100') . "% quality)";
                        }
                    } else {
                        $errors[] = "Passport Size ID is required.";
                    }

                    if (isset($_FILES['birth_cert']) && $_FILES['birth_cert']['error'] === 0) {
                        $detected[] = "PSA Birth Certificate";
                    }

                    if (isset($_FILES['form_138']) && $_FILES['form_138']['error'] === 0) {
                        $detected[] = "Form 138 (Report Card)";
                    }

                    if (!empty($errors)) {
                        echo json_encode(['success' => false, 'status' => 'error', 'message' => implode("\n", $errors)]);
                        exit;
                    }
                    
                    echo json_encode([
                        'success' => true,
                        'status' => 'success',
                        'message' => 'Documents Detected: ' . implode(", ", $detected),
                        'detected_files' => $detected
                    ]);
                    break;
                    
                case 'secondary_docs':
                    $detected = [];
                    if (isset($_FILES['form_137']) && $_FILES['form_137']['error'] === 0) $detected[] = "Form 137 (TOR)";
                    if (isset($_FILES['good_moral']) && $_FILES['good_moral']['error'] === 0) $detected[] = "Good Moral Character Certificate";
                    if (isset($_FILES['barangay_clearance']) && $_FILES['barangay_clearance']['error'] === 0) $detected[] = "Barangay Clearance";

                    echo json_encode([
                        'success' => true,
                        'status' => 'success',
                        'message' => empty($detected) ? 'No secondary documents uploaded.' : 'Documents Detected: ' . implode(", ", $detected),
                        'detected_files' => $detected
                    ]);
                    break;
                    
                default:
                    echo json_encode(['success' => true, 'status' => 'success', 'message' => 'Validation bypassed.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Unsupported POST action.']);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'API Error: ' . $e->getMessage()]);
}
