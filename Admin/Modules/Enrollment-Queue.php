<?php
session_start();
require_once '../../Database/config.php';

// Check if user is logged in
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: ../../Auth/log-reg.php");
    exit();
}

// Fetch queue items
try {
    $stmt = $pdo->query("SELECT e.*, c.course_name FROM enrollments e LEFT JOIN courses c ON e.course_id = c.courseId WHERE e.status = 'Pending Review' ORDER BY e.created_at ASC");
    $queue_items = $stmt->fetchAll();
} catch (PDOException $e) { $queue_items = []; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Queue - SMS</title>
    <link rel="icon" type="image/x-icon" href="../../Assets/image/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/admin.css">
    <style>
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .btn-back { background: var(--primary-blue); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-back:hover { background: #1034a6; transform: translateY(-2px); }
        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-gray); }
        .empty-state i { font-size: 3rem; margin-bottom: 15px; opacity: 0.3; }
    </style>
</head>
<body>
    <?php include '../Components/Side-bar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/Head-bar.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h2>Enrollment Queue</h2>
                <a href="Enrollment.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Enrollments</a>
            </div>

            <div class="table-container">
                <div class="table-header">
                    <h2>Active Queue</h2>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Queue #</th>
                                <th>Student Name</th>
                                <th>Reference Code</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($queue_items) > 0): ?>
                                <?php $count = 1; foreach ($queue_items as $item): ?>
                                    <tr>
                                        <td>#<?php echo str_pad($count++, 3, '0', STR_PAD_LEFT); ?></td>
                                        <td class="student-name"><?php echo htmlspecialchars($item->last_name . ", " . $item->first_name); ?></td>
                                        <td class="ref-code"><?php echo htmlspecialchars($item->reference_code); ?></td>
                                        <td><span class="status-badge status-pending-review"><?php echo htmlspecialchars($item->status); ?></span></td>
                                        <td>
                                            <button class="btn-view" style="padding: 6px 12px; font-size: 0.8rem;" onclick='quickView(<?php echo json_encode($item); ?>)'>Quick View</button>
                                            <a href="Enrollment.php" class="btn-approve" style="padding: 6px 12px; font-size: 0.8rem; text-decoration:none; display:inline-block;">Process</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5"><div class="empty-state"><i class="fas fa-folder-open"></i><p>No queue items found.</p></div></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick View Modal -->
    <div id="queueModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h2><i class="fas fa-search"></i> Application Preview</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="queueData">
                <!-- Populated by JS -->
            </div>
            <div class="modal-footer">
                <button class="btn-approve" onclick="location.href='Enrollment.php'">Go to Process</button>
                <button class="btn-reject" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        function quickView(data) {
            const modal = document.getElementById('queueModal');
            const container = document.getElementById('queueData');
            container.innerHTML = `
                <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9;">
                    <img src="/sms/${data.id_picture}" style="width: 80px; height: 80px; border-radius: 10px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=${data.first_name}+${data.last_name}&background=1648bc&color=fff'">
                    <div>
                        <h3 style="margin: 0; color: #1648bc;">${data.last_name}, ${data.first_name}</h3>
                        <p style="color: #64748b; margin: 3px 0;">Ref: ${data.reference_code}</p>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div><label style="font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Course</label><div style="font-weight:600;">${data.course_name}</div></div>
                    <div><label style="font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Admission</label><div style="font-weight:600;">${data.admission_type}</div></div>
                    <div><label style="font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Year Level</label><div style="font-weight:600;">${data.year_level}</div></div>
                    <div><label style="font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Wait Time</label><div style="font-weight:600;">${new Date(data.created_at).toLocaleDateString()}</div></div>
                </div>
            `;
            modal.style.display = "block";
        }
        function closeModal() { document.getElementById('queueModal').style.display = "none"; }
        window.onclick = function(event) { if (event.target == document.getElementById('queueModal')) closeModal(); }
    </script>
</body>
</html>