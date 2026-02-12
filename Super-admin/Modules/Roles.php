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
    <link rel="stylesheet" href="/Super-admin/assets/super-admin.css">

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
