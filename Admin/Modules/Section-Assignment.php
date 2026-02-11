<?php
session_start();
require_once '../../Database/config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: ../../auth/Login.php");
    exit();
}

$role = $_SESSION['role'];

// 1. Handle Add Section POST (Super Admin Only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_section'])) {
    if ($role !== 'superadmin') {
        header("Location: Section-Assignment.php?error=unauthorized");
        exit();
    }
    $name = $_POST['section_name'];
    $courseId = $_POST['course_id'];
    $year = $_POST['year_level'];
    $capacity = $_POST['capacity'];

    try {
        $stmt = $pdo->prepare("INSERT INTO sections (section_name, course_id, year_level, capacity) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $courseId, $year, $capacity]);
        header("Location: Section-Assignment.php?success=1");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to create section.";
    }
}

// 2. Handle Edit Section POST (Super Admin Only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_section'])) {
    if ($role !== 'superadmin') {
        header("Location: Section-Assignment.php?error=unauthorized");
        exit();
    }
    $id = $_POST['sectionId'];
    $name = $_POST['section_name'];
    $courseId = $_POST['course_id'];
    $year = $_POST['year_level'];
    $capacity = $_POST['capacity'];

    try {
        $stmt = $pdo->prepare("UPDATE sections SET section_name=?, course_id=?, year_level=?, capacity=? WHERE sectionId=?");
        $stmt->execute([$name, $courseId, $year, $capacity, $id]);
        header("Location: Section-Assignment.php?updated=1");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to update section.";
    }
}

// 3. Handle Delete Section (Super Admin Only)
if (isset($_GET['delete'])) {
    if ($role !== 'superadmin') {
        header("Location: Section-Assignment.php?error=unauthorized");
        exit();
    }
    try {
        $stmt = $pdo->prepare("DELETE FROM sections WHERE sectionId = ?");
        $stmt->execute([$_GET['delete']]);
        header("Location: Section-Assignment.php?deleted=1");
        exit();
    } catch (PDOException $e) {
        $error = "Cannot delete: Section is in use.";
    }
}

// Fetch Courses
try {
    $cStmt = $pdo->query("SELECT * FROM courses ORDER BY course_name");
    $courses = $cStmt->fetchAll();
} catch (PDOException $e) {
    $courses = [];
}

// Fetch Sections
try {
    $stmt = $pdo->query("SELECT s.*, c.course_code FROM sections s LEFT JOIN courses c ON s.course_id = c.courseId ORDER BY s.section_name");
    $sections = $stmt->fetchAll();
} catch (PDOException $e) {
    $sections = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Section Assignment - SMS</title>
    <link rel="icon" type="image/x-icon" href="../../Assets/image/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/Admin/Assets/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include '../Components/Side-bar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/Head-bar.php'; ?>
        <div class="content-area">
            <?php if (isset($_GET['success'])): ?>
                <script>Swal.fire('Created!', 'Section created successfully.', 'success');</script>
            <?php endif; ?>
            <?php if (isset($_GET['updated'])): ?>
                <script>Swal.fire('Updated!', 'Section updated successfully.', 'success');</script>
            <?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?>
                <script>Swal.fire('Deleted!', 'Section has been removed.', 'success');</script>
            <?php endif; ?>

            <div class="table-container">
                <div class="table-header">
                    <h2>Section Management</h2>
                    <?php if ($role === 'superadmin'): ?>
                        <button class="btn-view" id="btnCreateSection"><i class="fas fa-plus"></i> Create Section</button>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Section Name</th>
                                <th>Course</th>
                                <th>Year Level</th>
                                <th>Capacity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sections as $section): ?>
                                <tr>
                                    <td class="student-name"><?php echo htmlspecialchars($section->section_name); ?></td>
                                    <td><?php echo htmlspecialchars($section->course_code); ?></td>
                                    <td><?php echo htmlspecialchars($section->year_level); ?></td>
                                    <td><?php echo htmlspecialchars($section->capacity); ?> students</td>
                                    <td>
                                        <?php if ($role === 'superadmin'): ?>
                                            <button class="btn-view" style="padding: 6px 12px; font-size: 0.8rem;"
                                                onclick='openEditModal(<?php echo json_encode($section); ?>)'><i class="fas fa-edit"></i> Edit</button>
                                            <button class="btn-reject" style="padding: 6px 12px; font-size: 0.8rem;"
                                                onclick="confirmDelete(<?php echo $section->sectionId; ?>)"><i class="fas fa-trash"></i> Delete</button>
                                        <?php else: ?>
                                            <span style="color: #64748b; font-size: 0.75rem; font-style: italic;">
                                                <i class="fas fa-eye"></i> View Only
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div id="sectionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalLabel"><i class="fas fa-users-viewfinder"></i> Create New Section</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form action="" method="POST">
                <input type="hidden" name="sectionId" id="field_id">
                <div class="modal-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <label class="info-label">Section Name</label>
                            <input type="text" name="section_name" id="field_name" placeholder="e.g. BSIT-1A" required
                                style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                        </div>
                        <div class="info-item">
                            <label class="info-label">Course</label>
                            <select name="course_id" id="field_course" required
                                style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?php echo $c->courseId; ?>">
                                        <?php echo htmlspecialchars($c->course_code . " - " . $c->course_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Year Level</label>
                            <select name="year_level" id="field_year" required
                                style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                                <option value="First Year">First Year</option>
                                <option value="Second Year">Second Year</option>
                                <option value="Third Year">Third Year</option>
                                <option value="Fourth Year">Fourth Year</option>
                            </select>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Capacity</label>
                            <input type="number" name="capacity" id="field_capacity" value="40" required
                                style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-reject" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="add_section" id="submitBtn" class="btn-approve">Create Section</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById("sectionModal");

        document.getElementById("btnCreateSection").onclick = function () {
            document.getElementById("modalLabel").innerHTML = '<i class="fas fa-users-viewfinder"></i> Create New Section';
            document.getElementById("submitBtn").name = "add_section";
            document.getElementById("field_id").value = "";
            document.getElementById("field_name").value = "";
            document.getElementById("field_capacity").value = "40";
            modal.style.display = "block";
        }

        function openEditModal(data) {
            document.getElementById("modalLabel").innerHTML = '<i class="fas fa-edit"></i> Edit Section';
            document.getElementById("submitBtn").name = "edit_section";
            document.getElementById("field_id").value = data.sectionId;
            document.getElementById("field_name").value = data.section_name;
            document.getElementById("field_course").value = data.course_id;
            document.getElementById("field_year").value = data.year_level;
            document.getElementById("field_capacity").value = data.capacity;
            modal.style.display = "block";
        }

        function closeModal() { modal.style.display = "none"; }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting this section might affect student assignments!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = 'Section-Assignment.php?delete=' + id;
            });
        }

        window.onclick = function (event) { if (event.target == modal) closeModal(); }
    </script>
</body>

</html>