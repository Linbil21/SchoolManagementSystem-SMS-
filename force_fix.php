<?php
/**
 * SERVER FORCE-FIX SCRIPT
 * Overwrites ALL admission PHP files on server with clean versions.
 * Run this ONCE at: https://ems.jampzdev.com/force_fix.php
 * DELETE AFTER USE.
 */
header('Content-Type: text/plain; charset=utf-8');
echo "=== SERVER FORCE-FIX ===\n";
echo "Time: " . date('c') . "\n\n";

$base = __DIR__;
$strayPattern = '/\.student-modal-footer\s*\{[^}]+\}/i';
$strayLiteral = '.student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }';

// Scan ALL PHP files in Admission folder
$admissionDir = $base . '/Admission';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($admissionDir));
$fixed = 0;
$checked = 0;

foreach ($iterator as $file) {
    if ($file->getExtension() !== 'php') continue;
    $checked++;
    $content = file_get_contents($file->getPathname());
    
    if (strpos($content, '.student-modal-footer') !== false && strpos($content, 'student-modal-footer {') !== false) {
        // Check if it's inside a <style> tag (that's OK) or plain text (bad)
        if (preg_match('/\?>[^<]*\.student-modal-footer/s', $content)) {
            // Stray text OUTSIDE of HTML tags — fix it
            $newContent = str_replace($strayLiteral, '', $content);
            $newContent = preg_replace($strayPattern, '', $newContent);
            if ($newContent !== $content) {
                file_put_contents($file->getPathname(), $newContent);
                $fixed++;
                echo "FIXED: " . str_replace($base, '', $file->getPathname()) . "\n";
            }
        }
    }
}

echo "\nScanned: $checked files\n";
echo "Fixed: $fixed files\n\n";

// ---- OVERWRITE New-Applications.php with 100% clean version ----
$newAppsFile = $base . '/Admission/Modules/New-Applications.php';
$cleanNewApps = <<<'PHPEOF'
<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admission') {
    header("Location: ../../auth/Login.php");
    exit();
}
require_once '../../Database/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    $appId = $_POST['application_no'] ?? null;
    if (!$appId) { echo json_encode(['success'=>false,'message'=>'Application ID missing.']); exit; }
    try {
        if ($action === 'proceed_to_evaluation') {
            $stmt = $pdo->prepare("UPDATE admission_applications SET status = 'Processing' WHERE application_no = ?");
            $stmt->execute([$appId]);
            echo json_encode(['success'=>true,'message'=>'Application moved to evaluation.']);
        } elseif ($action === 'delete_application') {
            $stmt = $pdo->prepare("DELETE FROM admission_applications WHERE application_no = ?");
            $stmt->execute([$appId]);
            echo json_encode(['success'=>true,'message'=>'Application deleted successfully.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success'=>false,'message'=>'Operation failed: '.$e->getMessage()]);
    }
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT a.*, COALESCE(c.course_name, a.preferred_course_1) as course_display_name 
        FROM admission_applications a 
        LEFT JOIN courses c ON (TRIM(a.preferred_course_1) = CAST(c.courseId AS CHAR) OR a.preferred_course_1 = c.course_name)
        WHERE a.status = 'Pending' ORDER BY a.submission_date DESC
    ");
    $stmt->execute();
    $applications = $stmt->fetchAll();
} catch (PDOException $e) {
    $applications = [];
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Applications - Admission</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../Assets/css/theme.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
        :root {
            --blue:#1648bc; --dark:#0f172a; --light:#f8fafc;
            --border:#e2e8f0; --muted:#64748b; --text:#1e293b;
            --danger:#ef4444; --warn-bg:#fffbeb; --warn-border:#fef3c7; --warn-text:#92400e;
            --shadow:0 20px 25px -5px rgba(0,0,0,.05),0 10px 10px -5px rgba(0,0,0,.02);
        }
        body { display:flex; min-height:100vh; background:var(--light); color:var(--text); }
        .main-wrapper { flex:1; display:flex; flex-direction:column; }
        .content-area { padding:40px; max-width:1400px; margin:0 auto; width:100%; }
        .page-header { margin-bottom:40px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px; }
        .page-header h1 { font-size:2.2rem; font-weight:800; letter-spacing:-.02em; color:var(--dark); margin-bottom:8px; }
        .page-header p { color:var(--muted); font-size:1rem; }
        .search-wrap { position:relative; }
        .search-wrap i { position:absolute; left:18px; top:50%; transform:translateY(-50%); color:#94a3b8; }
        .search-input { padding:14px 20px 14px 45px; border-radius:16px; border:2px solid #f1f5f9; outline:none; width:320px; font-size:.9rem; font-weight:600; transition:.3s; }
        .search-input:focus { border-color:var(--blue); }
        .table-card { background:white; border-radius:30px; box-shadow:var(--shadow); border:1px solid var(--border); overflow:hidden; }
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; padding:20px 40px; background:#f8fafc; color:var(--muted); font-weight:700; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; }
        td { padding:25px 40px; border-bottom:1px solid #f1f5f9; font-size:.95rem; vertical-align:middle; }
        .app-row { transition:.2s; }
        .app-row:hover { background:#f8faff; }
        .app-id { font-weight:800; color:var(--dark); font-size:.9rem; }
        .sname { font-weight:800; color:var(--dark); }
        .semail { font-size:.8rem; color:var(--muted); font-weight:500; }
        .cname { font-weight:700; color:#475569; font-size:.85rem; }
        .sdate { color:var(--muted); font-weight:600; }
        .btn { padding:10px 18px; border-radius:12px; font-weight:700; font-size:.85rem; border:none; cursor:pointer; transition:.3s; display:inline-flex; align-items:center; gap:8px; }
        .btn-view { background:#f1f5f9; color:#475569; }
        .btn-view:hover { background:#e2e8f0; color:var(--text); }
        .btn-del { background:#fef2f2; color:var(--danger); }
        .btn-del:hover { background:#fee2e2; }
        .btn-eval { background:var(--blue); color:white; box-shadow:0 4px 12px rgba(22,72,188,.2); }
        .btn-eval:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(22,72,188,.3); }
        .btn-dismiss { padding:12px 25px; border-radius:14px; border:2px solid var(--border); background:white; color:var(--muted); font-weight:700; cursor:pointer; transition:.3s; }
        .btn-dismiss:hover { background:#f8fafc; }
        .act-group { display:flex; gap:10px; }
        .empty-state { text-align:center; padding:80px; }
        .empty-ico { width:100px; height:100px; background:#f1f5f9; border-radius:30px; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; }
        .empty-ico i { color:#e2e8f0; font-size:3rem; }
        .empty-txt { color:#64748b; font-weight:600; }
        .modal-overlay { position:fixed; inset:0; background:rgba(15,23,42,.7); backdrop-filter:blur(12px); z-index:99999; display:none; align-items:center; justify-content:center; padding:20px; }
        .modal-box { background:white; width:100%; max-width:600px; border-radius:35px; box-shadow:0 30px 60px -12px rgba(0,0,0,.3); overflow:hidden; animation:mscale .4s cubic-bezier(.34,1.56,.64,1); }
        @keyframes mscale { from { transform:scale(.95); opacity:0; } to { transform:scale(1); opacity:1; } }
        .mhead { padding:40px; background:linear-gradient(135deg,#1648bc,#1e3a8a); color:white; text-align:center; }
        .mavatar { width:100px; height:100px; border-radius:30px; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-size:2.5rem; font-weight:800; margin:0 auto 20px; border:3px solid rgba(255,255,255,.2); }
        .mhead h2 { font-weight:800; font-size:1.6rem; letter-spacing:-.02em; margin-bottom:5px; }
        .mhead p { opacity:.8; font-weight:500; font-size:.95rem; }
        .mbody { padding:35px 40px; }
        .info-grid { background:#f8fafc; border-radius:20px; padding:20px; border:1px solid #eef2ff; margin-bottom:25px; display:grid; grid-template-columns:repeat(2,1fr); gap:20px; }
        .info-item label { display:block; font-size:.7rem; font-weight:800; color:var(--muted); text-transform:uppercase; margin-bottom:6px; }
        .info-item p { font-weight:700; color:var(--dark); font-size:.95rem; }
        .warn-note { background:var(--warn-bg); border:1px solid var(--warn-border); padding:20px; border-radius:20px; color:var(--warn-text); font-size:.85rem; display:flex; gap:12px; align-items:center; }
        .warn-note p { font-weight:600; }
        .mfooter { padding:30px 40px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:15px; }
        #evalSpinner { margin-right:8px; display:none; }
    </style>
</head>
<body>
<div id="viewModal" class="modal-overlay">
    <div class="modal-box">
        <div class="mhead">
            <div class="mavatar" id="modalAvatar">JD</div>
            <h2 id="modalName">John Doe</h2>
            <p id="modalCourse">BS Computer Science</p>
        </div>
        <div class="mbody">
            <div class="info-grid">
                <div class="info-item"><label>Application ID</label><p id="modalAppId">#APP-10293</p></div>
                <div class="info-item"><label>Submission Date</label><p id="modalDate">Jan 12, 2024</p></div>
            </div>
            <div class="info-grid">
                <div class="info-item"><label>Email Address</label><p id="modalEmail">john@university.edu</p></div>
                <div class="info-item"><label>Contact Number</label><p id="modalContact">+63 912 345 6789</p></div>
            </div>
            <div class="warn-note">
                <i class="fas fa-info-circle fa-lg"></i>
                <p>Moving to evaluation will alert the student and lock this application phase.</p>
            </div>
        </div>
        <div class="mfooter">
            <button class="btn-dismiss" onclick="closeModal()">Dismiss</button>
            <button class="btn btn-eval" onclick="proceedToEval(event)">
                <i class="fas fa-circle-notch fa-spin" id="evalSpinner"></i>
                <i class="fas fa-arrow-right" id="evalIcon"></i>
                Move to Evaluation
            </button>
        </div>
    </div>
</div>

<?php include '../Components/Sidebar.php'; ?>
<div class="main-wrapper">
    <?php include '../Components/header.php'; ?>
    <div class="content-area">
        <div class="page-header">
            <div>
                <h1>New Applications</h1>
                <p>Identify and process newly submitted enrollment requests.</p>
            </div>
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="appSearch" onkeyup="filterTable()" placeholder="Search applicants..." class="search-input">
            </div>
        </div>
        <div class="table-card">
            <table id="appTable">
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Student Information</th>
                        <th>Applied Course</th>
                        <th>Date Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($applications)): ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <div class="empty-ico"><i class="fas fa-inbox"></i></div>
                                <p class="empty-txt">No pending applications in your queue.</p>
                            </td>
                        </tr>
                    <?php else: foreach ($applications as $app): ?>
                        <tr class="app-row">
                            <td class="app-id">#<?=htmlspecialchars($app->application_no)?></td>
                            <td>
                                <div class="sname"><?=htmlspecialchars($app->first_name.' '.$app->last_name)?></div>
                                <div class="semail"><?=htmlspecialchars($app->email)?></div>
                            </td>
                            <td class="cname"><?=htmlspecialchars($app->course_display_name)?></td>
                            <td class="sdate"><?=date('M d, Y', strtotime($app->submission_date))?></td>
                            <td>
                                <?php $d=['name'=>$app->first_name.' '.$app->last_name,'no'=>$app->application_no,'course'=>$app->course_display_name,'date'=>date('M d, Y',strtotime($app->submission_date)),'email'=>$app->email,'phone'=>$app->phone_number]; ?>
                                <div class="act-group">
                                    <button class="btn btn-view" onclick='openModal(<?=json_encode($d,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP)?>)'>
                                        <i class="fas fa-eye"></i> Details
                                    </button>
                                    <button class="btn btn-del" onclick="deleteApp('<?=htmlspecialchars($app->application_no)?>','<?=addslashes($app->first_name.' '.$app->last_name)?>')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../Components/GlobalScripts.php'; ?>
<script>
let cId=null;
function filterTable(){
    const f=document.getElementById('appSearch').value.toLowerCase();
    const rows=document.getElementById('appTable').getElementsByTagName('tr');
    for(let i=1;i<rows.length;i++){
        let v=false;
        for(const c of rows[i].getElementsByTagName('td'))
            if((c.textContent||c.innerText).toLowerCase().includes(f)){v=true;break;}
        rows[i].style.display=v?'':'none';
    }
}
function openModal(d){
    cId=d.no;
    document.getElementById('modalName').textContent=d.name;
    document.getElementById('modalAvatar').textContent=d.name.split(' ').map(n=>n[0]).join('').toUpperCase().substring(0,2);
    document.getElementById('modalAppId').textContent='#'+d.no;
    document.getElementById('modalCourse').textContent=d.course;
    document.getElementById('modalDate').textContent=d.date;
    document.getElementById('modalEmail').textContent=d.email;
    document.getElementById('modalContact').textContent=d.phone;
    document.getElementById('viewModal').style.display='flex';
    document.body.style.overflow='hidden';
}
function closeModal(){
    document.getElementById('viewModal').style.display='none';
    document.body.style.overflow='auto';
    cId=null;
}
async function proceedToEval(e){
    if(!cId)return;
    const btn=e.currentTarget,sp=document.getElementById('evalSpinner'),ic=document.getElementById('evalIcon');
    sp.style.display='inline-block';ic.style.display='none';btn.disabled=true;
    const fd=new FormData();fd.append('action','proceed_to_evaluation');fd.append('application_no',cId);
    try{
        const r=await(await fetch(window.location.href,{method:'POST',body:fd})).json();
        if(r.success){await Swal.fire({title:'Moved!',icon:'success',timer:1500,showConfirmButton:false});window.location.href='Evaluation.php';}
        else{Swal.fire('Error!',r.message,'error');sp.style.display='none';ic.style.display='inline-block';btn.disabled=false;}
    }catch{Swal.fire('Error!','Something went wrong.','error');sp.style.display='none';ic.style.display='inline-block';btn.disabled=false;}
}
async function deleteApp(id,name){
    const r=await Swal.fire({title:'Are you sure?',text:`Delete application of ${name}?`,icon:'warning',showCancelButton:true,confirmButtonColor:'#ef4444',cancelButtonColor:'#718096',confirmButtonText:'Yes, delete it!'});
    if(r.isConfirmed){
        const fd=new FormData();fd.append('action','delete_application');fd.append('application_no',id);
        try{
            const d=await(await fetch(window.location.href,{method:'POST',body:fd})).json();
            if(d.success){await Swal.fire('Deleted!',d.message,'success');location.reload();}
            else Swal.fire('Error!',d.message,'error');
        }catch{Swal.fire('Error!','Something went wrong.','error');}
    }
}
window.onclick=e=>{if(e.target===document.getElementById('viewModal'))closeModal();}
</script>
</body>
</html>
PHPEOF;

if (file_put_contents($newAppsFile, $cleanNewApps)) {
    echo "OVERWRITTEN: New-Applications.php (" . strlen($cleanNewApps) . " bytes)\n";
    // Verify
    $v = file_get_contents($newAppsFile);
    echo strpos($v,'student-modal-footer {') !== false ? "WARNING: stray still found!\n" : "VERIFIED: Clean!\n";
} else {
    echo "ERROR: Could not write New-Applications.php!\n";
}

// Clear OPCache
if (function_exists('opcache_reset')) { opcache_reset(); echo "\nOPCache cleared!\n"; }
if (function_exists('opcache_invalidate')) { opcache_invalidate($newAppsFile, true); echo "File OPCache invalidated!\n"; }

echo "\n=== DONE! Open New-Applications.php and hard reload (Ctrl+Shift+R) ===\n";
echo "DELETE this file (force_fix.php) after!\n";
?>
