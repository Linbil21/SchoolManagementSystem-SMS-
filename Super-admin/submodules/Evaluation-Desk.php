<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../../Auth/log-reg.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Desk - Super Admin</title>
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
                    <h1>Evaluation Desk</h1>
                    <p style="color: var(--text-gray);">Monitor and manage student evaluation queues.</p>
                </div>
            </div>
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Evaluator</th>
                            <th>Queue Count</th>
                            <th>Avg. Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="avatar-circle" style="background: #10b981;">SM</div>
                                    <span style="font-weight: 700;">Sarah Miller</span>
                                </div>
                            </td>
                            <td><span style="font-weight: 600;">12</span></td>
                            <td><span style="color: var(--text-gray);">15m / app</span></td>
                            <td>
                                <button class="btn-action" title="View Queue" style="width: auto; padding: 0 15px;">
                                    View Queue
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>