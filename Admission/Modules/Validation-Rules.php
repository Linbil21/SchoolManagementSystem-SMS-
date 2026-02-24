<?php
require_once __DIR__ . '/../../auth/Security.php';
checkRole(['admission']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation Rules - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-blue: #1648bc; --bg-light: #f7fafc; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        .content-area { padding: 30px; }
        .module-header { margin-bottom: 25px; }
        .info-card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02); margin-bottom: 20px; }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>Validation Rules</h1>
                <p>Configure automated validation and cut-offs.</p>
            </div>
            
             <div class="info-card">
                 <h3>GPA Cut-off</h3>
                 <p style="margin-top: 10px; color: #64748b;">Minimum GPA for board courses: <strong>85%</strong></p>
                 <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
                 <button onclick="handleSimpleAction('Rules Update')" style="border: none; background: #1648bc; color: white; padding: 8px 16px; border-radius: 6px; cursor: pointer;">Update Rules</button>
            </div>
        </div>
    </div>
    <?php include '../Components/GlobalScripts.php'; ?>
</body>
</html>
