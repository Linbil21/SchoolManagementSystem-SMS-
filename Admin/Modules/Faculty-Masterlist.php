<?php
session_start();
require_once '../../Database/config.php';
require_once '../../auth/Security.php';

// Check access
checkRole(['admin', 'superadmin', 'admission']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../../Assets/image/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Assets/layout.css?v=<?php echo time(); ?>">
    <style>
        .faculty-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--primary-blue, #1648bc);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 4px 10px rgba(22, 72, 188, 0.2);
        }
        
        .badge-dept {
            background: rgba(22, 72, 188, 0.1);
            color: var(--primary-blue, #1648bc);
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .btn-refresh {
            background: var(--primary-blue, #1648bc);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-refresh:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(22, 72, 188, 0.3);
        }
    </style>
</head>

<body>
    <?php include '../Components/Side-bar.php'; ?>
    <div class="main-wrapper">
        <?php include '../Components/Head-bar.php'; ?>
        
        <div class="content-area">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark, #1e293b); margin-bottom: 5px;">Faculty Masterlist</h1>
                    <p style="color: var(--text-muted, #64748b); font-size: 0.95rem;"> Faculty Management System </p>
                </div>
                <button onclick="fetchFacultyData()" class="btn-refresh">
                    <i class="fas fa-sync-alt"></i> Refresh Data
                </button>
            </div>

            <div class="table-container">
                <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; gap: 15px;">
                    <h2 style="font-size: 1.1rem;">Faculty Records</h2>
                    <div class="search-box" style="width: 300px; position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="text" id="facultySearch" onkeyup="filterTable()" placeholder="Search faculty..." 
                            style="width: 100%; padding: 10px 15px 10px 40px; border-radius: 10px; border: 1px solid #e2e8f0; outline: none; font-size: 0.9rem; transition: 0.2s;">
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="facultyTable">
                        <thead>
                            <tr>
                                <th>Faculty Member</th>
                                <th>Email Address</th>
                                <th>Department</th>
                                <th>Position / Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="5">
                                    <div class="loading-state">
                                        <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--primary-blue);"></i>
                                        <p style="margin-top: 15px; font-weight: 500; color: var(--text-muted);">Fetching faculty information...</p>
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

        function filterTable() {
            const input = document.getElementById("facultySearch");
            const filter = input.value.toLowerCase();
            const table = document.getElementById("facultyTable");
            const tr = table.getElementsByTagName("tr");

            // Loop through all table rows, and hide those who don't match the search query
            for (let i = 1; i < tr.length; i++) { // Start from 1 to skip header
                let rowVisible = false;
                const tds = tr[i].getElementsByTagName("td");
                
                // Check relevant columns (Name/ID, Email, Dept, Role)
                // Name is in column 0 (inside div), Email col 1, Dept col 2, Role col 3
                if (tds.length > 0) {
                    const nameText = tds[0] ? tds[0].textContent || tds[0].innerText : "";
                    const emailText = tds[1] ? tds[1].textContent || tds[1].innerText : "";
                    const deptText = tds[2] ? tds[2].textContent || tds[2].innerText : "";
                    const roleText = tds[3] ? tds[3].textContent || tds[3].innerText : "";
                    
                    if (
                        nameText.toLowerCase().indexOf(filter) > -1 ||
                        emailText.toLowerCase().indexOf(filter) > -1 ||
                        deptText.toLowerCase().indexOf(filter) > -1 ||
                        roleText.toLowerCase().indexOf(filter) > -1
                    ) {
                        rowVisible = true;
                    }
                    tr[i].style.display = rowVisible ? "" : "none";
                }
            }
        }

        async function fetchFacultyData() {
            const tableBody = document.getElementById('tableBody');
            
            // Show Loading
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; padding: 50px;">
                        <div class="loading-state">
                            <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--primary-blue);"></i>
                            <p style="margin-top: 15px; font-weight: 500; color: var(--text-muted);">Fetching faculty records...</p>
                        </div>
                    </td>
                </tr>
            `;

            try {
                const response = await fetch('https://faculy-management-system-backend.onrender.com/api/v1/faculty/all');
                
                if (!response.ok) throw new Error('API Request Failed');
                
                const data = await response.json();
                const facultyList = Array.isArray(data) ? data : (data.data || []);

                if (facultyList.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 50px;">
                                <div class="empty-state">
                                    <i class="fas fa-user-slash fa-2x" style="color: #cbd5e1; margin-bottom: 15px;"></i>
                                    <p style="color: var(--text-muted);">No faculty records found.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                tableBody.innerHTML = facultyList.map(faculty => {
                    const name = `${faculty.firstName || faculty.first_name || 'Unknown'} ${faculty.lastName || faculty.last_name || ''}`;
                    const initials = name.match(/\b\w/g) || [];
                    const initialStr = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
                    
                    return `
                        <tr style="transition: background 0.2s;">
                            <td style="padding: 15px 25px;">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="faculty-avatar" style="flex-shrink: 0;">${initialStr}</div>
                                    <div>
                                        <div style="font-weight: 600; color: var(--text-dark);">${name}</div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);">ID: <span style="font-family: monospace;">${faculty.facultyId || faculty.id || 'N/A'}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td style="color: var(--primary-blue); font-weight: 500;">${faculty.email || 'N/A'}</td>
                            <td><span class="badge badge-dept" style="background: #e0f2fe; color: #0284c7;">${faculty.department || 'General'}</span></td>
                            <td style="font-weight: 500;">${faculty.position || faculty.role || 'Faculty Member'}</td>
                            <td>
                                <span style="background: #dcfce7; color: #16a34a; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-check-circle" style="font-size: 0.8rem;"></i> Active
                                </span>
                            </td>
                        </tr>
                    `;
                }).join('');
                
                // Re-apply filter if search box has value
                filterTable();

            } catch (error) {
                console.error('Fetch Error:', error);
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px;">
                            <div class="error-state">
                                <i class="fas fa-exclamation-triangle fa-2x" style="color: #ef4444; margin-bottom: 15px;"></i>
                                <p style="font-weight: 600; color: var(--text-dark);">Failed to load faculty data</p>
                                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px; margin-bottom: 15px;">${error.message}</p>
                                <button onclick="fetchFacultyData()" style="padding: 8px 20px; border: 1px solid #e2e8f0; background: white; color: var(--text-dark); border-radius: 8px; cursor: pointer; font-weight: 600; transition: 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                    <i class="fas fa-redo" style="margin-right: 5px;"></i> Try Again
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }
        }
    </script>
</body>
</html>
