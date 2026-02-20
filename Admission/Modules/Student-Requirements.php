<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}

require_once '../../Database/config.php';

try {
    // Fetch students who have submitted enrollment applications
    $stmt = $pdo->prepare("
        SELECT 
            e.reference_code as student_id, 
            e.first_name, 
            e.last_name, 
            e.status,
            e.birth_cert, 
            e.form_138, 
            e.good_moral,
            DATE(e.created_at) as submission_date,
            c.course_name as course_name
        FROM enrollments e
        LEFT JOIN courses c ON e.course_id = c.course_id
        ORDER BY e.created_at DESC
    ");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    // Fallback if there's no created_at or courses
    try {
        $stmt = $pdo->prepare("SELECT reference_code as student_id, first_name, last_name, status, birth_cert, form_138, good_moral FROM enrollments ORDER BY reference_code DESC");
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $ex) {
        $students = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Requirements List - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-blue: #1648bc; --bg-light: #f7fafc; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        .content-area { padding: 30px; }
        .module-header { margin-bottom: 25px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02); margin-bottom: 25px; }
        .search-row { display: flex; gap: 15px; margin-bottom: 25px; align-items: flex-end; }
        .input-group { flex: 1; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #4a5568; }
        .input-group input { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; }
        .req-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .req-table th { background: #f8fafc; text-align: left; padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f7; }
        .req-table td { padding: 15px; border-bottom: 1px solid #edf2f7; color: #2d3748; font-size: 0.95rem; vertical-align: middle; }
        
        .doc-badge { 
            display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; 
            font-weight: 600; margin-right: 5px; margin-bottom: 5px; 
            background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;
        }
        .doc-badge.submitted { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
        .doc-badge.missing { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
        
        .btn-view { background: #1648bc; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-view:hover { background: #1e3a8a; }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>Student Requirements List</h1>
                <p>View the list of students who have submitted their registration requirements.</p>
            </div>
            
            <div class="card">
                <div class="search-row">
                    <div class="input-group">
                        <label>Search Student</label>
                        <input type="text" id="searchInput" placeholder="Search ID or Name..." onkeyup="filterTable()">
                    </div>
                </div>

                <table class="req-table" id="studentsTable">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Status/Course</th>
                            <th>Submitted Documents</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 30px; color: #64748b;">No enrollment records found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($student->student_id); ?></strong></td>
                                    <td><?php echo htmlspecialchars($student->first_name . ' ' . $student->last_name); ?></td>
                                    <td>
                                        <div style="font-size: 0.8rem; color: #64748b;"><?php echo htmlspecialchars($student->status ?? 'Enrollment'); ?></div>
                                        <?php if(isset($student->course_name)): ?>
                                            <div style="font-weight: 600; font-size: 0.85rem;"><?php echo htmlspecialchars($student->course_name); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="doc-badge <?php echo !empty($student->birth_cert) ? 'submitted' : 'missing'; ?>">
                                            <i class="fas <?php echo !empty($student->birth_cert) ? 'fa-check' : 'fa-times'; ?>"></i> PSA
                                        </span>
                                        <span class="doc-badge <?php echo !empty($student->form_138) ? 'submitted' : 'missing'; ?>">
                                            <i class="fas <?php echo !empty($student->form_138) ? 'fa-check' : 'fa-times'; ?>"></i> Form 138
                                        </span>
                                        <span class="doc-badge <?php echo !empty($student->good_moral) ? 'submitted' : 'missing'; ?>">
                                            <i class="fas <?php echo !empty($student->good_moral) ? 'fa-check' : 'fa-times'; ?>"></i> Good Moral
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        function filterTable() {
            var input = document.getElementById("searchInput");
            var filter = input.value.toLowerCase();
            var table = document.getElementById("studentsTable");
            var tr = table.getElementsByTagName("tr");
            for (var i = 1; i < tr.length; i++) {
                var tdId = tr[i].getElementsByTagName("td")[0];
                var tdName = tr[i].getElementsByTagName("td")[1];
                if (tdId || tdName) {
                    var txtValueId = tdId.textContent || tdId.innerText;
                    var txtValueName = tdName.textContent || tdName.innerText;
                    if (txtValueId.toLowerCase().indexOf(filter) > -1 || txtValueName.toLowerCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>
</html>
