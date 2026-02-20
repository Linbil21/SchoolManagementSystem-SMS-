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
            e.id_picture,
            e.birth_cert, 
            e.form_138, 
            e.form_137,
            e.good_moral,
            e.barangay_clearance,
            e.guardian_first,
            e.guardian_last,
            e.guardian_contact,
            DATE(e.created_at) as submission_date,
            c.course_name as course_name
        FROM enrollments e
        LEFT JOIN courses c ON e.course_id = c.courseId
        ORDER BY e.created_at DESC
    ");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    // Fallback if there's no created_at or courses
    try {
        $stmt = $pdo->prepare("SELECT reference_code as student_id, first_name, last_name, status, id_picture, birth_cert, form_138, form_137, good_moral, barangay_clearance, guardian_first, guardian_last, guardian_contact FROM enrollments ORDER BY reference_code DESC");
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
        
        .btn-view { background: #1648bc; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
        .btn-view:hover { background: #1e3a8a; }

        /* Modal Styles */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.7); display: none; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px); }
        .modal-content { background: white; width: 400px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; animation: popIn 0.3s ease-out forwards; }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
        .modal-header h3 { margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700; }
        .btn-close { background: none; border: none; font-size: 1.2rem; color: #94a3b8; cursor: pointer; }
        .btn-close:hover { color: #ef4444; }
        .modal-body { padding: 24px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }
        
        .req-item { display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; }
        .req-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; }
        .req-icon.yes { background: #dcfce7; color: #16a34a; }
        .req-icon.no { background: #fee2e2; color: #ef4444; }
        
        @keyframes popIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
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
                            <th>Course</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: #64748b;">No enrollment records found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($student->student_id); ?></strong></td>
                                    <td><?php echo htmlspecialchars($student->first_name . ' ' . $student->last_name); ?></td>
                                    <td>
                                        <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;"><?php echo htmlspecialchars($student->course_name ?? 'N/A'); ?></div>
                                    </td>
                                    <td>
                                        <span style="background: #eef2ff; color: #1648bc; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                                            <?php echo htmlspecialchars($student->status ?? 'Enrollment'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-view" onclick="openViewModal(
                                            '<?php echo addslashes($student->first_name . ' ' . $student->last_name); ?>', 
                                            '<?php echo !empty($student->id_picture) ? 'yes' : 'no'; ?>', 
                                            '<?php echo !empty($student->birth_cert) ? 'yes' : 'no'; ?>', 
                                            '<?php echo !empty($student->form_138) ? 'yes' : 'no'; ?>', 
                                            '<?php echo !empty($student->form_137) ? 'yes' : 'no'; ?>', 
                                            '<?php echo !empty($student->good_moral) ? 'yes' : 'no'; ?>',
                                            '<?php echo !empty($student->barangay_clearance) ? 'yes' : 'no'; ?>',
                                            '<?php echo addslashes(($student->guardian_first ?? '') . ' ' . ($student->guardian_last ?? '')); ?>',
                                            '<?php echo addslashes($student->guardian_contact ?? ''); ?>'
                                        )"><i class="fas fa-eye"></i> View</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- View Modal -->
    <div id="viewModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalStudentName">Student Name</h3>
                <button class="btn-close" onclick="closeViewModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 8px; color: #64748b; font-size: 0.85rem; font-weight: 500;">Guardian Information</p>
                <div style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
                    <div style="font-weight: 600; color: #1e293b; font-size: 0.9rem;" id="modalGuardianName">Name</div>
                    <div style="color: #64748b; font-size: 0.85rem;"><i class="fas fa-phone-alt" style="margin-right: 5px; font-size: 0.75rem;"></i> <span id="modalGuardianContact">Contact</span></div>
                </div>

                <p style="margin-bottom: 15px; color: #64748b; font-size: 0.85rem; font-weight: 500;">Submitted Requirements</p>
                <div id="modalRequirementsList" style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Items injected via JS -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-view" style="background: #f1f5f9; color: #475569;" onclick="closeViewModal()">Close</button>
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

        function openViewModal(name, idpic, psa, f138, f137, moral, brgy, gName, gContact) {
            document.getElementById('modalStudentName').textContent = name;
            
            document.getElementById('modalGuardianName').textContent = gName.trim() ? gName : 'Not Provided';
            document.getElementById('modalGuardianContact').textContent = gContact.trim() ? gContact : 'No Contact Number';
            
            const docs = [
                { title: 'Passport Size ID', status: idpic },
                { title: 'PSA Birth Certificate', status: psa },
                { title: 'Form 138 (Report Card)', status: f138 },
                { title: 'Form 137 (TOR)', status: f137 },
                { title: 'Good Moral Certificate', status: moral },
                { title: 'Barangay Clearance', status: brgy }
            ];

            const list = document.getElementById('modalRequirementsList');
            list.innerHTML = '';

            docs.forEach(doc => {
                const iconClass = doc.status === 'yes' ? 'fa-check' : 'fa-times';
                const colorClass = doc.status === 'yes' ? 'yes' : 'no';
                const statusText = doc.status === 'yes' ? '<span style="color: #16a34a; font-size: 0.8rem; font-weight: 600;">Submitted</span>' : '<span style="color: #ef4444; font-size: 0.8rem; font-weight: 600;">Missing</span>';
                
                list.innerHTML += `
                    <div class="req-item">
                        <div class="req-icon ${colorClass}">
                            <i class="fas ${iconClass}"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; font-size: 0.9rem; color: #1e293b;">${doc.title}</div>
                        </div>
                        <div>
                            ${statusText}
                        </div>
                    </div>
                `;
            });

            document.getElementById('viewModal').style.display = 'flex';
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }
    </script>
</body>
</html>
