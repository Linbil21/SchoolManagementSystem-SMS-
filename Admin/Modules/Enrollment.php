<?php
session_start();
require_once '../../Database/config.php';

// Check if user is logged in
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: ../../auth/Login.php");
    exit();
}

// Detect root path for assets
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/Admin/', '/Super-admin/', '/Cashier/', '/Admission/', '/student/', '/modules/', '/submodules/', '/auth/'];
$project_base = '';

foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        // Extract everything BEFORE the module folder
        $project_base = substr($script_name, 0, $pos);
        break;
    }
}

// Build root path - ensure it starts with / and ends with /
if (empty($project_base) || $project_base === '') {
    $root = '/';
} else {
    $root = rtrim($project_base, '/') . '/';
    if ($root[0] !== '/') {
        $root = '/' . $root;
    }
}



// 1. Handle AJAX Status Updates (Self-submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once '../../auth/Security.php';
    
    // Check if Read-Only mode is active
    if (isReadOnly()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'View-Only Mode: Super Admin cannot modify data in this portal.']);
        exit;
    }

    // ACTION: UPDATE STATUS
    if ($_POST['action'] === 'update_status') {
        require_once '../../auth/Security.php';
        header('Content-Type: application/json');
        $enrollmentId = $_POST['enrollmentId'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$enrollmentId || !$status) {
            echo json_encode(['success' => false, 'message' => 'Parameters missing.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE enrollmentId = ?");
            $stmt->execute([$status, $enrollmentId]);
            echo json_encode(['success' => true, 'message' => 'Status updated.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Update failed.']);
        }
        exit;
    }

    // ACTION: LOG REVEAL
    if ($_POST['action'] === 'log_reveal') {
        // Here we would effectively log to a database table `audit_logs`
        // For now, we just acknowledge the request or log to a file
        $user = $_SESSION['role'] ?? 'Unknown';
        $item = $_POST['data_type'] ?? 'Unknown Data';
        $ref = $_POST['enrollment_ref'] ?? 'Unknown Ref';
        
        // Example File Log:
        $logEntry = date('Y-m-d H:i:s') . " | User: $user | Revealed: $item | Ref: $ref" . PHP_EOL;
        file_put_contents('../../logs/audit.log', $logEntry, FILE_APPEND | LOCK_EX);
        
        echo json_encode(['success' => true]);
        exit;
    }
}

$role = $_SESSION['role'];

// Fetch enrollments from database
try {
    $stmt = $pdo->query("SELECT e.*, c.course_name FROM enrollments e LEFT JOIN courses c ON e.course_id = c.courseId ORDER BY e.created_at DESC");
    $enrollments = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Management - SMS</title>
    <link rel="icon" type="image/png" href="<?php echo $root; ?>Assets/image/logo.png">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- External CSS -->
    <link rel="stylesheet" href="<?php echo $root; ?>Admin/Assets/layout.css">
</head>

<body>

    <!-- Sidebar -->
    <?php include '../Components/Side-bar.php'; ?>

    <div class="main-wrapper">
        <!-- Head-bar -->
        <?php include '../Components/Head-bar.php'; ?>

        <div class="content-area">
            <div class="table-container">
                <div class="table-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>Enrollment List</h2>
                    <div class="search-box" style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="text" id="enrollmentSearch" onkeyup="filterTable('enrollmentSearch', 'enrollmentTable')" placeholder="Search enrollments..." 
                            style="padding: 10px 15px 10px 40px; border-radius: 10px; border: 1px solid #e2e8f0; outline: none; width: 280px;">
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="enrollmentTable">
                        <thead>
                            <tr>
                                <th>Reference Code</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($enrollments) > 0): ?>
                                <?php foreach ($enrollments as $enroll): ?>
                                    <tr>
                                        <td class="ref-code"><?php echo htmlspecialchars($enroll->reference_code); ?></td>
                                        <td class="student-name">
                                            <?php echo htmlspecialchars($enroll->last_name . ", " . $enroll->first_name . " " . ($enroll->middle_name ?? '')); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $status_class = 'status-pending-review';
                                            if ($enroll->status == 'Enrolled')
                                                $status_class = 'status-enrolled';
                                            elseif ($enroll->status == 'Pending Payment')
                                                $status_class = 'status-pending-payment';
                                            ?>
                                            <span class="status-badge <?php echo $status_class; ?>">
                                                <?php echo htmlspecialchars($enroll->status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" class="btn-view"
                                                onclick='viewEnrollment(<?php echo json_encode($enroll); ?>)'>View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 50px; color: var(--text-gray);">No
                                        enrollment records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle" style="font-weight: 800; font-size: 1.4rem;">Enrollment Details</h2>
                <div class="close" onclick="closeModal()"><i class="fas fa-times"></i></div>
            </div>
            <div class="modal-body">
                <div class="info-grid" id="modalData">
                    <!-- Data will be populated by JS -->
                </div>
            </div>
            <div class="modal-footer">
                <?php if ($role === 'superadmin' && isReadOnly()): ?>
                    <p style="color: #6366f1; font-size: 0.85rem; font-weight: 600; margin-right: auto; background: #eef2ff; padding: 10px 15px; border-radius: 12px; border: 1px solid #e0e7ff;">
                        <i class="fas fa-shield-alt"></i> SYSTEM VIEW: You are in View-Only mode for this portal.
                    </p>
                    <button class="btn-view" style="background: #e2e8f0; color: #475569; box-shadow: none;" onclick="closeModal()">Close Viewer</button>
                <?php elseif ($role === 'superadmin' || $role === 'admin'): ?>
                    <button class="btn-approve" onclick="updateStatus('Enrolled')"><i class="fas fa-check-circle"></i> Approve</button>
                    <button class="btn-reject" onclick="updateStatus('Rejected')"><i class="fas fa-times-circle"></i> Reject</button>
                    <button class="btn-edit-modal" onclick="editEnrollment()"><i class="fas fa-edit"></i> Edit</button>
                <?php else: ?>
                    <p style="color: #64748b; font-size: 0.85rem; font-style: italic; margin-right: auto;">
                        <i class="fas fa-info-circle"></i> Account management restricted
                    </p>
                    <button class="btn-view" style="background: #e2e8f0; color: #475569; box-shadow: none;" onclick="closeModal()">Close Details</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function filterTable(inputId, tableId) {
            const input = document.getElementById(inputId);
            const filter = input.value.toLowerCase();
            const table = document.getElementById(tableId);
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let rowVisible = false;
                const td = tr[i].getElementsByTagName("td");
                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            rowVisible = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = rowVisible ? "" : "none";
            }
        }

        let currentEnrollment = null;

        function viewEnrollment(data) {
            currentEnrollment = data;
            const modal = document.getElementById('viewModal');
            const container = document.getElementById('modalData');

            // Masking Logic
            const maskEmail = (email) => {
                if(!email) return '';
                const parts = email.split('@');
                if(parts.length < 2) return email;
                return parts[0].substring(0, 1) + '***@' + parts[1];
            };
            const maskPhone = (phone) => {
                if(!phone || phone.length < 5) return phone;
                return phone.substring(0, 4) + '****' + phone.substring(phone.length - 3);
            };

            const maskedEmail = maskEmail(data.email);
            const maskedContact = maskPhone(data.contact_number);
            const maskedGuardianContact = maskPhone(data.guardian_contact);

            let html = `
                <div class="profile-pic-container">
                    <img src="/${data.id_picture}" alt="Profile" class="student-profile-img" onerror="this.src='https://ui-avatars.com/api/?name=${data.first_name}+${data.last_name}&background=1648bc&color=fff'">
                </div>
                <div class="section-title">ENROLLMENT INFORMATION</div>
                <div class="info-item"><div class="info-label">Reference Code</div><div class="info-value">${data.reference_code}</div></div>
                <div class="info-item"><div class="info-label">Status</div><div class="info-value">${data.status}</div></div>
                <div class="info-item"><div class="info-label">Admission Type</div><div class="info-value">${data.admission_type}</div></div>
                <div class="info-item"><div class="info-label">Course</div><div class="info-value">${data.course_name || 'N/A'}</div></div>
                <div class="info-item"><div class="info-label">Year Level</div><div class="info-value">${data.year_level}</div></div>
                
                <div class="section-title">STUDENT INFORMATION</div>
                <div class="info-item"><div class="info-label">Full Name</div><div class="info-value">${data.last_name}, ${data.first_name} ${data.middle_name || ''}</div></div>
                <div class="info-item"><div class="info-label">Gender</div><div class="info-value">${data.gender}</div></div>
                <div class="info-item"><div class="info-label">Birthdate</div><div class="info-value">${data.birthdate}</div></div>
                
                <!-- PRIVATE DATA WITH MASKING -->
                <div class="info-item">
                    <div class="info-label">Contact</div>
                    <div class="info-value">
                        <span id="display-contact">${maskedContact}</span>
                        <i class="fas fa-eye reveal-btn" onclick="revealData('display-contact', '${data.contact_number}', this)" title="Reveal"></i>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">
                        <span id="display-email">${maskedEmail}</span>
                        <i class="fas fa-eye reveal-btn" onclick="revealData('display-email', '${data.email}', this)" title="Reveal"></i>
                    </div>
                </div>
                
                <div class="info-item" style="grid-column: span 2;"><div class="info-label">Address</div><div class="info-value">${data.address}</div></div>

                <div class="section-title">DOCUMENTS</div>
                <div class="info-item" style="grid-column: span 2;">
                    ${data.birth_cert ? `<div class="doc-item"><span>Birth Certificate (PSA)</span> <a href="/${data.birth_cert}" target="_blank" class="btn-doc-view"><i class="fas fa-eye"></i> View</a></div>` : ''}
                    ${data.form_138 ? `<div class="doc-item"><span>Form 138 (Report Card)</span> <a href="/${data.form_138}" target="_blank" class="btn-doc-view"><i class="fas fa-eye"></i> View</a></div>` : ''}
                    ${data.form_137 ? `<div class="doc-item"><span>Form 137</span> <a href="/${data.form_137}" target="_blank" class="btn-doc-view"><i class="fas fa-eye"></i> View</a></div>` : ''}
                    ${data.good_moral ? `<div class="doc-item"><span>Good Moral</span> <a href="/${data.good_moral}" target="_blank" class="btn-doc-view"><i class="fas fa-eye"></i> View</a></div>` : ''}
                    ${data.barangay_clearance ? `<div class="doc-item"><span>Barangay Clearance</span> <a href="/${data.barangay_clearance}" target="_blank" class="btn-doc-view"><i class="fas fa-eye"></i> View</a></div>` : ''}
                </div>

                <div class="section-title">GUARDIAN INFORMATION</div>
                <div class="info-item"><div class="info-label">Guardian Name</div><div class="info-value">${data.guardian_last}, ${data.guardian_first}</div></div>
                <div class="info-item"><div class="info-label">Relationship</div><div class="info-value">${data.relationship}</div></div>
                <div class="info-item">
                    <div class="info-label">Guardian Contact</div>
                    <div class="info-value">
                        <span id="display-guardian-contact">${maskedGuardianContact}</span>
                         <i class="fas fa-eye reveal-btn" onclick="revealData('display-guardian-contact', '${data.guardian_contact}', this)" title="Reveal"></i>
                    </div>
                </div>

                <div class="section-title">EDUCATIONAL BACKGROUND</div>
                <div class="info-item"><div class="info-label">Primary School</div><div class="info-value">${data.primary_school} (${data.primary_year})</div></div>
                <div class="info-item"><div class="info-label">Secondary School</div><div class="info-value">${data.secondary_school} (${data.secondary_year})</div></div>
            `;

            container.innerHTML = html;
            modal.style.display = "block";
        }

        function closeModal() {
            document.getElementById('viewModal').style.display = "none";
        }

        window.onclick = function (event) {
            const modal = document.getElementById('viewModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        async function updateStatus(newStatus) {
            if (!currentEnrollment) return;

            const confirmText = newStatus === 'Enrolled' ? 'approve' : 'reject';
            const confirmColor = newStatus === 'Enrolled' ? '#10b981' : '#ef4444';

            const result = await Swal.fire({
                title: 'Are you sure?',
                text: `You are about to ${confirmText} this enrollment for ${currentEnrollment.first_name} ${currentEnrollment.last_name}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#718096',
                confirmButtonText: `Yes, ${confirmText} it!`
            });

            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'update_status');
                formData.append('enrollmentId', currentEnrollment.enrollmentId);
                formData.append('status', newStatus);

                try {
                    const response = await fetch('Enrollment.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        Swal.fire(
                            'Updated!',
                            `The enrollment has been ${newStatus.toLowerCase()}.`,
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            }
        }

        function editEnrollment() {
            if (!currentEnrollment) return;
            Swal.fire({
                title: 'Edit Feature',
                text: 'The edit module is currently under development. You will be able to modify student details here soon.',
                icon: 'info',
                confirmButtonColor: '#1648bc'
            });
        }
        // ... existing functions ...
        function revealData(elementId, realData, btn) {
            const element = document.getElementById(elementId);
            if (element.textContent.includes('*')) {
                // REVEAL
                element.textContent = realData;
                btn.classList.remove('fa-eye');
                btn.classList.add('fa-eye-slash');
                
                // Log the reveal action
                const formData = new FormData();
                formData.append('action', 'log_reveal');
                formData.append('data_type', elementId);
                formData.append('enrollment_ref', currentEnrollment.reference_code || 'N/A');
                fetch('Enrollment.php', { method: 'POST', body: formData });

            } else {
                // MASK AGAIN (Optional, basic masking logic or just reload modal)
                // For simplicity, we just toggle icon back, but re-masking properly requires the original mask function 
                // or we just reload the modal to re-mask.
                // Let's just re-render the modal to be safe and simple
                viewEnrollment(currentEnrollment);
            }
        }
    </script>
    <style>
        .reveal-btn {
            margin-left: 10px;
            color: #64748b;
            cursor: pointer;
            transition: 0.2s;
        }
        .reveal-btn:hover {
            color: #1648bc;
        }
    </style>
</body>

</html>