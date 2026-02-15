<?php
header('Content-Type: application/json');
require_once 'OcrProcessor.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}

if (!isset($_FILES['document'])) {
    echo json_encode(['error' => 'No document uploaded.']);
    exit;
}

$file = $_FILES['document'];
$tempFile = $file['tmp_name'];

// Basic validation
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['error' => 'Unsupported file type. Please upload an image.']);
    exit;
}

$ocr = new OcrProcessor();
$type = $_POST['type'] ?? 'generic';
$result = $ocr->scanDocument($tempFile, $type);

echo json_encode($result);
