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

    // DUMMY DATA FOR SECTIONS
    if (empty($sections)) {
        $sec1 = new stdClass();
        $sec1->sectionId = 9991;
        $sec1->section_name = 'BSIT-1A';
        $sec1->course_code = 'BSIT';
        $sec1->year_level = '1st Year';
        $sec1->capacity = 40;
        $sec1->course_id = 1;

        $sec2 = new stdClass();
        $sec2->sectionId = 9992;
        $sec2->section_name = 'BSIT-2A';
        $sec2->course_code = 'BSIT';
        $sec2->year_level = '2nd Year';
        $sec2->capacity = 40;
        $sec2->course_id = 1;

        $sec3 = new stdClass();
        $sec3->sectionId = 9993;
        $sec3->section_name = 'BSBA-1A';
        $sec3->course_code = 'BSBA';
        $sec3->year_level = '1st Year';
        $sec3->capacity = 35;
        $sec3->course_id = 2;

        $sections = [$sec1, $sec2, $sec3];
    }
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
    <link rel="icon" type="image/png" href="../../Assets/image/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Assets/layout.css">
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
                <script>Swal.fire('Archived!', 'Section has been archived.', 'success');</script>
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
                                                onclick="confirmArchive(<?php echo $section->sectionId; ?>)"><i class="fas fa-archive"></i> Archive</button>
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

        function confirmArchive(id) {
            Swal.fire({
                title: 'Archive this section?',
                text: "This section will be moved to archives and hidden from active lists.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                confirmButtonText: 'Yes, archive it!'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = 'Section-Assignment.php?delete=' + id;
            });
        }

        window.onclick = function (event) { if (event.target == modal) closeModal(); }
    </script>
</body>

</html>