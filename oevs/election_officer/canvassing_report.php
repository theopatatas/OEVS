<?php
include('session.php');
include('header.php');
include('dbcon.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

/* ==== Department → Courses mapping (colleges) ==== */
$DEPT_COURSES = [
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
    'Bachelor of Science in Elementary Education',
    'Bachelor of Science in Secondary Education',
    'Bachelor of Science in Political Science',
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
    // when CAS is picked, we’ll show all courses (dynamic)
  ],
];

/* Course FULL → Acronym map */
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
  'Bachelor of Science in Midwifery'=>'BSM',
  'Bachelor of Science in Information Technology'=>'BSIT',
];

$to_acro = function($full) use ($COURSE_ACRO){
  $full = trim((string)$full);
  if ($full === '') return '';
  if (isset($COURSE_ACRO[$full])) return $COURSE_ACRO[$full];
  if (in_array($full, $COURSE_ACRO, true)) return $full;
  return $full;
};

/* Custom position order */
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

/* Excel export id */
$id_excel = '';
if ($r = mysqli_query($conn, "SELECT CandidateID FROM candidate ORDER BY CandidateID ASC LIMIT 1")) {
  $row = mysqli_fetch_assoc($r);
  $id_excel = $row['CandidateID'] ?? '';
}

/* Distinct values for filters (from DB so we don’t miss anything) */
$positions = $courses = $departments = $campuses = [];

$qPos = mysqli_query($conn, "SELECT DISTINCT Position FROM candidate WHERE Position<>'' ORDER BY Position");
if ($qPos) while ($x = mysqli_fetch_assoc($qPos)) $positions[]   = $x['Position'];

$qCourse = mysqli_query($conn, "SELECT DISTINCT Course FROM candidate WHERE Course<>'' ORDER BY Course");
if ($qCourse) while ($x = mysqli_fetch_assoc($qCourse)) $courses[] = $x['Course'];

$qDept = mysqli_query($conn, "SELECT DISTINCT Department FROM candidate WHERE Department<>'' ORDER BY Department");
if ($qDept) while ($x = mysqli_fetch_assoc($qDept)) $departments[] = $x['Department'];

$qCampus = mysqli_query($conn, "SELECT DISTINCT Campus FROM candidate WHERE Campus<>'' ORDER BY Campus");
if ($qCampus) while ($x = mysqli_fetch_assoc($qCampus)) $campuses[]  = $x['Campus'];

/* Sort the $positions dropdown using the custom order first, then the rest */
$posIndex = array_flip($POSITION_ORDER);
usort($positions, function($a,$b) use($posIndex){
  $ia = $posIndex[$a] ?? 999; $ib = $posIndex[$b] ?? 999;
  if ($ia === $ib) return strcmp($a,$b);
  return $ia <=> $ib;
});

/* ORDER BY CASE for positions per custom order */
$caseParts = [];
foreach ($POSITION_ORDER as $i => $label) {
  $safe = mysqli_real_escape_string($conn, $label);
  $caseParts[] = "WHEN '$safe' THEN ".($i+1);
}
$posOrderCase = "CASE c.Position ".implode(' ', $caseParts)." ELSE 999 END";

/* Table data (LEFT JOIN for vote totals + custom position order) */
$sql = "
  SELECT c.CandidateID, c.Position, c.FirstName, c.LastName, c.MiddleName,
         c.Gender, c.Year, c.Photo, c.Campus, c.Course, c.Department,
         COUNT(v.CandidateID) AS Votes
  FROM candidate c
  LEFT JOIN votes v ON v.CandidateID = c.CandidateID
  GROUP BY c.CandidateID, c.Position, c.FirstName, c.LastName, c.MiddleName,
           c.Gender, c.Year, c.Photo, c.Campus, c.Course, c.Department
  ORDER BY $posOrderCase ASC, c.LastName ASC, c.FirstName ASC
";
$data = mysqli_query($conn, $sql);

/* Photo URL normalizer (fallback + relative support) */
function photo_url($raw){
  $raw = trim((string)$raw);
  if ($raw === '') return 'assets/img/avatar-placeholder.png';
  if (preg_match('~^(https?://|/)~', $raw)) return $raw;
  $base = 'uploads/candidates/';               // adjust if different
  $fs   = __DIR__ . '/' . $base . $raw;
  if (is_file($fs)) return $base . $raw;
  return $raw;
}
?>

<!-- Kill leftover modal/offcanvas markup that header.php might add -->
<style>
  .modal, #logoutModal, .logout-modal, #aboutModal, .about-modal, #profileModal, .profile-modal,
  .offcanvas, .offcanvas-backdrop { display:none!important; visibility:hidden!important; }
  .wrapper, .home_body { margin:0; padding:0; }
</style>

<div class="wrapper">
  <div class="home_body">
    <section style="margin-top:20px;">
      <style>
        :root{ --ink:#0f1b2d; --muted:#6a7b91; --border:#e6ebf4; --bg:#f5f7fb; --white:#fff; --brand:#0a3b8e; --brand2:#0f56c6; --ring:#cfe1ff; }
        *{box-sizing:border-box} body{background:#f5f7fb}
        .page{max-width:1360px;margin:0 auto;padding:0 16px}
        .page-title{display:none!important}
        .card{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:0 10px 24px rgba(15,27,45,.06)}

        /* Filter bar */
        .toolbar{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:12px;padding:16px;border-radius:16px}
        .field{display:flex;flex-direction:column}
        .label{font-size:14px;font-weight:700;color:#14366b;margin:4px 8px 8px}
        .control{height:50px;background:#fff;border:1px solid #d9e3f4;border-radius:14px;padding:0 16px;font:inherit;outline:none;color:#173b77}
        .control:focus{border-color:#b8cdf8;box-shadow:0 0 0 3px rgba(74,117,255,.15)}
        .search{position:relative}
        .search i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#7a8fb0}
        .search .control{padding-left:42px}

        .btn{height:52px;display:inline-flex;align-items:center;gap:10px;border:1px solid #d9e3f4;background:#eaf2ff;color:#123a74;border-radius:16px;padding:0 22px;font-weight:700;cursor:pointer;line-height:1}
        .btn:hover{background:#dfeaff}
        .btn-primary{background:#eaf2ff;border-color:#cfe1ff;color:#123a74}
        .btn-icon{width:52px;justify-content:center;padding:0}
        .btn-group{display:flex;gap:8px;align-items:center} /* snug to search */

        /* Row layout */
        .dept-field,.course-field,.pos-field,.campus-field{grid-column:span 3}
        .reset-field{grid-column:span 12; display:flex; justify-content:flex-end}
        .search-field{grid-column:1 / span 8}
        .actions-field{grid-column:9 / span 4; display:flex; justify-content:flex-end; align-items:end}

        @media (min-width:981px){
          .reset-field{grid-column:12 / span 1}
          .dept-field{grid-column:1 / span 3}
          .course-field{grid-column:4 / span 3}
          .pos-field{grid-column:7 / span 3}
          .campus-field{grid-column:10 / span 2}
        }
        @media (max-width:980px){
          .dept-field,.course-field,.pos-field,.campus-field{grid-column:span 6}
          .reset-field{grid-column:span 6}
          .search-field{grid-column:span 6}
          .actions-field{grid-column:span 6}
        }

        /* Table styling */
        .table-card{margin-top:16px}
        .table-wrap{overflow:auto}
        table{width:100%;border-collapse:separate;border-spacing:0;table-layout:fixed}
        thead th{background:linear-gradient(180deg,#0a3b8e 0%,#0f56c6 100%);color:#fff;padding:12px 14px;text-align:left;font-size:14px}
        tbody td{background:#fff;border-top:1px solid var(--border);padding:12px 14px;color:#11223b;font-size:14px;vertical-align:middle}
        tbody tr:hover td{background:#f9fbff}
        .photo{width:56px;height:56px;object-fit:cover;border-radius:10px;border:1px solid #e6ebf4;background:#f2f6ff}
        .votes{font-weight:800;text-align:center}
        .empty{padding:22px;color:#6a7b91;text-align:center}
        .footer{display:flex;justify-content:space-between;align-items:center;padding:10px 14px;color:#6a7b91;font-size:12px}
        .pill{border:1px dashed var(--border);padding:6px 10px;border-radius:999px;background:#fff}
      </style>

      <div class="page">
        <h1 class="page-title">Election Report</h1>

        <!-- FILTERS -->
        <div class="card">
          <div class="toolbar">
            <!-- Row 1 -->
            <div class="field dept-field">
              <label class="label">Department</label>
              <select id="fDept" class="control">
                <option value="">All</option>
                <?php foreach ($departments as $d): ?>
                  <option value="<?= h($d) ?>"><?= h($d) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="field course-field">
              <label class="label">Course</label>
              <select id="fCourse" class="control">
                <option value="">All</option>
                <?php foreach ($courses as $c): ?>
                  <option value="<?= h($c) ?>"><?= h($c) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="field pos-field">
              <label class="label">Position</label>
              <select id="fPosition" class="control">
                <option value="">All</option>
                <?php foreach ($positions as $p): ?>
                  <option value="<?= h($p) ?>"><?= h($p) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="field campus-field">
              <label class="label">Campus</label>
              <select id="fCampus" class="control">
                <option value="">All</option>
                <?php foreach ($campuses as $c): ?>
                  <option value="<?= h($c) ?>"><?= h($c) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="field reset-field">
              <button id="reset" class="btn btn-icon" title="Reset">
                <i class="fa fa-rotate"></i>
              </button>
            </div>

            <!-- Row 2 -->
            <div class="field search-field search">
              <label class="label" style="position:absolute;left:-9999px">Search</label>
              <i class="fa fa-search"></i>
              <input id="q" class="control" placeholder="Search name, party, position, course, department, year…" />
            </div>

            <div class="field actions-field">
              <div class="btn-group">
                <button type="button" class="btn" onclick="window.print()">
                  <i class="fa fa-print"></i> Print
                </button>
                <form method="POST" action="canvassing_excel.php">
                  <input type="hidden" name="id_excel" value="<?= h($id_excel) ?>">
                  <button class="btn btn-primary" type="submit">
                    <i class="fa fa-file-excel"></i> Export Excel
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- TABLE -->
        <div class="card table-card">
          <div class="table-wrap">
            <table id="candTable">
              <thead>
                <tr>
                  <th style="width:160px">Position</th>
                  <th style="width:110px">Course</th>
                  <th style="width:140px">Department</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th style="width:90px">Year</th>
                  <th style="width:100px">Campus</th>
                  <th style="width:84px">Photo</th>
                  <th style="width:120px">Votes</th>
                </tr>
              </thead>
              <tbody id="tableBody">
                <?php if ($data && mysqli_num_rows($data) > 0): $count=0; ?>
                  <?php while ($row = mysqli_fetch_assoc($data)): $count++;
                        $courseFull = $row['Course'] ?? '';
                        $courseAcr  = $to_acro($courseFull);
                        $photoUrl   = photo_url($row['Photo'] ?? '');
                  ?>
                    <tr
                      data-position="<?= h(strtolower($row['Position'])) ?>"
                      data-course="<?= h(strtolower($courseFull)) ?>"
                      data-dept="<?= h(strtolower($row['Department'])) ?>"
                      data-campus="<?= h(strtolower($row['Campus'])) ?>"
                    >
                      <td><?= h($row['Position']) ?></td>
                      <td><?= h($courseAcr) ?></td>
                      <td><?= h($row['Department']) ?></td>
                      <td><?= h($row['FirstName']) ?></td>
                      <td><?= h($row['LastName']) ?></td>
                      <td><?= h($row['Year']) ?></td>
                      <td><?= h($row['Campus']) ?></td>
                      <td><img class="photo" src="<?= h($photoUrl) ?>" alt="<?= h(($row['FirstName'] ?? '').' '.($row['LastName'] ?? '')) ?>"></td>
                      <td class="votes"><?= (int)$row['Votes'] ?></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="9" class="empty">No candidates found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <div class="footer">
            <div><span class="pill" id="countLabel"><?= isset($count)?(int)$count:0 ?></span> rows</div>
            <div>Tip: combine Position, Course, Department, and Campus to drill down.</div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<input type="hidden" class="pc_date" name="pc_date"/>
<input type="hidden" class="pc_time" name="pc_time"/>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
(function(){
  document.title = 'Election Report - Online Voting System';

  const $ = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));

  const q        = $('#q'),
        fPosition= $('#fPosition'),
        fCourse  = $('#fCourse'),
        fDept    = $('#fDept'),
        fCampus  = $('#fCampus'),
        reset    = $('#reset'),
        rows     = $$('#tableBody tr'),
        countLbl = $('#countLabel');

  const norm = s => (s||'').toLowerCase().trim();

  /* Department → Courses mapping to JS */
  const DEPT_COURSES = <?=
    json_encode($DEPT_COURSES, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
  ?>;

  // Build a full unique course list (used when Dept = CAS or All)
  const ALL_COURSES = Array.from(new Set(
    Object.values(DEPT_COURSES).flatMap(arr => arr.filter(v => v !== '__COLLEGE__'))
  ));

  function rebuildCourseOptions(deptVal){
    const sel = fCourse;
    if (!sel) return;
    const current = sel.value;

    let list = [];
    if (!deptVal || deptVal === 'CAS') {
      list = ALL_COURSES;
    } else if (DEPT_COURSES[deptVal]) {
      list = DEPT_COURSES[deptVal].filter(v => v !== '__COLLEGE__');
    }

    sel.innerHTML = ''; // reset
    const optAll = document.createElement('option');
    optAll.value = '';
    optAll.textContent = 'All';
    sel.appendChild(optAll);

    list.forEach(c => {
      const o = document.createElement('option');
      o.value = c;
      o.textContent = c;
      sel.appendChild(o);
    });

    // keep previous value if still valid
    if (list.includes(current)) sel.value = current;
  }

  function applyFilters(){
    const term = norm(q.value);
    const pos  = norm(fPosition.value);
    const crs  = norm(fCourse.value);
    const dpt  = norm(fDept.value);
    const cam  = norm(fCampus.value);
    let shown = 0;

    rows.forEach(tr=>{
      if (!tr.dataset) return;
      const txt = tr.textContent.toLowerCase();
      const okTerm = !term || txt.includes(term);
      const okPos  = !pos  || tr.dataset.position === pos;
      const okCrs  = !crs  || tr.dataset.course === crs.toLowerCase();
      const okDpt  = !dpt  || tr.dataset.dept === dpt.toLowerCase();
      const okCam  = !cam  || tr.dataset.campus === cam.toLowerCase();
      const show   = okTerm && okPos && okCrs && okDpt && okCam;
      tr.style.display = show ? '' : 'none';
      if (show) shown++;
    });

    if (countLbl) countLbl.textContent = shown;
  }

  [q, fPosition, fCourse, fDept, fCampus].forEach(el=>{
    el && el.addEventListener('input', applyFilters);
    el && el.addEventListener('change', applyFilters);
  });

  // When department changes, rebuild the course list to only show its courses
  fDept && fDept.addEventListener('change', (e)=>{
    const deptVal = fDept.value;
    rebuildCourseOptions(deptVal);
    // clear course filter on dept change (optional comment this if not needed)
    fCourse.value = '';
    applyFilters();
  });

  // initial course population if a department is preselected
  rebuildCourseOptions(fDept ? fDept.value : '');

  reset && reset.addEventListener('click', ()=>{
    if (q) q.value='';
    if (fPosition) fPosition.value='';
    if (fDept) fDept.value='';
    rebuildCourseOptions(''); // resets to all
    if (fCourse) fCourse.value='';
    if (fCampus) fCampus.value='';
    applyFilters();
  });

  applyFilters();

  const d = new Date();
  const pd = document.querySelector('.pc_date'), pt = document.querySelector('.pc_time');
  if (pd) pd.value = `${d.getMonth()+1}/${d.getDate()}/${d.getFullYear()}`;
  if (pt) pt.value = `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}:${String(d.getSeconds()).padStart(2,'0')}`;
})();
</script>
