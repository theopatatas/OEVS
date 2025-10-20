<?php
// voter_verification.php — dept↔course filters (like voters.php), course acronyms in table,
// status/department swapped, Reset beside Status
include('session.php');
include('dbcon.php');
if (file_exists(__DIR__ . '/header.php')) include(__DIR__ . '/header.php');

/* === Department → Courses (FULL names) === */
$deptCourses = [
  'CMA'  => [
    '__COLLEGE__' => 'College of Management and Accountancy',
    'Bachelor of Science in Accountancy',
    'Bachelor of Science in Business Administration',
    'Bachelor of Science in Management Accounting',
    'Bachelor of Science in Accounting Information System',
    'Bachelor of Science in Entrepreneurship',
    'Bachelor of Science in Hospitality Management',
    'Bachelor of Science in Tourism Management',
  ],
  'CELA' => [
    '__COLLEGE__' => 'College of Education and Liberal Arts',
    'Bachelor of Elementary Education',
    'Bachelor of Secondary Education',
    'Bachelor of Arts in Political Science',
  ],
  'CCJE' => [
    '__COLLEGE__' => 'College of Criminal Justice Education',
    'Bachelor of Science in Criminology',
  ],
  'COE'  => [
    '__COLLEGE__' => 'College of Engineering',
    'Bachelor of Science in Civil Engineering',
  ],
  'CAHS' => [
    '__COLLEGE__' => 'College of Allied Health Sciences',
    'Bachelor of Science in Nursing',
    'Bachelor of Science in Pharmacy',
    'Bachelor of Science in Midwifery',
  ],
  'CIT'  => [
    '__COLLEGE__' => 'College of Information Technology',
    'Bachelor of Science in Information Technology',
  ],
  'CAS'  => [
    '__COLLEGE__' => 'College of Arts and Sciences',
    // CAS = all courses (we’ll compute in JS)
  ],
];

/* === Course FULL → Acronym === */
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

/* === Helpers === */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function course_to_acro($v, $MAP){
  $v = trim((string)$v);
  if ($v==='') return '';
  if (isset($MAP[$v])) return $MAP[$v];
  if (in_array($v, $MAP, true)) return $v; // already an acronym
  return $v; // fallback
}

/* === Optional server-side narrowing via ?view= === */
$view = isset($_GET['view']) ? strtolower(trim($_GET['view'])) : 'all';
if (!in_array($view, ['all','verified','unverified'], true)) $view = 'all';

$where = '';
if ($view === 'verified') {
  $where = " WHERE COALESCE(Verified,'') LIKE 'Verified'";
} elseif ($view === 'unverified') {
  $where = " WHERE COALESCE(Verified,'') NOT LIKE 'Verified'";
}

$sql = "SELECT * FROM voters{$where} ORDER BY VoterID ASC";
$res = mysqli_query($conn, $sql);
if (!$res) { die('Query error: '.h(mysqli_error($conn))); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Voter Verification - Online Voting System</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
<style>
:root{
  --bg:#f4f6f8; --white:#fff; --ink:#0f1b2d; --muted:#6a7b91; --border:#e6ebf4;
  --brand:#0a3b8e; --brand-2:#0f56c6; --ring:#cfe1ff;
}
*{box-sizing:border-box}
html,body{margin:0; background:var(--bg); color:var(--ink); font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}
.container{max-width:1400px; margin:24px auto; padding:0 16px}

/* ===== FILTER CARD (matches voters.php) ===== */
.card{
  background:#fff; border:1px solid var(--border); border-radius:16px;
  box-shadow:0 10px 24px rgba(10,59,142,.06);
}
.filters{
  display:grid;
  grid-template-columns: repeat(12, 1fr);
  grid-template-rows: auto auto;
  gap:16px;
  padding:16px;
}
/* Row 1: Department · Course · Status + Reset (inline) */
.filters .field:nth-of-type(1){ grid-column: span 3; } /* Department */
.filters .field:nth-of-type(2){ grid-column: span 3; } /* Course     */
.filters .field:nth-of-type(3){ grid-column: span 3; } /* Status     */
.filters .inline-reset{ grid-column: span 3; display:flex; align-items:flex-end; justify-content:flex-end; }

/* Row 2: Search (wide) */
.filters .search{ grid-column: 1 / span 12; position:relative; }

.label{display:block; font-size:13px; font-weight:700; color:#2a3a52; margin:4px 0 8px;}
.sr-only{position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); border:0;}

.control{
  width:100%; padding:12px 14px; border:1px solid var(--border); border-radius:14px; background:#fff; outline:none;
}
.control:focus{border-color:var(--brand-2); box-shadow:0 0 0 3px var(--ring);}

/* Search with icon */
.search .control{ padding-left:42px; }
.search .icon{ position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#9aa8bc; font-size:14px; }

/* Buttons */
.btn{
  display:inline-flex; align-items:center; gap:8px;
  padding:11px 16px; border-radius:12px; border:1px solid var(--border);
  background:#fff; cursor:pointer; color:#0f1b2d; font-weight:700;
}
.btn:focus{ outline:0; box-shadow:0 0 0 3px var(--ring); }

/* ===== TABLE (same base as voters.php) ===== */
.table-card{margin-top:18px; border-radius:16px; overflow:hidden; background:#fff; border:1px solid var(--border); box-shadow:0 10px 24px rgba(10,59,142,.06)}
.table-wrap{overflow:auto}
table{width:100%; border-collapse:separate; border-spacing:0; table-layout:fixed;}
thead th{background:linear-gradient(180deg,#0a3b8e 0%, #0f56c6 100%); color:#fff; text-align:left; font-weight:700; padding:14px 16px; font-size:14px}
tbody td{padding:14px 16px; border-top:1px solid var(--border); font-size:14px; color:#11223b; background:#fff; word-wrap:break-word}
tbody tr:hover td{background:#f9fbff}

/* Status badges: green = Verified, red = Unverified */
.badge{display:inline-block; padding:6px 10px; font-weight:700; border-radius:999px; font-size:12px}
.badge-voted{background:#e9f8ef; color:#0f6d2c; border:1px solid #b9ebc8}      /* Verified */
.badge-unvoted{background:#ffe9e9; color:#962020; border:1px solid #ffc5c5}   /* Unverified */

/* Footer */
.footer{display:flex; justify-content:space-between; align-items:center; padding:10px 14px; color:#6a7b91; font-size:12px}
.pill{border:1px dashed var(--border); padding:6px 10px; border-radius:999px; background:#fff}

/* Responsive */
@media (max-width:1100px){
  .filters .field:nth-of-type(1),
  .filters .field:nth-of-type(2),
  .filters .field:nth-of-type(3),
  .filters .inline-reset{ grid-column: span 6; }
}
@media (max-width:640px){
  .filters{ grid-template-columns: repeat(6, 1fr); }
  .filters .field, .filters .inline-reset, .filters .search{ grid-column: span 6; }
}
</style>
</head>
<body>
<div class="container">

  <!-- FILTER BAR -->
  <div class="card">
    <div class="filters">
      <div class="field">
        <label class="label">Department</label>
        <select id="fDepartment" class="control">
          <option value="">All</option>
          <?php foreach(array_keys($deptCourses) as $deptKey): ?>
            <option value="<?= h(strtolower($deptKey)) ?>"><?= h($deptKey) ?></option>
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
        <label class="label">Status</label>
        <select id="fStatus" class="control">
          <option value="">All</option>
          <option value="verified">Verified</option>
          <option value="unverified">Unverified</option>
        </select>
      </div>
      <div class="inline-reset">
        <button id="reset" class="btn"><i class="fa-solid fa-rotate"></i> Reset</button>
      </div>

      <!-- Row 2 -->
      <div class="search">
        <label class="label sr-only">Search</label>
        <i class="fa-solid fa-magnifying-glass icon"></i>
        <input id="q" class="control" placeholder="Search first/last name, username, student ID…" />
      </div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-card">
    <div class="table-wrap">
      <table id="votersTable">
        <thead>
          <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Middle Name</th>
            <th>Username</th>
            <th>Student ID</th>
            <th>Course</th>       <!-- acronym shown -->
            <th>Status</th>       <!-- swapped before Department -->
            <th>Department</th>   <!-- after Status -->
            <th style="width:220px">Actions</th>
          </tr>
        </thead>
        <tbody id="tableBody">
<?php
$count = 0;
while ($row = mysqli_fetch_assoc($res)):
  $count++;
  $id   = $row['VoterID'] ?? '';
  $fn   = $row['FirstName'] ?? '';
  $ln   = $row['LastName'] ?? '';
  $mn   = $row['MiddleName'] ?? '';
  $un   = $row['Username'] ?? '';
  $sid  = $row['SchoolID'] ?? '';
  // Read raw course/department (rename here if your column names differ)
  $courseRaw = $row['Course'] ?? ($row['Program'] ?? '');
  $deptCode  = $row['Department'] ?? ($row['Dept'] ?? '');
  // Compute acronym for display, and full (best effort) for filtering
  $courseAcr = course_to_acro($courseRaw, $COURSE_ACRO);
  $courseFull = array_search($courseAcr, $COURSE_ACRO, true);
  if ($courseFull === false) $courseFull = $courseRaw;

  $isV  = strcasecmp(trim($row['Verified'] ?? ''), 'Verified') === 0;
  $statusKey = $isV ? 'verified' : 'unverified';
  $badge = $isV ? "<span class='badge badge-voted'>Verified</span>" : "<span class='badge badge-unvoted'>Unverified</span>";
?>
          <tr class="del<?= (int)$id ?>"
              data-status="<?= h($statusKey) ?>"
              data-course="<?= h(strtolower($courseFull)) ?>"
              data-department="<?= h(strtolower($deptCode)) ?>">
            <td><?= h($fn) ?></td>
            <td><?= h($ln) ?></td>
            <td><?= h($mn) ?></td>
            <td><?= h($un) ?></td>
            <td><?= h($sid) ?></td>
            <td><?= h($courseAcr) ?></td>   <!-- acronym -->
            <td><?= $badge ?></td>          <!-- Status -->
            <td><?= h($deptCode) ?></td>    <!-- Department -->
            <td>
              <?php if(!$isV): ?>
                <button class="btn verify-btn" data-id="<?= (int)$id ?>"><i class="fa fa-check"></i> Verify</button>
              <?php endif; ?>
              <a class="btn" style="border-color:#ffd1d6;background:#ffe9ec;color:#b61e1e" data-id="<?= (int)$id ?>" href="#" id="del-<?= (int)$id ?>"><i class="fa fa-trash"></i> Delete</a>
              <input type="hidden" name="data_name" class="data_name<?= (int)$id ?>" value="<?= h($fn.' '.$ln) ?>"/>
              <input type="hidden" name="user_name" class="user_name" value="<?= h($_SESSION['User_Type'] ?? '') ?>"/>
            </td>
          </tr>
<?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <div class="footer">
      <div><span class="pill" id="countLabel"><?= (int)$count ?></span> total rows</div>
      <div>Tip: use search + filters to narrow results.</div>
    </div>
  </div>
</div>

<input type="hidden" class="pc_date" name="pc_date"/>
<input type="hidden" class="pc_time" name="pc_time"/>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
(function(){
  const $ = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));

  const q = $('#q'),
        fDepartment = $('#fDepartment'),
        fCourse = $('#fCourse'),
        fStatus = $('#fStatus'),
        reset = $('#reset'),
        rows = $$('#votersTable tbody tr'),
        countLabel = $('#countLabel');

  // PHP -> JS: dept courses (exclude __COLLEGE__)
  const DEPT_COURSES = <?php
    $dcOut = [];
    foreach ($deptCourses as $dk => $list) {
      $dcOut[$dk] = array_values(array_filter($list, fn($v,$k)=>$k!=='__COLLEGE__', ARRAY_FILTER_USE_BOTH));
    }
    echo json_encode($dcOut, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  ?>;
  const ALL_COURSES = Array.from(new Set(Object.values(DEPT_COURSES).flat())).sort();

  // Department->Course binding (course uses FULL names)
  function populateCourse(){
    const dept = (fDepartment.value||'').toUpperCase();
    const prev = fCourse.value;
    fCourse.innerHTML = '<option value="">All</option>';
    if (!dept) { fCourse.disabled = true; return; }
    fCourse.disabled = false;
    const list = (dept === 'CAS') ? ALL_COURSES : (DEPT_COURSES[dept] || []);
    list.forEach(full=>{
      const o = document.createElement('option');
      o.value = full.toLowerCase();
      o.textContent = full;
      fCourse.appendChild(o);
    });
    if (prev && Array.from(fCourse.options).some(o=>o.value===prev)) fCourse.value = prev;
  }
  populateCourse();
  fDepartment.addEventListener('change', populateCourse);

  // Respect ?view=
  (function initStatusFromView(){
    const params = new URLSearchParams(location.search);
    const view = (params.get('view')||'').toLowerCase();
    if (view === 'verified' || view === 'unverified') fStatus.value = view;
  })();

  const norm = s => (s||'').toLowerCase().trim();

  function applyFilters(){
    const term = norm(q.value);
    const dep  = norm(fDepartment.value);
    const crs  = norm(fCourse.value);
    const st   = norm(fStatus.value);
    let shown = 0;
    rows.forEach(tr=>{
      const txt = tr.textContent.toLowerCase();
      const okTerm = !term || txt.includes(term);
      const okDep  = !dep || tr.dataset.department === dep;
      const okCrs  = !crs || tr.dataset.course === crs;    // compares to FULL name stored in data-course
      const okSt   = !st  || tr.dataset.status === st;
      const show = okTerm && okDep && okCrs && okSt;
      tr.style.display = show ? '' : 'none';
      if (show) shown++;
    });
    if (countLabel) countLabel.textContent = shown;
  }

  [q, fDepartment, fCourse, fStatus].forEach(el=>{
    el.addEventListener('input', applyFilters);
    el.addEventListener('change', applyFilters);
  });

  reset.addEventListener('click', ()=>{
    q.value=''; fDepartment.value=''; fCourse.innerHTML='<option value="">All</option>'; fCourse.disabled=true; fStatus.value='';
    applyFilters();
  });

  applyFilters();

  // Verify action (AJAX)
  $('.table-card').on('click', '.verify-btn', function(e){
    e.preventDefault();
    const id = $(this).data('id');
    if(!confirm("Verify this voter?")) return;
    $.post('verify_voter.php', { id }, (res)=>{
      if(String(res).trim()==='success'){
        const $row = $(this).closest('tr');
        // Status is column 7 now (after Course)
        $row.find('td:nth-child(7)').html('<span class="badge badge-voted">Verified</span>');
        $row.attr('data-status','verified');
        $(this).remove();
        applyFilters();
      }else{
        alert('Verification failed. Try again.');
      }
    });
  });

  // Delete action (AJAX)
  $('.table-card').on('click', 'a[id^="del-"]', function(e){
    e.preventDefault();
    const id = $(this).data('id');
    if(!confirm("Delete this voter?")) return;
    $.post('delete_voter.php', {
      id,
      pc_time: $('.pc_time').val(),
      pc_date: $('.pc_date').val(),
      data_name: $('.data_name'+id).val(),
      user_name: $('.user_name').val()
    }, function(){
      $('.del'+id).fadeOut('fast', function(){ $(this).remove(); applyFilters(); });
    });
  });

  // Timestamp helpers
  const d = new Date();
  document.querySelector('.pc_date').value = `${d.getMonth()+1}/${d.getDate()}/${d.getFullYear()}`;
  document.querySelector('.pc_time').value = `${d.getHours()}:${d.getMinutes()}:${d.getSeconds()}`;
})();
</script>
</body>
</html>
