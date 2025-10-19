<?php
include('session.php');
include('dbcon.php');

/* ===== DATA ===== */

// Candidates per position
$candidates_per_position = [];
$res = mysqli_query($conn, "SELECT Position, COUNT(*) AS total FROM candidate GROUP BY Position ORDER BY Position ASC");
while ($row = mysqli_fetch_assoc($res)) {
  $candidates_per_position[$row['Position']] = (int)$row['total'];
}
$totalCandidates = array_sum($candidates_per_position);
if ($totalCandidates <= 0) $totalCandidates = 1; // avoid /0

$positions = array_keys($candidates_per_position);

// GET filter (position)
$filterPos = isset($_GET['pos']) ? trim($_GET['pos']) : '';
$filterActive = $filterPos !== '' && in_array($filterPos, $positions, true);

// Build arrays for chart (respect filter)
if ($filterActive) {
  $posLabels = [$filterPos];
  $posCounts = [$candidates_per_position[$filterPos]];
} else {
  $posLabels = $positions;
  $posCounts = array_values($candidates_per_position);
}
$posPerc = [];
foreach ($posCounts as $count) {
  $posPerc[] = round(($count / $totalCandidates) * 100, 2);
}
// For the summary chip (selected share)
$selectedSharePerc = $filterActive ? $posPerc[0] : 100.00;
$selectedShareCount = $filterActive ? $candidates_per_position[$filterPos] : $totalCandidates;

// Voted vs Unvoted
$voted_counts = ['Voted'=>0, 'Unvoted'=>0];
$res = mysqli_query($conn, "SELECT Status, COUNT(*) AS total FROM voters GROUP BY Status");
while ($row = mysqli_fetch_assoc($res)) {
  $status = $row['Status'];
  if (!isset($voted_counts[$status])) $voted_counts[$status] = 0;
  $voted_counts[$status] = (int)$row['total'];
}
$totalVoters = max(1, $voted_counts['Voted'] + $voted_counts['Unvoted']);
$votedPerc   = round(($voted_counts['Voted']   / $totalVoters) * 100, 2);
$unvotedPerc = round(($voted_counts['Unvoted'] / $totalVoters) * 100, 2);

// helper
function j($v){ return json_encode($v, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Analytics - Online Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- If header.php already loads these, you can remove them -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root{
      --primary:#002f6c;
      --accent:#0056b3;
      --bg:#f4f6f8;
      --white:#fff;
      --shadow:0 5px 15px rgba(0,0,0,.10);
      --muted:#6b7280;
      --danger:#c1121f;
      --chip:#eef4ff;
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial;background:var(--bg);color:#0b1324}

    .content{max-width:1200px;margin:24px auto;padding:0 16px}

    /* Toolbar (filter) */
    .toolbar{
      background:var(--white);box-shadow:var(--shadow);border-radius:12px;padding:14px;margin-bottom:16px;
      display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap
    }
    .left-tools,.right-tools{display:flex;align-items:center;gap:10px}
    .filter-wrap{position:relative;display:inline-block}
    .btn-filter{
      background:transparent;color:var(--primary);border:1px solid var(--primary);
      border-radius:10px;padding:10px 14px;font-weight:700;display:inline-flex;align-items:center;gap:10px;cursor:pointer
    }
    .btn-filter i{font-size:14px;color:var(--primary)}
    .btn-filter:hover{background:#f0f6ff}
    .caret{border:solid var(--primary);border-width:0 2px 2px 0;display:inline-block;padding:3px;transform:rotate(45deg)}
    .filter-menu{
      position:absolute;top:110%;left:0;background:#fff;border:1px solid #e5e7eb;border-radius:12px;min-width:240px;
      box-shadow:0 12px 28px rgba(0,0,0,.12);padding:8px 0;display:none;z-index:30
    }
    .filter-menu.open{display:block}
    .filter-item{display:flex;align-items:center;gap:10px;padding:10px 14px;color:#0b1324;text-decoration:none;cursor:pointer}
    .filter-item:hover{background:#f3f6ff}
    .dot{width:16px;height:16px;border:2px solid #9aa1ac;border-radius:999px;display:inline-flex;align-items:center;justify-content:center}
    .dot .checked{width:8px;height:8px;background:#0b4a9f;border-radius:999px;display:none}
    .filter-item.active .dot{border-color:#0b4a9f}
    .filter-item.active .dot .checked{display:block}
    .filter-label{font-weight:600}
    .filter-caption{font-size:12px;color:#6b7280}

    /* Chips */
    .chips{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-top:8px}
    .chip{background:var(--chip);border:1px solid #d0d7de;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;color:#163d7a}

    /* Chart cards */
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
    .card{background:var(--white);box-shadow:var(--shadow);border-radius:12px;padding:18px;min-height:420px;display:flex;flex-direction:column}
    .card h3{margin:0 0 6px;color:#000}
    .rule{height:3px;background:#000;border-radius:2px;margin-bottom:10px}
    .chart-wrap{position:relative;flex:1}
    canvas{position:absolute;inset:0;width:100% !important;height:100% !important}

    footer{color:#666;text-align:center;padding:20px 0;margin-top:16px;border-top:1px solid #e5e7eb}
    @media (max-width:900px){ .grid{grid-template-columns:1fr} }
  </style>
</head>
<body>

  <?php
    // highlight current page if your header supports it
    $activePage = 'analytics';
    include 'header.php';
  ?>

  <div class="content">
    <!-- Filter toolbar (Candidates) -->
    <div class="toolbar">
      <div class="left-tools">
        <div class="filter-wrap">
          <button class="btn-filter" id="filterBtn" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-filter"></i> Filter By Position <span class="caret"></span>
          </button>
          <div class="filter-menu" id="filterMenu" role="menu" aria-labelledby="filterBtn">
            <a class="filter-item <?php echo !$filterActive?'active':''; ?>" data-value="">
              <span class="dot"><span class="checked"></span></span>
              <div><div class="filter-label">All</div><div class="filter-caption">Show all positions</div></div>
            </a>
            <?php foreach ($positions as $p): ?>
              <a class="filter-item <?php echo ($filterActive && $filterPos===$p)?'active':''; ?>" data-value="<?php echo h($p); ?>">
                <span class="dot"><span class="checked"></span></span>
                <div><div class="filter-label"><?php echo h($p); ?></div><div class="filter-caption">Only <?php echo h($p); ?></div></div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="right-tools">
      </div>
    </div>

    <div class="grid">
      <!-- Voted / Unvoted -->
      <div class="card">
        <h3>Voted vs Unvoted (Percent)</h3>
        <div class="rule"></div>
        <div class="chips">
          <span class="chip">Voted: <?php echo number_format($votedPerc,2); ?>% (<?php echo $voted_counts['Voted']; ?> of <?php echo $totalVoters; ?>)</span>
          <span class="chip">Unvoted: <?php echo number_format($unvotedPerc,2); ?>% (<?php echo $voted_counts['Unvoted']; ?> of <?php echo $totalVoters; ?>)</span>
        </div>
        <div class="chart-wrap"><canvas id="votedChart"></canvas></div>
      </div>

      <!-- Candidates -->
      <div class="card">
        <h3>Candidates by Position (Percent of Total)</h3>
        <div class="rule"></div>
        <div class="chips">
          <?php if ($filterActive): ?>
            <span class="chip">Position: <?php echo h($filterPos); ?></span>
            <span class="chip">Share: <?php echo number_format($selectedSharePerc,2); ?>% (<?php echo $selectedShareCount; ?> of <?php echo $totalCandidates; ?>)</span>
          <?php else: ?>
            <span class="chip">All positions</span>
            <span class="chip">Total: 100% (<?php echo $totalCandidates; ?> candidates)</span>
          <?php endif; ?>
        </div>
        <div class="chart-wrap"><canvas id="candidatesChart"></canvas></div>
      </div>
    </div>

    <footer>© 2025 Online Election Voting System</footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Filter dropdown
    (function(){
      const btn  = document.getElementById('filterBtn');
      const menu = document.getElementById('filterMenu');
      btn.addEventListener('click', (e)=>{ e.preventDefault(); const open=menu.classList.toggle('open'); btn.setAttribute('aria-expanded', open?'true':'false'); });
      document.addEventListener('click',(e)=>{ if(!menu.contains(e.target) && !btn.contains(e.target)){ menu.classList.remove('open'); btn.setAttribute('aria-expanded','false'); }});
      menu.querySelectorAll('.filter-item').forEach(it=>{
        it.addEventListener('click', ()=>{
          const val = it.getAttribute('data-value') || '';
          const url = new URL(window.location.href);
          if(val) url.searchParams.set('pos', val); else url.searchParams.delete('pos');
          window.location.href = url.toString();
        });
      });
    })();

    // Data from PHP
    const labelsPos   = <?php echo j($posLabels); ?>;
    const dataPosPerc = <?php echo j($posPerc); ?>;
    const dataVoted   = <?php echo j([$votedPerc, $unvotedPerc]); ?>;

    const percentAxis = {
      beginAtZero: true,
      max: 100,
      ticks: { callback: v => v + '%', color: '#000', font: { size: 12 } },
      grid: { color: '#ccc' }
    };

    // Line: Voted vs Unvoted
    new Chart(document.getElementById('votedChart').getContext('2d'), {
      type: 'line',
      data: {
        labels: ['Voted','Unvoted'],
        datasets: [{
          label: 'Voters %',
          data: dataVoted,
          tension: 0.35,
          pointRadius: 4,
          pointHoverRadius: 5,
          borderWidth: 2,
          borderColor: '#002f6c',
          backgroundColor: '#002f6c',
          fill: false
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

    // Line: Candidates by Position (with filter)
    new Chart(document.getElementById('candidatesChart').getContext('2d'), {
      type: 'line',
      data: {
        labels: labelsPos,
        datasets: [{
          label: 'Candidates %',
          data: dataPosPerc,
          tension: 0.35,
          pointRadius: 4,
          pointHoverRadius: 5,
          borderWidth: 2,
          borderColor: '#002f6c',
          backgroundColor: '#002f6c',
          fill: false
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        scales: {
          x: { ticks:{color:'#000', autoSkip:false, maxRotation:45}, grid:{color:'#eee'} },
          y: percentAxis
        },
        plugins: {
          tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: ${ctx.parsed.y.toFixed(2)}%` } },
          legend: { labels: { color:'#000', font:{size:12} } }
        }
      }
    });
  </script>
</body>
</html>
