<?php
session_start();
require_once '../../Database/config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: ../../auth/Login.php");
    exit();
}

// Fetch Account Status Counts
try {
    $admin_active = $pdo->query("SELECT COUNT(*) FROM users WHERE role IN ('admin', 'superadmin') AND status = 'online'")->fetchColumn();
    $admin_total = $pdo->query("SELECT COUNT(*) FROM users WHERE role IN ('admin', 'superadmin')")->fetchColumn();

    $staff_active = $pdo->query("SELECT COUNT(*) FROM users WHERE role NOT IN ('admin', 'superadmin', 'student') AND status = 'online'")->fetchColumn();
    $staff_total = $pdo->query("SELECT COUNT(*) FROM users WHERE role NOT IN ('admin', 'superadmin', 'student')")->fetchColumn();

    $student_total = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status = 'Enrolled'")->fetchColumn();

    // Fetch Online Users List
    $online_users = $pdo->query("SELECT username, email, role, last_login FROM users WHERE status = 'online' ORDER BY last_login DESC")->fetchAll();
} catch (PDOException $e) {
    $admin_active = $admin_total = $staff_active = $staff_total = $student_total = 0;
    $online_users = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Status - SMS</title>
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
                    <h2>Account Status Overview</h2>
                    <button class="btn-view" onclick="showOnlineUsers()"><i class="fas fa-users"></i> View Online
                        List</button>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>User Type</th>
                                <th>Online / Active</th>
                                <th>Offline / Inactive</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-weight:600;">Administrators</td>
                                <td style="color:#10b981; font-weight:700;"><?php echo $admin_active; ?></td>
                                <td style="color:var(--text-gray);"><?php echo $admin_total - $admin_active; ?></td>
                                <td style="font-weight:700;"><?php echo $admin_total; ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Staff & Faculty</td>
                                <td style="color:#10b981; font-weight:700;"><?php echo $staff_active; ?></td>
                                <td style="color:var(--text-gray);"><?php echo $staff_total - $staff_active; ?></td>
                                <td style="font-weight:700;"><?php echo $staff_total; ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight:600;">Enrolled Students</td>
                                <td style="color:#10b981; font-weight:700;"><?php echo $student_total; ?></td>
                                <td style="color:var(--text-gray);">0</td>
                                <td style="font-weight:700;"><?php echo $student_total; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Online Users Modal -->
    <div id="onlineModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2><i class="fas fa-circle" style="color:#10b981; font-size: 0.8rem; vertical-align: middle;"></i>
                    Currently Online Users</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table style="font-size: 0.9rem;">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Last Pulse</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($online_users as $user): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight:600;"><?php echo htmlspecialchars($user->username); ?></div>
                                        <div style="font-size:0.75rem; color:#718096;">
                                            <?php echo htmlspecialchars($user->email); ?></div>
                                    </td>
                                    <td><span class="status-badge"
                                            style="background:#f1f5f9; color:#475569; font-size:0.7rem;"><?php echo strtoupper($user->role); ?></span>
                                    </td>
                                    <td><?php echo date('h:i A', strtotime($user->last_login)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($online_users)): ?>
                                <tr>
                                    <td colspan="3" style="text-align:center; padding:20px; color:#a0aec0;">No users
                                        currently online.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-reject" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        function showOnlineUsers() { document.getElementById('onlineModal').style.display = "block"; }
        function closeModal() { document.getElementById('onlineModal').style.display = "none"; }
        window.onclick = function (event) { if (event.target == document.getElementById('onlineModal')) closeModal(); }
    </script>
</body>

</html>