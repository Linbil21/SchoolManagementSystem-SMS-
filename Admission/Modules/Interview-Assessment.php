<?php
require_once __DIR__ . '/../../auth/Security.php';
checkRole(['admission']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Assessment - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-blue: #1648bc; --bg-light: #f7fafc; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        .content-area { padding: 30px; }
        .module-header { margin-bottom: 25px; }
        .table-card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #edf2f7; color: #718096; font-size: 0.85rem; }
        td { padding: 12px; border-bottom: 1px solid #edf2f7; color: #2d3748; font-size: 0.9rem; }
         /* Modal Styles needed for GlobalModal */
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); overflow-y: auto; }
        .modal-content { background: white; margin: 50px auto; width: 90%; max-width: 600px; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: modalSlide 0.3s ease-out; }
        @keyframes modalSlide { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { padding: 20px 30px; background: #f8fafc; border-bottom: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: center; }
        .modal-body { padding: 30px; }
        .modal-footer { padding: 20px 30px; background: #f8fafc; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; gap: 12px; }
        .modal-profile-box { display: flex; flex-direction: column; align-items: center; padding: 20px; background: linear-gradient(to bottom, #f8fafc, #ffffff); border-radius: 20px; margin-bottom: 30px; border: 1px solid #edf2f7; }
        .modal-avatar { width: 80px; height: 80px; border-radius: 50%; background: #eef2ff; display: flex; justify-content: center; align-items: center; font-size: 2rem; color: #1648bc; margin-bottom: 15px; border: 4px solid white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info-item label { display: block; font-size: 0.8rem; color: #718096; font-weight: 600; margin-bottom: 5px; }
        .info-item p { font-weight: 700; color: #2d3748; }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>Interview Assessment</h1>
                <p>Schedule and grade student interviews.</p>
            </div>
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Interview Date</th>
                            <th>Interviewer</th>
                            <th>Result</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Wick</td>
                            <td>2024-01-22</td>
                            <td>Mr. Smith</td>
                            <td><span style="color: green; font-weight: 600;">Recommended</span></td>
                            <td><button onclick="openViewModal('John Wick', '#INT-005', 'Interview', 'Recommended', 'Finalize')" style="border: none; background: #1648bc; color: white; padding: 6px 12px; border-radius: 6px; cursor: pointer;">View Form</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php include '../Components/GlobalModal.php'; ?>
    <?php include '../Components/GlobalScripts.php'; ?>
</body>
</html>


