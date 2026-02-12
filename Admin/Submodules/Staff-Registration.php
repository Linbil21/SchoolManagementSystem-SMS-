<?php
session_start();
require_once '../../Database/config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: ../../auth/Login.php");
    exit();
}

// 1. Handle Add Staff POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $role]);
        header("Location: Staff-Registration.php?success=1");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to add staff account.";
    }
}

// 2. Handle Edit Staff POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_staff'])) {
    $id = $_POST['userId'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    try {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, role=?, password=? WHERE userId=?");
            $stmt->execute([$username, $email, $role, $password, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, role=? WHERE userId=?");
            $stmt->execute([$username, $email, $role, $id]);
        }
        header("Location: Staff-Registration.php?updated=1");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to update staff account.";
    }
}

// 3. Handle Delete Staff
if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE userId = ?");
        $stmt->execute([$_GET['delete']]);
        header("Location: Staff-Registration.php?deleted=1");
        exit();
    } catch (PDOException $e) {
        $error = "Delete failed.";
    }
}

// Fetch Staff Users
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role IN ('admission', 'registrar', 'staff', 'cashier') ORDER BY created_at DESC");
    $stmt->execute();
    $staff = $stmt->fetchAll();
} catch (PDOException $e) {
    $staff = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff & Registration - SMS</title>
    <link rel="icon" type="image/x-icon" href="../../Assets/image/logo.png">
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
                <script>Swal.fire('Registered!', 'Staff account created successfully.', 'success');</script>
            <?php endif; ?>
            <?php if (isset($_GET['updated'])): ?>
                <script>Swal.fire('Updated!', 'Staff account updated successfully.', 'success');</script>
            <?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?>
                <script>Swal.fire('Deleted!', 'Staff account has been removed.', 'success');</script>
            <?php endif; ?>

            <div class="table-container">
                <div class="table-header">
                    <h2>Staff & Registration Accounts</h2>
                    <button class="btn-view" id="btnAddStaff"><i class="fas fa-user-plus"></i> Add Staff</button>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff as $s): ?>
                                <tr>
                                    <td class="student-name"><?php echo htmlspecialchars($s->username ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($s->email); ?></td>
                                    <td><span class="status-badge"
                                            style="background:#f1f5f9; color:#475569;"><?php echo ucfirst($s->role); ?></span>
                                    </td>
                                    <td><span
                                            class="status-badge <?php echo ($s->status == 'online') ? 'status-enrolled' : 'status-pending-payment'; ?>"><?php echo ucfirst($s->status); ?></span>
                                    </td>
                                    <td>
                                        <button class="btn-view" style="padding: 6px 12px; font-size: 0.8rem;"
                                            onclick='openEditModal(<?php echo json_encode($s); ?>)'>Edit</button>
                                        <button class="btn-reject" style="padding: 6px 12px; font-size: 0.8rem;"
                                            onclick="confirmDelete(<?php echo $s->userId; ?>)">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Modal -->
    <div id="staffModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalLabel"><i class="fas fa-id-badge"></i> Register New Staff</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form action="" method="POST">
                <input type="hidden" name="userId" id="field_id">
                <div class="modal-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <label class="info-label">Full Name</label>
                            <input type="text" name="username" id="field_name" required
                                style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                        </div>
                        <div class="info-item">
                            <label class="info-label">Email Address</label>
                            <input type="email" name="email" id="field_email" required
                                style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                        </div>
                        <div class="info-item">
                            <label class="info-label">Password <span id="passHint"
                                    style="font-weight:normal; font-size:0.75rem; color:var(--text-gray);">(Leave blank
                                    to keep current)</span></label>
                            <input type="password" name="password" id="field_password"
                                style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                        </div>
                        <div class="info-item">
                            <label class="info-label">Designation / Role</label>
                            <select name="role" id="field_role" required
                                style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                                <option value="staff">General Staff</option>
                                <option value="registrar">Registrar</option>
                                <option value="admission">Admission Officer</option>
                                <option value="cashier">Cashier</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-reject" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="add_staff" id="submitBtn" class="btn-approve">Register Account</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById("staffModal");

        document.getElementById("btnAddStaff").onclick = function () {
            document.getElementById("modalLabel").innerHTML = '<i class="fas fa-id-badge"></i> Register New Staff';
            document.getElementById("submitBtn").name = "add_staff";
            document.getElementById("field_password").required = true;
            document.getElementById("passHint").style.display = "none";
            document.getElementById("field_id").value = "";
            document.getElementById("field_name").value = "";
            document.getElementById("field_email").value = "";
            modal.style.display = "block";
        }

        function openEditModal(data) {
            document.getElementById("modalLabel").innerHTML = '<i class="fas fa-user-edit"></i> Edit Staff Account';
            document.getElementById("submitBtn").name = "edit_staff";
            document.getElementById("field_password").required = false;
            document.getElementById("passHint").style.display = "inline";
            document.getElementById("field_id").value = data.userId;
            document.getElementById("field_name").value = data.username;
            document.getElementById("field_email").value = data.email;
            document.getElementById("field_role").value = data.role;
            modal.style.display = "block";
        }

        function closeModal() { modal.style.display = "none"; }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting this staff account will remove their system access!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = 'Staff-Registration.php?delete=' + id;
            });
        }

        window.onclick = function (event) { if (event.target == modal) closeModal(); }
    </script>
</body>

</html>