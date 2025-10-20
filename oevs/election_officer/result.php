<?php
include('session.php');
include('dbcon.php');

if (!function_exists('h')) { function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
function int0($v){ return is_numeric($v)?(int)$v:0; }

/* ---------- Inputs ---------- */
$department = isset($_GET['department']) ? trim($_GET['department']) : 'All';
$course     = isset($_GET['course'])     ? trim($_GET['course'])     : 'All';
$position   = isset($_GET['position'])   ? trim($_GET['position'])   : 'All';
$campus     = isset($_GET['campus'])     ? trim($_GET['campus'])     : 'All';
$q          = isset($_GET['q'])          ? trim($_GET['q'])          : '';

/* ---------- Canonical position order ---------- */
$POS_ORDER = [
  'President','Vice-President','Governor','Vice-Governor',
  'Secretary','Treasurer','Representative','Social-Media Officer'
];

/* ---------- Lists + Dept->Course map ---------- */
$depts   = ['All'];
$courses = ['All'];
$campuses = ['All','Au Main','Au South','Au San Jose'];
$deptCourseMap = [];

/* Distinct Departments */
$qd = mysqli_query($conn, "SELECT DISTINCT Department d
                           FROM candidate
                           WHERE Department IS NOT NULL AND TRIM(Department)<>'' 
                           ORDER BY Department");
while ($qd && ($r=mysqli_fetch_assoc($qd))) $depts[] = $r['d'];

/* Courses overall + per dept */
$dc = mysqli_query($conn, "SELECT DISTINCT Department d, Course c
                           FROM candidate
                           WHERE Department IS NOT NULL AND TRIM(Department)<>''
                             AND Course IS NOT NULL AND TRIM(Course)<>''
                           ORDER BY Department, Course");
while ($dc && ($r=mysqli_fetch_assoc($dc))) {
  $deptCourseMap[$r['d']][$r['c']] = true;
}
if ($department === 'All') {
  $qc = mysqli_query($conn, "SELECT DISTINCT Course c FROM candidate WHERE Course IS NOT NULL AND TRIM(Course)<>'' ORDER BY Course");
  while ($qc && ($r=mysqli_fetch_assoc($qc))) $courses[] = $r['c'];
} elseif (!empty($deptCourseMap[$department])) {
  $courses = array_merge(['All'], array_keys($deptCourseMap[$department]));
}

/* ---------- Base WHERE for global filters (NOT including Position here) ---------- */
$where=[]; $params=[]; $types='';
if ($department!=='All'){ $where[]="c.Department=?"; $params[]=$department; $types.='s'; }
if ($course!=='All'){     $where[]="c.Course=?";     $params[]=$course;     $types.='s'; }
if ($campus!=='All'){     $where[]="COALESCE(c.Campus,'')=?"; $params[]=$campus; $types.='s'; }

$baseSql = "FROM candidate c";
if ($where) $baseSql .= " WHERE ".implode(' AND ',$where);

/* ---------- Department list to render ---------- */
$deptRender = [];
if ($department==='All') {
  $sqlD = "SELECT DISTINCT c.Department d $baseSql";
  $std = mysqli_prepare($conn,$sqlD);
  if ($std){ if ($types!=='') mysqli_stmt_bind_param($std,$types,...$params);
    mysqli_stmt_execute($std); $rs = mysqli_stmt_get_result($std);
    while ($rs && ($r=mysqli_fetch_assoc($rs))) if ($r['d']!=='') $deptRender[]=$r['d'];
    mysqli_stmt_close($std);
  }
} else {
  $deptRender = [$department];
}

/* ---------- Titles ---------- */
$deptTitle = ($department==='All') ? 'All Departments' : $department;
$campTitle = ($campus==='All') ? 'All Campuses' : $campus;

/* ---------- Position sections to show (THIS is the change) ---------- */
$posSectionList = ($position==='All') ? $POS_ORDER : [$position];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Election Results</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root{ --primary:#002f6c; --accent:#0056b3; --bg:#f4f6f8; --white:#fff; --shadow:0 4px 12px rgba(0,0,0,.08);
           --font:'Inter',sans-serif; --muted:#39557a; --radius:14px; }
    *{box-sizing:border-box}
    body{font-family:var(--font); background:var(--bg); margin:0; color:#23374d}
    .container{max-width:1400px; margin:16px auto; padding:0 20px}

    .card{background:#fff; border:1px solid #e8eef7; box-shadow:var(--shadow); border-radius:var(--radius); padding:12px}

    /* Filter bar */
    .filters .row{display:grid; grid-template-columns: repeat(4, minmax(220px,1fr)) auto; gap:10px; align-items:end}
    .field{display:flex; flex-direction:column; gap:6px}
    .field label{font-size:12px; color:var(--muted); font-weight:600}
    select{appearance:none; padding:12px 14px; border-radius:12px; border:1px solid #d8e2f0; background:#fff; color:#0d2f66; outline:none}
    .filters .right{display:flex; gap:8px}
    .btn{display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border-radius:12px; border:1px solid #d8e2f0; background:#fff; color:#0d2f66; text-decoration:none; cursor:pointer}
    .btn:hover{background:#f6f9ff}

    /* Controls */
    .controls{display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:10px}
    .controls .search{position:relative; flex:1 1 520px; min-width:260px}
    .controls .search input{width:100%; padding:12px 40px; border-radius:12px; border:1px solid #d8e2f0; background:#fff; color:#0d2f66}
    .controls .search .icon-left{position:absolute; left:12px; top:50%; transform:translateY(-50%); opacity:.7}
    .controls .search .clear{position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer; opacity:.55; display:none}

    /* Title band */
    .titleband{margin-top:14px; padding:14px 16px; background:#fff; border:1px solid #e8eef7; border-radius:12px; box-shadow:var(--shadow)}
    .titleband h1{margin:0; font-size:18px; color:#0b2b6a; font-weight:800}
    .titleband .sub{margin-top:4px; color:#4b5b70; font-size:13px}

    /* Department block */
    .dept-section{background:#fff; border:1px solid #e8eef7; box-shadow:var(--shadow); border-radius:var(--radius); padding:18px; margin-top:14px}
    .dept-title{margin:0 0 6px; font-size:20px; color:#0b2b6a; text-align:left}
    .dept-caption{margin:0 0 12px; color:#4b5b70; font-size:13px}

    /* Position sub-section */
    .pos-title{margin:8px 0 10px; font-size:16px; color:#0b2b6a; font-weight:800; border-left:4px solid #c7dbff; padding-left:8px}

    /* Candidate cards */
    .grid{display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:16px}
    .card-cand{
      border:1px solid #e5e7eb; border-radius:12px; padding:14px; background:#fff;
      display:flex; flex-direction:column; align-items:center; gap:8px; min-height:220px
    }
    .avatar{width:72px; height:72px; border-radius:50%; object-fit:cover; border:2px solid #000; background:#f3f4f6}
    .name{font-size:15px; font-weight:800; text-align:center}
    .meta{font-size:12px; color:#4b5b70; text-align:center}
    .badge-votes{
      margin-top:6px; font-weight:800; font-size:13px; color:#134e4a;
      background:#ecfdf5; border:1px solid #a7f3d0; border-radius:999px; padding:6px 10px
    }

    @media (max-width:1100px){ .filters .row{grid-template-columns:repeat(3,minmax(220px,1fr)) auto} }
    @media (max-width:760px){ .filters .row{grid-template-columns:1fr} }
  </style>
</head>
<body>

  <?php $activePage='results'; include 'header.php'; ?>

  <div class="container">

    <!-- FILTER BAR -->
    <form id="filtersForm" class="card filters" method="GET" action="<?php echo h($_SERVER['PHP_SELF']); ?>">
      <div class="row">
        <div class="field">
          <label for="department">Department</label>
          <select id="department" name="department">
            <?php foreach ($depts as $d): ?>
              <option value="<?php echo h($d); ?>" <?php echo ($d===$department)?'selected':''; ?>><?php echo h($d); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="course">Course</label>
          <select id="course" name="course">
            <?php foreach ($courses as $c): ?>
              <option value="<?php echo h($c); ?>" <?php echo ($c===$course)?'selected':''; ?>><?php echo h($c); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="position">Position</label>
          <select id="position" name="position">
            <option value="All" <?php echo $position==='All'?'selected':''; ?>>All</option>
            <?php foreach ($POS_ORDER as $p): ?>
              <option value="<?php echo h($p); ?>" <?php echo ($position===$p)?'selected':''; ?>><?php echo h($p); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="campus">Campus</label>
          <select id="campus" name="campus">
            <?php foreach ($campuses as $cm): ?>
              <option value="<?php echo h($cm); ?>" <?php echo ($cm===$campus)?'selected':''; ?>><?php echo $cm==='All'?'All':h($cm); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="right">
          <a class="btn" href="<?php echo h($_SERVER['PHP_SELF']); ?>"><i class="fa fa-rotate-left"></i> Reset</a>
        </div>
      </div>

      <!-- CONTROLS -->
      <div class="controls">
        <div class="search">
          <i class="fa fa-search icon-left"></i>
          <input id="liveSearch" name="q" type="text" value="<?php echo h($q); ?>" placeholder="Search name, party, year, course, department, campus…">
          <i id="clearSearch" class="fa fa-xmark clear" title="Clear"></i>
        </div>
      </div>
    </form>

    <!-- Title -->
    <div class="titleband">
      <h1>Candidates — <?php echo h($deptTitle); ?> · <?php echo h($campTitle); ?></h1>
      <div class="sub">
        <?php
          $bits=[];
          if ($course!=='All')   $bits[]="Course: ".h($course);
          if ($position!=='All') $bits[]="Position: ".h($position);
          echo $bits?implode(' | ',$bits):'All courses • All positions';
        ?>
      </div>
    </div>

    <?php if (!$deptRender): ?>
      <div class="dept-section"><div class="dept-caption">No results match your filters.</div></div>
    <?php endif; ?>

    <?php
    /* ===== RENDER BY DEPARTMENT, then by SELECTED POSITION(S) ===== */
    foreach ($deptRender as $deptVal): ?>
      <section class="dept-section">
        <h2 class="dept-title"><?php echo h($deptVal); ?></h2>
        <p class="dept-caption">
          <?php
            $bits=[];
            $bits[] = ($campus==='All') ? 'All Campuses' : ('Campus: '.h($campus));
            if ($course!=='All') $bits[] = 'Course: '.h($course);
            echo implode(' • ', $bits);
          ?>
        </p>

        <?php foreach ($posSectionList as $posName): ?>
          <h3 class="pos-title"><?php echo h($posName); ?></h3>
          <div class="grid">
            <?php
              // Build WHERE per section: Department + this Position + (Course/Campus)
              $w2 = ["c.Department=?","c.Position=?"]; $p2 = [$deptVal,$posName]; $t2='ss';
              if ($course!=='All'){ $w2[]="c.Course=?"; $p2[]=$course; $t2.='s'; }
              if ($campus!=='All'){ $w2[]="COALESCE(c.Campus,'')=?"; $p2[]=$campus; $t2.='s'; }

              $sql = "SELECT c.CandidateID, c.FirstName, c.LastName, c.Year, c.Photo, c.Party,
                             c.Course, COALESCE(c.Campus,'') AS Campus,
                             COALESCE(vv.cnt,0) AS vote_count
                      FROM candidate c
                      LEFT JOIN (SELECT CandidateID, COUNT(*) cnt FROM votes GROUP BY CandidateID) vv
                             ON vv.CandidateID = c.CandidateID
                      WHERE ".implode(' AND ',$w2)."
                      ORDER BY vote_count DESC, c.LastName ASC, c.FirstName ASC";
              $list=[]; $st2 = mysqli_prepare($conn,$sql);
              if ($st2){
                mysqli_stmt_bind_param($st2,$t2,...$p2);
                mysqli_stmt_execute($st2);
                $rs = mysqli_stmt_get_result($st2);
                while ($rs && ($row=mysqli_fetch_assoc($rs))) $list[]=$row;
                mysqli_stmt_close($st2);
              }

              if (!$list){
                echo '<div class="card-cand" style="opacity:.7"><div class="meta">No candidates for this position.</div></div>';
              } else {
                foreach ($list as $c):
                  $votes = int0($c['vote_count']);
                  $name = trim(($c['FirstName']??'').' '.($c['LastName']??''));
                  $photoPath = $c['Photo'] ?? '';
                  $photo = (is_string($photoPath) && $photoPath!=='' && file_exists($photoPath)) ? $photoPath : 'images/default-avatar.png';
                  $meta = trim(($c['Year']?:'').' • '.($c['Course']?:'').' • '.($c['Campus']?:'')); ?>
                <div class="card-cand row" data-name="<?php echo h(strtolower($name.' '.$meta.' '.$c['Party'])); ?>">
                  <img class="avatar" src="<?php echo h($photo); ?>" alt="<?php echo h($name); ?>">
                  <div class="name"><?php echo h($name); ?></div>
                  <div class="meta"><?php echo h($meta); ?></div>
                  <?php if (!empty($c['Party'])): ?>
                    <div class="meta"><strong>Party:</strong> <?php echo h($c['Party']); ?></div>
                  <?php endif; ?>
                  <div class="badge-votes"><?php echo $votes; ?> votes</div>
                </div>
                <?php endforeach;
              }
            ?>
          </div>
        <?php endforeach; ?>
      </section>
    <?php endforeach; ?>

    <footer style="text-align:center; padding:20px 0; color:#666; font-size:14px;">© 2025 Online Election Voting System</footer>
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
    $qc2 = mysqli_query($conn,"SELECT DISTINCT Course c FROM candidate WHERE Course IS NOT NULL AND TRIM(Course)<>'' ORDER BY Course");
    while ($qc2 && ($r=mysqli_fetch_assoc($qc2))) $all[]=$r['c'];
    echo json_encode($all, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  ?>;

  function rebuildCourses(dept, keep){
    const current = keep ? selCourse.value : 'All';
    const options = ['All'].concat(dept==='All' ? allCourses : (map[dept] || []));
    selCourse.innerHTML = '';
    options.forEach(v=>{
      const o = document.createElement('option');
      o.value = v; o.textContent = v;
      if (v===current) o.selected = true;
      selCourse.appendChild(o);
    });
  }

  selDept.addEventListener('change', ()=>{ rebuildCourses(selDept.value,false); form.submit(); });
  selCourse.addEventListener('change', ()=> form.submit());
  selPos.addEventListener('change', ()=> form.submit());
  selCampus.addEventListener('change', ()=> form.submit());

  rebuildCourses(selDept.value, true);
})();

/* --- Live search on cards --- */
(function(){
  const input = document.getElementById('liveSearch');
  const clear = document.getElementById('clearSearch');
  const allCards = Array.from(document.querySelectorAll('.card-cand'));
  if(!input) return;
  function run(){
    const term = (input.value || '').trim().toLowerCase();
    allCards.forEach(card=>{
      const hay = (card.getAttribute('data-name') || '').toLowerCase();
      card.style.display = !term || hay.includes(term) ? '' : 'none';
    });
    if (clear) clear.style.display = input.value ? 'block' : 'none';
  }
  input.addEventListener('input', run);
  if (clear) clear.addEventListener('click', ()=>{ input.value=''; run(); });
  if (input.value) run();
})();
</script>
</body>
</html>
