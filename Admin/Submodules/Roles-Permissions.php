<?php
session_start();
require_once '../../Database/config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin' && $_SESSION['role'] !== 'admission')) {
    header("Location: ../../auth/Login.php");
    exit();
}

// Fetch Roles
try {
    $stmt = $pdo->query("SELECT * FROM roles_config");
    $roles = $stmt->fetchAll();
} catch (PDOException $e) {
    $roles = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles & Permissions - SMS</title>
    <link rel="icon" type="image/x-icon" href="../../Assets/image/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Assets/style.css">
</head>

<body>
    <?php include '../Components/Side-bar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/Head-bar.php'; ?>
        <div class="content-area">
            <div class="table-container">
                <div class="table-header">
                    <h2>System Roles</h2>
                    <button class="btn-view" onclick="openModal()"><i class="fas fa-plus"></i> New Role</button>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($roles) > 0): ?>
                                <?php foreach ($roles as $role): ?>
                                    <tr>
                                        <td class="student-name"><?php echo htmlspecialchars(ucfirst($role->role_name)); ?></td>
                                        <td><?php echo htmlspecialchars($role->description); ?></td>
                                        <td><span class="status-badge status-enrolled">Active</span></td>
                                        <td>
                                            <div style="display: flex; gap: 8px;">
                                                <button onclick='openEditModal(<?php echo json_encode($role); ?>)' class="btn-view" style="padding: 6px 12px; font-size: 0.8rem; border:none; cursor:pointer; background: var(--primary-blue); color: white; border-radius: 4px;">Edit Permissions</button>
                                                <button onclick="deleteRole(<?php echo $role->roleId; ?>)" class="btn-view" style="padding: 6px 12px; font-size: 0.8rem; border:none; cursor:pointer; background: #f59e0b; color: white; border-radius: 4px;"><i class="fas fa-archive"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align:center; padding:50px;">No roles configured.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Fetch all available permissions for the modal
    $permStmt = $pdo->query("SELECT * FROM permissions ORDER BY description ASC");
    $all_permissions = $permStmt->fetchAll();
    ?>

    <!-- Role Modal -->
    <div id="roleModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2 id="modalTitle">Manage Role</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="roleForm">
                    <input type="hidden" id="roleId">
                    <div class="info-item">
                        <label class="info-label">Role Name</label>
                        <input type="text" id="roleName" class="form-control"
                            style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; margin-top: 5px;"
                            placeholder="e.g. Registrar" required>
                    </div>
                    <div class="info-item" style="margin-top: 15px;">
                        <label class="info-label">Description</label>
                        <textarea id="roleDesc" rows="3"
                            style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; margin-top: 5px;"
                            placeholder="Describe the responsibilities of this role..."></textarea>
                    </div>

                    <div style="margin-top: 20px; font-weight: 700; color: var(--text-dark); margin-bottom: 15px; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Permissions</div>
                    <div class="info-grid" style="grid-template-columns: 1fr; gap: 10px;">
                        <?php foreach($all_permissions as $perm): ?>
                        <label
                            style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8fafc; border-radius: 10px; cursor: pointer; transition: background 0.2s;">
                            <input type="checkbox" name="perms[]" value="<?php echo $perm->permissionId; ?>" style="width: 20px; height: 20px; cursor: pointer;">
                            <span style="font-size: 0.9rem; color: #475569; font-weight: 500;"><?php echo htmlspecialchars($perm->description); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-reject" onclick="closeModal()" style="background: #94a3b8; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button class="btn-approve" onclick="saveRole()" style="background: var(--primary-blue); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;" id="saveBtn">Save Role</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const modal = document.getElementById('roleModal');

        function openModal() {
            document.getElementById('modalTitle').innerText = "New Role";
            document.getElementById('roleId').value = "";
            document.getElementById('roleForm').reset();
            modal.style.display = "block";
        }

        function openEditModal(role) {
            document.getElementById('modalTitle').innerText = "Edit Role: " + role.role_name;
            document.getElementById('roleId').value = role.roleId;
            document.getElementById('roleName').value = role.role_name;
            document.getElementById('roleDesc').value = role.description;
            
            // Clear checks
            document.querySelectorAll('input[name="perms[]"]').forEach(cb => cb.checked = false);
            
            // Fetch current permissions
            fetch('../api/manage_roles.php?action=get_role_permissions&role_id=' + role.roleId)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    data.permissions.forEach(pId => {
                        const cb = document.querySelector(`input[name="perms[]"][value="${pId}"]`);
                        if(cb) cb.checked = true;
                    });
                }
            });

            modal.style.display = "block";
        }

        function closeModal() {
            modal.style.display = "none";
        }

        function saveRole() {
            const saveBtn = document.getElementById('saveBtn');
            const roleId = document.getElementById('roleId').value;
            const roleName = document.getElementById('roleName').value;
            const roleDesc = document.getElementById('roleDesc').value;
            const perms = Array.from(document.querySelectorAll('input[name="perms[]"]:checked')).map(cb => cb.value);

            if(!roleName) {
                Swal.fire('Error', 'Role name is required', 'error');
                return;
            }

            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            const formData = new FormData();
            formData.append('role_id', roleId);
            formData.append('role_name', roleName);
            formData.append('description', roleDesc);
            perms.forEach(id => formData.append('permissions[]', id));

            fetch('../api/manage_roles.php?action=save_role', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('Success', data.message, 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = 'Save Role';
                }
            })
            .catch(err => {
                console.error(err);
                saveBtn.disabled = false;
                saveBtn.innerHTML = 'Save Role';
            });
        }

        function deleteRole(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting a role will remove all its assigned permissions.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('role_id', id);

                    fetch('../api/manage_roles.php?action=delete_role', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire('Deleted!', data.message, 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
                }
            });
        }

        // Close on outside click
        window.onclick = function (event) {
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>