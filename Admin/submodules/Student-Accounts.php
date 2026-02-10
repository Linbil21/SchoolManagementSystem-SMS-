<?php
session_start();
require_once '../../Database/config.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: ../../Auth/log-reg.php");
    exit();
}

// Fetch Registered Students from 'students' table
try {
    $stmt = $pdo->query("
        SELECT 
            s.student_id, 
            s.first_name, 
            s.last_name, 
            s.email, 
            s.contact_number, 
            s.address, 
            s.profile_image,
            s.year_level,
            s.created_at,
            c.course_code, 
            c.course_name,
            'Active' as account_status
        FROM students s 
        LEFT JOIN courses c ON s.course_id = c.courseId 
        ORDER BY s.created_at DESC
    ");
    $students = $stmt->fetchAll();
} catch (PDOException $e) { 
    $students = []; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Accounts - SMS</title>
    <link rel="icon" type="image/x-icon" href="../../Assets/image/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                    <h2>Active Student Accounts</h2>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Year Level</th>
                                <th>Account status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $s): ?>
                                <tr>
                                    <td class="ref-code"><?php echo htmlspecialchars($s->student_id); ?></td>
                                    <td class="student-name"><?php echo htmlspecialchars($s->last_name . ", " . $s->first_name); ?></td>
                                    <td><?php echo htmlspecialchars($s->course_code ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($s->year_level); ?></td>
                                    <td><span class="status-badge status-enrolled">Active</span></td>
                                    <td>
                                        <button class="btn-view" style="padding: 6px 12px; font-size: 0.8rem;" onclick='viewProfile(<?php echo json_encode($s); ?>)'>Profile</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($students)): ?>
                                <tr><td colspan="6" style="text-align:center; padding:50px; color:var(--text-gray);">No active student accounts found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2><i class="fas fa-user-graduate"></i> Student Account Profile</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="profileData">
                <!-- Populated by JS -->
            </div>
            <div class="modal-footer">
                <button class="btn-approve" onclick="window.print()">Print Information</button>
                <button class="btn-reject" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        function viewProfile(data) {
            const modal = document.getElementById('profileModal');
            const container = document.getElementById('profileData');
            const studentId = data.student_id;

            container.innerHTML = `
                <div style="display: flex; gap: 30px; align-items: center; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                    <img src="/SMS/${data.profile_image}" style="width: 120px; height: 120px; border-radius: 15px; object-fit: cover; border: 4px solid #f1f5f9;" onerror="this.src='https://ui-avatars.com/api/?name=${data.first_name}+${data.last_name}&background=1648bc&color=fff&size=128'">
                    <div>
                        <h2 style="margin: 0; color: #1648bc;">${data.last_name}, ${data.first_name}</h2>
                        <p style="color: #718096; margin: 5px 0; font-weight: 600;">ID: ${studentId}</p>
                        <span class="status-badge status-enrolled">Successfully Enrolled</span>
                    </div>
                </div>
                <div class="info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #a0aec0; font-weight: 700; text-transform: uppercase;">Course</label>
                        <span style="font-weight: 600;">${data.course_name} (${data.course_code})</span>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #a0aec0; font-weight: 700; text-transform: uppercase;">Year Level</label>
                        <span style="font-weight: 600;">${data.year_level}</span>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #a0aec0; font-weight: 700; text-transform: uppercase;">Email Address</label>
                        <span style="font-weight: 600;">${data.email}</span>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #a0aec0; font-weight: 700; text-transform: uppercase;">Contact Number</label>
                        <span style="font-weight: 600;">${data.contact_number}</span>
                    </div>
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.75rem; color: #a0aec0; font-weight: 700; text-transform: uppercase;">Home Address</label>
                        <span style="font-weight: 600;">${data.address}</span>
                    </div>
                </div>
            `;
            modal.style.display = "block";
        }

        function closeModal() { document.getElementById('profileModal').style.display = "none"; }
        window.onclick = function(event) { if (event.target == document.getElementById('profileModal')) closeModal(); }
    </script>
</body>
</html>