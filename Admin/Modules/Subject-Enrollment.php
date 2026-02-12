<?php
session_start();
require_once '../../Database/config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: ../../auth/Login.php");
    exit();
}

$role = $_SESSION['role'];

// 1. Handle Add Subject POST (Super Admin Only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subject'])) {
    if ($role !== 'superadmin') {
        header("Location: Subject-Enrollment.php?error=unauthorized");
        exit();
    }
    $code = $_POST['subject_code'];
    $name = $_POST['subject_name'];
    $units = $_POST['units'];
    $year = $_POST['year_level'];
    $sem = $_POST['semester'];

    try {
        $stmt = $pdo->prepare("INSERT INTO subjects (subject_code, subject_name, units, year_level, semester) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$code, $name, $units, $year, $sem]);
        header("Location: Subject-Enrollment.php?success=1");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to add subject. Code might already exist.";
    }
}

// 2. Handle Edit Subject POST (Super Admin Only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_subject'])) {
    if ($role !== 'superadmin') {
        header("Location: Subject-Enrollment.php?error=unauthorized");
        exit();
    }
    $id = $_POST['subjectId'];
    $code = $_POST['subject_code'];
    $name = $_POST['subject_name'];
    $units = $_POST['units'];
    $year = $_POST['year_level'];
    $sem = $_POST['semester'];

    try {
        $stmt = $pdo->prepare("UPDATE subjects SET subject_code=?, subject_name=?, units=?, year_level=?, semester=? WHERE subjectId=?");
        $stmt->execute([$code, $name, $units, $year, $sem, $id]);
        header("Location: Subject-Enrollment.php?updated=1");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to update subject.";
    }
}

// 3. Handle Delete Subject (Super Admin Only)
if (isset($_GET['delete'])) {
    if ($role !== 'superadmin') {
        header("Location: Subject-Enrollment.php?error=unauthorized");
        exit();
    }
    try {
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE subjectId = ?");
        $stmt->execute([$_GET['delete']]);
        header("Location: Subject-Enrollment.php?deleted=1");
        exit();
    } catch (PDOException $e) {
        $error = "Cannot delete: Subject is linked to other records.";
    }
}

// Fetch Subjects
try {
    $stmt = $pdo->query("SELECT * FROM subjects ORDER BY year_level, semester");
    $subjects = $stmt->fetchAll();
} catch (PDOException $e) {
    $subjects = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Enrollment - SMS</title>
    <link rel="icon" type="image/png" href="../../Assets/image/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include '../Components/Side-bar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/Head-bar.php'; ?>
        <div class="content-area">
            <?php if (isset($_GET['success'])): ?>
                <script>Swal.fire('Added!', 'Subject added successfully.', 'success');</script>
            <?php endif; ?>
            <?php if (isset($_GET['updated'])): ?>
                <script>Swal.fire('Updated!', 'Subject updated successfully.', 'success');</script>
            <?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?>
                <script>Swal.fire('Deleted!', 'Subject has been removed.', 'success');</script>
            <?php endif; ?>

            <div class="table-container">
                <div class="table-header">
                    <h2>Subject Enrollment</h2>
                    <?php if ($role === 'superadmin'): ?>
                        <button class="btn-view" id="btnAddSubject"><i class="fas fa-plus"></i> Add Subject</button>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Units</th>
                                <th>Schedule Info</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subjects as $subject): ?>
                                <tr>
                                    <td class="ref-code"><?php echo htmlspecialchars($subject->subject_code); ?></td>
                                    <td class="student-name"><?php echo htmlspecialchars($subject->subject_name); ?></td>
                                    <td><?php echo htmlspecialchars($subject->units); ?></td>
                                    <td><span class="status-badge"
                                            style="background:#f1f5f9; color:#475569;"><?php echo htmlspecialchars($subject->year_level . " | " . $subject->semester); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($role === 'superadmin'): ?>
                                            <button class="btn-view" style="padding: 6px 12px; font-size: 0.8rem;"
                                                onclick='openEditModal(<?php echo json_encode($subject); ?>)'><i class="fas fa-edit"></i> Edit</button>
                                            <button class="btn-reject" style="padding: 6px 12px; font-size: 0.8rem;"
                                                onclick="confirmDelete(<?php echo $subject->subjectId; ?>)"><i class="fas fa-trash"></i> Delete</button>
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

    <!-- Add/Edit Modal -->
    <div id="subjectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalLabel"><i class="fas fa-book-medical"></i> Add New Subject</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form action="" method="POST">
                <input type="hidden" name="subjectId" id="field_id">
                <div class="modal-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <label class="info-label">Subject Code</label>
                            <input type="text" name="subject_code" id="field_code" class="form-control" required
                                style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                        </div>
                        <div class="info-item">
                            <label class="info-label">Subject Name</label>
                            <input type="text" name="subject_name" id="field_name" class="form-control" required
                                style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                        </div>
                        <div class="info-item">
                            <label class="info-label">Units</label>
                            <input type="number" name="units" id="field_units" class="form-control" required
                                style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:6px;">
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
                            <label class="info-label">Semester</label>
                            <select name="semester" id="field_sem" required
                                style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                                <option value="First Semester">First Semester</option>
                                <option value="Second Semester">Second Semester</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-reject" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="add_subject" id="submitBtn" class="btn-approve">Save Subject</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById("subjectModal");

        document.getElementById("btnAddSubject").onclick = function () {
            document.getElementById("modalLabel").innerHTML = '<i class="fas fa-book-medical"></i> Add New Subject';
            document.getElementById("submitBtn").name = "add_subject";
            document.getElementById("field_id").value = "";
            document.getElementById("field_code").value = "";
            document.getElementById("field_name").value = "";
            document.getElementById("field_units").value = "";
            modal.style.display = "block";
        }

        function openEditModal(data) {
            document.getElementById("modalLabel").innerHTML = '<i class="fas fa-edit"></i> Edit Subject';
            document.getElementById("submitBtn").name = "edit_subject";
            document.getElementById("field_id").value = data.subjectId;
            document.getElementById("field_code").value = data.subject_code;
            document.getElementById("field_name").value = data.subject_name;
            document.getElementById("field_units").value = data.units;
            document.getElementById("field_year").value = data.year_level;
            document.getElementById("field_sem").value = data.semester;
            modal.style.display = "block";
        }

        function closeModal() { modal.style.display = "none"; }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Removing this subject might affect enrollment records!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = 'Subject-Enrollment.php?delete=' + id;
            });
        }

        window.onclick = function (event) { if (event.target == modal) closeModal(); }
    </script>
</body>

</html>