<?php
/**
 * STRAY CSS DIAGNOSTIC - Access at:
 * https://ems.jampzdev.com/Admission/diag.php
 * DELETE AFTER USE.
 */
header('Content-Type: text/plain; charset=utf-8');
echo "=== STRAY CSS DIAGNOSTIC ===\n";
echo "Time: " . date('c') . "\n\n";

$base    = dirname(__DIR__); // Go up to sms root
$needle  = 'student-modal-footer';
$stray   = '.student-modal-footer { padding: 20px 32px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; background: #f8fafc; }';

$scanDirs = [
    __DIR__ . '/Components',                          // Admission/Components
    __DIR__ . '/Modules',                             // Admission/Modules
    $base . '/Components',                            // /Components
    $base . '/Database',                              // /Database
];

foreach ($scanDirs as $dir) {
    if (!is_dir($dir)) { echo "MISSING DIR: $dir\n"; continue; }
    foreach (glob($dir . '/*.php') as $fp) {
        $content = file_get_contents($fp);
        if (strpos($content, $needle) !== false) {
            $rel = str_replace($base, '', $fp);
            preg_match_all('/(.{0,50})' . preg_quote($needle, '/') . '(.{0,80})/s', $content, $m);
            foreach ($m[0] as $match) {
                $clean = str_replace(["\r", "\n", "\t"], ['\r', '\n', '\t'], $match);
                echo "FOUND: $rel\n  >>> $clean\n\n";
            }
        }
    }
}

// ALSO: Check if the stray text is literally in New-Applications.php
$newApps = __DIR__ . '/Modules/New-Applications.php';
$content = file_get_contents($newApps);
echo "--- New-Applications.php on SERVER ---\n";
echo "File size: " . filesize($newApps) . " bytes\n";
echo "Contains 'student-modal-footer': " . (strpos($content, $needle) !== false ? "YES (BAD)" : "NO (clean)") . "\n";
echo "First 300 chars: " . str_replace(["\r","\n"], ['\r','\n'], substr($content, 0, 300)) . "\n\n";

// Check Sidebar.php
$sidebar = __DIR__ . '/Components/Sidebar.php';
if (file_exists($sidebar)) {
    $sc = file_get_contents($sidebar);
    echo "--- Sidebar.php ---\n";
    echo "Contains stray CSS: " . (strpos($sc, $needle) !== false ? "YES (BAD)" : "NO (clean)") . "\n";
    echo "File size: " . filesize($sidebar) . " bytes\n\n";
} else {
    echo "SIDEBAR NOT FOUND at: $sidebar\n";
}

// Check header.php
$headerf = __DIR__ . '/Components/header.php';
if (file_exists($headerf)) {
    $hc = file_get_contents($headerf);
    echo "--- header.php ---\n";
    echo "Contains stray CSS: " . (strpos($hc, $needle) !== false ? "YES (BAD)" : "NO (clean)") . "\n";
    echo "File size: " . filesize($headerf) . " bytes\n\n";
}

// NOW FIX: Directly overwrite New-Applications.php with clean version
echo "\n--- FIXING New-Applications.php ---\n";
$cleanCode = <<<'PHPEOF'
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
    $appId  = $_POST['application_no'] ?? null;
    if (!$appId) { echo json_encode(['success'=>false,'message'=>'Missing ID']); exit; }
    try {
        if ($action === 'proceed_to_evaluation') {
            $pdo->prepare("UPDATE admission_applications SET status='Processing' WHERE application_no=?")->execute([$appId]);
            echo json_encode(['success'=>true,'message'=>'Moved to evaluation.']);
        } elseif ($action === 'delete_application') {
            $pdo->prepare("DELETE FROM admission_applications WHERE application_no=?")->execute([$appId]);
            echo json_encode(['success'=>true,'message'=>'Application deleted.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT a.*, COALESCE(c.course_name, a.preferred_course_1) as course_display_name
        FROM admission_applications a
        LEFT JOIN courses c ON (TRIM(a.preferred_course_1)=CAST(c.courseId AS CHAR) OR a.preferred_course_1=c.course_name)
        WHERE a.status='Pending' ORDER BY a.submission_date DESC
    ");
    $stmt->execute();
    $applications = $stmt->fetchAll();
} catch (PDOException $e) { $applications = []; }
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
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
:root{--blue:#1648bc;--dark:#0f172a;--light:#f8fafc;--border:#e2e8f0;--muted:#64748b;--text:#1e293b;--danger:#ef4444;--warn-bg:#fffbeb;--warn-b:#fef3c7;--warn-t:#92400e;--shadow:0 20px 25px -5px rgba(0,0,0,.05)}
body{display:flex;min-height:100vh;background:var(--light);color:var(--text)}
.main-wrapper{flex:1;display:flex;flex-direction:column}
.content-area{padding:40px;max-width:1400px;margin:0 auto;width:100%}
.page-hdr{margin-bottom:40px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}
.page-hdr h1{font-size:2.2rem;font-weight:800;letter-spacing:-.02em;color:var(--dark);margin-bottom:8px}
.page-hdr p{color:var(--muted);font-size:1rem}
.srch{position:relative}
.srch i{position:absolute;left:18px;top:50%;transform:translateY(-50%);color:#94a3b8}
.srch input{padding:14px 20px 14px 45px;border-radius:16px;border:2px solid #f1f5f9;outline:none;width:320px;font-size:.9rem;font-weight:600;transition:.3s}
.srch input:focus{border-color:var(--blue)}
.tbl-card{background:white;border-radius:30px;box-shadow:var(--shadow);border:1px solid var(--border);overflow:hidden}
table{width:100%;border-collapse:collapse}
th{text-align:left;padding:20px 40px;background:#f8fafc;color:var(--muted);font-weight:700;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em}
td{padding:25px 40px;border-bottom:1px solid #f1f5f9;font-size:.95rem;vertical-align:middle}
.app-row:hover{background:#f8faff}
.aid{font-weight:800;color:var(--dark);font-size:.9rem}
.snm{font-weight:800;color:var(--dark)}
.sml{font-size:.8rem;color:var(--muted);font-weight:500}
.cnm{font-weight:700;color:#475569;font-size:.85rem}
.sdt{color:var(--muted);font-weight:600}
.btn{padding:10px 18px;border-radius:12px;font-weight:700;font-size:.85rem;border:none;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:8px}
.btn-v{background:#f1f5f9;color:#475569}.btn-v:hover{background:#e2e8f0}
.btn-d{background:#fef2f2;color:var(--danger)}.btn-d:hover{background:#fee2e2}
.btn-e{background:var(--blue);color:#fff;box-shadow:0 4px 12px rgba(22,72,188,.2)}.btn-e:hover{transform:translateY(-2px)}
.btn-cl{padding:12px 25px;border-radius:14px;border:2px solid var(--border);background:#fff;color:var(--muted);font-weight:700;cursor:pointer;transition:.3s}
.ag{display:flex;gap:10px}
.empty{text-align:center;padding:80px}
.eico{width:100px;height:100px;background:#f1f5f9;border-radius:30px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px}
.eico i{color:#e2e8f0;font-size:3rem}
.mol{position:fixed;inset:0;background:rgba(15,23,42,.7);backdrop-filter:blur(12px);z-index:99999;display:none;align-items:center;justify-content:center;padding:20px}
.mbox{background:#fff;width:100%;max-width:600px;border-radius:35px;box-shadow:0 30px 60px -12px rgba(0,0,0,.3);overflow:hidden;animation:ms .4s cubic-bezier(.34,1.56,.64,1)}
@keyframes ms{from{transform:scale(.95);opacity:0}to{transform:scale(1);opacity:1}}
.mhd{padding:40px;background:linear-gradient(135deg,#1648bc,#1e3a8a);color:#fff;text-align:center}
.mav{width:100px;height:100px;border-radius:30px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:2.5rem;font-weight:800;margin:0 auto 20px;border:3px solid rgba(255,255,255,.2)}
.mhd h2{font-weight:800;font-size:1.6rem;letter-spacing:-.02em;margin-bottom:5px}
.mhd p{opacity:.8;font-weight:500;font-size:.95rem}
.mbd{padding:35px 40px}
.igrid{background:#f8fafc;border-radius:20px;padding:20px;border:1px solid #eef2ff;margin-bottom:25px;display:grid;grid-template-columns:repeat(2,1fr);gap:20px}
.iitm label{display:block;font-size:.7rem;font-weight:800;color:var(--muted);text-transform:uppercase;margin-bottom:6px}
.iitm p{font-weight:700;color:var(--dark);font-size:.95rem}
.wn{background:var(--warn-bg);border:1px solid var(--warn-b);padding:20px;border-radius:20px;color:var(--warn-t);font-size:.85rem;display:flex;gap:12px;align-items:center}
.wn p{font-weight:600}
.mft{padding:30px 40px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:15px}
#esp{margin-right:8px;display:none}
</style>
</head>
<body>
<div id="vm" class="mol">
  <div class="mbox">
    <div class="mhd">
      <div class="mav" id="mav">JD</div>
      <h2 id="mnm">John Doe</h2>
      <p id="mcs">BS Computer Science</p>
    </div>
    <div class="mbd">
      <div class="igrid">
        <div class="iitm"><label>Application ID</label><p id="mid">#APP-0001</p></div>
        <div class="iitm"><label>Date Submitted</label><p id="mdt">Jan 1, 2024</p></div>
      </div>
      <div class="igrid">
        <div class="iitm"><label>Email</label><p id="mem">—</p></div>
        <div class="iitm"><label>Phone</label><p id="mph">—</p></div>
      </div>
      <div class="wn">
        <i class="fas fa-info-circle fa-lg"></i>
        <p>Moving to evaluation will alert the student and lock this application phase.</p>
      </div>
    </div>
    <div class="mft">
      <button class="btn-cl" onclick="cmo()">Dismiss</button>
      <button class="btn btn-e" onclick="pte(event)">
        <i class="fas fa-circle-notch fa-spin" id="esp"></i>
        <i class="fas fa-arrow-right" id="eic"></i>
        Move to Evaluation
      </button>
    </div>
  </div>
</div>
<?php include '../Components/Sidebar.php'; ?>
<div class="main-wrapper">
  <?php include '../Components/header.php'; ?>
  <div class="content-area">
    <div class="page-hdr">
      <div>
        <h1>New Applications</h1>
        <p>Identify and process newly submitted enrollment requests.</p>
      </div>
      <div class="srch">
        <i class="fas fa-search"></i>
        <input type="text" id="as" onkeyup="ft()" placeholder="Search applicants...">
      </div>
    </div>
    <div class="tbl-card">
      <table id="at">
        <thead><tr><th>Application ID</th><th>Student Information</th><th>Applied Course</th><th>Date Submitted</th><th>Action</th></tr></thead>
        <tbody>
          <?php if(empty($applications)): ?>
          <tr><td colspan="5" class="empty"><div class="eico"><i class="fas fa-inbox"></i></div><p style="color:#64748b;font-weight:600">No pending applications.</p></td></tr>
          <?php else: foreach($applications as $app): ?>
          <tr class="app-row">
            <td class="aid">#<?=htmlspecialchars($app->application_no)?></td>
            <td><div class="snm"><?=htmlspecialchars($app->first_name.' '.$app->last_name)?></div><div class="sml"><?=htmlspecialchars($app->email)?></div></td>
            <td class="cnm"><?=htmlspecialchars($app->course_display_name)?></td>
            <td class="sdt"><?=date('M d, Y',strtotime($app->submission_date))?></td>
            <td><?php $d=['name'=>$app->first_name.' '.$app->last_name,'no'=>$app->application_no,'course'=>$app->course_display_name,'date'=>date('M d, Y',strtotime($app->submission_date)),'email'=>$app->email,'phone'=>$app->phone_number]; ?>
              <div class="ag">
                <button class="btn btn-v" onclick='om(<?=json_encode($d,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP)?> )'><i class="fas fa-eye"></i> Details</button>
                <button class="btn btn-d" onclick="da('<?=htmlspecialchars($app->application_no)?>','<?=addslashes($app->first_name.' '.$app->last_name)?>')"><i class="fas fa-trash-alt"></i></button>
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
function ft(){const f=document.getElementById('as').value.toLowerCase();const r=document.getElementById('at').getElementsByTagName('tr');for(let i=1;i<r.length;i++){let v=false;for(const c of r[i].getElementsByTagName('td'))if((c.textContent||c.innerText).toLowerCase().includes(f)){v=true;break;}r[i].style.display=v?'':'none';}}
function om(d){cId=d.no;document.getElementById('mnm').textContent=d.name;document.getElementById('mav').textContent=d.name.split(' ').map(n=>n[0]).join('').toUpperCase().substring(0,2);document.getElementById('mid').textContent='#'+d.no;document.getElementById('mcs').textContent=d.course;document.getElementById('mdt').textContent=d.date;document.getElementById('mem').textContent=d.email;document.getElementById('mph').textContent=d.phone;document.getElementById('vm').style.display='flex';document.body.style.overflow='hidden';}
function cmo(){document.getElementById('vm').style.display='none';document.body.style.overflow='auto';cId=null;}
async function pte(e){if(!cId)return;const b=e.currentTarget,s=document.getElementById('esp'),i=document.getElementById('eic');s.style.display='inline-block';i.style.display='none';b.disabled=true;const f=new FormData();f.append('action','proceed_to_evaluation');f.append('application_no',cId);try{const r=await(await fetch(window.location.href,{method:'POST',body:f})).json();if(r.success){await Swal.fire({title:'Moved!',icon:'success',timer:1500,showConfirmButton:false});window.location.href='Evaluation.php';}else{Swal.fire('Error!',r.message,'error');s.style.display='none';i.style.display='inline-block';b.disabled=false;}}catch{Swal.fire('Error!','Something went wrong.','error');s.style.display='none';i.style.display='inline-block';b.disabled=false;}}
async function da(id,name){const r=await Swal.fire({title:'Are you sure?',text:`Delete application of ${name}?`,icon:'warning',showCancelButton:true,confirmButtonColor:'#ef4444',cancelButtonColor:'#718096',confirmButtonText:'Yes, delete it!'});if(r.isConfirmed){const f=new FormData();f.append('action','delete_application');f.append('application_no',id);try{const d=await(await fetch(window.location.href,{method:'POST',body:f})).json();if(d.success){await Swal.fire('Deleted!',d.message,'success');location.reload();}else Swal.fire('Error!',d.message,'error');}catch{Swal.fire('Error!','Something went wrong.','error');}}}
window.onclick=e=>{if(e.target===document.getElementById('vm'))cmo();}
</script>
</body>
</html>
PHPEOF;

if (file_put_contents($newApps, $cleanCode)) {
    echo "OVERWRITTEN New-Applications.php (" . strlen($cleanCode) . " bytes)\n";
    $v = file_get_contents($newApps);
    echo strpos($v,'student-modal-footer') !== false ? "WARNING: stray STILL found!\n" : "VERIFIED: 100% clean!\n";
} else {
    echo "ERROR: Cannot write file - permission denied?\n";
}

if (function_exists('opcache_reset')) { opcache_reset(); echo "OPCache cleared!\n"; }
if (function_exists('opcache_invalidate')) { opcache_invalidate($newApps, true); }

echo "\n=== DONE! Hard reload New-Applications.php (Ctrl+Shift+R) ===\n";
echo "DELETE THIS FILE after!\n";
?>
