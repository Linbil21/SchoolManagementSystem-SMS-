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
    <title>Roles & Permissions - Super Admin</title>
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
                    <h1>Roles & Permissions</h1>
                    <p style="color: var(--text-gray);">Define access levels for different system roles.</p>
                </div>
            </div>

            <div class="roles-grid">
                <!-- Admission Role -->
                <div class="card-premium role-card">
                    <div class="role-header">
                        <div class="role-icon-box">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="role-meta">
                            <h3>Admission</h3>
                            <p>Manage student applications and evaluations</p>
                        </div>
                    </div>
                    <div class="permissions-list">
                        <div class="permission-item"><i class="fas fa-check-circle"></i> View Applications</div>
                        <div class="permission-item"><i class="fas fa-check-circle"></i> Evaluate Credentials</div>
                        <div class="permission-item"><i class="fas fa-check-circle"></i> Schedule Interviews</div>
                        <div class="permission-item"><i class="fas fa-check-circle"></i> Generate Reports</div>
                    </div>
                    <button class="btn-manage" onclick="openRolesModal('Admission')">
                        <i class="fas fa-sliders-h"></i> Configure Permissions
                    </button>
                </div>

                <!-- Cashier Role -->
                <div class="card-premium role-card">
                    <div class="role-header">
                        <div class="role-icon-box">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <div class="role-meta">
                            <h3>Cashier</h3>
                            <p>Handle financial records and payments</p>
                        </div>
                    </div>
                    <div class="permissions-list">
                        <div class="permission-item"><i class="fas fa-check-circle"></i> Access Payments</div>
                        <div class="permission-item"><i class="fas fa-check-circle"></i> Process Refunds</div>
                        <div class="permission-item"><i class="fas fa-check-circle"></i> View Invoices</div>
                        <div class="permission-item"><i class="fas fa-check-circle"></i> Financial Analytics</div>
                    </div>
                    <button class="btn-manage" onclick="openRolesModal('Cashier')">
                        <i class="fas fa-sliders-h"></i> Configure Permissions
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Modal -->
    <div id="rolesModal" class="modal centered">
        <div class="modal-content premium" style="max-width: 600px;">
            <div class="modal-header">
                <div>
                    <h2 id="modalTitle" style="font-weight: 850; color: var(--text-color); line-height: 1.2;">Configure Permissions</h2>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">Select the modules this role can access.</p>
                </div>
                <div class="modal-close" onclick="closeRolesModal()">
                    <i class="fas fa-times"></i>
                </div>
            </div>
            <div class="modal-body">
                <div class="checkbox-group">
                    <label class="checkbox-item"><input type="checkbox" checked> <span>Dashboard Access</span></label>
                    <label class="checkbox-item"><input type="checkbox" checked> <span>User Management</span></label>
                    <label class="checkbox-item"><input type="checkbox" checked> <span>Reports View</span></label>
                    <label class="checkbox-item"><input type="checkbox" checked> <span>Data Export</span></label>
                    <label class="checkbox-item"><input type="checkbox"> <span>System Settings</span></label>
                    <label class="checkbox-item"><input type="checkbox"> <span>Database Access</span></label>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeRolesModal()" class="btn-secondary">Cancel</button>
                <button onclick="saveRoles()" class="btn-premium">
                    <i class="fas fa-save"></i> Save Permissions
                </button>
            </div>
        </div>
    </div>

    <script>
        function openRolesModal(role) {
            document.getElementById('modalTitle').textContent = 'Manage Access: ' + role;
            document.getElementById('rolesModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeRolesModal() {
            document.getElementById('rolesModal').classList.remove('show');
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

