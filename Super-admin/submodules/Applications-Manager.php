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
    <title>Applications Manager - Super Admin</title>
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
                    <h1 style="font-weight: 800; color: #1e293b;">Applications Manager</h1>
                    <p style="color: #64748b;">Oversee and audit student admission applications.</p>
                </div>
            </div>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Reference ID</th>
                            <th>Applied Course</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="avatar-circle">JD</div>
                                    <div>
                                        <p style="font-weight: 700;">John Doe</p>
                                        <p style="font-size: 0.8rem; color: var(--text-gray);">john@sms.com</p>
                                    </div>
                                </div>
                            </td>
                            <td><span style="font-weight: 600;">#APP-2024-001</span></td>
                            <td>BS Architecture</td>
                            <td><span class="status-pill status-pending">Pending Review</span></td>
                            <td>
                                <button class="btn-action" title="Manage Application"
                                    onclick="openModal('John Doe', '#APP-2024-001', 'BS Architecture', 'Pending Review')">
                                    <i class="fas fa-cog"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="avatar-circle" style="background: #3b82f6;">JS</div>
                                    <div>
                                        <p style="font-weight: 700;">Jane Smith</p>
                                        <p style="font-size: 0.8rem; color: var(--text-gray);">jane@sms.com</p>
                                    </div>
                                </div>
                            </td>
                            <td><span style="font-weight: 600;">#APP-2024-002</span></td>
                            <td>BS Computer Science</td>
                            <td><span class="status-pill status-approved">Approved</span></td>
                            <td>
                                <button class="btn-action" title="Manage Application"
                                    onclick="openModal('Jane Smith', '#APP-2024-002', 'BS Computer Science', 'Approved')">
                                    <i class="fas fa-cog"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Application Action Modal -->
    <div id="actionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-weight: 800; color: var(--text-dark);">Application Review</h2>
                <i class="fas fa-times" style="cursor: pointer; color: var(--text-gray);" onclick="closeModal()"></i>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <span class="detail-label">Full Name</span>
                    <span class="detail-value" id="valName"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Application ID</span>
                    <span class="detail-value" id="valId"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Target Course</span>
                    <span class="detail-value" id="valCourse"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Current Status</span>
                    <span class="detail-value" id="valStatus"></span>
                </div>

                <div class="form-group" style="margin-top: 30px;">
                    <label>Super Admin Override</label>
                    <select>
                        <option>Keep Current Status</option>
                        <option>Force Approve</option>
                        <option>Force Reject</option>
                        <option>Request Re-evaluation</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal()" style="padding: 12px 24px; border-radius: 12px; border: 1px solid var(--border-color); background: white; font-weight: 600; cursor: pointer; color: var(--text-gray);">Cancel</button>
                <button onclick="closeModal()" class="btn-primary">Save Changes</button>
            </div>
        </div>
    </div>

    <script>
        function openModal(name, id, course, status) {
            document.getElementById('valName').textContent = name;
            document.getElementById('valId').textContent = id;
            document.getElementById('valCourse').textContent = course;
            document.getElementById('valStatus').textContent = status;
            document.getElementById('actionModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('actionModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        window.onclick = function (event) {
            if (event.target == document.getElementById('actionModal')) {
                closeModal();
            }
        }
    </script>
</body>

</html>
