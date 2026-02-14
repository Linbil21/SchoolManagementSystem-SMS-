<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Requirements - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-blue: #1648bc; --bg-light: #f7fafc; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        .content-area { padding: 30px; }
        .module-header { margin-bottom: 25px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02); margin-bottom: 25px; }
        .search-row { display: flex; gap: 15px; margin-bottom: 25px; align-items: flex-end; }
        .input-group { flex: 1; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #4a5568; }
        .input-group input, .input-group select { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; }
        .btn-search { background: #1648bc; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.3s; height: 46px; }
        .req-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .req-table th { background: #f8fafc; text-align: left; padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f7; }
        .req-table td { padding: 15px; border-bottom: 1px solid #edf2f7; color: #2d3748; font-size: 0.95rem; }
        .status-badge { padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.8rem; }
        .status-verified { background: #ecfdf5; color: #059669; }
        .status-pending { background: #fffbeb; color: #d97706; }
        .status-missing { background: #fef2f2; color: #dc2626; }
        .btn-action { background: #f1f5f9; color: #475569; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 600; }
        .btn-action:hover { background: #e2e8f0; }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>Student Requirements</h1>
                <p>Monitor and verify mandatory documents for enrolled students.</p>
            </div>
            
            <div class="card">
                <div class="search-row">
                    <div class="input-group">
                        <label>Search Student</label>
                        <input type="text" placeholder="Student ID, Name, or Email...">
                    </div>
                    <div class="input-group">
                        <label>Category</label>
                        <select>
                            <option>All Requirements</option>
                            <option>Academic Records</option>
                            <option>Legal Documents</option>
                            <option>Medical Clearances</option>
                        </select>
                    </div>
                    <button class="btn-search" onclick="handleSimpleAction('Filtering Requirements')">Filter</button>
                </div>

                <table class="req-table">
                    <thead>
                        <tr>
                            <th>Requirement Name</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>PSA Birth Certificate</strong></td>
                            <td>Original copy of PSA Birth Certificate</td>
                            <td>Mandatory</td>
                            <td><span class="status-badge status-verified">Verified</span></td>
                            <td><button class="btn-action" onclick="handleSimpleAction('Viewing Document')">View</button></td>
                        </tr>
                        <tr>
                            <td><strong>Form 138 (Report Card)</strong></td>
                            <td>Senior High School Report Card</td>
                            <td>Academic</td>
                            <td><span class="status-badge status-pending">In Review</span></td>
                            <td><button class="btn-action" onclick="handleSimpleAction('Evaluating Document')">Process</button></td>
                        </tr>
                        <tr>
                            <td><strong>Good Moral Certificate</strong></td>
                            <td>Certificate of Good Moral Character</td>
                            <td>Mandatory</td>
                            <td><span class="status-badge status-missing">Missing</span></td>
                            <td><button class="btn-action" onclick="handleSimpleAction('Notifying Student')">Notified</button></td>
                        </tr>
                        <tr>
                            <td><strong>Medically Fit Certificate</strong></td>
                            <td>Latest medical examination result</td>
                            <td>Health</td>
                            <td><span class="status-badge status-verified">Verified</span></td>
                            <td><button class="btn-action" onclick="handleSimpleAction('Viewing Document')">View</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php include '../Components/GlobalScripts.php'; ?>
</body>
</html>
