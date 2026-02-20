<?php
header('Content-Type: application/json');
require_once '../Database/config.php';
require_once '../auth/OcrProcessor.php';

/**
 * Documents Integration API
 * Handles multi-file validation and OCR verification during enrollment steps
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If someone tries to visit the API page directly from the browser, redirect them.
    header('Location: ../auth/Login.php');
    exit;
}

$action = $_POST['action'] ?? 'validate_step';
$ocr = new OcrProcessor();

try {
    if ($action === 'validate_step') {
        $step = $_POST['step'] ?? '';
        
        switch ($step) {
            case 'primary_docs':
                $detected = [];
                $errors = [];
                
                // 1. Check ID Picture (Required & Quality Check)
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

                // 2. Check PSA (Optional but Detect if present)
                if (isset($_FILES['birth_cert']) && $_FILES['birth_cert']['error'] === 0) {
                    $detected[] = "PSA Birth Certificate";
                }

                // 3. Check Form 138 (Optional but Detect if present)
                if (isset($_FILES['form_138']) && $_FILES['form_138']['error'] === 0) {
                    $detected[] = "Form 138 (Report Card)";
                }

                if (!empty($errors)) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => implode("\n", $errors)
                    ]);
                    exit;
                }
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Documents Detected: ' . implode(", ", $detected),
                    'detected_files' => $detected
                ]);
                break;
                
            case 'secondary_docs':
                $detected = [];
                $errors = [];
                
                // 1. Check Form 137 (Optional but Detect if present)
                if (isset($_FILES['form_137']) && $_FILES['form_137']['error'] === 0) {
                    $detected[] = "Form 137 (TOR)";
                }

                // 2. Check Good Moral (Optional but Detect if present)
                if (isset($_FILES['good_moral']) && $_FILES['good_moral']['error'] === 0) {
                    $detected[] = "Good Moral Character Certificate";
                }

                // 3. Check Barangay Clearance (Optional but Detect if present)
                if (isset($_FILES['barangay_clearance']) && $_FILES['barangay_clearance']['error'] === 0) {
                    $detected[] = "Barangay Clearance";
                }

                if (!empty($errors)) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => implode("\n", $errors)
                    ]);
                    exit;
                }
                
                $msg = empty($detected) ? 'No secondary documents uploaded. Moving forward.' : 'Documents Detected: ' . implode(", ", $detected);
                
                echo json_encode([
                    'status' => 'success',
                    'message' => $msg,
                    'detected_files' => $detected
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
