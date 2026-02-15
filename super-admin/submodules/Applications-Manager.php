<?php
session_start();
require_once '../../auth/Security.php';
require_once '../../Database/config.php';
checkRole(['superadmin']);

$success_msg = "";
$error_msg = "";

// Handle status overrides
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'override') {
    try {
        $id = $_POST['enrollment_id'];
        $new_status = $_POST['new_status'];
        
        $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE enrollmentId = ?");
        $stmt->execute([$new_status, $id]);
        $success_msg = "Application status force-updated to $new_status.";
    } catch (PDOException $e) {
        $error_msg = "Error: " . $e->getMessage();
    }
}

// Fetch applications (enrollments)
$stmt = $pdo->query("SELECT e.*, c.course_name FROM enrollments e LEFT JOIN courses c ON e.course_id = c.courseId ORDER BY e.created_at DESC");
$apps = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel='icon' type='image/png' href='../../Assets/image/logo.png'>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications Manager - Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/super-admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .avatar-circle { width: 40px; height: 40px; border-radius: 10px; background: #eef2ff; color: #6366f1; display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .status-pill { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
        .status-enrolled { background: #dcfce7; color: #166534; }
        .status-pending { background: #fff7ed; color: #c2410c; }
        .status-rejected { background: #fef2f2; color: #991b1b; }
        .btn-action { width: 36px; height: 36px; border-radius: 8px; border: 1px solid #e2e8f0; background: white; cursor: pointer; transition: 0.2s; }
        .btn-action:hover { border-color: #6366f1; color: #6366f1; transform: translateY(-2px); }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header" style="margin-bottom: 30px;">
                <div>
                    <h1 style="font-weight: 800; letter-spacing: -1px; color: #1e293b;">Applications Manager</h1>
                    <p style="color: #64748b;">Oversee and audit student admission applications.</p>
                </div>
            </div>

            <div class="table-card" style="background: white; border-radius: 24px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f1f5f9;">
                            <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">Applicant</th>
                            <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">Reference</th>
                            <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">Course</th>
                            <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">Status</th>
                            <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($apps as $app): 
                            $initials = strtoupper(substr($app->first_name, 0, 1) . substr($app->last_name, 0, 1));
                            $status_class = 'status-' . strtolower(str_replace(' ', '-', $app->status));
                        ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 15px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="avatar-circle"><?php echo $initials; ?></div>
                                    <div>
                                        <p style="font-weight: 700; color: #1e293b; margin: 0;"><?php echo htmlspecialchars($app->first_name . ' ' . $app->last_name); ?></p>
                                        <p style="font-size: 0.75rem; color: #64748b; margin: 0;"><?php echo htmlspecialchars($app->email); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 15px;"><span style="font-weight: 600; font-family: monospace;"><?php echo htmlspecialchars($app->reference_code); ?></span></td>
                            <td style="padding: 15px; color: #475569;"><?php echo htmlspecialchars($app->course_name ?? 'Not Assigned'); ?></td>
                            <td style="padding: 15px;">
                                <span class="status-pill <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($app->status); ?>
                                </span>
                            </td>
                            <td style="padding: 15px;">
                                <button class="btn-action" onclick='openOverrideModal(<?php echo json_encode($app); ?>)' title="Override Status">
                                    <i class="fas fa-shield-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($apps)): ?>
                            <tr><td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">No applications found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Override Modal -->
    <div id="overrideModal" class="modal" style="display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; width: 500px; max-width: 90%; border-radius: 24px; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2 style="font-weight: 800; color: #1e293b; margin: 0;">Status Override</h2>
                <i class="fas fa-times" style="cursor: pointer; color: #94a3b8;" onclick="closeModal()"></i>
            </div>
            
            <div style="background: #f8fafc; padding: 20px; border-radius: 16px; margin-bottom: 25px; border: 1px solid #e2e8f0;">
                <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 4px; font-weight: 600;">Currently reviewing:</p>
                <h3 id="modalName" style="font-weight: 700; color: #1e293b; margin: 0;">--</h3>
                <p id="modalRef" style="font-family: monospace; color: #6366f1; margin-top: 5px; font-weight: 700;">--</p>
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="override">
                <input type="hidden" name="enrollment_id" id="modalId">
                
                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 700; color: #475569; margin-bottom: 10px; font-size: 0.85rem;">Select New Status</label>
                    <select name="new_status" id="modalStatusSelect" style="width: 100%; padding: 14px; border-radius: 12px; border: 1.5px solid #e2e8f0; outline: none; background: white; font-weight: 600; font-family: inherit;">
                        <option value="Pending Review">Pending Review</option>
                        <option value="Document Verified">Document Verified</option>
                        <option value="For Medical">For Medical</option>
                        <option value="Enrolled">Enrolled (Complete)</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 8px;"><i class="fas fa-info-circle"></i> This will bypass all admission checks.</p>
                </div>

                <div style="display: flex; gap: 12px;">
                    <button type="button" onclick="closeModal()" style="flex: 1; padding: 14px; border-radius: 12px; border: 1.5px solid #e2e8f0; background: white; font-weight: 700; cursor: pointer;">Cancel</button>
                    <button type="submit" style="flex: 1; padding: 14px; border-radius: 12px; background: #6366f1; color: white; border: none; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);">Apply Override</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openOverrideModal(data) {
            document.getElementById('modalId').value = data.enrollmentId;
            document.getElementById('modalName').textContent = data.first_name + ' ' + data.last_name;
            document.getElementById('modalRef').textContent = '#' + data.reference_code;
            document.getElementById('modalStatusSelect').value = data.status;
            
            document.getElementById('overrideModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('overrideModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('overrideModal');
            if (event.target == modal) closeModal();
        }

        <?php if ($success_msg): ?>
            Swal.fire('Updated!', '<?php echo $success_msg; ?>', 'success');
        <?php endif; ?>
        <?php if ($error_msg): ?>
            Swal.fire('Error!', '<?php echo $error_msg; ?>', 'error');
        <?php endif; ?>
    </script>
</body>
</html>

