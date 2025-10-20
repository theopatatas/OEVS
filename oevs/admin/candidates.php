<?php
include('session.php');
include('dbcon.php');

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(16)); }
$csrf = $_SESSION['csrf_token'];
$esc  = fn($v)=>htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

/* ---------- CONFIG (edit if your schema differs) ---------- */
$TABLE_CAND = 'candidate';
$PK_CAND    = 'CandidateID';
$COL_POS    = 'Position';
$COL_PARTY  = 'Party';
$COL_FN     = 'FirstName';
$COL_LN     = 'LastName';
$COL_MI     = 'MiddleInitial';        // keep as-is for legacy schema
$COL_YEAR   = 'Year';
$COL_COURSE = 'Course';
$COL_DEPT   = 'Department';
$COL_PHOTO  = 'Photo';
$COL_QUAL   = 'qualification';
$COL_CAMPUS = 'Campus';

/* Custom position order (filter + table) */
$POSITION_ORDER = [
  'President',
  'Vice-President',
  'Governor',
  'Vice-Governor',
  'Secretary',
  'Treasurer',
  'Representative',
  'Social-Media Officer',
];
$posIndex = array_flip($POSITION_ORDER);

/* ---------- INPUTS ---------- */
$department  = isset($_GET['department']) ? trim($_GET['department']) : 'All';
$course      = isset($_GET['course'])     ? trim($_GET['course'])     : 'All';
$position    = isset($_GET['position'])   ? trim($_GET['position'])   : 'All';
$campus_raw  = isset($_GET['campus'])     ? trim($_GET['campus'])     : 'All'; // UI-facing
$q           = isset($_GET['q'])          ? trim($_GET['q'])          : '';
$campus      = ($campus_raw === 'All' || $campus_raw === '') ? null : $campus_raw; // logic-facing

/* ---------- DELETE handler ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $delete_id  = (int)($_POST['delete_id'] ?? 0);
  $posted_csrf = $_POST['csrf_token'] ?? '';

  if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $posted_csrf)) {
    http_response_code(400);
    die('Invalid CSRF token.');
  }

  mysqli_begin_transaction($conn);
  try {
    // Get photo path (optional cleanup)
    $photo = '';
    if ($stmt = mysqli_prepare($conn, "SELECT {$COL_PHOTO} FROM {$TABLE_CAND} WHERE {$PK_CAND}=?")) {
      mysqli_stmt_bind_param($stmt, 'i', $delete_id);
      mysqli_stmt_execute($stmt);
      $rs = mysqli_stmt_get_result($stmt);
      if ($rs && ($r = mysqli_fetch_assoc($rs))) $photo = $r[$COL_PHOTO] ?? '';
      mysqli_stmt_close($stmt);
    }

    // Delete votes
    if ($stmt = mysqli_prepare($conn, "DELETE FROM votes WHERE CandidateID=?")) {
      mysqli_stmt_bind_param($stmt, 'i', $delete_id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
    }

    // Delete candidate
    if ($stmt = mysqli_prepare($conn, "DELETE FROM {$TABLE_CAND} WHERE {$PK_CAND}=?")) {
      mysqli_stmt_bind_param($stmt, 'i', $delete_id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
    }

    mysqli_commit($conn);

    // Remove photo (best-effort)
    if (!empty($photo)) {
      $p = __DIR__ . '/' . ltrim($photo, '/');
      $rp = @realpath($p);
      $rd = @realpath(__DIR__);
      if ($rp && $rd && strpos($rp, $rd) === 0 && file_exists($rp)) @unlink($rp);
    }

    $_SESSION['flash_success'] = 'Candidate deleted successfully.';

    // Redirect preserving filters (use raw campus for UI)
    $qs = http_build_query([
      'department' => $department,
      'course'     => $course,
      'position'   => $position,
      'campus'     => $campus_raw,
      'q'          => $q,
    ]);
    header("Location: " . $_SERVER['PHP_SELF'] . ($qs ? "?{$qs}" : ''));
    exit;

  } catch (Throwable $e) {
    mysqli_rollback($conn);
    error_log('Delete error: '.$e->getMessage());
    http_response_code(500);
    die('Failed to delete candidate.');
  }
}

/* ---------- Course acronyms (display only) ---------- */
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
  'Bachelor of Science in Information Technology'=>'BSIT',
];

/* ---------- Distinct lists & Dept->Course map ---------- */
/* Positions (distinct, then sorted by custom order) */
$tmpPos = [];
$resP = mysqli_query($conn, "SELECT DISTINCT {$COL_POS} p FROM {$TABLE_CAND} WHERE {$COL_POS} IS NOT NULL AND TRIM({$COL_POS})<>''");
while ($resP && ($r=mysqli_fetch_assoc($resP))) $tmpPos[] = $r['p'];
usort($tmpPos, function($a,$b) use($posIndex){
  $ia = $posIndex[$a] ?? PHP_INT_MAX;
  $ib = $posIndex[$b] ?? PHP_INT_MAX;
  if ($ia === $ib) return strcasecmp($a,$b);
  return $ia <=> $ib;
});
$positions = array_merge(['All'], $tmpPos);

/* Departments */
$depts = ['All'];
$resD = mysqli_query($conn, "SELECT DISTINCT {$COL_DEPT} d FROM {$TABLE_CAND} WHERE {$COL_DEPT} IS NOT NULL AND TRIM({$COL_DEPT})<>'' ORDER BY {$COL_DEPT}");
while ($resD && ($r=mysqli_fetch_assoc($resD))) $depts[] = $r['d'];

/* Campuses (fixed list so options always show) */
$campuses = ['All', 'Au Main', 'Au South', 'Au San Jose'];

/* Dept -> Course map (for dependent select) */
$deptCourseMap = [];
$resDC = mysqli_query($conn, "SELECT DISTINCT {$COL_DEPT} d, {$COL_COURSE} c FROM {$TABLE_CAND}
  WHERE {$COL_DEPT} IS NOT NULL AND TRIM({$COL_DEPT})<>'' AND {$COL_COURSE} IS NOT NULL AND TRIM({$COL_COURSE})<>'' 
  ORDER BY {$COL_DEPT}, {$COL_COURSE}");
while ($resDC && ($r=mysqli_fetch_assoc($resDC))) {
  $deptCourseMap[$r['d']][$r['c']] = true;
}

/* Courses to show initially (based on selected dept) */
$courses = ['All'];
if ($department === 'All') {
  $resC = mysqli_query($conn, "SELECT DISTINCT {$COL_COURSE} c FROM {$TABLE_CAND} WHERE {$COL_COURSE} IS NOT NULL AND TRIM({$COL_COURSE})<>'' ORDER BY {$COL_COURSE}");
  while ($resC && ($r=mysqli_fetch_assoc($resC))) $courses[] = $r['c'];
} else {
  if (!empty($deptCourseMap[$department])) $courses = array_merge(['All'], array_keys($deptCourseMap[$department]));
}

/* ---------- Build query with server-side filters ---------- */
$where=[]; $params=[]; $types='';

if ($department !== 'All') { $where[] = "c.{$COL_DEPT}=?";   $params[]=$department; $types.='s'; }
if ($course     !== 'All') { $where[] = "c.{$COL_COURSE}=?"; $params[]=$course;     $types.='s'; }
if ($position   !== 'All') { $where[] = "c.{$COL_POS}=?";    $params[]=$position;   $types.='s'; }
if ($campus !== null)       { $where[] = "c.{$COL_CAMPUS}=?"; $params[]=$campus;     $types.='s'; }

$sql = "SELECT c.* FROM {$TABLE_CAND} c";
if ($where) $sql .= " WHERE ".implode(' AND ',$where);

/* Custom position order for table sort (then Party, Last, First) */
$sql .= " ORDER BY FIELD(c.{$COL_POS},"
      . " 'President','Vice-President','Governor','Vice-Governor',"
      . " 'Secretary','Treasurer','Representative','Social-Media Officer'"
      . "), c.{$COL_PARTY}, c.{$COL_LN}, c.{$COL_FN}";

/* Fetch candidates */
$candidates=[]; 
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
  if ($types!=='') { mysqli_stmt_bind_param($stmt, $types, ...$params); }
  mysqli_stmt_execute($stmt);
  $rs = mysqli_stmt_get_result($stmt);
  while ($rs && ($row=mysqli_fetch_assoc($rs))) $candidates[] = $row;
  mysqli_stmt_close($stmt);
}

/* Batch vote counts */
$voteCounts=[];
if ($candidates) {
  $ids = array_map(fn($r)=>(int)$r[$PK_CAND], $candidates);
  $idList = implode(',', array_map('intval',$ids));
  if ($idList!=='') {
    $vq = mysqli_query($conn, "SELECT CandidateID, COUNT(*) cnt FROM votes WHERE CandidateID IN ($idList) GROUP BY CandidateID");
    while ($vq && ($vr=mysqli_fetch_assoc($vq))) $voteCounts[(int)$vr['CandidateID']] = (int)$vr['cnt'];
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Candidates</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root{ --primary:#002f6c; --accent:#0056b3; --bg:#f4f6f8; --white:#fff; --shadow:0 4px 12px rgba(0,0,0,.08); --font:'Inter',sans-serif; --muted:#39557a; --radius:14px; }
    *{box-sizing:border-box}
    body{font-family:var(--font); background:var(--bg); margin:0; color:#23374d}

    .container{max-width:1400px; margin:16px auto; padding:0 20px}
    .card{background:#fff; border:1px solid #e8eef7; box-shadow:var(--shadow); border-radius:var(--radius); padding:12px}

    .filters .row{display:grid; grid-template-columns: repeat(4, minmax(220px,1fr)) auto; gap:10px; align-items:end}
    .field{display:flex; flex-direction:column; gap:6px}
    .field label{font-size:12px; color:var(--muted); font-weight:600}
    select{appearance:none; padding:12px 14px; border-radius:12px; border:1px solid #d8e2f0; background:#fff; color:#0d2f66; outline:none}
    .filters .right{display:flex; gap:8px}
    .btn{display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border-radius:12px; border:1px solid #d8e2f0; background:#fff; color:#0d2f66; text-decoration:none; cursor:pointer}
    .btn:hover{background:#f6f9ff}

    .controls{display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:10px}
    .controls .search{position:relative; flex:1 1 520px; min-width:260px}
    .controls .search input{width:100%; padding:12px 40px; border-radius:12px; border:1px solid #d8e2f0; background:#fff; color:#0d2f66}
    .controls .search .icon-left{position:absolute; left:12px; top:50%; transform:translateY(-50%); opacity:.7}
    .controls .search .clear{position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer; opacity:.55; display:none}

    .table-wrap{margin-top:12px; overflow:hidden}
    table{width:100%; border-collapse:collapse; background:#fff; border:1px solid #e8eef7; border-radius:var(--radius); overflow:hidden}
    thead{background:linear-gradient(135deg,var(--primary) 0%, var(--accent) 100%)}
    th{padding:14px; text-align:left; font-weight:600; color:#fff; font-size:13px}
    td{padding:14px; border-bottom:1px solid #f0f2f6; font-size:14px}
    tr:hover td{background:#fbfdff}

    /* Flash notification */
    .flash {
      position: fixed;
      top: 18px;
      right: 18px;
      z-index: 3000;
      padding: 12px 16px;
      border-radius: 10px;
      box-shadow: 0 6px 20px rgba(29,41,74,0.18);
      color: white;
      font-weight:600;
      opacity: 1;
    }
    .flash.success { background: linear-gradient(90deg,#1e8f4b,#12935a); }
    .flash.hide { opacity: 0; pointer-events: none; transform: translateY(-8px); transition: opacity .35s, transform .35s; }

    @media (max-width:1100px){ .filters .row{grid-template-columns:1fr 1fr 1fr} }
    @media (max-width:700px){ .filters .row{grid-template-columns:1fr} }
  </style>
</head>
<body>

  <?php
    $activePage = 'candidates';
    include 'header.php';
  ?>

  <?php if (!empty($_SESSION['flash_success'])): ?>
    <div id="flash" class="flash success"><?php echo $esc($_SESSION['flash_success']); ?></div>
    <?php unset($_SESSION['flash_success']); ?>
  <?php endif; ?>

  <div class="container">
    <!-- FILTER BAR -->
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
          <label for="position">Position</label>
          <select id="position" name="position">
            <?php foreach ($positions as $p): ?>
              <option value="<?php echo $esc($p); ?>" <?php echo ($p===$position)?'selected':''; ?>><?php echo $esc($p); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="campus">Campus</label>
          <select id="campus" name="campus">
            <?php foreach ($campuses as $c): ?>
              <option value="<?php echo $esc($c); ?>" <?php echo ($c===$campus_raw)?'selected':''; ?>><?php echo $esc($c); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="right">
          <button type="button" id="resetBtn" class="btn"><i class="fa fa-rotate-left"></i> Reset</button>
        </div>
      </div>

      <!-- CONTROLS -->
      <div class="controls">
        <div class="search">
          <i class="fa fa-search icon-left"></i>
          <input id="liveSearch" name="q" type="text" value="<?php echo $esc($q); ?>" placeholder="Search name, party, position, course, department, year…">
          <i id="clearSearch" class="fa fa-xmark clear" title="Clear"></i>
        </div>

        <a class="btn" href="add_candidate.php"><i class="fa fa-user-plus"></i> Add Candidate</a>
        <button class="btn" type="button" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
        <button class="btn" type="submit" form="exportForm"><i class="fa fa-file-excel"></i> Export Excel</button>
      </div>
    </form>

    <!-- Hidden Export Excel form -->
    <form id="exportForm" method="POST" action="canvassing_excel.php" style="display:none">
      <?php
        $id_excel = '';
        $q1 = mysqli_query($conn, "SELECT {$PK_CAND} FROM {$TABLE_CAND} LIMIT 1");
        if ($q1 && mysqli_num_rows($q1)) { $r1 = mysqli_fetch_assoc($q1); $id_excel = $r1[$PK_CAND]; }
      ?>
      <input type="hidden" name="id_excel" value="<?php echo $esc($id_excel); ?>">
      <input type="hidden" name="position" value="<?php echo $esc($position); ?>">
      <input type="hidden" name="course" value="<?php echo $esc($course); ?>">
      <input type="hidden" name="department" value="<?php echo $esc($department); ?>">
      <input type="hidden" name="campus" value="<?php echo ($campus === null) ? '' : $esc($campus); ?>">
      <input id="exportQ" type="hidden" name="q" value="<?php echo $esc($q); ?>">
    </form>

    <!-- TABLE -->
    <div class="table-wrap">
      <table id="candidateTable">
        <thead>
          <tr>
            <th>Position</th>
            <th>Party</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Year</th>
            <th>Course</th>
            <th>Department</th>
            <th>Campus</th>
            <th>Votes</th>
            <th style="width:200px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($candidates)): ?>
            <tr class="no-rows"><td colspan="10" style="text-align:center;color:#666;padding:24px;">No candidates found.</td></tr>
          <?php else: foreach ($candidates as $row):
            $id         = (int)$row[$PK_CAND];
            $votes      = $voteCounts[$id] ?? 0;
            $dept       = $row[$COL_DEPT] ?? '';
            $courseFull = $row[$COL_COURSE] ?? '';
            $courseAcr  = $COURSE_ACRO[$courseFull] ?? $courseFull;
            $campRawDb  = isset($row[$COL_CAMPUS]) ? trim((string)$row[$COL_CAMPUS]) : '';
            $campCell   = $campRawDb === '' ? '' : $campRawDb; // blank if NULL/empty
          ?>
            <tr class="row">
              <td><?php echo $esc($row[$COL_POS]); ?></td>
              <td><?php echo $esc($row[$COL_PARTY]); ?></td>
              <td><?php echo $esc($row[$COL_FN]); ?></td>
              <td><?php echo $esc($row[$COL_LN]); ?></td>
              <td><?php echo $esc($row[$COL_YEAR]); ?></td>
              <td title="<?php echo $esc($courseFull); ?>"><?php echo $esc($courseAcr); ?></td>
              <td><?php echo $esc($dept); ?></td>
              <td><?php echo $esc($campCell); ?></td>
              <td><?php echo (int)$votes; ?></td>
              <td>
                <a href="#" class="btn js-view"
                   data-id="<?php echo $id; ?>"
                   data-photo="<?php echo $esc($row[$COL_PHOTO] ?? ''); ?>"
                   data-fn="<?php echo $esc($row[$COL_FN]); ?>"
                   data-ln="<?php echo $esc($row[$COL_LN]); ?>"
                   data-mi="<?php echo $esc($row[$COL_MI] ?? ''); ?>"
                   data-pos="<?php echo $esc($row[$COL_POS]); ?>"
                   data-party="<?php echo $esc($row[$COL_PARTY]); ?>"
                   data-yr="<?php echo $esc($row[$COL_YEAR]); ?>"
                   data-crs="<?php echo $esc($courseFull); ?>"
                   data-dept="<?php echo $esc($dept); ?>"
                   data-campus="<?php echo $esc($campCell); ?>"
                   data-qual="<?php echo $esc($row[$COL_QUAL] ?? ''); ?>"
                ><i class="fa fa-eye"></i> View</a>

                <a class="btn" href="edit_candidate.php?id=<?php echo $id; ?>"><i class="fa fa-pen"></i> Edit</a>

                <form method="POST" action="<?php
                    echo $esc($_SERVER['PHP_SELF'].'?'.http_build_query(['department'=>$department,'course'=>$course,'position'=>$position,'campus'=>$campus_raw,'q'=>$q]));
                  ?>" style="display:inline">
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

  <!-- VIEW MODAL -->
  <style>
    #viewModalOverlay{position:fixed;inset:0;background:rgba(0,0,0,.45);display:none;align-items:center;justify-content:center;z-index:2000}
    #viewModal{background:#fff;border-radius:12px;max-width:720px;width:92%;padding:18px;box-shadow:0 10px 30px rgba(0,0,0,.25)}
    #viewModal .row{display:flex;gap:12px;align-items:flex-start}
    #viewModal img{height:120px;width:120px;object-fit:cover;border-radius:8px;border:1px solid #e9eef6}
    #viewModal h3{margin:0 0 8px;font-size:20px}
    #viewModal .meta{color:#456; font-size:14px}
    #viewModal .closeBtn{float:right;border:0;background:none;font-size:20px;cursor:pointer}
    @media (max-width:600px){ #viewModal .row{flex-direction:column;align-items:center;text-align:center} }
  </style>

  <div id="viewModalOverlay" aria-hidden="true">
    <div id="viewModal" role="dialog" aria-modal="true" aria-labelledby="viewModalTitle">
      <button class="closeBtn" id="viewModalClose" title="Close">&times;</button>
      <div class="row">
        <div style="flex:0 0 140px">
          <img id="viewPhoto" src="" alt="Candidate photo">
        </div>
        <div style="flex:1">
          <h3 id="viewModalTitle">Candidate Name</h3>
          <div class="meta"><strong>Position:</strong> <span id="viewPos"></span></div>
          <div class="meta"><strong>Party:</strong> <span id="viewParty"></span></div>
          <div class="meta"><strong>Year / Course:</strong> <span id="viewYear"></span> / <span id="viewCourse"></span></div>
          <div class="meta"><strong>Department:</strong> <span id="viewDept"></span></div>
          <div class="meta"><strong>Campus:</strong> <span id="viewCampus"></span></div>
          <div style="margin-top:10px"><strong>Qualification / Notes:</strong>
            <div id="viewQual" style="margin-top:6px;color:#334;"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>
/* --- Dependent Course list + auto-submit --- */
(function(){
  const form = document.getElementById('filtersForm');
  const selDept = document.getElementById('department');
  const selCourse = document.getElementById('course');
  const selPos = document.getElementById('position');
  const selCampus = document.getElementById('campus');

  const map = <?php
    $out = [];
    foreach ($deptCourseMap as $d=>$arr) $out[$d] = array_keys($arr);
    echo json_encode($out, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  ?>;

  const allCourses = <?php
    $all = [];
    $qc2 = mysqli_query($conn,"SELECT DISTINCT {$COL_COURSE} c FROM {$TABLE_CAND} WHERE {$COL_COURSE} IS NOT NULL AND TRIM({$COL_COURSE})<>'' ORDER BY {$COL_COURSE}");
    while ($qc2 && ($r=mysqli_fetch_assoc($qc2))) $all[]=$r['c'];
    echo json_encode($all, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  ?>;

  function rebuildCourses(dept, keepCurrent){
    const current = keepCurrent ? selCourse.value : 'All';
    const options = ['All'].concat(dept==='All' ? allCourses : (map[dept] || []));
    selCourse.innerHTML = '';
    options.forEach(v=>{
      const o = document.createElement('option');
      o.value = v; o.textContent = v;
      if (v === current) o.selected = true;
      selCourse.appendChild(o);
    });
  }
  window.rebuildCourses = (d,k)=>rebuildCourses(d,k);

  selDept.addEventListener('change', ()=>{ rebuildCourses(selDept.value, false); form.submit(); });
  selCourse.addEventListener('change', ()=> form.submit());
  selPos.addEventListener('change', ()=> form.submit());
  if (selCampus) selCampus.addEventListener('change', ()=> form.submit());

  rebuildCourses(selDept.value, true);
})();

/* --- Realtime table search --- */
(function(){
  const input = document.getElementById('liveSearch');
  const clear = document.getElementById('clearSearch');
  const tbody = document.querySelector('#candidateTable tbody');
  if (!input || !tbody) return;

  const rows = Array.from(tbody.querySelectorAll('tr.row'));
  const ensureEmpty = ()=>{
    let n = tbody.querySelector('.no-rows');
    if (!n) {
      n = document.createElement('tr');
      n.className = 'no-rows';
      const td = document.createElement('td');
      td.colSpan = 10; td.style.textAlign='center'; td.style.color='#666'; td.style.padding='24px';
      td.textContent = 'No candidates match your search.';
      n.appendChild(td); tbody.appendChild(n);
    }
  };

  let raf=null;
  const run=()=>{
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

    const eq = document.getElementById('exportQ');
    if (eq) eq.value = term;
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

/* --- Reset button logic --- */
(function(){
  const resetBtn = document.getElementById('resetBtn');
  const form = document.getElementById('filtersForm');
  const selDept = document.getElementById('department');
  const selCourse = document.getElementById('course');
  const selPos = document.getElementById('position');
  const selCampus = document.getElementById('campus');
  const input = document.getElementById('liveSearch');
  const exportQ = document.getElementById('exportQ');

  if (resetBtn) {
    resetBtn.addEventListener('click', ()=>{
      selDept.value = 'All';
      window.rebuildCourses('All', false);
      selPos.value = 'All';
      if (selCampus) selCampus.value = 'All';
      if (input) input.value = '';
      if (exportQ) exportQ.value = '';
      form.submit();
    });
  }
})();

/* --- Quick View modal logic --- */
(function(){
  const overlay = document.getElementById('viewModalOverlay');
  const closeBtn = document.getElementById('viewModalClose');
  if (!overlay) return;

  function openModal(data){
    document.getElementById('viewPhoto').src = data.photo || 'upload/placeholder.png';
    document.getElementById('viewModalTitle').textContent = (data.fn || '') + (data.ln ? (' ' + data.ln) : '');
    document.getElementById('viewPos').textContent = data.pos || '';
    document.getElementById('viewParty').textContent = data.party || '';
    document.getElementById('viewYear').textContent = data.yr || '';
    document.getElementById('viewCourse').textContent = data.crs || '';
    document.getElementById('viewDept').textContent = data.dept || '';
    document.getElementById('viewCampus').textContent = data.campus || '';
    document.getElementById('viewQual').textContent = data.qual || '';
    overlay.style.display = 'flex';
    overlay.setAttribute('aria-hidden','false');
  }

  function closeModal(){
    overlay.style.display = 'none';
    overlay.setAttribute('aria-hidden','true');
  }

  closeBtn.addEventListener('click', closeModal);
  overlay.addEventListener('click', (e)=>{ if (e.target === overlay) closeModal(); });
  document.addEventListener('keydown', (e)=>{ if (e.key === 'Escape' && overlay.style.display === 'flex') closeModal(); });

  document.addEventListener('click', function(e){
    const el = e.target.closest && e.target.closest('.js-view');
    if (!el) return;
    e.preventDefault();
    const data = {
      id: el.dataset.id || '',
      photo: el.dataset.photo || '',
      fn: el.dataset.fn || '',
      ln: el.dataset.ln || '',
      mi: el.dataset.mi || '',
      pos: el.dataset.pos || '',
      party: el.dataset.party || '',
      yr: el.dataset.yr || '',
      crs: el.dataset.crs || '',
      dept: el.dataset.dept || '',
      campus: el.dataset.campus || '',
      qual: el.dataset.qual || ''
    };
    openModal(data);
  }, false);
})();

/* --- Single JS confirm for deletes --- */
(function(){
  document.addEventListener('submit', function(e){
    const form = e.target;
    if (form && form.querySelector && form.querySelector('input[name="delete_id"]')) {
      if (!confirm('Delete this candidate? This also removes their votes. This action cannot be undone.')) {
        e.preventDefault();
        return false;
      }
    }
  }, true);
})();

/* --- Flash auto-dismiss (3s) --- */
(function(){
  const flash = document.getElementById('flash');
  if (!flash) return;
  setTimeout(()=>{
    flash.classList.add('hide');
    setTimeout(()=>{ flash.remove(); }, 400);
  }, 3000);
})();
</script>
</body>
</html>
