<?php
require_once 'Database/config.php';
header('Content-Type: text/plain');

try {
    // Check students with Toribio
    $stmt = $pdo->prepare("SELECT a.applicationId, a.first_name, a.last_name, e.enrollmentId, p.proof_of_payment 
                           FROM admission_applications a
                           LEFT JOIN enrollments e ON a.email = e.email
                           LEFT JOIN payments p ON e.enrollmentId = p.enrollment_id
                           WHERE a.last_name LIKE '%TORIBIO%' 
                           OR a.first_name LIKE '%LOWELL%'");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "DEBUG RESULTS FOR TORIBIO:\n";
    print_r($rows);
    
    echo "\nROBUST PATH CHECK:\n";
    $script_name = $_SERVER['SCRIPT_NAME'] ?? '/Admission/Modules/Evaluation.php';
    $check_paths = ['/Super-admin/', '/Admin/', '/Cashier/', '/Admission/', '/auth/', '/student/', '/modules/'];
    $project_base = '';
    foreach ($check_paths as $path) {
        if (($pos = stripos($script_name, $path)) !== false) {
            $project_base = rtrim(substr($script_name, 0, $pos), '/');
            break;
        }
    }
    $root_path = $project_base . '/';
    echo "Script Name: $script_name\n";
    echo "Project Base: '$project_base'\n";
    echo "Root Path: '$root_path'\n";
    
    foreach ($rows as $r) {
        $pop = $r['proof_of_payment'];
        if ($pop) {
             echo "\nTesting File: $pop\n";
             if (file_exists($pop)) echo " - EXISTS (Relative to root)\n";
             else echo " - NOT FOUND (Relative to root)\n";
             
             $abs = __DIR__ . '/' . $pop;
             if (file_exists($abs)) echo " - EXISTS (Absolute: $abs)\n";
             else echo " - NOT FOUND (Absolute: $abs)\n";
        }
    }
    
} catch (Exception $e) { echo "ERROR: " . $e->getMessage(); }
