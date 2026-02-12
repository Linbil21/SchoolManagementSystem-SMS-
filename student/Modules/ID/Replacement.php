<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Replacement Request</title>
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

        .form-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            max-width: 700px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #475569;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .form-select, .form-textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.95rem;
            color: #1e293b;
        }
        
        .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .submit-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.2s;
        }
        
        .submit-btn:hover {
            background: #1d4ed8;
        }
        
        .info-box {
            background: #fff7ed;
            border-left: 4px solid #f97316;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 4px;
            display: flex;
            gap: 15px;
        }
        
        .info-box p {
            font-size: 0.9rem;
            color: #9a3412;
            line-height: 1.5;
        }

    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">ID Replacement Request</h1>
            </div>

            <div class="form-card">
                <div class="info-box">
                    <i class="fas fa-info-circle" style="color: #f97316; margin-top: 3px;"></i>
                    <p>Replacement of Student ID incurs a fee of <strong>₱150.00</strong>. You will need to submit an affidavit of loss for lost IDs. Processing time is 3-5 working days.</p>
                </div>
            
                <form>
                    <div class="form-group">
                        <label class="form-label">Reason for Replacement</label>
                        <select class="form-select">
                            <option>Lost ID</option>
                            <option>Damaged ID</option>
                            <option>Correction of Information</option>
                            <option>Wear and Tear</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Additional Details</label>
                        <textarea class="form-textarea" rows="4" placeholder="Please provide more details about your request..."></textarea>
                    </div>
                    
                    <button type="button" class="submit-btn" onclick="submitRequest()">Submit Request</button>
                </form>
            </div>

        </div>
    </div>

    <!-- Success Modal Logic (Simple Alert for Demo) -->
    <script>
        function submitRequest() {
            alert("Request Submitted! Please proceed to the cashier for payment.");
        }
    </script>
</body>

</html>
