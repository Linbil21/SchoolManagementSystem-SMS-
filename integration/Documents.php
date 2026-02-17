<?php
header('Content-Type: application/json');
require_once '../Database/config.php';
require_once '../auth/OcrProcessor.php';

/**
 * Documents Integration API
 * Handles multi-file validation and OCR verification during enrollment steps
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$action = $_POST['action'] ?? 'validate_step';
$ocr = new OcrProcessor();

try {
    if ($action === 'validate_step') {
        $step = $_POST['step'] ?? '';
        
        switch ($step) {
            case 'primary_docs':
                // Validate required files for Step 2
                $required = ['id_picture']; // Minimum requirement
                $missing = [];
                
                foreach ($required as $field) {
                    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== 0) {
                        $missing[] = $field;
                    }
                }
                
                if (!empty($missing)) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Required documents are missing.',
                        'missing' => $missing
                    ]);
                    exit;
                }
                
                // If files are present, we can perform a quick check
                // For example, validating the ID picture format via OCR simulation
                if (isset($_FILES['id_picture'])) {
                    $result = $ocr->scanDocument($_FILES['id_picture']['tmp_name'], 'id_picture', $_FILES['id_picture']['name']);
                    if (isset($result['error'])) {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'ID Photo Validation Failed: ' . $result['error']
                        ]);
                        exit;
                    }
                }
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Primary documents validated successfully.'
                ]);
                break;
                
            default:
                echo json_encode(['status' => 'success', 'message' => 'Validation bypassed.']);
        }
    } else {
         echo json_encode(['status' => 'error', 'message' => 'Unsupported action.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Internal Server Error: ' . $e->getMessage()]);
}
