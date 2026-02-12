<?php
session_start();
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

        .page-subtitle {
            color: var(--secondary);
            font-size: 0.95rem;
        }

        .requirements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .req-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            border: 1px solid #f1f5f9;
        }

        .req-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .req-icon {
            width: 50px;
            height: 50px;
            background: #eff6ff;
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            background: #f1f5f9;
            color: #64748b;
            font-weight: 600;
        }

        .status-badge.uploaded {
            background: #f0fdf4;
            color: #16a34a;
        }

        .req-title {
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 5px;
            font-size: 1.05rem;
        }

        .req-desc {
            font-size: 0.85rem;
            color: var(--secondary);
            margin-bottom: 20px;
            line-height: 1.5;
            flex-grow: 1;
        }

        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }

        .upload-area:hover {
            border-color: var(--primary);
            background: #f8fafc;
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
            margin-top: 10px;
            font-size: 0.85rem;
            color: var(--primary);
            font-weight: 600;
        }

        .upload-icon {
            font-size: 1.5rem;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Upload Requirements</h1>
                <p class="page-subtitle">Please submit clear scanned copies of the following documents.</p>
            </div>

            <div class="requirements-grid">
                <!-- PSA Birth Certificate -->
                <div class="req-card">
                    <div class="req-header">
                        <div class="req-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <span class="status-badge">Pending</span>
                    </div>
                    <h3 class="req-title">PSA Birth Certificate</h3>
                    <p class="req-desc">Original copy of Philippine Statistics Authority (PSA) Birth Certificate.</p>
                    <div class="upload-area">
                        <input type="file" accept="image/*,application/pdf">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <span class="upload-label">Click to Upload</span>
                    </div>
                </div>

                <!-- Form 138 -->
                <div class="req-card">
                    <div class="req-header">
                        <div class="req-icon">
                            <i class="fas fa-scroll"></i>
                        </div>
                        <span class="status-badge uploaded">Uploaded</span>
                    </div>
                    <h3 class="req-title">Form 138 (Report Card)</h3>
                    <p class="req-desc">Senior High School Report Card / Grade 12 Report Card.</p>
                    <div class="upload-area" style="border-color: #22c55e; background: #f0fdf4;">
                        <i class="fas fa-check-circle" style="color: #22c55e; font-size: 1.5rem;"></i>
                        <span class="upload-label" style="color: #16a34a;">File Submitted</span>
                    </div>
                </div>

                <!-- Good Moral -->
                <div class="req-card">
                    <div class="req-header">
                        <div class="req-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <span class="status-badge">Pending</span>
                    </div>
                    <h3 class="req-title">Good Moral Character</h3>
                    <p class="req-desc">Certificate of Good Moral Character from the last school attended.</p>
                    <div class="upload-area">
                        <input type="file" accept="image/*,application/pdf">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <span class="upload-label">Click to Upload</span>
                    </div>
                </div>

                <!-- 2x2 ID Picture -->
                <div class="req-card">
                    <div class="req-header">
                        <div class="req-icon">
                            <i class="fas fa-id-badge"></i>
                        </div>
                        <span class="status-badge">Pending</span>
                    </div>
                    <h3 class="req-title">2x2 ID Picture</h3>
                    <p class="req-desc">Recent 2x2 colored picture with white background and name tag.</p>
                    <div class="upload-area">
                        <input type="file" accept="image/*,application/pdf">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <span class="upload-label">Click to Upload</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>

</html>