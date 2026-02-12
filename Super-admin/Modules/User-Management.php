<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../../auth/Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/Super-admin/assets/super-admin.css">

</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <div>
                    <h1>User Management</h1>
                    <p style="color: var(--text-gray);">Manage system administrators and staff accounts.</p>
                </div>
                <button class="btn-primary" onclick="openUserModal()">
                    <i class="fas fa-plus"></i> Add New User
                </button>
            </div>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>User Info</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="avatar-circle">AU</div>
                                    <div>
                                        <p style="font-weight: 700;">Admin User</p>
                                        <p style="font-size: 0.8rem; color: var(--text-gray);">admin@sms.com</p>
                                    </div>
                                </div>
                            </td>
                            <td><span style="font-weight: 600;">Super Admin</span></td>
                            <td><span class="status-pill status-active">Active</span></td>
                            <td>Jan 11, 2024</td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <button class="btn-action" onclick="editUser('Admin User')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action" style="color: #ef4444;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="avatar-circle" style="background: #3b82f6;">SM</div>
                                    <div>
                                        <p style="font-weight: 700;">Sarah Miller</p>
                                        <p style="font-size: 0.8rem; color: var(--text-gray);">sarah.m@sms.com</p>
                                    </div>
                                </div>
                            </td>
                            <td><span style="font-weight: 600;">Admission</span></td>
                            <td><span class="status-pill status-active">Active</span></td>
                            <td>Jan 10, 2024</td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <button class="btn-action" onclick="editUser('Sarah Miller')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action" style="color: #ef4444;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle" style="font-weight: 800; color: var(--text-dark);">Add New User</h2>
                <i class="fas fa-times" style="cursor: pointer; color: var(--text-gray);"
                    onclick="closeUserModal()"></i>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" placeholder="e.g. John Doe">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" placeholder="e.g. john@sms.com">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select>
                        <option>Super Admin</option>
                        <option>Admission</option>
                        <option>Cashier</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Initial Password</label>
                    <input type="password" placeholder="********">
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeUserModal()" style="padding: 12px 24px; border-radius: 12px; border: 1px solid var(--border-color); background: white; font-weight: 600; cursor: pointer; color: var(--text-gray);">Cancel</button>
                <button onclick="saveUser()" class="btn-primary">Create Account</button>
            </div>
        </div>
    </div>

    <script>
        function openUserModal() {
            document.getElementById('modalTitle').textContent = 'Add New User';
            document.getElementById('userModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeUserModal() {
            document.getElementById('userModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function editUser(name) {
            document.getElementById('modalTitle').textContent = 'Edit User: ' + name;
            document.getElementById('userModal').style.display = 'block';
        }

        function saveUser() {
            alert('User profile updated successfully!');
            closeUserModal();
        }

        window.onclick = function (event) {
            if (event.target == document.getElementById('userModal')) {
                closeUserModal();
            }
        }
    </script>
</body>

</html>
