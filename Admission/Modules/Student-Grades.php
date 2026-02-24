<?php
require_once __DIR__ . '/../../auth/Security.php';
checkRole(['admission']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grades - Admission</title>
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
        .grades-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .grades-table th { background: #f8fafc; text-align: left; padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #edf2f7; }
        .grades-table td { padding: 15px; border-bottom: 1px solid #edf2f7; color: #2d3748; font-size: 0.95rem; }
        .grade-badge { padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; }
        .grade-passed { background: #f0fdf4; color: #16a34a; }
        .grade-failed { background: #fef2f2; color: #dc2626; }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>Student Grades Management</h1>
                <p>View and manage academic performance records.</p>
            </div>
            
            <div class="card">
                <div class="search-row">
                    <div class="input-group">
                        <label>Student Search</label>
                        <input type="text" placeholder="Enter Student ID or Name...">
                    </div>
                    <div class="input-group">
                        <label>Semester</label>
                        <select>
                            <option>1st Semester 2025-2026</option>
                            <option>2nd Semester 2025-2026</option>
                        </select>
                    </div>
                    <button class="btn-search" onclick="handleSimpleAction('Searching Grades')">Search</button>
                </div>

                <table class="grades-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Midterm</th>
                            <th>Finals</th>
                            <th>Average</th>
                            <th>Status / Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Web Development 101</strong></td>
                            <td>92</td>
                            <td>95</td>
                            <td>93.5</td>
                            <td><span class="grade-badge grade-passed">Passed</span></td>
                        </tr>
                        <tr>
                            <td><strong>Database Systems</strong></td>
                            <td>88</td>
                            <td>90</td>
                            <td>89.0</td>
                            <td><span class="grade-badge grade-passed">Passed</span></td>
                        </tr>
                        <tr>
                            <td><strong>Data Structures</strong></td>
                            <td>85</td>
                            <td>87</td>
                            <td>86.0</td>
                            <td><span class="grade-badge grade-passed">Passed</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php include '../Components/GlobalScripts.php'; ?>
</body>
</html>


