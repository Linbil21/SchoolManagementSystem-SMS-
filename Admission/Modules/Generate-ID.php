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
    <title>Generate Student ID - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-blue: #1648bc; --bg-light: #f7fafc; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        .content-area { padding: 30px; }
        .module-header { margin-bottom: 25px; }
        .form-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02); max-width: 600px; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #4a5568; }
        .input-group input, .input-group select { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; }
        .btn-generate { background: #1648bc; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; width: 100%; transition: background 0.3s; }
        .btn-generate:hover { background: #0f3691; }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>
        <div class="content-area">
            <div class="module-header">
                <h1>Generate Student ID</h1>
                <p>Create official student identification numbers.</p>
            </div>
            
            <div class="form-card">
                <div class="input-group">
                    <label>Select Student</label>
                    <select>
                        <option>Select newly enrolled student...</option>
                        <option>John Doe</option>
                        <option>Jane Smith</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>ID Series Year</label>
                    <input type="text" value="2026" readonly>
                </div>
                <button class="btn-generate" onclick="handleSimpleAction('ID Generation')">Generate ID Number</button>
            </div>
        </div>
    </div>
    
    <?php include '../Components/GlobalScripts.php'; ?>
</body>
</html>
