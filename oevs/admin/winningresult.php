<?php
include('session.php');
include('dbcon.php');

/* ---------- helpers ---------- */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function int0($v){ return is_numeric($v) ? (int)$v : 0; }

/* ---------- inputs ---------- */
$department = $_GET['department'] ?? 'All';
$course     = $_GET['course']     ?? 'All';
$campus     = $_GET['campus']     ?? 'All';
$position   = $_GET['position']   ?? 'All';
$q          = $_GET['q']          ?? '';

/* ---------- canonical order ---------- */
$POS_ORDER = [
  'President','Vice-President','Governor','Vice-Governor',
  'Secretary','Treasurer','Representative','Social-Media Officer'
];

/* ---------- filter lists ---------- */
$depts = ['All'];
$courses = ['All'];
$campuses = ['All','Au Main','Au South','Au San Jose'];
$deptCourseMap = [];

/* departments */
$qd = mysqli_query($conn,"SELECT DISTINCT Department d
                          FROM candidate
                          WHERE Department IS NOT NULL AND TRIM(Department)<>'' 
                          ORDER BY Department");
while ($qd && ($r=mysqli_fetch_assoc($qd))) $depts[] = $r['d'];

/* dept -> courses + global courses */
$dc = mysqli_query($conn,"SELECT DISTINCT Department d, Course c
                          FROM candidate
                          WHERE Department IS NOT NULL AND TRIM(Department)<>'' 
                            AND Course IS NOT NULL AND TRIM(Course)<>'' 
                          ORDER BY Department, Course");
while ($dc && ($r=mysqli_fetch_assoc($dc))) $deptCourseMap[$r['d']][$r['c']] = true;

if ($department==='All') {
  $qc = mysqli_query($conn,"SELECT DISTINCT Course c FROM candidate
                            WHERE Course IS NOT NULL AND TRIM(Course)<>'' 
                            ORDER BY Course");
  while ($qc && ($r=mysqli_fetch_assoc($qc))) $courses[] = $r['c'];
} elseif (!empty($deptCourseMap[$department])) {
  $courses = array_merge(['All'], array_keys($deptCourseMap[$department]));
}

/* ---------- base where (NO department/position yet) ---------- */
$where=[]; $params=[]; $types='';
if ($course!=='All'){     $where[]="c.Course=?";             $params[]=$course;     $types.='s'; }
if ($campus!=='All'){     $where[]="COALESCE(c.Campus,'')=?";$params[]=$campus;     $types.='s'; }
$baseWhereSql = $where ? ('WHERE '.implode(' AND ',$where)) : '';

/* ---------- which departments to render ---------- */
$deptRender = [];
if ($department==='All') {
  $sqlD = "SELECT DISTINCT c.Department d FROM candidate c $baseWhereSql ORDER BY d";
  if ($st = mysqli_prepare($conn,$sqlD)) {
    if ($types!=='') mysqli_stmt_bind_param($st,$types,...$params);
    mysqli_stmt_execute($st);
    $rs = mysqli_stmt_get_result($st);
    while ($rs && ($r=mysqli_fetch_assoc($rs))) if ($r['d']!=='') $deptRender[] = $r['d'];
    mysqli_stmt_close($st);
  }
} else {
  $deptRender = [$department];
}

/* ---------- positions to show ---------- */
$posList = ($position==='All') ? $POS_ORDER : [$position];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Final Result</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root{ --primary:#002f6c; --accent:#0056b3; --bg:#f4f6f8; --white:#fff; --shadow:0 4px 12px rgba(0,0,0,.08);
           --muted:#39557a; --radius:14px; }
    *{box-sizing:border-box}
    body{font-family:Inter,system-ui,Segoe UI,Roboto,Arial;background:var(--bg);color:#0b1324;margin:0}
    .container{max-width:1200px;margin:16px auto;padding:0 16px}

    /* filter bar */
    .card{background:#fff;border:1px solid #e8eef7;border-radius:var(--radius);box-shadow:var(--shadow);padding:12px}
    .filters .row{display:grid;grid-template-columns:repeat(4,minmax(220px,1fr)) auto;gap:10px;align-items:end}
    .field{display:flex;flex-direction:column;gap:6px}
    .field label{font-size:12px;color:var(--muted);font-weight:600}
    select{appearance:none;padding:12px 14px;border-radius:12px;border:1px solid #d8e2f0;background:#fff;color:#0d2f66;outline:none}
    .filters .right{display:flex;gap:8px}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;border:1px solid #d8e2f0;background:#fff;color:#0d2f66;text-decoration:none;cursor:pointer}
    .btn:hover{background:#f6f9ff}
    .controls{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-top:10px}
    .controls .search{position:relative;flex:1 1 520px;min-width:260px}
    .controls .search input{width:100%;padding:12px 40px;border-radius:12px;border:1px solid #d8e2f0;background:#fff;color:#0d2f66}
    .controls .search .icon-left{position:absolute;left:12px;top:50%;transform:translateY(-50%);opacity:.7}

    /* sections */
    .dept{background:#fff;border:1px solid #e8eef7;border-radius:var(--radius);box-shadow:var(--shadow);padding:18px;margin-top:14px}
    .dept h2{margin:0 0 8px;font-size:20px;color:#0b2b6a}
    .dept .sub{margin:0 0 10px;color:#4b5b70;font-size:13px}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px}
    .win-card{border:1px solid #e5e7eb;border-radius:12px;padding:14px;background:#fff;display:flex;flex-direction:column;align-items:center;gap:8px;min-height:210px}
    .pos{font-size:12px;font-weight:800;color:#0b2b6a}
    .avatar{width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid #000;background:#f3f4f6}
    .name{font-weight:800;text-align:center}
    .meta{font-size:12px;color:#4b5b70;text-align:center}
    .badge{margin-top:6px;font-weight:800;font-size:13px;color:#134e4a;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:999px;padding:6px 10px}
    .empty{opacity:.7;border-style:dashed}
  </style>
</head>
<body>

<?php $activePage='final_result'; include 'header.php'; ?>

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
        <label for="campus">Campus</label>
        <select id="campus" name="campus">
          <?php foreach ($campuses as $cm): ?>
            <option value="<?php echo h($cm); ?>" <?php echo ($cm===$campus)?'selected':''; ?>><?php echo $cm==='All'?'All':h($cm); ?></option>
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

      <div class="right">
        <a class="btn" href="<?php echo h($_SERVER['PHP_SELF']); ?>"><i class="fa fa-rotate-left"></i> Reset</a>
      </div>
    </div>

    <div class="controls">
      <div class="search">
        <i class="fa fa-search icon-left"></i>
        <input id="liveSearch" type="text" value="<?php echo h($q); ?>" placeholder="Quick filter by name/party (client-side)">
      </div>
    </div>
  </form>

  <!-- DEPARTMENT SECTIONS -->
  <?php if (!$deptRender): ?>
    <div class="dept"><div class="sub">No departments match your filters.</div></div>
  <?php endif; ?>

  <?php
  foreach ($deptRender as $deptVal):
    // subtitle
    $subBits = [];
    $subBits[] = 'Campus: '.($campus==='All' ? 'All' : h($campus));
    if ($course!=='All') $subBits[] = 'Course: '.h($course);
  ?>
    <section class="dept">
      <h2><?php echo h($deptVal); ?></h2>
      <p class="sub"><?php echo implode(' • ', $subBits); ?></p>

      <div class="grid" id="grid_<?php echo h($deptVal); ?>">
        <?php foreach (($position==='All' ? $POS_ORDER : [$position]) as $posName):
          // winner query for THIS department+position (and course/campus filters)
          $w2 = ["c.Department=?","c.Position=?"]; $p2 = [$deptVal,$posName]; $t2='ss';
          if ($course!=='All'){ $w2[]="c.Course=?";             $p2[]=$course; $t2.='s'; }
          if ($campus!=='All'){ $w2[]="COALESCE(c.Campus,'')=?";$p2[]=$campus; $t2.='s'; }

          $sql = "SELECT c.CandidateID, c.FirstName, c.LastName, c.Year, c.Photo, c.Party,
                         COALESCE(c.Course,'') AS Course, COALESCE(c.Campus,'') AS Campus,
                         (SELECT COUNT(*) FROM votes v WHERE v.CandidateID=c.CandidateID) AS vote_count
                  FROM candidate c
                  WHERE ".implode(' AND ',$w2)."
                  ORDER BY vote_count DESC, c.LastName ASC, c.FirstName ASC
                  LIMIT 1";
          $winner = null;
          if ($st = mysqli_prepare($conn,$sql)) {
            mysqli_stmt_bind_param($st,$t2,...$p2);
            mysqli_stmt_execute($st);
            $rs = mysqli_stmt_get_result($st);
            $winner = $rs ? mysqli_fetch_assoc($rs) : null;
            mysqli_stmt_close($st);
          }

          if (!$winner): ?>
            <div class="win-card empty" data-name="">
              <div class="pos"><?php echo h($posName); ?></div>
              <div class="meta">No winner yet.</div>
            </div>
          <?php else:
            $name  = trim(($winner['FirstName']??'').' '.($winner['LastName']??'')); 
            $votes = int0($winner['vote_count']);
            $photoPath = $winner['Photo'] ?? '';
            $photo = (is_string($photoPath) && $photoPath!=='' && file_exists($photoPath)) ? $photoPath : 'images/default-avatar.png';
            $meta = implode(' • ', array_filter([$winner['Year'] ?? '', $winner['Course'] ?? '', ($campus==='All' ? ($winner['Campus'] ?: '') : '')]));
          ?>
            <div class="win-card row" data-name="<?php echo h(strtolower($name.' '.$winner['Party'].' '.$meta)); ?>">
              <div class="pos"><?php echo h($posName); ?></div>
              <img class="avatar" src="<?php echo h($photo); ?>" alt="<?php echo h($name); ?>">
              <div class="name"><?php echo h($name); ?></div>
              <?php if ($meta): ?><div class="meta"><?php echo h($meta); ?></div><?php endif; ?>
              <?php if (!empty($winner['Party'])): ?><div class="meta"><strong>Party:</strong> <?php echo h($winner['Party']); ?></div><?php endif; ?>
              <div class="badge"><?php echo $votes; ?> votes</div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endforeach; ?>
</div>

<script>
/* dependent Course list + auto-submit */
(function(){
  const form = document.getElementById('filtersForm');
  const selDept = document.getElementById('department');
  const selCourse = document.getElementById('course');
  const selPos = document.getElementById('position');
  const selCampus = document.getElementById('campus');

  const map = <?php
    $out=[]; foreach ($deptCourseMap as $d=>$arr) $out[$d]=array_keys($arr);
    echo json_encode($out, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  ?>;
  const allCourses = <?php
    $all=[]; $qc2=mysqli_query($conn,"SELECT DISTINCT Course c FROM candidate WHERE Course IS NOT NULL AND TRIM(Course)<>'' ORDER BY Course");
    while($qc2 && ($r=mysqli_fetch_assoc($qc2))) $all[]=$r['c'];
    echo json_encode($all, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  ?>;

  function rebuildCourses(dept, keep){
    const curr = keep ? selCourse.value : 'All';
    const list = ['All'].concat(dept==='All' ? allCourses : (map[dept]||[]));
    selCourse.innerHTML=''; list.forEach(v=>{
      const o=document.createElement('option'); o.value=v; o.textContent=v;
      if(v===curr) o.selected=true; selCourse.appendChild(o);
    });
  }
  selDept.addEventListener('change', ()=>{ rebuildCourses(selDept.value,false); form.submit(); });
  selCourse.addEventListener('change', ()=> form.submit());
  selPos.addEventListener('change', ()=> form.submit());
  selCampus.addEventListener('change', ()=> form.submit());
  rebuildCourses(selDept.value, true);
})();

/* client-side quick filter inside a department grid */
(function(){
  const input = document.getElementById('liveSearch');
  if(!input) return;
  const rows = Array.from(document.querySelectorAll('.win-card.row'));
  function run(){
    const term = (input.value||'').trim().toLowerCase();
    rows.forEach(card=>{
      const hay = (card.getAttribute('data-name')||'').toLowerCase();
      card.style.display = !term || hay.includes(term) ? '' : 'none';
    });
  }
  input.addEventListener('input', run);
})();
</script>
</body>
</html>
