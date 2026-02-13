<?php
session_start();
require_once '../../Database/config.php';
require_once '../../auth/Security.php';

// Check access
checkRole(['admin', 'superadmin']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Masterlist - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/Assets/css/theme.css">
    <style>
        :root {
            --primary: #4f46e5;
            --secondary: #64748b;
            --bg: #f8fafc;
            --text-main: #1e293b;
            --table-header: #f1f5f9;
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
        }

        .content-area {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 5px;
        }
        
        .page-title p {
            color: var(--secondary);
            font-size: 0.95rem;
        }

        .card {
            background: var(--card-bg, #ffffff);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 25px;
            border: 1px solid var(--border-color);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background: var(--table-header);
            font-weight: 600;
            color: var(--secondary);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            color: var(--text-main);
            font-size: 0.95rem;
        }

        .loading-state {
            text-align: center;
            padding: 50px;
            color: var(--secondary);
        }

        .error-state {
            text-align: center;
            padding: 50px;
            color: #ef4444;
            background: #fef2f2;
            border-radius: 12px;
            border: 1px solid #fee2e2;
        }

        .faculty-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-dept {
            background: #e0e7ff;
            color: var(--primary);
        }
    </style>
</head>

<body>
    <?php include '../Components/Side-bar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/Head-bar.php'; ?>
        
        <div class="content-area">
            <div class="page-header">
                <div class="page-title">
                    <h1>External Faculty Masterlist</h1>
                    <p>Live data from Faculty Management System API</p>
                </div>
                <button onclick="fetchFacultyData()" class="btn-refresh" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-sync-alt"></i> Refresh Data
                </button>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table id="facultyTable">
                        <thead>
                            <tr>
                                <th>Faculty</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="5">
                                    <div class="loading-state">
                                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                                        <p style="margin-top: 15px;">Fetching faculty records...</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', fetchFacultyData);

        async function fetchFacultyData() {
            const tableBody = document.getElementById('tableBody');
            
            // Show Loading
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5">
                        <div class="loading-state">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p style="margin-top: 15px;">Fetching faculty records...</p>
                        </div>
                    </td>
                </tr>
            `;

            try {
                // Use the provided API URL
                const response = await fetch('https://faculy-management-system-backend.onrender.com/api/v1/faculty/all');
                
                if (!response.ok) throw new Error('API Request Failed');
                
                const data = await response.json();
                
                // Assuming data structure based on common patterns. 
                // Adjusting based on potential response structure (data vs direct array)
                const facultyList = Array.isArray(data) ? data : (data.data || []);

                if (facultyList.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="5">
                                <div class="loading-state">
                                    <i class="fas fa-inbox fa-2x"></i>
                                    <p style="margin-top: 15px;">No faculty records found.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                tableBody.innerHTML = facultyList.map(faculty => {
                    // Extract initials
                    const name = `${faculty.firstName || faculty.first_name || 'Unknown'} ${faculty.lastName || faculty.last_name || ''}`;
                    const initials = name.match(/\b\w/g) || [];
                    const initialStr = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
                    
                    return `
                        <tr>
                            <td style="display: flex; align-items: center; gap: 15px;">
                                <div class="faculty-avatar">${initialStr}</div>
                                <div>
                                    <div style="font-weight: 600;">${name}</div>
                                    <div style="font-size: 0.8rem; color: var(--secondary);">ID: ${faculty.facultyId || faculty.id || 'N/A'}</div>
                                </div>
                            </td>
                            <td>${faculty.email || 'N/A'}</td>
                            <td><span class="badge badge-dept">${faculty.department || 'General'}</span></td>
                            <td>${faculty.position || faculty.role || 'Faculty Member'}</td>
                            <td>
                                <span style="color: #10b981; font-weight: 600; font-size: 0.85rem;">
                                    <i class="fas fa-check-circle"></i> Active
                                </span>
                            </td>
                        </tr>
                    `;
                }).join('');

            } catch (error) {
                console.error('Fetch Error:', error);
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5">
                            <div class="error-state">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                                <p style="margin-top: 15px;">Failed to load faculty data.</p>
                                <p style="font-size: 0.85rem; margin-top: 5px;">${error.message}</p>
                                <button onclick="fetchFacultyData()" style="margin-top: 15px; padding: 8px 16px; border: 1px solid #ef4444; background: white; color: #ef4444; border-radius: 8px; cursor: pointer;">Try Again</button>
                            </div>
                        </td>
                    </tr>
                `;
            }
        }
    </script>
</body>
</html>
