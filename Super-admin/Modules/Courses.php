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
    <link rel="stylesheet" href="../Assets/super-admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #1648bc;
            --primary-dark: #0f3a8f;
            --accent: #2563eb;
            --success: #10b981;
            --danger: #ef4444;
            --bg: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%);
            --text-main: #0f172a;
            --text-muted: #64748b;
        }
        
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Poppins', sans-serif; 
        }
        
        body { 
            background: var(--bg); 
            display: flex; 
            min-height: 100vh; 
        }
        
        .main-wrapper { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            overflow-x: hidden; 
        }
        
        .content-area { 
            padding: 40px; 
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .header-box { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 35px; 
            animation: slideDown 0.5s ease-out;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .header-box h1 { 
            font-size: 2.2rem; 
            font-weight: 900; 
            background: linear-gradient(135deg, #1e293b 0%, #1648bc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }
        
        .header-box p {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-top: 5px;
            font-weight: 500;
        }
        
        .card { 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 28px; 
            padding: 35px; 
            box-shadow: 0 20px 40px -10px rgba(22, 72, 188, 0.15),
                        0 0 0 1px rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.8);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 30px 60px -15px rgba(22, 72, 188, 0.25),
                        0 0 0 1px rgba(255, 255, 255, 0.8);
        }
        
        .btn { 
            padding: 14px 28px; 
            border-radius: 14px; 
            font-weight: 700; 
            font-size: 0.9rem;
            cursor: pointer; 
            border: none; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            display: inline-flex; 
            align-items: center; 
            gap: 10px; 
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-primary { 
            background: linear-gradient(135deg, #1648bc 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 10px 25px -5px rgba(22, 72, 188, 0.4),
                        0 0 0 1px rgba(37, 99, 235, 0.1);
        }
        
        .btn-primary:hover { 
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px -5px rgba(22, 72, 188, 0.5),
                        0 0 0 1px rgba(37, 99, 235, 0.2);
        }
        
        .btn-primary:active {
            transform: translateY(-1px) scale(0.98);
        }
        
        .btn-edit { 
            color: var(--accent); 
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            padding: 10px 16px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }
        
        .btn-edit:hover { 
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }
        
        .btn-delete { 
            color: var(--danger); 
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            padding: 10px 16px;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
        }
        
        .btn-delete:hover { 
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
        }
        
        table { 
            width: 100%; 
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px; 
        }
        
        th { 
            text-align: left; 
            padding: 18px 20px; 
            color: var(--text-muted); 
            font-size: 0.8rem; 
            text-transform: uppercase; 
            font-weight: 700;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 2px solid #e2e8f0;
        }
        
        th:first-child {
            border-top-left-radius: 12px;
        }
        
        th:last-child {
            border-top-right-radius: 12px;
        }
        
        td { 
            padding: 22px 20px; 
            border-bottom: 1px solid #f1f5f9; 
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        tr:hover td {
            background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
        }
        
        tr:last-child td:first-child {
            border-bottom-left-radius: 12px;
        }
        
        tr:last-child td:last-child {
            border-bottom-right-radius: 12px;
        }
        
        .badge { 
            padding: 8px 16px; 
            border-radius: 10px; 
            font-size: 0.75rem; 
            font-weight: 800; 
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #4338ca;
            box-shadow: 0 4px 10px rgba(67, 56, 202, 0.2);
            display: inline-block;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .modal { 
            display: none; 
            position: fixed; 
            z-index: 2000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(8px);
        }
        
        .modal-content { 
            background: white; 
            margin: 8vh auto; 
            width: 90%; 
            max-width: 550px; 
            border-radius: 32px; 
            padding: 45px; 
            animation: modalPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 30px 60px -10px rgba(0, 0, 0, 0.3),
                        0 0 0 1px rgba(255, 255, 255, 0.5);
        }
        
        @keyframes modalPop { 
            from { transform: scale(0.8) translateY(30px); opacity: 0; } 
            to { transform: scale(1) translateY(0); opacity: 1; } 
        }
        
        .form-group { 
            margin-bottom: 24px; 
        }
        
        .form-group label { 
            display: block; 
            font-size: 0.85rem; 
            font-weight: 700; 
            color: #334155; 
            margin-bottom: 10px;
            letter-spacing: 0.3px;
        }
        
        .form-group input, 
        .form-group select { 
            width: 100%; 
            padding: 14px 16px; 
            border-radius: 14px; 
            border: 2px solid #e2e8f0; 
            outline: none; 
            transition: all 0.3s ease;
            font-size: 0.95rem;
            font-weight: 500;
            background: #f8fafc;
        }
        
        .form-group input:focus,
        .form-group select:focus { 
            border-color: var(--accent); 
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1),
                        0 8px 20px rgba(37, 99, 235, 0.15);
            transform: translateY(-2px);
        }
        
        .form-group input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }
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
