<?php
/**
 * Student Integration API
 * Access restricted via API Public Key
 */

header('Content-Type: application/json');
require_once '../Database/config.php';

// Action Handler
$action = $_GET['action'] ?? 'list_enrollments';

try {
    switch ($action) {
        case 'list_enrollments':
            // Fetch the enrollment list with basic student information
            $stmt = $pdo->query("SELECT 
                                    e.enrollmentId,
                                    e.reference_code,
                                    e.first_name,
                                    e.last_name,
                                    e.middle_name,
                                    e.status,
                                    e.admission_type,
                                    e.year_level,
                                    e.email,
                                    c.course_name,
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

        case 'get_enrollment':
            $ref = $_GET['ref'] ?? '';
            if (!$ref) {
                throw new Exception('Reference code is required.');
            }
            
            $stmt = $pdo->prepare("SELECT e.*, c.course_name FROM enrollments e LEFT JOIN courses c ON e.course_id = c.courseId WHERE e.reference_code = ?");
            $stmt->execute([$ref]);
            $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($enrollment) {
                echo json_encode([
                    'success' => true,
                    'data' => $enrollment
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Enrollment not found.'
                ]);
            }
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action.'
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'API Error: ' . $e->getMessage()
    ]);
}
