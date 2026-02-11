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
    <title>Roles & Permissions - Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Assets/super-admin.css">
    <style>
        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
        }

        .role-card {
            background: var(--surface-color);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            transition: 0.3s;
        }

        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .role-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .role-icon {
            width: 50px;
            height: 50px;
            background: #eef2ff;
            color: var(--primary-blue);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .permissions-list {
            margin-bottom: 30px;
        }

        .permission-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .permission-item i {
            color: #10b981;
            font-size: 0.8rem;
        }

        .btn-manage {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid var(--primary-blue);
            background: transparent;
            color: var(--primary-blue);
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-manage:hover {
            background: var(--primary-blue);
            color: white;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: var(--bg-light);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <div>
                    <h1>Roles & Permissions</h1>
                    <p style="color: var(--text-gray);">Define access levels for different system roles.</p>
                </div>
            </div>

            <div class="roles-grid">
                <!-- Admission Role -->
                <div class="role-card">
                    <div class="role-header">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div class="role-icon"><i class="fas fa-user-graduate"></i></div>
                            <div>
                                <h3 style="font-weight: 800;">Admission</h3>
                                <p style="font-size: 0.8rem; color: var(--text-gray);">Manage student applications</p>
                            </div>
                        </div>
                    </div>
                    <div class="permissions-list">
                        <div class="permission-item"><i class="fas fa-check-circle"></i> View Applications</div>
                        <div class="permission-item"><i class="fas fa-check-circle"></i> Evaluate Credentials</div>
                        <div class="permission-item"><i class="fas fa-check-circle"></i> Schedule Interviews</div>
                        <div class="permission-item"><i class="fas fa-check-circle"></i> Generate Reports</div>
                    </div>
                    <button class="btn-manage" onclick="openRolesModal('Admission')">Configure Permissions</button>
                </div>

                <!-- Cashier Role -->
                <div class="role-card">
                    <div class="role-header">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div class="role-icon"><i class="fas fa-cash-register"></i></div>
                            <div>
                                <h3 style="font-weight: 800;">Cashier</h3>
                                <p style="font-size: 0.8rem; color: var(--text-gray);">Handle financial records</p>
                            </div>
                        </div>
                    </div>
                    <div class="permissions-list">
                        <div class="permission-item"><i class="fas fa-check-circle"></i> Access Payments</div>
                        <div class="permission-item"><i class="fas fa-check-circle"></i> Process Refunds</div>
                        <div class="permission-item"><i class="fas fa-check-circle"></i> View Invoices</div>
                        <div class="permission-item"><i class="fas fa-check-circle"></i> Financial Analytics</div>
                    </div>
                    <button class="btn-manage" onclick="openRolesModal('Cashier')">Configure Permissions</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Modal -->
    <div id="rolesModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle" style="font-weight: 800; color: var(--text-dark);">Configure Permissions</h2>
                <i class="fas fa-times" style="cursor: pointer; color: var(--text-gray);"
                    onclick="closeRolesModal()"></i>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 25px; font-size: 0.9rem; color: var(--text-gray); font-weight: 500;">Select the modules and actions this role has access to.</p>
                <div class="checkbox-group">
                    <label class="checkbox-item"><input type="checkbox" checked> Dashboard Access</label>
                    <label class="checkbox-item"><input type="checkbox" checked> User Management</label>
                    <label class="checkbox-item"><input type="checkbox" checked> Reports View</label>
                    <label class="checkbox-item"><input type="checkbox" checked> Data Export</label>
                    <label class="checkbox-item"><input type="checkbox"> System Settings</label>
                    <label class="checkbox-item"><input type="checkbox"> Database Access</label>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeRolesModal()" style="padding: 12px 24px; border-radius: 12px; border: 1px solid var(--border-color); background: white; font-weight: 600; cursor: pointer; color: var(--text-gray);">Cancel</button>
                <button onclick="saveRoles()" class="btn-primary">Save Permissions</button>
            </div>
        </div>
    </div>

    <script>
        function openRolesModal(role) {
            document.getElementById('modalTitle').textContent = 'Manage Access: ' + role;
            document.getElementById('rolesModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeRolesModal() {
            document.getElementById('rolesModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function saveRoles() {
            alert('Permissions updated successfully!');
            closeRolesModal();
        }

        window.onclick = function (event) {
            if (event.target == document.getElementById('rolesModal')) {
                closeRolesModal();
            }
        }
    </script>
</body>

</html>