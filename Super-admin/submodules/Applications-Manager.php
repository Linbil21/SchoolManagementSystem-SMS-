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
    <title>Applications Manager - Super Admin</title>
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
    <div id="actionModal" class="modal-overlay">
        <div class="modal-content-premium">
            <div class="modal-header-premium">
                <div class="modal-title-group">
                    <div class="modal-icon-bg">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div>
                        <h2>Application Review</h2>
                        <p>Manage and override application status</p>
                    </div>
                </div>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-body-premium">
                <!-- Applicant Details Card -->
                <div class="details-card">
                    <h3 class="section-label">Applicant Information</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="label">Full Name</span>
                            <span class="value" id="valName">--</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Application ID</span>
                            <span class="value mono" id="valId">--</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Target Course</span>
                            <span class="value highlight" id="valCourse">--</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Current Status</span>
                            <span class="status-badge" id="valStatusBadge">--</span>
                        </div>
                    </div>
                </div>

                <!-- Action Section -->
                <div class="action-section">
                    <h3 class="section-label">Super Admin Override</h3>
                    <div class="form-group-premium">
                        <label>Update Status</label>
                        <div class="select-wrapper">
                            <i class="fas fa-shield-alt select-icon"></i>
                            <select class="premium-select">
                                <option>Keep Current Status</option>
                                <option value="approved">Force Approve</option>
                                <option value="rejected">Force Reject</option>
                                <option value="reeval">Request Re-evaluation</option>
                            </select>
                            <i class="fas fa-chevron-down arrow-down"></i>
                        </div>
                        <p class="field-hint">This action will override any ongoing evaluation processes.</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer-premium">
                <button onclick="closeModal()" class="btn-cancel-premium">Cancel</button>
                <button onclick="closeModal()" class="btn-save-premium">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </div>

    <style>
        /* Premium Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.show {
            display: flex;
            opacity: 1;
        }

        .modal-content-premium {
            background: #ffffff;
            width: 100%;
            max-width: 550px;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.95);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .modal-overlay.show .modal-content-premium {
            transform: scale(1);
        }

        .modal-header-premium {
            padding: 24px 30px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background: #f8fafc;
        }

        .modal-title-group {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .modal-icon-bg {
            width: 48px;
            height: 48px;
            background: #eef2ff;
            color: #6366f1;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            border: 1px solid #e0e7ff;
        }

        .modal-header-premium h2 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
            line-height: 1.2;
        }

        .modal-header-premium p {
            font-size: 0.85rem;
            color: #64748b;
            margin: 4px 0 0 0;
            font-weight: 500;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 1.25rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: #f1f5f9;
            color: #ef4444;
        }

        .modal-body-premium {
            padding: 30px;
        }

        .details-card {
            background: #fcfcfc;
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .section-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            font-weight: 700;
            margin-bottom: 15px;
            display: block;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .detail-item .label {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
        }

        .detail-item .value {
            font-size: 0.95rem;
            color: #1e293b;
            font-weight: 600;
        }

        .detail-item .value.mono {
            font-family: 'Courier New', monospace;
            letter-spacing: -0.5px;
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            width: fit-content;
        }

        .detail-item .value.highlight {
            color: #6366f1;
        }

        .status-badge {
            display: inline-flex;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: capitalize;
        }

        .status-badge.pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
        .status-badge.approved { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
        .status-badge.rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

        .form-group-premium label {
            display: block;
            margin-bottom: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e293b;
        }

        .select-wrapper {
            position: relative;
        }

        .select-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            pointer-events: none;
        }

        .arrow-down {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            font-size: 0.8rem;
        }

        .premium-select {
            width: 100%;
            padding: 14px 40px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 0.95rem;
            color: #1e293b;
            appearance: none;
            background: #ffffff;
            font-family: 'Poppins', sans-serif;
            transition: all 0.2s;
            cursor: pointer;
        }

        .premium-select:hover {
            border-color: #cbd5e1;
        }

        .premium-select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .field-hint {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .field-hint::before {
            content: '\f05a';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
        }

        .modal-footer-premium {
            padding: 24px 30px;
            border-top: 1px solid #f1f5f9;
            background: #fcfcfc;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-cancel-premium {
            padding: 12px 24px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #64748b;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            font-family: 'Poppins', sans-serif;
        }

        .btn-cancel-premium:hover {
            background: #f8fafc;
            color: #475569;
        }

        .btn-save-premium {
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
            transition: 0.2s;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-save-premium:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
        }
    </style>

    <script>
        function openModal(name, id, course, status) {
            document.getElementById('valName').textContent = name;
            document.getElementById('valId').textContent = id;
            document.getElementById('valCourse').textContent = course;
            
            // Set status with correct badge style
            const statusBadge = document.getElementById('valStatusBadge');
            statusBadge.textContent = status;
            
            // Reset classes
            statusBadge.className = 'status-badge';
            
            // Determine class based on status text
            if (status.toLowerCase().includes('pending')) {
                statusBadge.classList.add('pending');
            } else if (status.toLowerCase().includes('approved')) {
                statusBadge.classList.add('approved');
            } else if (status.toLowerCase().includes('rejected')) {
                statusBadge.classList.add('rejected');
            } else {
                statusBadge.classList.add('pending'); // Default
            }

            const modal = document.getElementById('actionModal');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('actionModal');
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        window.onclick = function (event) {
            const modal = document.getElementById('actionModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>

