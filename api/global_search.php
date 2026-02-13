<?php
session_start();
require_once '../Database/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$role = strtolower($_SESSION['role']);
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$results = [];
$searchTerm = "%$query%";

function addResult(&$results, $title, $subtitle, $link, $type) {
    $results[] = [
        'title' => htmlspecialchars($title),
        'subtitle' => htmlspecialchars($subtitle),
        'link' => $link,
        'type' => $type
    ];
}

try {
    // 1. STUDENTS SEARCH (All Staff Roles)
    if (in_array($role, ['superadmin', 'admin', 'admission', 'cashier'])) {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE first_name LIKE ? OR last_name LIKE ? OR student_id LIKE ? LIMIT 5");
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $link = '#';
            if ($role === 'admission') $link = '/sms/Admission/Modules/Applications-Summary.php?search=' . $row['student_id']; // Example link
            if ($role === 'cashier') $link = '/sms/Cashier/Modules/Payment-History.php?search=' . $row['student_id'];
            if ($role === 'superadmin') $link = '/sms/Admin/Submodules/Student-Accounts.php?search=' . $row['student_id'];
            
            addResult($results, 
                $row['first_name'] . ' ' . $row['last_name'], 
                'Student ID: ' . $row['student_id'] . ' • ' . $row['course'], 
                $link, 
                'student'
            );
        }
    }

    // 2. EMPLOYEES SEARCH (Superadmin & Admin)
    if (in_array($role, ['superadmin', 'admin'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? LIMIT 5");
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            addResult($results, 
                $row['first_name'] . ' ' . $row['last_name'], 
                'Staff Role: ' . ucfirst($row['role']), 
                '#', 
                'staff'
            );
        }
    }

    // 3. ENROLLMENT / APPLICANTS (Admission & Superadmin)
    if (in_array($role, ['superadmin', 'admission'])) {
        $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE reference_code LIKE ? OR last_name LIKE ? LIMIT 5");
        $stmt->execute([$searchTerm, $searchTerm]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            addResult($results, 
                $row['first_name'] . ' ' . $row['last_name'], 
                'Application Ref: ' . $row['reference_code'], 
                '/sms/Admission/Modules/New-Applications.php?id=' . $row['id'], 
                'application'
            );
        }
    }

    // 4. STUDENT PORTAL SEARCH (Student Only)
    // Search for grades, subjects, etc. is complex without knowing tables.
    // For now, map static quick links based on query.
    if ($role === 'student') {
        $navItems = [
            ['Grades', 'My Grades', '/sms/student/Modules/Academic/Grades.php'],
            ['Schedule', 'Class Schedule', '/sms/student/Modules/Academic/Schedule.php'],
            ['Balance', 'Account Balance', '/sms/student/Modules/Payments/Balance.php'],
            ['Profile', 'My Profile', '/sms/student/Submodules/profile.php']
        ];

        foreach ($navItems as $item) {
            if (stripos($item[0], $query) !== false || stripos($item[1], $query) !== false) {
                addResult($results, $item[1], 'Go to page', $item[2], 'link');
            }
        }
    }

} catch (PDOException $e) {
    // Handle specific errors log
}

echo json_encode($results);
