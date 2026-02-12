<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Selection</title>
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
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
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

        .subject-table-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table-header {
            padding: 20px 25px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-label {
            font-weight: 700;
            color: #1e293b;
            font-size: 1.1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px 25px;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            background: #f8fafc;
        }

        td {
            padding: 15px 25px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            font-size: 0.95rem;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .checkbox-wrapper input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .units-badge {
            background: #eff6ff;
            color: var(--primary);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .summary-card {
            background: #1e293b;
            color: white;
            border-radius: 20px;
            padding: 25px;
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-info h3 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .summary-info p {
            color: #94a3b8;
        }

        .action-btn {
            background: var(--primary);
            color: white !important;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            flex-shrink: 0;
            white-space: nowrap;
            min-width: fit-content;
        }

        .action-btn:hover {
            background: #1d4ed8;
            color: white !important;
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Subject Selection</h1>
                    <p class="page-subtitle">Select the subjects you want to enroll in for this semester.</p>
                </div>
                <div style="text-align: right;">
                    <span style="display: block; font-size: 0.85rem; color: #64748b; font-weight: 600;">ACADEMIC
                        YEAR</span>
                    <span style="font-weight: 800; color: var(--primary); font-size: 1.1rem;">2026-2027 • 1ST SEM</span>
                </div>
            </div>

            <div class="subject-table-card">
                <div class="table-header">
                    <span class="section-label">Available Subjects</span>
                    <span style="font-size: 0.9rem; color: #64748b;">BSIT - 1st Year, 1st Semester</span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;"><input type="checkbox" checked disabled></th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Schedule</th>
                            <th>Room</th>
                            <th>Units</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="checkbox-wrapper"><input type="checkbox" checked></div>
                            </td>
                            <td style="font-weight: 600;">IT 101</td>
                            <td>Introduction to Computing</td>
                            <td>MW 8:00AM - 9:30AM</td>
                            <td>CL-1</td>
                            <td><span class="units-badge">3.0</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="checkbox-wrapper"><input type="checkbox" checked></div>
                            </td>
                            <td style="font-weight: 600;">CC 102</td>
                            <td>Computer Programming 1</td>
                            <td>TTh 10:00AM - 12:00PM</td>
                            <td>CL-2</td>
                            <td><span class="units-badge">3.0</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="checkbox-wrapper"><input type="checkbox" checked></div>
                            </td>
                            <td style="font-weight: 600;">GE 1</td>
                            <td>Understanding the Self</td>
                            <td>F 1:00PM - 4:00PM</td>
                            <td>RM-304</td>
                            <td><span class="units-badge">3.0</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="checkbox-wrapper"><input type="checkbox" checked></div>
                            </td>
                            <td style="font-weight: 600;">PE 1</td>
                            <td>Physical Fitness</td>
                            <td>S 8:00AM - 10:00AM</td>
                            <td>GYM</td>
                            <td><span class="units-badge">2.0</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="checkbox-wrapper"><input type="checkbox" checked></div>
                            </td>
                            <td style="font-weight: 600;">NSTP 1</td>
                            <td>Civic Welfare Training Service 1</td>
                            <td>S 1:00PM - 4:00PM</td>
                            <td>FIELD</td>
                            <td><span class="units-badge">3.0</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="checkbox-wrapper"><input type="checkbox" checked></div>
                            </td>
                            <td style="font-weight: 600;">MATH 1</td>
                            <td>Mathematics in the Modern World</td>
                            <td>MW 1:00PM - 2:30PM</td>
                            <td>RM-201</td>
                            <td><span class="units-badge">3.0</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="summary-card">
                <div class="summary-info">
                    <h3>Total Units: 17.0</h3>
                    <p>Maximum allowable units: 23.0</p>
                </div>
                <a href="View-Assessment.php" class="action-btn">
                    Proceed to Assessment <i class="fas fa-arrow-right"
                        style="margin-left: 10px; hover: color: white;"></i>
                </a>
            </div>

        </div>
    </div>
</body>

</html>