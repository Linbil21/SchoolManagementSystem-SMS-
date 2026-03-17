<?php
session_start();
header('Content-Type: application/json');
require_once '../../Database/config.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$student_id = $_SESSION['student_id'];

// Check if file was uploaded
if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error occurred']);
    exit();
}

$file = $_FILES['profile_image'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];
$fileType = $file['type'];

// Allowed extensions
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($fileExt, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP allowed']);
    exit();
}

// Max size: 5MB
if ($fileSize > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File is too large. Max 5MB allowed']);
    exit();
}

// Orientation Check (Portrait Required)
$img_info = getimagesize($fileTmpName);
if ($img_info && $img_info[0] >= $img_info[1]) {
    echo json_encode(['success' => false, 'message' => 'Profile image must be in PORTRAIT orientation (Vertical). Private logos or landscape images are not allowed for IDs.']);
    exit();
}

// Generate unique filename
$newFileName = 'student_' . $student_id . '_' . uniqid() . '.' . $fileExt;
$uploadDir = '../../Assets/image/uploads/students/';
$uploadPath = $uploadDir . $newFileName;

// Ensure directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Move file
if (move_uploaded_file($fileTmpName, $uploadPath)) {
    // Relative path for database
    $dbPath = 'Assets/image/uploads/students/' . $newFileName;

    try {
        // Update database
        $stmt = $pdo->prepare("UPDATE students SET profile_image = ? WHERE student_id = ?");
        $stmt->execute([$dbPath, $student_id]);

        // Update session
        $_SESSION['profile_image'] = $dbPath;

        echo json_encode([
            'success' => true, 
            'message' => 'Profile image updated successfully',
            'image_path' => $dbPath
        ]);
    } catch (PDOException $e) {
        // Cleanup file if DB update fails
        if (file_exists($uploadPath)) {
            unlink($uploadPath);
        }
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
}
?>
