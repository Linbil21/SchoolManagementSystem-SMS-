<?php
require_once __DIR__ . '/../../auth/Security.php';
checkRole(['admission']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print / Export ID - Admission</title>
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
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>Print / Export ID</h1>
                <p>Batch printing and exporting of IDs.</p>
            </div>
            
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Batch #</th>
                            <th>Date Generated</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>BATCH-001</td>
                            <td>2024-01-25</td>
                            <td>50 IDs</td>
                            <td><span style="color: green;">Ready</span></td>
                            <td><button onclick="handlePrint()" style="border: none; background: #1648bc; color: white; padding: 6px 12px; border-radius: 6px; cursor: pointer;">Print</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
     <?php include '../Components/GlobalScripts.php'; ?>
</body>
</html>


