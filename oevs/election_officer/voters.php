<?php
// voters.php — fixed status column, footer removed
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('session.php');
include('dbcon.php');
if (file_exists(__DIR__ . '/header.php')) include(__DIR__ . '/header.php');

if (!isset($con) || !($con instanceof mysqli)) {
  if (isset($conn) && ($conn instanceof mysqli)) $con = $conn;
  else die("DB connection handle \$con not found. Check dbcon.php.");
}

/* Department → Courses (FULL names) */
$deptCourses = [
  'CMA'=>['__COLLEGE__'=>'College of Management and Accountancy','Bachelor of Science in Accountancy','Bachelor of Science in Business Administration','Bachelor of Science in Management Accounting','Bachelor of Science in Accounting Information System','Bachelor of Science in Entrepreneurship','Bachelor of Science in Hospitality Management','Bachelor of Science in Tourism Management'],
  'CELA'=>['__COLLEGE__'=>'College of Education and Liberal Arts','Bachelor of Science in Elementary Education','Bachelor of Science in Secondary Education','Bachelor of Science in Political Science'],
  'CCJE'=>['__COLLEGE__'=>'College of Criminal Justice Education','Bachelor of Science in Criminology'],
  'COE'=>['__COLLEGE__'=>'College of Engineering','Bachelor of Science in Civil Engineering'],
  'CAHS'=>['__COLLEGE__'=>'College of Allied Health Sciences','Bachelor of Science in Nursing','Bachelor of Science in Pharmacy','Bachelor of Science in Midwifery'],
  'CIT'=>['__COLLEGE__'=>'College of Information Technology','Bachelor of Science in Information Technology'],
  'CAS'=>['__COLLEGE__'=>'College of Arts and Sciences'],
];

/* Course FULL → ACRONYM */
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

/* Fetch rows */
$TABLE = 'voters';
$rowsRaw = [];
$sql = "SELECT * FROM `{$TABLE}` ORDER BY 1";
$res = $con->query($sql);
if (!$res) die("SQL error: ".$con->error." in ".$sql);
while ($r = $res->fetch_assoc()) $rowsRaw[] = $r;
$rows = array_map(fn($r) => array_change_key_case($r, CASE_LOWER), $rowsRaw);

/* Helpers + column guesses */
function pick(array $row, array $candidates, $default=''){
  foreach ($candidates as $k){ if (array_key_exists($k,$row) && $row[$k]!=='' && $row[$k]!==null) return $row[$k]; }
  return $default;
}
function toStatusText($val){
  $v = is_string($val) ? strtolower(trim($val)) : $val;
  if ($v===1 || $v==='1' || $v===true || $v==='yes' || $v==='y' || $v==='voted') return 'Voted';
  if ($v===0 || $v==='0' || $v===false || $v==='no' || $v==='n' || $v==='unvoted' || $v==='') return 'Unvoted';
  return is_string($val) ? ucwords(trim($val)) : 'Unvoted';
}
$K_ID    = ['voterid','student_id','voter_id','id','stud_id','studid','stud_no','studentno','student_number','sid','schoolid'];
$K_LN    = ['lastname','last_name','lname','last','surname','family_name'];
$K_FN    = ['firstname','first_name','fname','first','given_name'];
$K_FULL  = ['fullname','full_name','name'];
$K_YEAR  = ['year','year_level','yearlevel','yr','yearofstudy','year_of_study','grade_level'];
$K_COURSE= ['course','program','strand','major','degree'];
$K_DEPT  = ['department','dept','department_name'];
$K_CAMP  = ['campus','branch','site','location'];
$K_STAT  = ['status','voted','is_voted','has_voted','vote_status','voting_status'];

function course_to_acronym($v, $MAP){
  $v = trim((string)$v);
  if ($v==='' ) return '';
  if (isset($MAP[$v])) return $MAP[$v];
  if (in_array($v, $MAP, true)) return $v;
  return $v;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Voters</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
<style>
:root{ --bg:#f4f6f8; --white:#fff; --ink:#0f1b2d; --muted:#6a7b91; --border:#e6ebf4; --brand:#0a3b8e; --brand-2:#0f56c6; --ring:#cfe1ff; }
*{box-sizing:border-box}
html,body{margin:0; background:var(--bg); color:var(--ink); font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}
.container{max-width:1400px; margin:24px auto; padding:0 16px}

/* FILTERS (unchanged from your last working layout) */
.card{background:#fff; border:1px solid var(--border); border-radius:16px; box-shadow:0 10px 24px rgba(10,59,142,.06)}
.filters{display:grid; grid-template-columns:repeat(12,1fr); grid-template-rows:auto auto; gap:16px; padding:16px}
.filters .field:nth-of-type(1){grid-column:span 3}
.filters .field:nth-of-type(2){grid-column:span 3}
.filters .field:nth-of-type(3){grid-column:span 3}
.filters .field:nth-of-type(4){grid-column:span 3}
.filters .search{grid-column:1 / span 10; position:relative}
.filters .actions{grid-column:11 / span 2; display:flex; align-items:center; justify-content:flex-end}
.label{display:block; font-size:13px; font-weight:700; color:#2a3a52; margin:4px 0 8px}
.control{width:100%; padding:12px 14px; border:1px solid var(--border); border-radius:14px; background:#fff; outline:none}
.control:focus{border-color:var(--brand-2); box-shadow:0 0 0 3px var(--ring)}
.search .control{padding-left:42px}
.search .icon{position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#9aa8bc; font-size:14px}
.btn{display:inline-flex; align-items:center; gap:8px; padding:11px 16px; border-radius:12px; border:1px solid var(--border); background:#fff; cursor:pointer; color:#0f1b2d; font-weight:700}

/* TABLE */
.table-card{margin-top:18px; border-radius:16px; overflow:hidden; background:#fff; border:1px solid var(--border); box-shadow:0 10px 24px rgba(10,59,142,.06)}
.table-wrap{overflow:auto}
table{width:100%; border-collapse:separate; border-spacing:0; table-layout:fixed;} /* fixed to keep cell alignment */
thead th{background:linear-gradient(180deg,#0a3b8e 0%, #0f56c6 100%); color:#fff; text-align:left; font-weight:700; padding:14px 16px; font-size:14px}
tbody td{padding:14px 16px; border-top:1px solid var(--border); font-size:14px; color:#11223b; background:#fff; word-wrap:break-word}
tbody tr:hover td{background:#f9fbff}

/* Status badges: Voted green, Unvoted red */
.badge{display:inline-block; padding:6px 10px; font-weight:700; border-radius:999px; font-size:12px}
.badge-voted{background:#e9f8ef; color:#0f6d2c; border:1px solid #b9ebc8}
.badge-unvoted{background:#ffe9e9; color:#962020; border:1px solid #ffc5c5}

/* Hide footer (as requested) */
.footer{display:none}
</style>
</head>
<body>
<div class="container">

  <!-- FILTER BAR -->
  <div class="card">
    <div class="filters">
      <!-- Row 1 -->
      <div class="field">
        <label class="label">Department</label>
        <select id="fDepartment" class="control">
          <option value="">All</option>
          <?php foreach(array_keys($deptCourses) as $deptKey): ?>
            <option value="<?= htmlspecialchars(strtolower($deptKey)) ?>"><?= htmlspecialchars($deptKey) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label class="label">Course</label>
        <select id="fCourse" class="control" disabled>
          <option value="">All</option>
        </select>
      </div>
      <div class="field">
        <label class="label">Campus</label>
        <select id="fCampus" class="control">
          <option value="">All</option>
          <option value="au main">AU Main</option>
          <option value="au san jose">AU San Jose</option>
        </select>
      </div>
      <div class="field">
        <label class="label">Status</label>
        <select id="fStatus" class="control">
          <option value="">All</option>
          <option value="Voted">Voted</option>
          <option value="Unvoted">Unvoted</option>
        </select>
      </div>

      <!-- Row 2 -->
      <div class="search">
        <label class="label sr-only">Search</label>
        <i class="fa-solid fa-magnifying-glass icon"></i>
        <input id="q" class="control" placeholder="Search name, ID, course, department, campus, year, status…" />
      </div>
      <div class="actions">
        <button id="reset" class="btn"><i class="fa-solid fa-rotate"></i> Reset</button>
      </div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-card">
    <div class="table-wrap">
      <table id="votersTable">
        <thead>
          <tr>
            <th style="width:120px">ID</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Year</th>
            <th>Course</th>
            <th>Department</th>
            <th>Campus</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
<?php
$autoId = 0; $printed = 0;
$e = fn($x) => htmlspecialchars((string)$x, ENT_QUOTES, 'UTF-8');

foreach ($rows as $r){
  $id = pick($r, $K_ID); if ($id === '') $id = (++$autoId);

  $ln = pick($r, $K_LN); $fn = pick($r, $K_FN);
  if ($ln==='' && $fn===''){
    $full = pick($r, $K_FULL);
    if ($full!==''){ $parts = preg_split('/\s+/', trim($full)); $fn = array_shift($parts); $ln = count($parts)?implode(' ',$parts):''; }
  }

  $depCode    = pick($r, $K_DEPT);
  $courseRaw  = pick($r, $K_COURSE);
  $courseAcr  = course_to_acronym($courseRaw, $COURSE_ACRO);
  $courseFull = array_search($courseAcr, $COURSE_ACRO, true);
  if ($courseFull === false) $courseFull = $courseRaw;

  $yr   = pick($r, $K_YEAR);
  $cmp  = pick($r, $K_CAMP);
  $st   = toStatusText(pick($r, $K_STAT));

  $badge = ($st==='Voted')
    ? "<span class='badge badge-voted'>Voted</span>"
    : "<span class='badge badge-unvoted'>Unvoted</span>";

  // data-* for filters (course uses FULL name)
  $ddep = strtolower(trim($depCode));
  $dcrs = strtolower(trim((string)$courseFull));
  $dcmp = strtolower(trim((string)$cmp));
  $dst  = strtolower($st);

  echo "<tr data-department='{$e($ddep)}' data-course='{$e($dcrs)}' data-campus='{$e($dcmp)}' data-status='{$e($dst)}'>";
  echo "<td>".$e($id)."</td>";
  echo "<td>".$e($ln)."</td>";
  echo "<td>".$e($fn)."</td>";
  echo "<td>".$e($yr)."</td>";
  echo "<td>".$e($courseAcr)."</td>";   // COURSE (acronym)
  echo "<td>".$e($depCode)."</td>";     // DEPARTMENT
  echo "<td>".$e($cmp)."</td>";         // CAMPUS
  echo "<td>{$badge}</td>";             // STATUS (badge) — always last col
  echo "</tr>";

  $printed++;
}
?>
        </tbody>
      </table>
    </div>
    <!-- footer removed -->
  </div>
</div>

<script>
(function(){
  const $ = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));

  const q = $('#q'), fDepartment = $('#fDepartment'), fCourse = $('#fCourse'),
        fCampus = $('#fCampus'), fStatus = $('#fStatus'), reset = $('#reset'),
        rows = $$('#votersTable tbody tr');

  const DEPT_COURSES = <?php
    $dcOut = [];
    foreach ($deptCourses as $dk => $list) {
      $dcOut[$dk] = array_values(array_filter($list, fn($v,$k)=>$k!=='__COLLEGE__', ARRAY_FILTER_USE_BOTH));
    }
    echo json_encode($dcOut, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  ?>;
  const ALL_COURSES = Array.from(new Set(Object.values(DEPT_COURSES).flat())).sort();

  // Seed campus (add from rows, AU Main/San Jose already present)
  (function seedCampus(){
    const existing = new Set(Array.from(fCampus.options).map(o => (o.value||'').trim().toLowerCase()));
    rows.forEach(tr=>{
      const v = (tr.getAttribute('data-campus')||'').trim();
      const key = v.toLowerCase();
      if (v && !existing.has(key)) {
        const o = document.createElement('option'); o.value = key; o.textContent = v;
        fCampus.appendChild(o); existing.add(key);
      }
    });
  })();

  function populateCourseOptions(){
    const dept = (fDepartment.value||'').toUpperCase();
    const keep = fCourse.value;
    fCourse.innerHTML = '<option value="">All</option>';

    if (!dept) { fCourse.disabled = true; return; }
    fCourse.disabled = false;

    const list = (dept === 'CAS') ? ALL_COURSES : (DEPT_COURSES[dept] || []);
    list.forEach(full=>{
      const opt = document.createElement('option');
      opt.value = full.toLowerCase(); // filter by FULL name
      opt.textContent = full;         // show FULL name
      fCourse.appendChild(opt);
    });

    if (keep && Array.from(fCourse.options).some(o=>o.value===keep)) fCourse.value = keep;
  }
  populateCourseOptions();
  fDepartment.addEventListener('change', populateCourseOptions);

  const norm = s => (s||'').toLowerCase().trim();

  function applyFilters(){
    const term = norm(q.value);
    const dep  = norm(fDepartment.value);
    const crs  = norm(fCourse.value);
    const cmp  = norm(fCampus.value);
    const st   = norm(fStatus.value);

    rows.forEach(tr => {
      const txt = tr.textContent.toLowerCase();
      const okTerm = !term || txt.includes(term);
      const okDep  = !dep || tr.dataset.department === dep;
      const okCrs  = !crs || tr.dataset.course === crs;
      const okCmp  = !cmp || tr.dataset.campus === cmp;
      const okSt   = !st  || tr.dataset.status === st;
      tr.style.display = (okTerm && okDep && okCrs && okCmp && okSt) ? '' : 'none';
    });
  }

  // respect ?view=voted|unvoted
  const params = new URLSearchParams(location.search);
  const view = (params.get('view')||'').toLowerCase();
  if (view === 'voted' || view === 'unvoted') fStatus.value = view[0].toUpperCase()+view.slice(1);

  [q, fDepartment, fCourse, fCampus, fStatus].forEach(el=>{
    el.addEventListener('input', applyFilters);
    el.addEventListener('change', applyFilters);
  });

  reset.addEventListener('click', ()=>{
    q.value=''; fDepartment.value=''; fCampus.value=''; fStatus.value='';
    populateCourseOptions(); fCourse.value='';
    applyFilters();
  });

  applyFilters();
})();
</script>
</body>
</html>
