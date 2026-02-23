<?php
require_once __DIR__ . '/../../auth/Security.php';
checkRole(['admission']);


require_once '../../Database/config.php';

// Robust absolute-relative path logic
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/Super-admin/', '/Admin/', '/Cashier/', '/Admission/', '/auth/', '/student/', '/modules/'];
$project_base = '';

foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        $project_base = rtrim(substr($script_name, 0, $pos), '/');
        break;
    }
}
$root_path = $project_base . '/';

// Handle AJAX Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_requirement') {
    header('Content-Type: application/json');
    $id = $_POST['id'] ?? null;

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Student ID missing.']);
        exit;
    }

    try {
        $pdo->beginTransaction();
        
        // 1. Get the enrollmentId from the reference_code
        $getStmt = $pdo->prepare("SELECT enrollmentId FROM enrollments WHERE reference_code = ?");
        $getStmt->execute([$id]);
        $enrollment = $getStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($enrollment) {
            $eId = $enrollment['enrollmentId'];
            
            // 2. Delete from payments first
            $delPay = $pdo->prepare("DELETE FROM payments WHERE enrollment_id = ?");
            $delPay->execute([$eId]);
            
            // 3. Delete the enrollment record
            $stmt = $pdo->prepare("DELETE FROM enrollments WHERE enrollmentId = ?");
            $stmt->execute([$eId]);
            
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Requirement record and associated payments deleted.']);
        } else {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Record not found.']);
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()]);
    }
    exit;
}

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
            COALESCE(c.course_name, CAST(e.course_id AS CHAR), 'N/A') as course_name
        FROM enrollments e
        LEFT JOIN courses c ON e.course_id = c.courseId
        ORDER BY e.created_at DESC
    ");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    // Fallback if there's no created_at or courses
    try {
        $stmt = $pdo->prepare("SELECT reference_code as student_id, first_name, last_name, status, course_id, id_picture, birth_cert, form_138, form_137, good_moral, barangay_clearance, guardian_first, guardian_last, guardian_contact FROM enrollments ORDER BY reference_code DESC");
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
    <title>Requirements List - Admission</title>
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

        /* Robust Modal Styles */
        .student-modal-overlay { 
            position: fixed !important; 
            top: 0 !important; 
            left: 0 !important; 
            width: 100vw !important; 
            height: 100vh !important; 
            background: rgba(15, 23, 42, 0.85) !important; 
            display: none; 
            align-items: center; 
            justify-content: center; 
            z-index: 999999 !important; 
            backdrop-filter: blur(8px) !important; 
            padding: 20px;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .student-modal-content { 
            background: white !important; 
            width: 100%;
            max-width: 550px; 
            border-radius: 28px; 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); 
            overflow: hidden; 
            position: relative;
            z-index: 1000000 !important;
        }

        .student-modal-header { 
            padding: 24px 32px; 
            border-bottom: 1px solid #edf2f7; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            background: #f8fafc; 
        }

        .student-btn-close { 
            width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
            background: #f1f5f9; border: none; border-radius: 12px; color: #64748b; cursor: pointer;
        }

        .student-modal-body { 
            padding: 32px; max-height: 70vh; overflow-y: auto; overflow-x: hidden;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        .student-modal-body::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        .student-modal-footer { 
            padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; 
        }

        /* Premium Modal Components */
        .modal-profile-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
            background: linear-gradient(135deg, #1648bc 0%, #1e3a8a 100%);
            border-radius: 24px;
            margin-bottom: 28px;
            text-align: center;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(22, 72, 188, 0.3);
        }

        .modal-avatar {
            width: 110px;
            height: 110px;
            border-radius: 35px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2.5rem;
            color: white;
            margin-bottom: 18px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .modal-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .guardian-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 20px;
            border: 1px solid #eef2ff;
            margin-bottom: 28px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .req-item { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 14px; 
            background: #ffffff;
            border: 1px solid #f1f5f9; 
            border-radius: 18px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .req-item.clickable {
            cursor: pointer;
        }

        .req-item.clickable:hover {
            border-color: #1648bc;
            background: #f8faff;
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        /* Grid specific styles */
        #modalRequirementsList {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 28px;
        }

        @media (max-width: 640px) {
            #modalRequirementsList {
                grid-template-columns: 1fr;
            }
        }

        .req-icon { 
            width: 45px; 
            height: 45px; 
            border-radius: 14px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.1rem; 
        }

        .req-icon.yes { background: #dcfce7; color: #16a34a; }
        .req-icon.no { background: #fee2e2; color: #ef4444; }

        .btn-view-doc {
            padding: 6px 12px;
            border-radius: 10px;
            background: #eef2ff;
            color: #1648bc;
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid #dbeafe;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        .req-item.active {
            border-color: #1648bc;
            background: #eef2ff;
            border-width: 2px;
        }
    </style>
</head>

<body>
    <!-- View Modal -->
    <div id="viewModal" class="student-modal-overlay">
        <div class="student-modal-content">
            <div class="student-modal-header">
                <h3>Requirement Details</h3>
                <button class="student-btn-close" onclick="closeViewModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="student-modal-body">
                <div class="modal-profile-box">
                    <div class="modal-avatar" id="modalAvatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2 id="modalStudentName" style="font-weight: 800; font-size: 1.6rem; margin-bottom: 2px;">Student Name</h2>
                    <div id="modalStudentID" style="opacity: 0.8; font-weight: 600; font-size: 0.95rem; margin-bottom: 12px;">ENR-2024-001</div>
                    <div id="modalCourse" style="background: rgba(255, 255, 255, 0.2); color: white; padding: 6px 18px; border-radius: 99px; font-size: 0.8rem; font-weight: 700; backdrop-filter: blur(5px); border: 1px solid rgba(255, 255, 255, 0.3);">Course Name</div>
                </div>

                <p style="margin-bottom: 12px; color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Guardian Details</p>
                <div class="guardian-card">
                    <div style="font-weight: 700; color: #1e293b; font-size: 1rem; margin-bottom: 4px;" id="modalGuardianName">Name</div>
                    <div style="color: #64748b; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-phone-alt" style="color: #1648bc;"></i> 
                        <span id="modalGuardianContact" style="font-weight: 500;">Contact</span>
                    </div>
                </div>

                <p style="margin-bottom: 12px; color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Submitted Documents</p>
                <div id="modalRequirementsList">
                </div>

                <!-- Preview Area -->
                <div id="modalPreviewArea" style="display: none; border-top: 2px dashed #e2e8f0; padding-top: 20px;">
                    <p style="margin-bottom: 12px; color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Document Preview</p>
                    <div id="previewContent" style="width: 100%; min-height: 200px; border-radius: 16px; overflow: hidden; background: #f1f5f9; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                        <!-- Preview injected here -->
                    </div>
                </div>
            </div>
            <div class="student-modal-footer">
                <button class="btn-view" style="background: #f1f5f9; color: #475569;" onclick="closeViewModal()">Close</button>
            </div>
        </div>
    </div>
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
                                        <?php 
                                            $studentData = [
                                                'name' => $student->first_name . ' ' . $student->last_name,
                                                'id' => $student->student_id,
                                                'course' => $student->course_name ?? 'N/A',
                                                'avatar' => $student->id_picture,
                                                'docs' => [
                                                    'id_pic' => $student->id_picture,
                                                    'psa' => $student->birth_cert,
                                                    'f138' => $student->form_138,
                                                    'f137' => $student->form_137,
                                                    'moral' => $student->good_moral,
                                                    'brgy' => $student->barangay_clearance
                                                ],
                                                'guardian' => [
                                                    'name' => ($student->guardian_first ?? '') . ' ' . ($student->guardian_last ?? ''),
                                                    'contact' => $student->guardian_contact ?? ''
                                                ]
                                            ];
                                        ?>
                                        <div style="display: flex; gap: 5px;">
                                            <button class="btn-view" onclick="openStudentModal(<?php echo htmlspecialchars(json_encode($studentData), ENT_QUOTES, 'UTF-8'); ?>)">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button onclick="deleteRequirement('<?php echo $student->student_id; ?>', '<?php echo addslashes($student->first_name . ' ' . $student->last_name); ?>')" 
                                                style="border: none; background: #fee2e2; color: #ef4444; padding: 8px 12px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s;"
                                                title="Delete Requirement Record">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    
    <?php include '../Components/GlobalScripts.php'; ?>
    <script>
        window.onclick = function (event) {
            const modal = document.getElementById('viewModal');
            if (event.target == modal) {
                closeViewModal();
            }
        }

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

    function openStudentModal(data) {
        try {
            console.log('Opening student modal...', data);
            document.getElementById('modalStudentName').textContent = data.name;
            document.getElementById('modalStudentID').textContent = data.id;
            document.getElementById('modalCourse').textContent = data.course;
            
            // Avatar handling
            const avatarBox = document.getElementById('modalAvatar');
            if (data.avatar && data.avatar.trim()) {
                const rootPath = '<?php echo $root_path; ?>';
                const avatarPath = data.avatar.startsWith('/') ? data.avatar : rootPath + data.avatar;
                avatarBox.innerHTML = `<img src="${avatarPath}" alt="Avatar">`;
            } else {
                avatarBox.innerHTML = `<i class="fas fa-user"></i>`;
            }

            document.getElementById('modalGuardianName').textContent = (data.guardian.name || '').trim() ? data.guardian.name : 'Not Provided';
            document.getElementById('modalGuardianContact').textContent = (data.guardian.contact || '').trim() ? data.guardian.contact : 'No Contact Number';
            
            const docs = [
                { title: 'Passport Size ID', path: data.docs.id_pic },
                { title: 'PSA Birth Certificate', path: data.docs.psa },
                { title: 'Form 138 (Report Card)', path: data.docs.f138 },
                { title: 'Form 137 (TOR)', path: data.docs.f137 },
                { title: 'Good Moral Certificate', path: data.docs.moral },
                { title: 'Barangay Clearance', path: data.docs.brgy }
            ];

            const list = document.getElementById('modalRequirementsList');
            list.innerHTML = '';

            docs.forEach(doc => {
                const isSubmitted = doc.path && String(doc.path).trim() !== '' && String(doc.path).toLowerCase() !== 'null';
                const iconClass = isSubmitted ? 'fa-check' : 'fa-times';
                const colorClass = isSubmitted ? 'yes' : 'no';
                const statusText = isSubmitted 
                    ? `<span style="color: #16a34a; font-size: 0.8rem; font-weight: 700;">Submitted</span>` 
                    : `<span style="color: #ef4444; font-size: 0.8rem; font-weight: 700;">Missing</span>`;
                
                const rootPath = '<?php echo $root_path; ?>';
                const docPath = doc.path.startsWith('http') ? doc.path : (doc.path.startsWith('/') ? doc.path : rootPath + doc.path);
                const clickAttr = isSubmitted ? `onclick="previewDocument('${docPath}', this)"` : '';
                const clickableClass = isSubmitted ? 'clickable' : '';

                list.innerHTML += `
                    <div class="req-item ${clickableClass}" ${clickAttr}>
                        <div class="req-icon ${colorClass}">
                            <i class="fas ${iconClass}"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 700; font-size: 0.85rem; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${doc.title}</div>
                            ${statusText}
                        </div>
                    </div>
                `;
            });

            // Reset preview
            document.getElementById('modalPreviewArea').style.display = 'none';
            document.getElementById('previewContent').innerHTML = '';

            document.getElementById('viewModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        } catch (err) {
            console.error('Error opening student modal:', err);
            alert('Could not open modal. Please check console for details.');
        }
    }

    function previewDocument(path, el) {
        // Visual Feedback
        document.querySelectorAll('.req-item').forEach(i => i.classList.remove('active'));
        if(el) el.classList.add('active');

        const previewArea = document.getElementById('modalPreviewArea');
        const previewContent = document.getElementById('previewContent');
        
        previewArea.style.display = 'block';
        previewContent.innerHTML = '<div style="padding: 20px; color: #64748b;"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
        
        const ext = path.split('.').pop().toLowerCase();
        
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            previewContent.innerHTML = `<img src="${path}" style="max-width: 100%; max-height: 500px; object-fit: contain; border-radius: 8px;">`;
        } else if (ext === 'pdf') {
            previewContent.innerHTML = `<iframe src="${path}" style="width: 100%; height: 500px; border: none; border-radius: 8px;"></iframe>`;
        } else {
            previewContent.innerHTML = `
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-file-alt" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                    <p style="color: #64748b; font-weight: 500;">Preview not available for this file type.</p>
                    <a href="${path}" target="_blank" style="color: #1648bc; text-decoration: underline; font-weight: 700; display: inline-block; margin-top: 10px;">Open in New Tab</a>
                </div>
            `;
        }
        
        // Scroll to preview
        previewArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        async function deleteRequirement(id, name) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete the requirement record for ${name}. This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#718096',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete_requirement');
                formData.append('id', id);

                try {
                    const response = await fetch('Student-Requirements.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        Swal.fire('Deleted!', data.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error!', 'Something went wrong while deleting.', 'error');
                }
            }
        }
    </script>
</body>
</html>
