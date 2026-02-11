<?php
session_start();
require_once '../../Database/config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: ../../auth/Login.php");
    exit();
}

// 1. Handle Add Admin POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $role]);
        header("Location: Admin-Users.php?success=1");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to add admin user.";
    }
}

// 2. Handle Edit Admin POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_admin'])) {
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
        header("Location: Admin-Users.php?updated=1");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to update admin user.";
    }
}

// 3. Handle Delete Admin
if (isset($_GET['delete'])) {
    try {
        // Prevent self-deletion
        if ($_GET['delete'] == $_SESSION['userId']) {
            header("Location: Admin-Users.php?error=self_delete");
            exit();
        }
        $stmt = $pdo->prepare("DELETE FROM users WHERE userId = ?");
        $stmt->execute([$_GET['delete']]);
        header("Location: Admin-Users.php?deleted=1");
        exit();
    } catch (PDOException $e) {
        $error = "Delete failed.";
    }
}

// Fetch Admin Users
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role IN ('admin', 'superadmin') ORDER BY created_at DESC");
    $stmt->execute();
    $admins = $stmt->fetchAll();
} catch (PDOException $e) {
    $admins = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users - SMS</title>
    <link rel="icon" type="image/x-icon" href="../../Assets/image/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Assets/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include '../Components/Side-bar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/Head-bar.php'; ?>
        <div class="content-area">
            <?php if (isset($_GET['success'])): ?>
                <script>Swal.fire('Created!', 'Admin account created successfully.', 'success');</script>
            <?php endif; ?>
            <?php if (isset($_GET['updated'])): ?>
                <script>Swal.fire('Updated!', 'Admin account updated successfully.', 'success');</script>
            <?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?>
                <script>Swal.fire('Deleted!', 'Admin account has been removed.', 'success');</script>
            <?php endif; ?>
            <?php if (isset($_GET['error']) && $_GET['error'] == 'self_delete'): ?>
                <script>Swal.fire('Error!', 'You cannot delete your own account.', 'error');</script>
            <?php endif; ?>

            <div class="table-container">
                <div class="table-header">
                    <h2>Admin Users</h2>
                    <button class="btn-view" id="btnAddAdmin"><i class="fas fa-plus"></i> Add Admin</button>
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
                            <?php foreach ($admins as $admin): ?>
                                <tr>
                                    <td class="student-name"><?php echo htmlspecialchars($admin->username ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($admin->email); ?></td>
                                    <td><span class="status-badge"
                                            style="background:#eef2ff; color:#1648bc;"><?php echo ucfirst($admin->role); ?></span>
                                    </td>
                                    <td><span
                                            class="status-badge <?php echo ($admin->status == 'online') ? 'status-enrolled' : 'status-pending-payment'; ?>"><?php echo ucfirst($admin->status); ?></span>
                                    </td>
                                    <td>
                                        <button class="btn-view" style="padding: 6px 12px; font-size: 0.8rem;"
                                            onclick='openEditModal(<?php echo json_encode($admin); ?>)'>Edit</button>
                                        <button class="btn-reject" style="padding: 6px 12px; font-size: 0.8rem;"
                                            onclick="confirmDelete(<?php echo $admin->userId; ?>)">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Modal -->
    <div id="adminModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalLabel"><i class="fas fa-user-shield"></i> Create Admin Account</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form action="" method="POST">
                <input type="hidden" name="userId" id="field_id">
                <div class="modal-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <label class="info-label">Full Name / Username</label>
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
                            <label class="info-label">System Role</label>
                            <select name="role" id="field_role" required
                                style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                                <option value="admin">Administrator</option>
                                <option value="superadmin">Super Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-reject" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="add_admin" id="submitBtn" class="btn-approve">Create Account</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById("adminModal");

        document.getElementById("btnAddAdmin").onclick = function () {
            document.getElementById("modalLabel").innerHTML = '<i class="fas fa-user-shield"></i> Create Admin Account';
            document.getElementById("submitBtn").name = "add_admin";
            document.getElementById("field_password").required = true;
            document.getElementById("passHint").style.display = "none";
            document.getElementById("field_id").value = "";
            document.getElementById("field_name").value = "";
            document.getElementById("field_email").value = "";
            modal.style.display = "block";
        }

        function openEditModal(data) {
            document.getElementById("modalLabel").innerHTML = '<i class="fas fa-user-edit"></i> Edit Admin Account';
            document.getElementById("submitBtn").name = "edit_admin";
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
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = 'Admin-Users.php?delete=' + id;
            });
        }

        window.onclick = function (event) { if (event.target == modal) closeModal(); }
    </script>
</body>

</html>