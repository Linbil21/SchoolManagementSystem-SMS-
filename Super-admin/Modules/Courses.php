<?php
session_start();
require_once '../../auth/Security.php';
require_once '../../Database/config.php';
checkRole(['superadmin']);

$success_msg = "";
$error_msg = "";

// Handle CRUD Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'add') {
                $name = $_POST['course_name'];
                $code = $_POST['course_code'];
                $dept = $_POST['department'];
                
                $stmt = $pdo->prepare("INSERT INTO courses (course_name, course_code, department) VALUES (?, ?, ?)");
                $stmt->execute([$name, $code, $dept]);
                $success_msg = "Course added successfully!";
            } elseif ($_POST['action'] === 'edit') {
                $id = $_POST['course_id'];
                $name = $_POST['course_name'];
                $code = $_POST['course_code'];
                $dept = $_POST['department'];
                
                $stmt = $pdo->prepare("UPDATE courses SET course_name = ?, course_code = ?, department = ? WHERE courseId = ?");
                $stmt->execute([$name, $code, $dept, $id]);
                $success_msg = "Course updated successfully!";
            } elseif ($_POST['action'] === 'delete') {
                $id = $_POST['course_id'];
                $stmt = $pdo->prepare("DELETE FROM courses WHERE courseId = ?");
                $stmt->execute([$id]);
                $success_msg = "Course deleted successfully!";
            }
        } catch (PDOException $e) {
            $error_msg = "Error: " . $e->getMessage();
        }
    }
}

// Fetch all courses
$stmt = $pdo->query("SELECT * FROM courses ORDER BY course_name ASC");
$courses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management - Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #1648bc;
            --accent: #2563eb;
            --bg: #f8fafc;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg); display: flex; min-height: 100vh; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; overflow-x: hidden; }
        .content-area { padding: 40px; }
        .header-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-box h1 { font-size: 1.8rem; font-weight: 800; color: #1e293b; }
        .card { background: white; border-radius: 24px; padding: 30px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .btn { padding: 12px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; border: none; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--accent); transform: translateY(-2px); }
        .btn-edit { color: var(--accent); background: #eff6ff; }
        .btn-edit:hover { background: var(--accent); color: white; }
        .btn-delete { color: #ef4444; background: #fef2f2; }
        .btn-delete:hover { background: #ef4444; color: white; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase; border-bottom: 2px solid #f1f5f9; }
        td { padding: 20px 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
        .badge { padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; background: #e0e7ff; color: #4338ca; }

        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
        .modal-content { background: white; margin: 10vh auto; width: 90%; max-width: 500px; border-radius: 32px; padding: 40px; animation: slideUp 0.3s ease-out; }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 8px; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; outline: none; transition: 0.3s; }
        .form-group input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
    </style>
</head>
<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <div class="content-area">
            <div class="header-box">
                <div>
                    <h1>Course Management</h1>
                    <p style="color: #64748b;">Manage academic programs and curriculum.</p>
                </div>
                <button class="btn btn-primary" onclick="openModal('add')">
                    <i class="fas fa-plus"></i> Add New Course
                </button>
            </div>

            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th>Code</th>
                            <th>Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                        <tr>
                            <td style="font-weight: 700; color: #1e293b;"><?php echo htmlspecialchars($course->course_name); ?></td>
                            <td><span class="badge"><?php echo htmlspecialchars($course->course_code); ?></span></td>
                            <td style="color: #64748b;"><?php echo htmlspecialchars($course->department); ?></td>
                            <td>
                                <div style="display: flex; gap: 10px;">
                                    <button class="btn btn-edit" title="Edit" onclick='openModal("edit", <?php echo json_encode($course); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-delete" title="Delete" onclick="deleteCourse(<?php echo $course->courseId; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="courseModal" class="modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2 id="modalTitle" style="font-weight: 800;">Add Course</h2>
                <i class="fas fa-times" style="cursor: pointer; color: #94a3b8;" onclick="closeModal()"></i>
            </div>
            <form method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="course_id" id="courseId">
                <div class="form-group">
                    <label>Course Full Name</label>
                    <input type="text" name="course_name" id="courseName" required placeholder="e.g. BS Information Technology">
                </div>
                <div class="form-group">
                    <label>Course Code</label>
                    <input type="text" name="course_code" id="courseCode" required placeholder="e.g. BSIT">
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <select name="department" id="courseDept" required>
                        <option value="College of Computer Studies">College of Computer Studies</option>
                        <option value="College of Business">College of Business</option>
                        <option value="College of Criminology">College of Criminology</option>
                        <option value="College of Hospitality">College of Hospitality</option>
                        <option value="College of Engineering">College of Engineering</option>
                        <option value="College of Education">College of Education</option>
                    </select>
                </div>
                <div style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(type, data = null) {
            const modal = document.getElementById('courseModal');
            document.getElementById('formAction').value = type;
            document.getElementById('modalTitle').textContent = type === 'add' ? 'Add New Course' : 'Edit Course';
            
            if (data) {
                document.getElementById('courseId').value = data.courseId;
                document.getElementById('courseName').value = data.course_name;
                document.getElementById('courseCode').value = data.course_code;
                document.getElementById('courseDept').value = data.department;
            } else {
                document.getElementById('courseId').value = '';
                document.getElementById('courseName').value = '';
                document.getElementById('courseCode').value = '';
                document.getElementById('courseDept').value = 'College of Computer Studies';
            }
            
            modal.style.display = 'block';
        }

        function closeModal() {
            document.getElementById('courseModal').style.display = 'none';
        }

        function deleteCourse(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently remove the course!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#1648bc',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `<input type="hidden" name="action" value="delete"><input type="hidden" name="course_id" value="${id}">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }

        // Show alerts if needed
        <?php if ($success_msg): ?>
            Swal.fire('Success!', '<?php echo $success_msg; ?>', 'success');
        <?php endif; ?>
        <?php if ($error_msg): ?>
            Swal.fire('Error!', '<?php echo $error_msg; ?>', 'error');
        <?php endif; ?>
    </script>
</body>
</html>
