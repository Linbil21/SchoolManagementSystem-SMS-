<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cashier') {
    header("Location: ../../auth/Login.php");
    exit();
}
// This is a Master Template for Cashier Modules
// You can copy this file to create new modules with a consistent UI.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Settings - Cashier</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1648bc;
            --bg: #f8fafc;
            --white: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
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
            color: var(--text-main);
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .content-area {
            padding: 40px;
        }

        /* Header Styles */
        .module-header {
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .module-header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--text-main);
        }

        .module-header p {
            color: var(--text-muted);
            font-size: 1rem;
        }

        /* Settings Card Grid */
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
        }

        .settings-card {
            background: var(--white);
            border-radius: 24px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.02);
            transition: transform 0.3s ease;
        }

        .settings-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .icon-box {
            width: 50px;
            height: 50px;
            background: #eef2ff;
            color: var(--primary);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .card-header h3 {
            font-size: 1.2rem;
            font-weight: 700;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 14px 18px;
            border-radius: 14px;
            border: 1.5px solid #edf2f7;
            background: #f8fafc;
            outline: none;
            transition: 0.2s;
            font-size: 0.95rem;
        }

        .form-group input:focus {
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(22, 72, 188, 0.05);
        }

        .btn-save {
            background: var(--primary);
            color: var(--white);
            padding: 14px 30px;
            border-radius: 14px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-save:hover {
            background: #123896;
            transform: scale(1.02);
        }

        /* Switch Toggle UI */
        .switch-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .switch-row:last-child {
            border: none;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: var(--primary);
        }

        input:checked+.slider:before {
            transform: translateX(24px);
        }
    </style>
</head>

<body>
    <?php include '../Components/Sidebar.php'; ?>

    <div class="main-wrapper">
        <?php include '../Components/header.php'; ?>

        <div class="content-area">
            <div class="module-header">
                <div>
                    <h1>Settings</h1>
                    <p>Customize your cashier workstation and financial parameters.</p>
                </div>
            </div>

            <div class="settings-grid">
                <!-- Station Settings -->
                <div class="settings-card">
                    <div class="card-header">
                        <div class="icon-box"><i class="fas fa-desktop"></i></div>
                        <h3>Workstation Prefs</h3>
                    </div>
                    <div class="form-group">
                        <label>Receipt Printer</label>
                        <select>
                            <option>Epson TM-T88VI (Default)</option>
                            <option>Star Micronics mC-Print3</option>
                            <option>Save as PDF</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Default Currency Display</label>
                        <select>
                            <option>Philippine Peso (₱)</option>
                            <option>US Dollar ($)</option>
                        </select>
                    </div>
                    <button class="btn-save" onclick="triggerToast()">Save Workstation</button>
                </div>

                <!-- Security & Notifications -->
                <div class="settings-card">
                    <div class="card-header">
                        <div class="icon-box" style="background: #fff1f2; color: #e11d48;"><i class="fas fa-bell"></i>
                        </div>
                        <h3>Preferences</h3>
                    </div>
                    <div class="switch-row">
                        <div>
                            <p style="font-weight: 600;">Email Notifications</p>
                            <p style="font-size: 0.75rem; color: var(--text-muted);">Daily collection summaries</p>
                        </div>
                        <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                    </div>
                    <div class="switch-row">
                        <div>
                            <p style="font-weight: 600;">Sound Effects</p>
                            <p style="font-size: 0.75rem; color: var(--text-muted);">Payment confirmation sound</p>
                        </div>
                        <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                    </div>
                    <div class="switch-row">
                        <div>
                            <p style="font-weight: 600;">Auto-Archive</p>
                            <p style="font-size: 0.75rem; color: var(--text-muted);">Archive receipts after 30 days</p>
                        </div>
                        <label class="switch"><input type="checkbox"><span class="slider"></span></label>
                    </div>
                </div>

                <!-- Financial Configuration -->
                <div class="settings-card">
                    <div class="card-header">
                        <div class="icon-box" style="background: #f0fdf4; color: #16a34a;"><i
                                class="fas fa-file-invoice-dollar"></i></div>
                        <h3>Billing Config</h3>
                    </div>
                    <div class="form-group">
                        <label>Standard Late Fee (%)</label>
                        <input type="number" value="5">
                    </div>
                    <div class="form-group">
                        <label>Tax Identification Number (TIN)</label>
                        <input type="text" value="001-223-445-000" disabled>
                    </div>
                    <button class="btn-save" style="background: #16a34a;">Update Parameters</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function triggerToast() {
            alert('Settings updated successfully!');
        }
    </script>
</body>

</html>