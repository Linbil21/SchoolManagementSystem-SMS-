<?php
session_start();

// Robust absolute-relative path logic
$script_name = $_SERVER['SCRIPT_NAME'];
$check_paths = ['/student/', '/Super-admin/', '/Admin/', '/Cashier/', '/Admission/', '/auth/', '/modules/'];
$project_base = '';
foreach ($check_paths as $path) {
    if (($pos = stripos($script_name, $path)) !== false) {
        $project_base = rtrim(substr($script_name, 0, $pos), '/');
        break;
    }
}
$root = $project_base . '/';

// Handle AJAX Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_requirement') {
    require_once dirname(__DIR__, 3) . '/Database/config.php';
    header('Content-Type: application/json');
    
    $email = $_SESSION['email'] ?? null;
    $field = $_POST['field'] ?? null;
    $valid_fields = ['id_picture', 'passport', 'birth_cert', 'form_138', 'good_moral', 'form_137'];

    if (!$email || !in_array($field, $valid_fields) || !isset($_FILES['requirement'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid upload request.']);
        exit;
    }

    try {
        $file = $_FILES['requirement'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

        if (!in_array($ext, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, and PDF files are allowed.']);
            exit;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB.']);
            exit;
        }

        $target_dir = dirname(__DIR__, 3) . "/Assets/image/uploads/requirements/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $filename = $field . "_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
        $target_path = $target_dir . $filename;
        $relative_path = "Assets/image/uploads/requirements/" . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            // Update database
            $stmt = $pdo->prepare("UPDATE enrollments SET {$field} = ? WHERE email = ?");
            $stmt->execute([$relative_path, $email]);
            
            echo json_encode(['success' => true, 'message' => 'File uploaded and secured successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save file.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// Handle AJAX Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_requirement') {
    require_once dirname(__DIR__, 3) . '/Database/config.php';
    header('Content-Type: application/json');
    $email = $_SESSION['email'] ?? null;
    $field = $_POST['field'] ?? null;

    $valid_fields = ['id_picture', 'passport', 'birth_cert', 'form_138', 'good_moral', 'form_137'];

    if (!$email || !in_array($field, $valid_fields)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE enrollments SET {$field} = NULL WHERE email = ?");
        $stmt->execute([$email]);
        echo json_encode(['success' => true, 'message' => 'Requirement deleted successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Requirements</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #eff6ff;
            --secondary: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--bg);
            display: flex;
            min-height: 100vh;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .content-area {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .page-subtitle {
            color: var(--secondary);
            font-size: 0.95rem;
        }

        .requirements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        .req-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            border: 1px solid var(--border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .req-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .req-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .req-icon {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .status-badge {
            font-size: 0.72rem;
            padding: 6px 14px;
            border-radius: 50px;
            background: #f1f5f9;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .status-badge.uploaded {
            background: #dcfce7 !important;
            color: #15803d !important;
        }

        .req-title {
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 8px;
            font-size: 1.15rem;
            letter-spacing: -0.3px;
        }

        .req-desc {
            font-size: 0.88rem;
            color: var(--secondary);
            margin-bottom: 24px;
            line-height: 1.6;
            flex-grow: 1;
        }

        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            background: #fcfdfe;
        }

        .upload-area:hover {
            border-color: var(--primary);
            background: #eff6ff;
        }

        .upload-area input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .upload-label {
            display: block;
            margin-top: 12px;
            font-size: 0.88rem;
            color: var(--primary);
            font-weight: 700;
        }

        .upload-icon {
            font-size: 1.8rem;
            color: #94a3b8;
            transition: 0.3s;
        }

        .upload-area:hover .upload-icon {
            transform: scale(1.1);
            color: var(--primary);
        }

        .btn-action {
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-view {
            background: var(--primary);
            color: white;
        }

        .btn-view:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #fee2e2;
            color: #ef4444;
        }

        .btn-delete:hover {
            background: #fecaca;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Student Requirements List</h1>
                <p class="page-subtitle">View and manage your submitted enrollment requirements.</p>
            </div>

            <!-- Payment Reminder Notice -->
            <div style="background: linear-gradient(135deg, #fef3c7, #fffbeb); border: 1.5px solid #fcd34d; border-radius: 16px; padding: 18px 24px; margin-bottom: 28px; display: flex; align-items: center; gap: 16px;">
                <div style="min-width: 44px; height: 44px; background: #fef08a; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                    ⚠️
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: #92400e; font-size: 0.95rem; margin-bottom: 3px;">Payment Required Before Submission</div>
                    <div style="font-size: 0.85rem; color: #78350f; line-height: 1.5;">Please visit the <strong>school cashier</strong> first to pay your <strong>Downpayment, Half Payment, or Full Payment</strong> and secure an <strong>Official Receipt</strong> before submitting your requirements.</div>
                </div>
                <a href="<?php echo $root; ?>student/Modules/Payments/History.php" style="flex-shrink: 0; padding: 9px 18px; background: #d97706; color: white; border-radius: 10px; font-size: 0.82rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 7px; white-space: nowrap;">
                    <i class="fas fa-file-invoice"></i> Issue Receipt
                </a>
            </div>

            <div class="requirements-grid">
                <?php
                // Fetch enrollment status for the current student
                require_once dirname(__DIR__, 3) . '/Database/config.php';
                $email = $_SESSION['email'] ?? '';
                $enrollment = null;
                if ($email) {
                    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE email = ?");
                    $stmt->execute([$email]);
                    $enrollment = $stmt->fetch(PDO::FETCH_OBJ);
                }

                $docs = [
                    ['title' => '2x2 ID Picture', 'field' => 'id_picture', 'desc' => 'Recent 2x2 colored picture with white background and name tag.', 'icon' => 'fa-id-badge', 'color' => '#3b82f6'],
                    ['title' => 'Passport', 'field' => 'passport', 'desc' => 'Valid passport (Bio page) for identity verification.', 'icon' => 'fa-passport', 'color' => '#8b5cf6'],
                    ['title' => 'PSA Birth Certificate', 'field' => 'birth_cert', 'desc' => 'Original copy of Philippine Statistics Authority (PSA) Birth Certificate.', 'icon' => 'fa-file-invoice', 'color' => '#10b981'],
                    ['title' => 'Form 138 (Report Card)', 'field' => 'form_138', 'desc' => 'Senior High School Report Card / Grade 12 Report Card.', 'icon' => 'fa-scroll', 'color' => '#f59e0b'],
                    ['title' => 'Good Moral Character', 'field' => 'good_moral', 'desc' => 'Certificate of Good Moral Character from the last school attended.', 'icon' => 'fa-certificate', 'color' => '#ef4444'],
                    ['title' => 'Form 137 (TOR)', 'field' => 'form_137', 'desc' => 'Permanent record from the previous school.', 'icon' => 'fa-file-alt', 'color' => '#6366f1'],
                ];

                foreach ($docs as $doc):
                    // Ensure the column exists in the object even if it's new
                    $isSubmitted = $enrollment && !empty($enrollment->{$doc['field']});
                ?>
                <div class="req-card" style="transition: transform 0.3s ease; cursor: default;">
                    <div class="req-header">
                        <div class="req-icon" style="background: <?php echo $doc['color']; ?>15; color: <?php echo $doc['color']; ?>;">
                            <i class="fas <?php echo $doc['icon']; ?>"></i>
                        </div>
                        <span class="status-badge <?php echo $isSubmitted ? 'uploaded' : ''; ?>" style="<?php echo $isSubmitted ? 'background: #dcfce7; color: #166534;' : ''; ?>">
                            <i class="fas <?php echo $isSubmitted ? 'fa-check-circle' : 'fa-clock'; ?>" style="margin-right: 4px;"></i>
                            <?php echo $isSubmitted ? 'Submitted' : 'Pending'; ?>
                        </span>
                    </div>
                    <h3 class="req-title"><?php echo $doc['title']; ?></h3>
                    <p class="req-desc"><?php echo $doc['desc']; ?></p>
                    <?php if ($isSubmitted): ?>
                        <div class="upload-area" style="border: 2px solid #22c55e; background: #f0fdf4; cursor: default;">
                            <i class="fas fa-check-circle" style="color: #22c55e; font-size: 2rem; margin-bottom: 10px;"></i>
                            <span class="upload-label" style="color: #16a34a; font-size: 1rem;">File Secured</span>
                            <div style="display: flex; gap: 12px; justify-content: center; margin-top: 20px;">
                                <a href="<?php echo $root . htmlspecialchars($enrollment->{$doc['field']}); ?>" target="_blank" class="btn-action btn-view">
                                    <i class="fas fa-eye"></i> View File
                                </a>
                                <button onclick="deleteDocument('<?php echo $doc['field']; ?>', '<?php echo $doc['title']; ?>')" class="btn-action btn-delete">
                                    <i class="fas fa-trash-alt"></i> Remove
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="upload-area">
                            <input type="file" onchange="handleFileUpload(this, '<?php echo $doc['field']; ?>', '<?php echo $doc['title']; ?>')" accept="image/*,application/pdf">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <span class="upload-label">Upload <?php echo $doc['title']; ?></span>
                            <p style="font-size: 0.72rem; color: #94a3b8; margin-top: 8px;">Max size: 5MB (JPG, PNG, PDF)</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

    <script>
        async function handleFileUpload(input, field, title) {
            if (!input.files || !input.files[0]) return;

            const file = input.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (file.size > maxSize) {
                Swal.fire('File Too Large', 'Please upload a file smaller than 5MB.', 'error');
                input.value = '';
                return;
            }

            Swal.fire({
                title: 'Uploading...',
                text: `Sending ${title} to secure storage`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('requirement', file);
            formData.append('field', field);
            formData.append('action', 'upload_requirement');

            try {
                // In this simplified setup, we'll use the same file for handling the upload action
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success'
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Communication with server failed.', 'error');
            }
        }

        async function deleteDocument(field, title) {
            const result = await Swal.fire({
                title: 'Confirm Deletion',
                text: `Are you sure you want to remove your ${title}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Keep it'
            });

            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete_requirement');
                formData.append('field', field);

                try {
                    const response = await fetch('', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        Swal.fire('Success', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Something went wrong.', 'error');
                }
            }
        }
    </script>
</body>

</html>
