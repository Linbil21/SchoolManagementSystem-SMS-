<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download ID</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
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

        .download-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .preview-box {
            background: #f1f5f9;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 2px dashed #cbd5e1;
        }
        
        .preview-icon {
            font-size: 3rem;
            color: #94a3b8;
            margin-bottom: 10px;
        }
        
        .format-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .format-btn {
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            text-align: left;
        }
        
        .format-btn:hover {
            border-color: var(--primary);
            background: #eff6ff;
        }
        
        .format-btn.active {
            border-color: var(--primary);
            background: #eff6ff;
            position: relative;
        }
        
        .format-btn h4 {
            margin: 0;
            color: #1e293b;
            font-size: 1rem;
        }
        
        .format-btn p {
            margin: 0;
            font-size: 0.8rem;
            color: #64748b;
        }

        .download-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s;
        }
        
        .download-btn:hover {
            background: #1d4ed8;
        }

    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Download ID</h1>
                <p style="color: #64748b;">Save a copy of your student ID for offline use.</p>
            </div>

            <div class="download-card">
                <div class="preview-box">
                    <i class="fas fa-id-card preview-icon"></i>
                    <p style="color: #64748b;">ID Card Preview Ready</p>
                </div>
                
                <h3 style="margin-bottom: 15px; text-align: left; color: #1e293b;">Select Format</h3>
                <div class="format-options">
                    <button class="format-btn active">
                        <h4><i class="fas fa-file-pdf" style="color: #ef4444; margin-right: 8px;"></i> PDF Document</h4>
                        <p>Best for printing</p>
                    </button>
                    <button class="format-btn">
                        <h4><i class="fas fa-file-image" style="color: #3b82f6; margin-right: 8px;"></i> PNG Image</h4>
                        <p>Best for digital use</p>
                    </button>
                </div>
                
                <button class="download-btn" onclick="startDownload()">
                    <i class="fas fa-download"></i> Download Now
                </button>
            </div>

        </div>
    </div>
    
    <script>
        function startDownload() {
            // Mock download action
            const btn = document.querySelector('.download-btn');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            btn.style.opacity = '0.8';
            
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check"></i> Downloaded!';
                btn.style.background = '#16a34a';
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = 'var(--primary)';
                    btn.style.opacity = '1';
                }, 2000);
            }, 1500);
        }
    </script>
</body>

</html>
