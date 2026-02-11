<?php
session_start();
require_once '../../Database/config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: ../../Auth/log-reg.php");
    exit();
}

// Fetch Enrollment History (Approved or Rejected students)
try {
    $stmt = $pdo->query("SELECT e.*, c.course_code, c.course_name FROM enrollments e LEFT JOIN courses c ON e.course_id = c.courseId WHERE e.status IN ('Enrolled', 'Rejected') ORDER BY e.created_at DESC");
    $history = $stmt->fetchAll();
} catch (PDOException $e) {
    $history = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment History - SMS</title>
    <link rel="icon" type="image/x-icon" href="../../Assets/image/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>
    <?php include '../Components/Side-bar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/Head-bar.php'; ?>
        <div class="content-area">
            <div class="table-container">
                <div class="table-header">
                    <h2>Enrollment Archive</h2>
                    <div style="display:flex; gap:10px;">
                        <input type="text" placeholder="Search record..."
                            style="padding:8px; border:1px solid #ddd; border-radius:6px; font-size:0.9rem;">
                    </div>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Ref Code</th>
                                <th>Student Name</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $h): ?>
                                <tr>
                                    <td class="ref-code"><?php echo htmlspecialchars($h->reference_code); ?></td>
                                    <td class="student-name">
                                        <?php echo htmlspecialchars($h->last_name . ", " . $h->first_name); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($h->course_code); ?></td>
                                    <td>
                                        <span
                                            class="status-badge <?php echo ($h->status == 'Enrolled') ? 'status-enrolled' : 'status-reject'; ?>"
                                            style="<?php echo ($h->status == 'Rejected') ? 'background:#fee2e2; color:#b91c1c;' : ''; ?>">
                                            <?php echo htmlspecialchars($h->status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-view" style="padding: 6px 12px; font-size: 0.8rem;"
                                            onclick='viewArchive(<?php echo json_encode($h); ?>)'>View</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($history)): ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding:50px;">No archived records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Archive Details Modal -->
    <div id="archiveModal" class="modal">
        <div class="modal-content" style="max-width: 550px;">
            <div class="modal-header">
                <h2><i class="fas fa-history"></i> Archived Record Details</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="archiveData">
                <!-- Populated by JS -->
            </div>
            <div class="modal-footer">
                <button class="btn-approve" onclick="window.print()">Print Record</button>
                <button class="btn-reject" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        function viewArchive(data) {
            const modal = document.getElementById('archiveModal');
            const container = document.getElementById('archiveData');
            const archivedDate = new Date(data.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

            container.innerHTML = `
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="/sms/${data.id_picture}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; background: #f8fafc;" onerror="this.src='https://ui-avatars.com/api/?name=${data.first_name}+${data.last_name}&background=64748b&color=fff'">
                    <h3 style="margin: 10px 0 5px 0;">${data.last_name}, ${data.first_name}</h3>
                    <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Ref: ${data.reference_code}</p>
                </div>
                <div style="background: #f8fafc; padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: #64748b;">Final Status:</span>
                        <span style="font-weight: 600; color: ${data.status === 'Enrolled' ? '#10b981' : '#ef4444'}">${data.status}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: #64748b;">Course:</span>
                        <span style="font-weight: 600;">${data.course_name}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: #64748b;">Contact:</span>
                        <span style="font-weight: 600;">${data.contact_number}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">Date Archived:</span>
                        <span style="font-weight: 600;">${archivedDate}</span>
                    </div>
                </div>
                <div style="font-size: 0.85rem; color: #64748b; line-height: 1.6;">
                    <strong>Note:</strong> This record is archived and can no longer be modified. For reactivation, please contact the System Administrator.
                </div>
            `;
            modal.style.display = "block";
        }

        function closeModal() { document.getElementById('archiveModal').style.display = "none"; }
        window.onclick = function (event) { if (event.target == document.getElementById('archiveModal')) closeModal(); }
    </script>
</body>

</html>