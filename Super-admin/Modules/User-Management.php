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
    <link rel='icon' type='image/png' href='../../Assets/image/logo.png'>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/super-admin.css">

</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <div>
                    <h1>User Management</h1>
                    <p>Manage system administrators and staff accounts with ease.</p>
                </div>
                <button class="btn-premium" onclick="openUserModal()">
                    <i class="fas fa-plus-circle"></i> Add New User
                </button>
            </div>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>User Profile</th>
                            <th>Access Level</th>
                            <th>Status Account</th>
                            <th>Recent Activity</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="avatar-circle">AU</div>
                                    <div>
                                        <p style="font-weight: 700; color: var(--text-color); margin: 0; font-size: 0.95rem;">Admin User</p>
                                        <p style="font-size: 0.78rem; color: var(--text-muted); margin: 0; font-weight: 500;">admin@sms.com</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--text-color); font-size: 0.9rem;">
                                    <i class="fas fa-shield-check" style="color: var(--accent-color); margin-right: 6px;"></i>Super Admin
                                </span>
                            </td>
                            <td><span class="status-pill status-active">Active</span></td>
                            <td>
                                <p style="margin: 0; font-weight: 600; color: var(--text-color); font-size: 0.85rem;">Jan 11, 2024</p>
                                <p style="margin: 0; font-size: 0.7rem; color: var(--text-muted);">10:45 AM</p>
                            </td>
                            <td>
                                <div style="display: flex; gap: 10px; justify-content: center;">
                                    <button class="btn-action" onclick="editUser('Admin User')" title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action" style="color: #ef4444; border-color: #fee2e2; background: #fff1f2;" title="Remove User">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="avatar-circle" style="background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);">SM</div>
                                    <div>
                                        <p style="font-weight: 700; color: var(--text-color); margin: 0; font-size: 0.95rem;">Sarah Miller</p>
                                        <p style="font-size: 0.78rem; color: var(--text-muted); margin: 0; font-weight: 500;">sarah.m@sms.com</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--text-color); font-size: 0.9rem;">
                                    <i class="fas fa-user-graduate" style="color: #06b6d4; margin-right: 6px;"></i>Admission
                                </span>
                            </td>
                            <td><span class="status-pill status-active">Active</span></td>
                            <td>
                                <p style="margin: 0; font-weight: 600; color: var(--text-color); font-size: 0.85rem;">Jan 10, 2024</p>
                                <p style="margin: 0; font-size: 0.7rem; color: var(--text-muted);">02:30 PM</p>
                            </td>
                            <td>
                                <div style="display: flex; gap: 10px; justify-content: center;">
                                    <button class="btn-action" onclick="editUser('Sarah Miller')" title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action" style="color: #ef4444; border-color: #fee2e2; background: #fff1f2;" title="Remove User">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- User Modal Overlay -->
    <div id="userModal" class="modal centered" style="display: none; align-items: center; justify-content: center;">
        <div class="modal-content" style="width: 500px; max-width: 90%; border-radius: 28px; border: 1px solid var(--border-color);">
            <div class="modal-header" style="padding: 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); background: var(--hover-bg);">
                <h2 id="modalTitle" style="font-weight: 800; color: var(--text-color); letter-spacing: -0.5px; margin: 0;">Add New User</h2>
                <div style="width: 36px; height: 36px; border-radius: 10px; background: white; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 1px solid var(--border-color); transition: 0.3s;" onclick="closeUserModal()" onmouseover="this.style.background='#fee2e2'; this.style.color='#ef4444'" onmouseout="this.style.background='white'; this.style.color='inherit'">
                    <i class="fas fa-times"></i>
                </div>
            </div>
            <div class="modal-body" style="padding: 40px;">
                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 700; color: var(--text-color); margin-bottom: 10px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Full Name</label>
                    <input type="text" placeholder="e.g. John Doe" style="width: 100%; padding: 14px 18px; border-radius: 14px; border: 1.5px solid var(--border-color); background: var(--bg-color); color: var(--text-color); font-weight: 500; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--accent-color)'; this.style.boxShadow='0 0 0 4px rgba(22, 72, 188, 0.1)'" onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'">
                </div>
                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 700; color: var(--text-color); margin-bottom: 10px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Email Address</label>
                    <input type="email" placeholder="e.g. john@sms.com" style="width: 100%; padding: 14px 18px; border-radius: 14px; border: 1.5px solid var(--border-color); background: var(--bg-color); color: var(--text-color); font-weight: 500; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--accent-color)'; this.style.boxShadow='0 0 0 4px rgba(22, 72, 188, 0.1)'" onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'">
                </div>
                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 700; color: var(--text-color); margin-bottom: 10px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Assign Role</label>
                    <select style="width: 100%; padding: 14px 18px; border-radius: 14px; border: 1.5px solid var(--border-color); background: var(--bg-color); color: var(--text-color); font-weight: 600; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2364748b%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right%2018px%20top%2050%25; background-size: 12px%20auto;">
                        <option>Super Admin</option>
                        <option>Admin</option>
                        <option>Admission</option>
                        <option>Cashier</option>
                        <option>Student</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display: block; font-weight: 700; color: var(--text-color); margin-bottom: 10px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Initial Password</label>
                    <input type="password" placeholder="********" style="width: 100%; padding: 14px 18px; border-radius: 14px; border: 1.5px solid var(--border-color); background: var(--bg-color); color: var(--text-color); font-weight: 500; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--accent-color)'; this.style.boxShadow='0 0 0 4px rgba(22, 72, 188, 0.1)'" onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'">
                </div>
            </div>
            <div class="modal-footer" style="padding: 30px; background: var(--hover-bg); display: flex; gap: 15px; justify-content: flex-end;">
                <button onclick="closeUserModal()" style="padding: 14px 25px; border-radius: 12px; border: 1.5px solid var(--border-color); background: white; font-weight: 700; cursor: pointer; color: var(--text-muted); transition: 0.3s;" onmouseover="this.style.background='var(--bg-color)'" onmouseout="this.style.background='white'">Cancel</button>
                <button onclick="saveUser()" class="btn-primary" style="padding: 14px 25px;">Create Account</button>
            </div>
        </div>
    </div>

    <script>
        function openUserModal() {
            document.getElementById('modalTitle').textContent = 'Add New User';
            document.getElementById('userModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeUserModal() {
            document.getElementById('userModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function editUser(name) {
            document.getElementById('modalTitle').textContent = 'Update Profile: ' + name;
            document.getElementById('userModal').style.display = 'flex';
        }

        function saveUser() {
            alert('User profile processed successfully!');
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

