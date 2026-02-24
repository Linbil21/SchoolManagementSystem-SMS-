<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS - Admission System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* FIX: Eto yung nawawalang style wrap kaya lumalabas yung text sa screen mo */
        .student-modal-footer { 
            padding: 20px 32px; 
            border-top: 1px solid #edf2f7; 
            display: flex; 
            justify-content: flex-end; 
            background: #f8fafc; 
        }

        body {
            background-color: #f3f4f6;
            font-family: 'Inter', sans-serif;
        }

        .sidebar-active {
            background-color: #ebf4ff;
            color: #1e40af;
            border-left: 4px solid #1e40af;
        }

        .nav-item {
            transition: all 0.2s;
            cursor: pointer;
        }

        .nav-item:hover {
            background-color: #f9fafb;
        }
    </style>
</head>
<body class="bg-gray-50 h-screen flex">

    <!-- Sidebar -->
    <aside class="w-72 bg-white border-r border-gray-200 flex flex-col h-full shadow-sm">
        <div class="p-6 flex items-center gap-3 border-b">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                <i class="fas fa-graduation-cap text-xl"></i>
            </div>
            <h1 class="text-xl font-bold text-blue-900 tracking-tight">ADMISSION</h1>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-3">
            <!-- Dashboard Section -->
            <div class="mb-6">
                <p class="text-xs font-semibold text-gray-400 uppercase px-4 mb-2 tracking-wider">Dashboard</p>
                <div class="nav-item flex items-center justify-between px-4 py-3 rounded-xl text-gray-600">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-th-large w-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>

            <!-- Admission Process Section -->
            <div class="mb-6">
                <p class="text-xs font-semibold text-gray-400 uppercase px-4 mb-2 tracking-wider">Admission Process</p>
                
                <div class="bg-blue-600 text-white flex items-center justify-between px-4 py-3 rounded-xl shadow-md mb-2">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-invoice w-5 text-blue-200"></i>
                        <span class="font-medium">Applications</span>
                    </div>
                    <i class="fas fa-chevron-up text-xs text-blue-200"></i>
                </div>

                <div class="ml-4 pl-4 border-l border-gray-200 space-y-1">
                    <a href="#" class="block px-4 py-2 text-sm text-blue-600 font-bold bg-blue-50 rounded-lg">New Applications</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-500 hover:text-blue-600 transition-colors">For Evaluation</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-500 hover:text-blue-600 transition-colors">Final Enrollment Check</a>
                </div>
            </div>
        </nav>

        <div class="p-4 border-t text-center">
            <p class="text-xs text-gray-400">EMS v2.0 &bull; jampzdev</p>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8">
            <div class="flex items-center gap-4 text-gray-400">
                <i class="fas fa-bars cursor-pointer hover:text-gray-600"></i>
                <span class="text-sm">Admission / New Applications</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs">AD</div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">New Applications</h2>
                    <p class="text-gray-500">Monitor and manage incoming student applications.</p>
                </div>
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add New Student
                </button>
            </div>

            <!-- Table Simulation -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Student Name</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Date Applied</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Course</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">Juan Dela Cruz</td>
                            <td class="px-6 py-4 text-gray-500">Oct 24, 2023</td>
                            <td class="px-6 py-4"><span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold">BSIT</span></td>
                            <td class="px-6 py-4 text-center">
                                <button class="text-blue-600 hover:text-blue-800 font-medium text-sm">Review</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer Demo for the CSS you saw -->
            <div class="mt-8 bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div class="p-8 text-center text-gray-400">
                    <p>Modal Preview Content Area</p>
                </div>
                <div class="student-modal-footer">
                    <button class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium mr-4">Cancel</button>
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700">Save Changes</button>
                </div>
            </div>
        </div>
    </main>

</body>
</html>