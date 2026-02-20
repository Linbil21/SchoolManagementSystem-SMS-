<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}

require_once '../../Database/config.php';

try {
    $stmt = $pdo->prepare("SELECT a.*, COALESCE(c.course_name, a.preferred_course_1) as course_display_name 
                           FROM admission_applications a 
                           LEFT JOIN courses c ON (a.preferred_course_1 = CAST(c.courseId AS CHAR) OR a.preferred_course_1 = c.course_name)
                           WHERE a.status = 'Pending' 
                           ORDER BY a.submission_date DESC");
    $stmt->execute();
    $applications = $stmt->fetchAll();
} catch (PDOException $e) {
    $applications = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Applications - Admission</title>
    <!-- Same styles as Settings.php (abbreviated for this example) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1648bc;
            --bg-light: #f7fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--bg-light);
            display: flex;
            min-height: 100vh;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .content-area {
            padding: 30px;
        }

        .module-header {
            margin-bottom: 25px;
        }

        .table-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #edf2f7;
            color: #718096;
            font-size: 0.85rem;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #edf2f7;
            color: #2d3748;
            font-size: 0.9rem;
        }

        /* Premium Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.8) !important;
            backdrop-filter: blur(12px) !important;
            overflow-y: auto;
            align-items: center;
            justify-content: center;
        }

        .modal[style*="display: block"] {
            display: flex !important;
        }

        .modal-content {
            background: white;
            margin: auto;
            width: 95%;
            max-width: 850px;
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            animation: modalPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes modalPop {
            from { transform: scale(0.9) translateY(20px); opacity: 0; }
            to { transform: scale(1) translateY(0); opacity: 1; }
        }

        .modal-header {
            padding: 20px 30px;
            background: #f8fafc;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 30px;
        }

        .modal-footer {
            padding: 20px 30px;
            background: #f8fafc;
            border-top: 1px solid #edf2f7;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .info-item label {
            display: block;
            font-size: 0.8rem;
            color: #718096;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .info-item p {
            font-weight: 700;
            color: #2d3748;
        }

        .document-preview {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #edf2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        /* Profile Box Styles */
        .modal-profile-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background: linear-gradient(to bottom, #f8fafc, #ffffff);
            border-radius: 20px;
            margin-bottom: 30px;
            border: 1px solid #edf2f7;
        }

        .modal-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #eef2ff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-bottom: 15px;
            border: 4px solid white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .modal-profile-box h3 {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1e293b;
        }

        .modal-profile-box p {
            color: #64748b;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header" style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <h1>New Applications</h1>
                    <p>Manage and process incoming student applications.</p>
                </div>
                <div class="search-box" style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    <input type="text" id="appSearch" onkeyup="filterTable('appSearch', 'appTable')" placeholder="Search applications..." 
                        style="padding: 10px 15px 10px 40px; border-radius: 10px; border: 1px solid #e2e8f0; outline: none; width: 280px;">
                </div>
            </div>
            <div class="table-card">
                <table id="appTable">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Applied Course</th>
                            <th>Submission Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($applications)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                                        <i class="fas fa-folder-open" style="font-size: 3rem; color: #e2e8f0;"></i>
                                        <p>No new applications found.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($app->application_no); ?></td>
                                    <td><?php echo htmlspecialchars($app->first_name . ' ' . $app->last_name); ?></td>
                                    <td><?php echo htmlspecialchars($app->course_display_name); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($app->submission_date)); ?></td>
                                    <td>
                                        <span style="background: #fef3c7; color: #d97706; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem;">
                                            <?php echo htmlspecialchars($app->status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                            $appData = [
                                                'name' => $app->first_name . ' ' . $app->last_name,
                                                'no' => $app->application_no,
                                                'course' => $app->course_display_name,
                                                'date' => date('Y-m-d', strtotime($app->submission_date)),
                                                'email' => $app->email,
                                                'phone' => $app->phone_number
                                            ];
                                        ?>
                                        <button
                                            onclick="openViewModal(<?php echo htmlspecialchars(json_encode($appData), ENT_QUOTES, 'UTF-8'); ?>)"
                                            style="border: none; background: #1648bc; color: white; padding: 6px 14px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-weight: 500;">
                                            <i class="fas fa-eye" style="font-size: 0.85rem;"></i> View
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- View Application Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-weight: 800; color: #1e293b;">Application Details</h2>
                <button onclick="closeViewModal()"
                    style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #64748b;"><i
                        class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="modal-profile-box">
                    <div class="modal-avatar" id="modalAvatar">JD</div>
                    <h3 id="modalProfileName">John Doe</h3>
                    <p id="modalProfileCourse">BS Information Technology</p>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <label>Application ID</label>
                        <p id="modalAppId">#APP-2024-001</p>
                    </div>
                    <div class="info-item">
                        <label>Full Name</label>
                        <p id="modalFullName">John Doe</p>
                    </div>
                    <div class="info-item">
                        <label>Applied Course</label>
                        <p id="modalCourse">BS Information Technology</p>
                    </div>
                    <div class="info-item">
                        <label>Submission Date</label>
                        <p id="modalDate">2024-01-10</p>
                    </div>
                    <div class="info-item">
                        <label>Email Address</label>
                        <p id="modalEmail">john.doe@example.com</p>
                    </div>
                    <div class="info-item">
                        <label>Contact Number</label>
                        <p id="modalContact">+63 912 345 6789</p>
                    </div>
                </div>

                <div style="margin-top: 30px;">
                    <h3 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 15px;">Attached
                        Documents</h3>
                    <div class="document-preview">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="fas fa-file-pdf" style="color: #ef4444; font-size: 1.5rem;"></i>
                            <span style="font-size: 0.9rem; font-weight: 600;">Form 138 (Grade 12 Report Card)</span>
                        </div>
                        <button
                            style="border: none; background: transparent; color: #1648bc; font-weight: 600; cursor: pointer;"><i
                                class="fas fa-eye"></i> View</button>
                    </div>
                    <div class="document-preview">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="fas fa-file-pdf" style="color: #ef4444; font-size: 1.5rem;"></i>
                            <span style="font-size: 0.9rem; font-weight: 600;">Good Moral Certificate</span>
                        </div>
                        <button
                            style="border: none; background: transparent; color: #1648bc; font-weight: 600; cursor: pointer;"><i
                                class="fas fa-eye"></i> View</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeViewModal()"
                    style="padding: 10px 20px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; font-weight: 600; cursor: pointer;">Close</button>
                <button onclick="proceedToEval()"
                    style="padding: 10px 25px; border-radius: 10px; background: #1648bc; color: white; border: none; font-weight: 600; cursor: pointer;">Proceed
                    to Evaluation</button>
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

        function openViewModal(data) {
            document.getElementById('modalFullName').textContent = data.name;
            document.getElementById('modalProfileName').textContent = data.name;
            document.getElementById('modalAppId').textContent = data.no;
            document.getElementById('modalCourse').textContent = data.course;
            document.getElementById('modalProfileCourse').textContent = data.course;
            document.getElementById('modalDate').textContent = data.date;
            document.getElementById('modalEmail').textContent = data.email;
            document.getElementById('modalContact').textContent = data.phone;

            // Set initials for avatar
            const initials = data.name.split(' ').map(n => n[0]).join('').toUpperCase();
            document.getElementById('modalAvatar').textContent = initials;

            document.getElementById('viewModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function proceedToEval() {
            window.location.href = 'Evaluation.php';
        }

        window.onclick = function (event) {
            const modal = document.getElementById('viewModal');
            if (event.target == modal) {
                closeViewModal();
            }
        }
    </script>
</body>

</html>