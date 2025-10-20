<?php
include('session.php');
include('dbcon.php');

/* ---------- helpers ---------- */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function j($v){ return json_encode($v, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }
function int0($v){ return is_numeric($v) ? (int)$v : 0; }
function db_name(mysqli $c){ $r=$c->query("SELECT DATABASE() AS d"); return $r?($r->fetch_assoc()['d']??''):''; }
function col_exists(mysqli $c,$t,$col){
  $db = db_name($c);
  $s = $c->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=? AND COLUMN_NAME=? LIMIT 1");
  $s->bind_param('sss',$db,$t,$col); $s->execute();
  $ok = $s->get_result()->num_rows>0; $s->close(); return $ok;
}

/* ---------- inputs ---------- */
$department = $_GET['department'] ?? 'All';
$course     = $_GET['course']     ?? 'All';
$position   = $_GET['position']   ?? 'All';
$campus     = $_GET['campus']     ?? 'All';

/* ---------- canonical position order ---------- */
$POS_ORDER = [
  'President','Vice-President','Governor','Vice-Governor',
  'Secretary','Treasurer','Representative','Social-Media Officer'
];

/* ---------- filter lists (from candidate) ---------- */
$depts   = ['All'];
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

/* ---------- VOTERS: voted vs unvoted (respects filters if columns exist) ---------- */
$hasVDept   = col_exists($conn,'voters','Department');
$hasVCourse = col_exists($conn,'voters','Course');
$hasVCampus = col_exists($conn,'voters','Campus');

$wv=[]; $pv=[]; $tv='';
if ($hasVDept   && $department!=='All'){ $wv[]="Department=?";         $pv[]=$department; $tv.='s'; }
if ($hasVCourse && $course!=='All'){     $wv[]="Course=?";             $pv[]=$course;     $tv.='s'; }
if ($hasVCampus && $campus!=='All'){     $wv[]="COALESCE(Campus,'')=?";$pv[]=$campus;     $tv.='s'; }

$sqlVoters = "SELECT COALESCE(Status,'Unvoted') AS S, COUNT(*) AS total FROM voters".
             ($wv?(" WHERE ".implode(' AND ',$wv)):'')." GROUP BY COALESCE(Status,'Unvoted')";
$voted_counts = ['Voted'=>0, 'Unvoted'=>0];
if ($st = mysqli_prepare($conn,$sqlVoters)) {
  if ($tv!=='') mysqli_stmt_bind_param($st,$tv,...$pv);
  mysqli_stmt_execute($st);
  $rs = mysqli_stmt_get_result($st);
  while ($rs && ($r=mysqli_fetch_assoc($rs))) {
    $key = ($r['S']==='Voted') ? 'Voted' : 'Unvoted';
    $voted_counts[$key] = (int)$r['total'];
  }
  mysqli_stmt_close($st);
}
$totalVoters = max(1, $voted_counts['Voted'] + $voted_counts['Unvoted']);
$votedPerc   = round(($voted_counts['Voted']   / $totalVoters) * 100, 2);
$unvotedPerc = round(($voted_counts['Unvoted'] / $totalVoters) * 100, 2);

/* ---------- Which departments to draw ---------- */
$deptDraw = [];
if ($department==='All') {
  $w=[]; $p=[]; $t='';
  if ($course!=='All'){ $w[]="c.Course=?";             $p[]=$course; $t.='s'; }
  if ($campus!=='All'){ $w[]="COALESCE(c.Campus,'')=?";$p[]=$campus; $t.='s'; }
  $sqlD = "SELECT DISTINCT c.Department d FROM candidate c".($w?(" WHERE ".implode(' AND ',$w)):'')." ORDER BY d";
  if ($st = mysqli_prepare($conn,$sqlD)) {
    if ($t!=='') mysqli_stmt_bind_param($st,$t,...$p);
    mysqli_stmt_execute($st);
    $rs = mysqli_stmt_get_result($st);
    while ($rs && ($r=mysqli_fetch_assoc($rs))) if ($r['d']!=='') $deptDraw[]=$r['d'];
    mysqli_stmt_close($st);
  }
} else {
  $deptDraw = [$department];
}

/* positions to include */
$posList = ($position==='All') ? $POS_ORDER : [$position];

/* ---------- Vote difference (winner vs runner-up) per dept+position ---------- */
$diffData = [];
foreach ($deptDraw as $deptVal) {
  $labels = $posList;
  $diffs  = [];
  $tips   = [];

  foreach ($labels as $posName) {
    $w=[]; $p=[]; $t='';
    $w[]="c.Department=?"; $p[]=$deptVal; $t.='s';
    $w[]="c.Position=?";   $p[]=$posName; $t.='s';
    if ($course!=='All'){ $w[]="c.Course=?";             $p[]=$course; $t.='s'; }
    if ($campus!=='All'){ $w[]="COALESCE(c.Campus,'')=?";$p[]=$campus; $t.='s'; }

    $sql = "SELECT c.CandidateID, c.FirstName, c.LastName,
                   COALESCE(vv.cnt,0) AS vote_count
            FROM candidate c
            LEFT JOIN (SELECT CandidateID, COUNT(*) cnt FROM votes GROUP BY CandidateID) vv
              ON vv.CandidateID = c.CandidateID
            WHERE ".implode(' AND ',$w)."
            ORDER BY vote_count DESC, c.LastName ASC, c.FirstName ASC
            LIMIT 2";
    $top=[]; 
    if ($st = mysqli_prepare($conn,$sql)) {
      mysqli_stmt_bind_param($st,$t,...$p);
      mysqli_stmt_execute($st);
      $rs = mysqli_stmt_get_result($st);
      while ($rs && ($row=mysqli_fetch_assoc($rs))) $top[]=$row;
      mysqli_stmt_close($st);
    }

    $v1 = isset($top[0]) ? int0($top[0]['vote_count']) : 0;
    $v2 = isset($top[1]) ? int0($top[1]['vote_count']) : 0;
    $winner = isset($top[0]) ? trim(($top[0]['FirstName']??'').' '.($top[0]['LastName']??'')) : '—';
    $runner = isset($top[1]) ? trim(($top[1]['FirstName']??'').' '.($top[1]['LastName']??'')) : '—';
    $margin = max(0, $v1 - $v2);

    $diffs[] = $margin;
    if ($runner==='—')
      $tips[] = "{$posName}: {$winner} ({$v1}) • no runner-up";
    else
      $tips[] = "{$posName}: {$winner} ({$v1}) vs {$runner} ({$v2}) • margin {$margin}";
  }

  $diffData[] = ['dept'=>$deptVal, 'labels'=>$labels, 'diffs'=>$diffs, 'tips'=>$tips];
}

/* ---------- Titles (kept for accessibility, not rendered) ---------- */
$deptTitle = ($department==='All') ? 'All Departments' : $department;
$campTitle = ($campus==='All') ? 'All Campuses' : $campus;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Analytics - Vote Differences</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root{
      --primary:#002f6c; --bg:#f4f6f8; --white:#fff; --shadow:0 5px 15px rgba(0,0,0,.10);
      --muted:#6b7280; --chip:#eef4ff; --radius:14px;
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial;background:var(--bg);color:#0b1324}
    /* WIDER PAGE */
    .content{max-width:1600px;margin:24px auto;padding:0 20px}

    .card{background:var(--white);box-shadow:var(--shadow);border-radius:var(--radius);border:1px solid #e8eef7}
    .filters{padding:12px}
    .filters .row{display:grid;grid-template-columns:repeat(4,minmax(220px,1fr)) auto;gap:10px;align-items:end}
    .field{display:flex;flex-direction:column;gap:6px}
    .field label{font-size:12px;color:var(--muted);font-weight:600}
    select{appearance:none;padding:12px 14px;border-radius:12px;border:1px solid #d8e2f0;background:#fff;color:#0d2f66;outline:none}
    .right{display:flex;gap:8px}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;border:1px solid #d8e2f0;background:#fff;color:#0d2f66;text-decoration:none;cursor:pointer}
    .btn:hover{background:#f6f9ff}

    /* Title band removed */
    .titleband{display:none !important;}

    /* STACK the charts vertically (updated) */
    .grid{
      display:grid;
      grid-template-columns:1fr; /* was 1fr 1fr */
      gap:20px;
      margin-top:14px;
    }

    .chart-card{padding:18px;min-height:420px;display:flex;flex-direction:column}
    .chart-card h3{margin:0 0 6px;color:#000}
    .rule{height:3px;background:#000;border-radius:2px;margin-bottom:10px}
    .chips{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:6px 0 8px}
    .chip{background:var(--chip);border:1px solid #d0d7de;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;color:#163d7a}
    .chart-wrap{position:relative;flex:1}
    canvas{position:absolute;inset:0;width:100% !important;height:100% !important}

    .diff-grid{display:grid;grid-template-columns:1fr;gap:16px;margin-top:14px}
    @media (min-width:1100px){ .diff-grid{grid-template-columns:1fr 1fr} }
    .mini{padding:16px; min-height:360px}
    .mini h4{margin:0 0 8px; color:#0b2b6a}
  </style>
</head>
<body>

<?php $activePage='analytics'; include 'header.php'; ?>

<div class="content">
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
  </form>

  <!-- CHARTS (now stacked) -->
  <div class="grid">
    <!-- Voted/Unvoted -->
    <div class="card chart-card">
      <h3>Voted vs Unvoted (Percent)</h3>
      <div class="rule"></div>
      <div class="chips">
        <span class="chip">Voted: <?php echo number_format($votedPerc,2); ?>% (<?php echo $voted_counts['Voted']; ?> of <?php echo $totalVoters; ?>)</span>
        <span class="chip">Unvoted: <?php echo number_format($unvotedPerc,2); ?>% (<?php echo $voted_counts['Unvoted']; ?> of <?php echo $totalVoters; ?>)</span>
      </div>
      <div class="chart-wrap"><canvas id="votedChart"></canvas></div>
    </div>

    <!-- Vote difference per department -->
    <div class="card chart-card">
      <!-- updated title -->
      <h3>Vote Difference — by Position (per Department)</h3>
      <div class="rule"></div>
      <!-- updated helper chip -->
      <div class="chips"><span class="chip">Winner’s lead over runner-up</span></div>

      <div class="diff-grid">
        <?php foreach ($diffData as $i=>$row): ?>
          <div class="card mini">
            <h4><?php echo h($row['dept']); ?></h4>
            <div class="chart-wrap" style="min-height:280px"><canvas id="df_<?php echo $i; ?>"></canvas></div>
          </div>
        <?php endforeach; ?>
        <?php if (!$diffData): ?>
          <div class="card mini"><h4>No departments match your filters.</h4></div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <footer style="color:#666;text-align:center;padding:20px 0;margin-top:16px;border-top:1px solid #e5e7eb">© 2025 Online Election Voting System</footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
/* Dependent Course list + auto-submit */
(function(){
  const form = document.getElementById('filtersForm');
  const selDept = document.getElementById('department');
  const selCourse = document.getElementById('course');
  const selPos = document.getElementById('position');
  const selCampus = document.getElementById('campus');

  const map = <?php
    $out=[]; foreach ($deptCourseMap as $d=>$arr) $out[$d]=array_keys($arr);
    echo j($out);
  ?>;
  const allCourses = <?php
    $all=[]; $qc2=mysqli_query($conn,"SELECT DISTINCT Course c FROM candidate WHERE Course IS NOT NULL AND TRIM(Course)<>'' ORDER BY Course");
    while($qc2 && ($r=mysqli_fetch_assoc($qc2))) $all[]=$r['c'];
    echo j($all);
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

/* Charts */
const percentAxis = {
  beginAtZero: true,
  max: 100,
  ticks: { callback: v => v + '%', color: '#000', font: { size: 12 } },
  grid: { color: '#ccc' }
};

/* Voted vs Unvoted (line) */
new Chart(document.getElementById('votedChart').getContext('2d'), {
  type: 'line',
  data: {
    labels: ['Voted','Unvoted'],
    datasets: [{
      label: 'Voters %',
      data: <?php echo j([$votedPerc, $unvotedPerc]); ?>,
      tension: 0.35, pointRadius: 4, pointHoverRadius: 5, borderWidth: 2,
      borderColor: '#002f6c', backgroundColor: '#002f6c', fill: false
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    scales: { x: { ticks:{color:'#000', font:{size:12}}, grid:{color:'#eee'} }, y: percentAxis },
    plugins: {
      tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: ${ctx.parsed.y.toFixed(2)}%` } },
      legend: { labels: { color:'#000', font:{size:12} } }
    }
  }
});

/* Vote Difference mini charts (bars) */
const diffData = <?php echo j($diffData); ?>;
diffData.forEach((row, i) => {
  const el = document.getElementById('df_'+i);
  if (!el) return;
  new Chart(el.getContext('2d'), {
    type: 'bar',
    data: {
      labels: row.labels,
      datasets: [{
        label: "Winner’s lead",     // updated label
        data: row.diffs,
        borderWidth: 2,
        borderColor: '#0b3b91',
        backgroundColor: '#bcd2f3'
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      scales: {
        x: { ticks:{color:'#000', maxRotation:45}, grid:{color:'#eee'} },
        y: { beginAtZero:true, ticks:{stepSize:1, color:'#000'}, grid:{color:'#eee'} }
      },
      plugins: {
        tooltip: { callbacks: { label: (ctx) => row.tips[ctx.dataIndex] || '' } },
        legend: { labels: { color:'#000', font:{size:12} } }
      }
    }
  });
});
</script>
</body>
</html>
