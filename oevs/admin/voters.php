<?php
include('session.php');
include('dbcon.php');

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(16)); }
$csrf = $_SESSION['csrf_token'];
$esc = fn($v)=>htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

/* ---------- CONFIG (adjust if your schema differs) ---------- */
$TABLE_VOTERS = 'voters';
$PK_VOTER     = 'VoterID';
$COL_FN       = 'FirstName';
$COL_LN       = 'LastName';
$COL_YEAR     = 'Year';
$COL_COURSE   = 'Course';
$COL_DEPT     = 'Department';

$VOTES_TABLES_CANDIDATES = ['votes','ballots','ballot_receipts'];
$VOTE_VOTER_COL_OPTIONS  = [$PK_VOTER,'voter_id','student_id'];

/* ---------- INPUTS ---------- */
$status     = isset($_GET['status'])     ? trim($_GET['status'])     : 'All';
$department = isset($_GET['department']) ? trim($_GET['department']) : 'All';
$course     = isset($_GET['course'])     ? trim($_GET['course'])     : 'All';
$q          = isset($_GET['q'])          ? trim($_GET['q'])          : '';

/* ---------- Helpers ---------- */
function _flash($t,$m){ $_SESSION['flash']=['type'=>$t,'msg'=>$m]; }
function _take_flash(){ if(!empty($_SESSION['flash'])){ $x=$_SESSION['flash']; unset($_SESSION['flash']); return $x; } return null; }
function db_name(mysqli $c){ $r=$c->query("SELECT DATABASE() AS d"); return $r?($r->fetch_assoc()['d']??''):''; }
function table_exists(mysqli $c,$t){ $db=db_name($c); $s=$c->prepare("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=? AND TABLE_NAME=? LIMIT 1"); $s->bind_param('ss',$db,$t); $s->execute(); $ok=$s->get_result()->num_rows>0; $s->close(); return $ok; }
function col_exists(mysqli $c,$t,$col){ $db=db_name($c); $s=$c->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=? AND COLUMN_NAME=? LIMIT 1"); $s->bind_param('sss',$db,$t,$col); $s->execute(); $ok=$s->get_result()->num_rows>0; $s->close(); return $ok; }

/* ---------- detect vote source ---------- */
$voteTable=null; $voteVoterCol=null;
foreach ($VOTES_TABLES_CANDIDATES as $candT) {
  if (table_exists($conn,$candT)) {
    foreach ($VOTE_VOTER_COL_OPTIONS as $candC) {
      if (col_exists($conn,$candT,$candC)) { $voteTable=$candT; $voteVoterCol=$candC; break 2; }
    }
  }
}
$hasStatusColOnVoters = col_exists($conn,$TABLE_VOTERS,'Status');

/* ---------- Acronyms (display-only) ---------- */
$COURSE_ACRO = [
  'Bachelor of Science in Accountancy'=>'BSA',
  'Bachelor of Science in Hospitality Management'=>'BSHM',
  'Bachelor of Science in Tourism Management'=>'BSTM',
  'Bachelor of Science in Entrepreneurship'=>'BSEntrep',
  'Bachelor of Science in Business Administration'=>'BSBA',
  'Bachelor of Science in Management Accounting'=>'BSMA',
  'Bachelor of Science in Accounting Information System'=>'BSAIS',
  'Bachelor of Elementary Education'=>'BEEd',
  'Bachelor of Secondary Education'=>'BSEd',
  'Bachelor of Arts in Political Science'=>'AB PolSci',
  'Bachelor of Science in Criminology'=>'BSCrim',
  'Bachelor of Science in Civil Engineering'=>'BSCE',
  'Bachelor of Science in Nursing'=>'BSN',
  'Bachelor of Science in Pharmacy'=>'BSP',
];

/* ---------- distinct lists ---------- */
$statuses = ['All','Voted','Unvoted'];

$depts = ['All'];
$qd = mysqli_query($conn,"SELECT DISTINCT {$COL_DEPT} AS d FROM {$TABLE_VOTERS} WHERE {$COL_DEPT} IS NOT NULL AND TRIM({$COL_DEPT})<>'' ORDER BY {$COL_DEPT}");
while ($qd && ($r=mysqli_fetch_assoc($qd))) { $depts[]=$r['d']; }

/* build Dept -> Courses map */
$deptCourseMap = [];
$dc = mysqli_query($conn,"SELECT DISTINCT {$COL_DEPT} AS d, {$COL_COURSE} AS c FROM {$TABLE_VOTERS} 
                          WHERE {$COL_DEPT} IS NOT NULL AND TRIM({$COL_DEPT})<>'' 
                            AND {$COL_COURSE} IS NOT NULL AND TRIM({$COL_COURSE})<>'' 
                          ORDER BY {$COL_DEPT}, {$COL_COURSE}");
while ($dc && ($r=mysqli_fetch_assoc($dc))) {
  $d = $r['d']; $c = $r['c'];
  if (!isset($deptCourseMap[$d])) $deptCourseMap[$d]=[];
  $deptCourseMap[$d][$c]=true;
}
/* courses to show initially (based on selected department) */
$courses = ['All'];
if ($department==='All') {
  $qc = mysqli_query($conn,"SELECT DISTINCT {$COL_COURSE} AS c FROM {$TABLE_VOTERS} WHERE {$COL_COURSE} IS NOT NULL AND TRIM({$COL_COURSE})<>'' ORDER BY {$COL_COURSE}");
  while ($qc && ($r=mysqli_fetch_assoc($qc))) { $courses[]=$r['c']; }
} else {
  if (!empty($deptCourseMap[$department])) { $courses = array_merge(['All'], array_keys($deptCourseMap[$department])); }
}

/* ---------- delete (optional) ---------- */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_id'],$_POST['csrf_token'])) {
  if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    _flash('error','Invalid CSRF token.');
    header("Location: ".$_SERVER['PHP_SELF']); exit;
  }
  $id=(int)$_POST['delete_id'];
  $sql="DELETE FROM {$TABLE_VOTERS} WHERE {$PK_VOTER}=?"; 
  if ($st=mysqli_prepare($conn,$sql)) { mysqli_stmt_bind_param($st,'i',$id); mysqli_stmt_execute($st); $ok=mysqli_stmt_affected_rows($st)>0; mysqli_stmt_close($st); _flash($ok?'success':'error',$ok?'Voter deleted.':'Delete failed.');}
  else { _flash('error','Database error (delete).');}
  header("Location: ".$_SERVER['PHP_SELF']); exit;
}

/* ---------- main query (safe joins) ---------- */
$where=[]; $params=[]; $types='';

if ($department!=='All'){ $where[]="v.{$COL_DEPT}=?";   $params[]=$department; $types.='s'; }
if ($course!=='All'){     $where[]="v.{$COL_COURSE}=?"; $params[]=$course;     $types.='s'; }

$selectStatus=''; $statusConstraint='';
if ($voteTable && $voteVoterCol){
  $selectStatus="CASE WHEN IFNULL(vv.c,0)>0 THEN 'Voted' ELSE 'Unvoted' END AS _Status";
  if ($status==='Voted')   $statusConstraint="IFNULL(vv.c,0)>0";
  if ($status==='Unvoted') $statusConstraint="IFNULL(vv.c,0)=0";
} else {
  if ($hasStatusColOnVoters){
    $selectStatus="COALESCE(v.Status,'Unvoted') AS _Status";
    if ($status!=='All'){ $where[]="COALESCE(v.Status,'Unvoted')=?"; $params[]=$status; $types.='s'; }
  } else {
    $selectStatus="'Unvoted' AS _Status";
  }
}
if ($statusConstraint) $where[]=$statusConstraint;

$sql="SELECT v.*, {$selectStatus} FROM {$TABLE_VOTERS} v";
if ($voteTable && $voteVoterCol){
  $sql.="
    LEFT JOIN (
      SELECT {$voteVoterCol} AS VID, COUNT(*) AS c
      FROM {$voteTable}
      GROUP BY {$voteVoterCol}
    ) vv ON vv.VID = v.{$PK_VOTER}";
}
if ($where){ $sql.=" WHERE ".implode(' AND ',$where); }
$sql.=" ORDER BY v.{$COL_LN}, v.{$COL_FN}";

$voters=[];
if ($st=mysqli_prepare($conn,$sql)){
  if ($types!==''){ mysqli_stmt_bind_param($st,$types,...$params); }
  mysqli_stmt_execute($st);
  $res=mysqli_stmt_get_result($st);
  while($res && ($row=mysqli_fetch_assoc($res))){ $voters[]=$row; }
  mysqli_stmt_close($st);
} else { _flash('error','Query failed. Check schema.'); }

$flash=_take_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Voters</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- If header.php already loads these, you can remove them -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root{ --primary:#002f6c; --accent:#0056b3; --bg:#f4f6f8; --white:#fff; --shadow:0 4px 12px rgba(0,0,0,.08); --font:'Inter',sans-serif; --muted:#39557a; --radius:14px; }
    *{box-sizing:border-box} body{font-family:var(--font); background:var(--bg); margin:0; color:#23374d}

    .container{max-width:1400px; margin:16px auto; padding:0 20px}
    .card{background:#fff; border:1px solid #e8eef7; box-shadow:var(--shadow); border-radius:var(--radius); padding:12px}

    /* Filters */
    .filters .row{display:grid; grid-template-columns: repeat(3, minmax(220px,1fr)) auto; gap:10px; align-items:end}
    .field{display:flex; flex-direction:column; gap:6px}
    .field label{font-size:12px; color:var(--muted); font-weight:600}
    select{appearance:none; padding:12px 14px; border-radius:12px; border:1px solid #d8e2f0; background:#fff; color:#0d2f66; outline:none}
    .filters .right{display:flex; gap:8px}
    .btn{display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border-radius:12px; border:1px solid #d8e2f0; background:#fff; color:#0d2f66; text-decoration:none; cursor:pointer}
    .btn:hover{background:#f6f9ff}

    /* Controls (search + add) */
    .controls{display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:10px}
    .controls .search{position:relative; flex:1 1 520px; min-width:260px}
    .controls .search input{width:100%; padding:12px 40px; border-radius:12px; border:1px solid #d8e2f0; background:#fff; color:#0d2f66}
    .controls .search .icon-left{position:absolute; left:12px; top:50%; transform:translateY(-50%); opacity:.7}
    .controls .search .clear{position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer; opacity:.55; display:none}

    /* Table */
    .table-wrap{margin-top:12px; overflow:hidden}
    table{width:100%; border-collapse:collapse; background:#fff; border:1px solid #e8eef7; border-radius:var(--radius); overflow:hidden}
    thead{background:linear-gradient(135deg,var(--primary) 0%, var(--accent) 100%)}
    th{padding:14px; text-align:left; font-weight:600; color:#fff; font-size:13px}
    td{padding:14px; border-bottom:1px solid #f0f2f6; font-size:14px}
    tr:hover td{background:#fbfdff}

    .flash{margin:12px 0; padding:12px 14px; border-radius:10px; font-size:14px}
    .flash.success{background:#e9f8ef; color:#146c2e; border:1px solid #b6ecc3}
    .flash.error{background:#fdecec; color:#9f2d2d; border:1px solid #f6c7c7}

    @media (max-width:1100px){ .filters .row{grid-template-columns:1fr 1fr auto} }
    @media (max-width:700px){ .filters .row{grid-template-columns:1fr} }
  </style>
</head>
<body>

  <?php
    // highlight current page if your header supports it
    $activePage = 'voters';
    include 'header.php';
  ?>

  <div class="container">
    <!-- FILTERS -->
    <form id="filtersForm" class="card filters" method="GET" action="<?php echo $esc($_SERVER['PHP_SELF']); ?>">
      <div class="row">
        <div class="field">
          <label for="department">Department</label>
          <select id="department" name="department">
            <?php foreach ($depts as $d): ?>
              <option value="<?php echo $esc($d); ?>" <?php echo ($d===$department)?'selected':''; ?>><?php echo $esc($d); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="course">Course</label>
          <select id="course" name="course">
            <?php foreach ($courses as $c): ?>
              <option value="<?php echo $esc($c); ?>" <?php echo ($c===$course)?'selected':''; ?>><?php echo $esc($c); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="status">Status</label>
          <select id="status" name="status">
            <?php foreach ($statuses as $s): ?>
              <option value="<?php echo $esc($s); ?>" <?php echo ($s===$status)?'selected':''; ?>><?php echo $esc($s); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="right">
          <a class="btn" href="<?php echo $esc($_SERVER['PHP_SELF']); ?>"><i class="fa fa-rotate-left"></i> Reset</a>
        </div>
      </div>

      <!-- CONTROLS -->
      <div class="controls">
        <div class="search">
          <i class="fa fa-search icon-left"></i>
          <input id="liveSearch" type="text" value="<?php echo $esc($q); ?>" placeholder="Search name, ID, course, department, year, status…">
          <i id="clearSearch" class="fa fa-xmark clear" title="Clear"></i>
        </div>
        <a class="btn" href="new_voter.php"><i class="fa fa-user-plus"></i> Add Voter</a>
      </div>
    </form>

    <?php if ($flash): ?>
      <div class="flash <?php echo $flash['type']==='success'?'success':'error'; ?>">
        <?php echo $esc($flash['msg']); ?>
      </div>
    <?php endif; ?>

    <div class="table-wrap">
      <table id="votersTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Year</th>
            <th>Course</th>
            <th>Department</th>
            <th>Status</th>
            <th style="width:200px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($voters)): ?>
            <tr class="no-rows"><td colspan="8" style="text-align:center;color:#666;padding:24px;">No voters found.</td></tr>
          <?php else: foreach ($voters as $row):
            $id    = (int)($row[$PK_VOTER] ?? 0);
            $yr    = $row[$COL_YEAR] ?? '';
            $courseFull = $row[$COL_COURSE] ?? '';
            $courseAcr  = $COURSE_ACRO[$courseFull] ?? $courseFull;
            $dept  = $row[$COL_DEPT] ?? '';
            $ln    = $row[$COL_LN] ?? '';
            $fn    = $row[$COL_FN] ?? '';
            $st    = $row['_Status'] ?? 'Unvoted';
          ?>
            <tr class="row">
              <td><?php echo $esc($id); ?></td>
              <td><?php echo $esc($ln); ?></td>
              <td><?php echo $esc($fn); ?></td>
              <td><?php echo $esc($yr); ?></td>
              <td title="<?php echo $esc($courseFull); ?>"><?php echo $esc($courseAcr); ?></td>
              <td><?php echo $esc($dept); ?></td>
              <td><?php echo $esc($st); ?></td>
              <td>
                <a class="btn" href="edit_voter.php?id=<?php echo $id; ?>"><i class="fa fa-pen"></i> Edit</a>
                <form method="POST" action="<?php echo $esc($_SERVER['PHP_SELF']); ?>" style="display:inline" onsubmit="return confirm('Delete this voter?');">
                  <input type="hidden" name="csrf_token" value="<?php echo $esc($csrf); ?>">
                  <input type="hidden" name="delete_id" value="<?php echo $id; ?>">
                  <button type="submit" class="btn" style="color:#b42318"><i class="fa fa-trash"></i> Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <footer style="text-align:center; padding:20px 0; color:#666; font-size:14px;">© 2025 Online Election Voting System</footer>

<script>
/* --- Dependent Course list + auto-submit (real-time) --- */
(function(){
  const form = document.getElementById('filtersForm');
  const selDept = document.getElementById('department');
  const selCourse = document.getElementById('course');
  const selStatus = document.getElementById('status');

  // Dept -> Courses map from PHP
  const map = <?php
    $out = [];
    foreach ($deptCourseMap as $d=>$arr) $out[$d] = array_keys($arr);
    echo json_encode($out, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  ?>;

  function rebuildCourses(dept, keepValue){
    const current = keepValue ? selCourse.value : 'All';
    const opts = ['All'].concat(dept==='All' ? (<?php
        $all = [];
        $qc2 = mysqli_query($conn,"SELECT DISTINCT {$COL_COURSE} AS c FROM {$TABLE_VOTERS} WHERE {$COL_COURSE} IS NOT NULL AND TRIM({$COL_COURSE})<>'' ORDER BY {$COL_COURSE}");
        while ($qc2 && ($r=mysqli_fetch_assoc($qc2))) $all[]=$r['c'];
        echo json_encode($all, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
      ?>) : (map[dept] || [])
    );
    selCourse.innerHTML = '';
    opts.forEach(v=>{
      const o = document.createElement('option');
      o.value = v; o.textContent = v;
      if (v === current) o.selected = true;
      selCourse.appendChild(o);
    });
  }

  selDept.addEventListener('change', ()=>{
    rebuildCourses(selDept.value, false);
    form.submit();
  });

  selCourse.addEventListener('change', ()=> form.submit());
  selStatus.addEventListener('change', ()=> form.submit());

  rebuildCourses(selDept.value, true);
})();

/* --- Realtime search --- */
(function(){
  const input = document.getElementById('liveSearch');
  const clear = document.getElementById('clearSearch');
  const tbody = document.querySelector('#votersTable tbody');
  if (!input || !tbody) return;

  const rows = Array.from(tbody.querySelectorAll('tr.row'));
  const ensureEmpty = ()=>{
    let n = tbody.querySelector('.no-rows');
    if (!n) {
      n = document.createElement('tr');
      n.className = 'no-rows';
      const td = document.createElement('td');
      td.colSpan = 8; td.style.textAlign='center'; td.style.color='#666'; td.style.padding='24px';
      td.textContent = 'No voters match your search.';
      n.appendChild(td); tbody.appendChild(n);
    }
  };

  let raf = null;
  const run = ()=>{
    const term = input.value.trim().toLowerCase();
    let shown = 0;
    rows.forEach(tr=>{
      const txt = tr.innerText.toLowerCase();
      const ok = term==='' || txt.includes(term);
      tr.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });
    const empty = tbody.querySelector('.no-rows');
    if (!shown) { if (!empty) ensureEmpty(); }
    else if (empty) empty.remove();
  };

  input.addEventListener('input', ()=>{
    clear.style.display = input.value ? 'block' : 'none';
    if (raf) cancelAnimationFrame(raf);
    raf = requestAnimationFrame(run);
  });
  clear.addEventListener('click', ()=>{ input.value=''; clear.style.display='none'; run(); });

  clear.style.display = input.value ? 'block' : 'none';
  if (input.value) run();
})();
</script>
</body>
</html>
