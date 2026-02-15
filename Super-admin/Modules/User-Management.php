<?php
session_start();
require_once '../../auth/Security.php';
require_once '../../Database/config.php';
checkRole(['superadmin']);

$success_msg = "";
$error_msg = "";

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'add') {
                $name = $_POST['full_name'];
                $email = $_POST['email'];
                $role = strtolower($_POST['role']);
                $password = $_POST['password'];
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert into users table
                $stmt = $pdo->prepare("INSERT INTO users (email, password, role, status) VALUES (?, ?, ?, 'offline')");
                $stmt->execute([$email, $hashed_password, $role]);
                
                $success_msg = "Account created successfully for $name!";
            } elseif ($_POST['action'] === 'edit') {
                $id = $_POST['user_id'];
                $role = strtolower($_POST['role']);
                $email = $_POST['email'];
                
                if (!empty($_POST['password'])) {
                    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET role = ?, email = ?, password = ? WHERE userId = ?");
                    $stmt->execute([$role, $email, $hashed_password, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET role = ?, email = ? WHERE userId = ?");
                    $stmt->execute([$role, $email, $id]);
                }
                $success_msg = "User account updated successfully!";
            } elseif ($_POST['action'] === 'delete') {
                $id = $_POST['user_id'];
                $stmt = $pdo->prepare("DELETE FROM users WHERE userId = ?");
                $stmt->execute([$id]);
                $success_msg = "User account permanently removed.";
            }
        } catch (PDOException $e) {
            $error_msg = "Error: " . $e->getMessage();
        }
    }
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .avatar-circle {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1648bc 0%, #2563eb 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            box-shadow: 0 4px 10px rgba(22, 72, 188, 0.2);
        }
        .btn-action {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            background: white;
            color: #64748b;
            cursor: pointer;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-action:hover {
            border-color: #1648bc;
            color: #1648bc;
            background: #eff6ff;
            transform: translateY(-2px);
        }
        .status-pill {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: capitalize;
        }
        .status-online { background: #dcfce7; color: #16a34a; }
        .status-offline { background: #f1f5f9; color: #64748b; }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="font-weight: 800; letter-spacing: -1px;">User Management</h1>
                    <p style="color: #64748b;">Manage system administrators and staff accounts.</p>
                </div>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <button class="btn-premium" onclick="openUserModal('add')" style="background: var(--primary); color: white; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-plus-circle"></i> Add New User
                    </button>
                </div>
            </div>

            <div class="table-card" style="background: white; border-radius: 24px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <table id="userTable" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f1f5f9;">
                            <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">User Account</th>
                            <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">Access Level</th>
                            <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">Status</th>
                            <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">Created At</th>
                            <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): 
                            $initials = strtoupper(substr($u->email, 0, 2));
                        ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 20px 15px;">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="avatar-circle"><?php echo $initials; ?></div>
                                    <div>
                                        <p style="font-weight: 700; color: #1e293b; margin: 0;"><?php echo htmlspecialchars($u->email); ?></p>
                                        <p style="font-size: 0.75rem; color: #64748b; margin: 0;">ID: #<?php echo $u->userId; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 20px 15px;">
                                <span style="font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-id-badge" style="color: #1648bc;"></i>
                                    <?php echo ucfirst($u->role); ?>
                                </span>
                            </td>
                            <td style="padding: 20px 15px;">
                                <span class="status-pill <?php echo $u->status === 'online' ? 'status-online' : 'status-offline'; ?>">
                                    <?php echo ucfirst($u->status); ?>
                                </span>
                            </td>
                            <td style="padding: 20px 15px; color: #64748b; font-size: 0.85rem;">
                                <?php echo date('M d, Y', strtotime($u->created_at)); ?>
                            </td>
                            <td style="padding: 20px 15px; text-align: center;">
                                <div style="display: flex; gap: 10px; justify-content: center;">
                                    <button class="btn-action" onclick='openUserModal("edit", <?php echo json_encode($u); ?>)' title="Edit Account">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($u->role !== 'superadmin'): ?>
                                    <button class="btn-action" style="color: #ef4444; border-color: #fee2e2; background: #fef2f2;" onclick="deleteUser(<?php echo $u->userId; ?>)" title="Remove Account">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- User Modal Overlay -->
    <div id="userModal" class="modal centered" style="display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; width: 500px; max-width: 90%; border-radius: 28px; padding: 40px; box-shadow: 0 20px 40px -5px rgba(0,0,0,0.25);">
            <h2 id="modalTitle" style="font-weight: 800; margin-bottom: 25px; letter-spacing: -0.5px;">Add New User</h2>
            <form method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="user_id" id="userId">
                
                <div id="nameGroup" class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; color: #475569; margin-bottom: 8px; font-size: 0.85rem;">Full Name (Reference)</label>
                    <input type="text" name="full_name" id="userName" placeholder="e.g. System Admin" style="width: 100%; padding: 12px; border-radius: 12px; border: 1.5px solid #e2e8f0; outline: none;">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; color: #475569; margin-bottom: 8px; font-size: 0.85rem;">Email Address</label>
                    <input type="email" name="email" id="userEmail" required placeholder="e.g. admin@sms.com" style="width: 100%; padding: 12px; border-radius: 12px; border: 1.5px solid #e2e8f0; outline: none;">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; color: #475569; margin-bottom: 8px; font-size: 0.85rem;">User Role</label>
                    <select name="role" id="userRole" required style="width: 100%; padding: 12px; border-radius: 12px; border: 1.5px solid #e2e8f0; outline: none; background: white;">
                        <option value="superadmin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="admission">Admission</option>
                        <option value="cashier">Cashier</option>
                        <option value="registrar">Registrar</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label style="display: block; font-weight: 700; color: #475569; margin-bottom: 8px; font-size: 0.85rem;">Password (Leave blank if no change)</label>
                    <input type="password" name="password" id="userPassword" placeholder="********" style="width: 100%; padding: 12px; border-radius: 12px; border: 1.5px solid #e2e8f0; outline: none;">
                </div>

                <div style="display: flex; gap: 12px;">
                    <button type="button" onclick="closeUserModal()" style="flex: 1; padding: 14px; border-radius: 12px; border: 1.5px solid #e2e8f0; background: white; font-weight: 700; cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-primary" style="flex: 1; padding: 14px; border-radius: 12px; background: #1648bc; color: white; border: none; font-weight: 700; cursor: pointer;">Save Account</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUserModal(type, data = null) {
            document.getElementById('userModal').style.display = 'flex';
            document.getElementById('formAction').value = type;
            document.getElementById('modalTitle').textContent = type === 'add' ? 'Create New Account' : 'Edit Account Access';
            
            if (data) {
                document.getElementById('userId').value = data.userId;
                document.getElementById('userEmail').value = data.email;
                document.getElementById('userRole').value = data.role;
                document.getElementById('nameGroup').style.display = 'none';
                document.getElementById('userPassword').required = false;
            } else {
                document.getElementById('userId').value = '';
                document.getElementById('userName').value = '';
                document.getElementById('userEmail').value = '';
                document.getElementById('userRole').value = 'admin';
                document.getElementById('nameGroup').style.display = 'block';
                document.getElementById('userPassword').required = true;
            }
        }

        function closeUserModal() {
            document.getElementById('userModal').style.display = 'none';
        }

        function deleteUser(id) {
            Swal.fire({
                title: 'Delete Account?',
                text: "This user will lose all system access immediately!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, remove them'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `<input type="hidden" name="action" value="delete"><input type="hidden" name="user_id" value="${id}">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }

        <?php if ($success_msg): ?>
            Swal.fire('Success!', '<?php echo $success_msg; ?>', 'success');
        <?php endif; ?>
        <?php if ($error_msg): ?>
            Swal.fire('Error!', '<?php echo $error_msg; ?>', 'error');
        <?php endif; ?>
    </script>
</body>
</html>
