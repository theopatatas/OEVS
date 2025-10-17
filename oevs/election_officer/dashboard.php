<?php
include('session.php');
include('dbcon.php');

/* ---------- DATA ---------- */
// Candidates per position
$candidates_per_position = [];
$res = mysqli_query($conn, "SELECT Position, COUNT(*) AS total FROM candidate GROUP BY Position ORDER BY Position ASC");
while ($row = mysqli_fetch_assoc($res)) {
  $candidates_per_position[$row['Position']] = (int)$row['total'];
}
$positions = array_keys($candidates_per_position);
$total_candidates = array_sum($candidates_per_position);

// Voted vs Unvoted
$voted_counts = ['Voted' => 0, 'Unvoted' => 0];
$res = mysqli_query($conn, "SELECT Status, COUNT(*) AS total FROM voters GROUP BY Status");
while ($row = mysqli_fetch_assoc($res)) {
  $key = trim($row['Status']);
  if (isset($voted_counts[$key])) $voted_counts[$key] = (int)$row['total'];
}
$total_voters = max(0, (int)$voted_counts['Voted'] + (int)$voted_counts['Unvoted']);

// Pre-compute percentages
$pos_percents = [];
if ($total_candidates > 0) {
  foreach ($candidates_per_position as $pos => $cnt) {
    $pos_percents[$pos] = round(($cnt / $total_candidates) * 100, 2);
  }
} else {
  foreach ($candidates_per_position as $pos => $cnt) $pos_percents[$pos] = 0;
}
$voted_pct   = $total_voters ? round(($voted_counts['Voted']   / $total_voters) * 100, 2) : 0;
$unvoted_pct = $total_voters ? round(($voted_counts['Unvoted'] / $total_voters) * 100, 2) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Analytics - Online Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
  <style>
    :root{
      --primary:#002f6c; --accent:#0056b3; --bg:#f4f6f8; --white:#fff; --ink:#0d2343;
      --muted:#6c7b90; --border:#e6ebf4; --shadow:0 8px 24px rgba(0,0,0,.08); --ring:#9ec5ff;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);margin:0;color:var(--ink)}
    a{text-decoration:none;color:inherit}

    /* ===== Header (same as home.php style) ===== */
    header{
      background:var(--white); box-shadow:var(--shadow); border-bottom:1px solid var(--border);
      padding:10px 22px; display:flex; justify-content:space-between; align-items:center; gap:16px; position:sticky; top:0; z-index:10;
    }
    .logo-section{display:flex; align-items:center; gap:10px}
    .logo-section img{height:40px}
    .logo-section .title{font-weight:700; font-size:16px; color:var(--primary); line-height:1.1}
    .logo-section .title small{font-weight:600; font-size:12px; color:var(--accent)}
    nav{display:flex; align-items:center; gap:12px}
    .nav-item{position:relative}
    .nav-item > a{display:inline-flex; align-items:center; gap:8px; padding:8px 14px; border-radius:10px; font-weight:700; color:var(--primary); transition:.2s}
    .nav-item > a i{width:18px; text-align:center}
    .nav-item > a:hover,.nav-item > a:focus-visible{background:var(--primary); color:#fff; outline:none}
    .nav-item.logout > a{color:#d92d2d; font-weight:800}
    .nav-item.logout > a:hover,.nav-item.logout > a:focus-visible{background:#ffe9e9; color:#b61e1e}

    .dropdown,.submenu{
      display:none; position:absolute; top:calc(100% - 2px); left:0; min-width:240px; background:#fff; border:1px solid #e7eef7;
      border-radius:14px; box-shadow:0 10px 30px rgba(13,35,67,.12), 0 2px 6px rgba(13,35,67,.06); padding:6px; z-index:999;
    }
    .nav-item:hover > .dropdown,.nav-item:focus-within > .dropdown{display:block}
    .dropdown a,.submenu a{display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; color:var(--primary); font-weight:600}
    .dropdown a:hover,.submenu a:hover,.dropdown a:focus-visible,.submenu a:focus-visible{background:var(--accent); color:#fff}
    .dropdown .divider{height:1px; background:#e9eff7; margin:6px 4px}
    .has-submenu{position:relative}
    .submenu{position:static; border:none; box-shadow:none; padding:4px 0 0}
    .submenu a{padding-left:36px}
    .has-submenu > a .chev{margin-left:auto; transition:transform .2s}
    .has-submenu:hover > .submenu,.has-submenu:focus-within > .submenu{display:block}
    .has-submenu:hover > a .chev,.has-submenu:focus-within > a .chev{transform:rotate(90deg)}

    /* ===== Page ===== */
    main{padding:22px 16px}
    .container{max-width:1100px; margin:0 auto}

    /* tools row */
    .tools{
      display:flex; align-items:center; justify-content:flex-start; gap:12px; background:#fff; border:1px solid var(--border);
      border-radius:12px; box-shadow:var(--shadow); padding:12px; margin-bottom:14px;
    }
    .btn{
      display:inline-flex; align-items:center; gap:8px; border:1px solid var(--border); background:#f7f9fc; color:#0b1b36;
      font-weight:700; border-radius:10px; padding:9px 12px; cursor:pointer;
    }
    .btn:hover{background:#eef4ff}

    /* filter menu styled like screenshot */
    .filter-wrap{position:relative}
    .filter-menu{
      display:none; position:absolute; top:calc(100% + 8px); left:0; background:#fff; border:1px solid var(--border);
      border-radius:12px; box-shadow:var(--shadow); min-width:260px; padding:8px; z-index:5;
    }
    .filter-option{display:flex; gap:10px; align-items:flex-start; padding:10px; border-radius:10px; cursor:pointer}
    .filter-option:hover{background:#f3f6ff}
    .filter-option input{margin-top:2px}
    .fo-text{display:flex; flex-direction:column}
    .fo-title{font-weight:700; color:#102b54}
    .fo-sub{font-size:12px; color:#6c7b90}

    /* cards area */
    .grid{display:grid; grid-template-columns:1fr 1fr; gap:16px}
    @media (max-width:900px){ .grid{grid-template-columns:1fr} }
    .card{
      background:#fff; border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow); padding:14px;
    }
    .card h3{display:flex; align-items:center; justify-content:space-between; margin:0 0 10px; color:#0d2343}
    .line{height:2px; background:#0d2343; margin-top:8px}
    .chips{display:flex; gap:8px; flex-wrap:wrap; margin-bottom:8px}
    .chip{
      display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; background:#eef2f7; color:#0d2343; border:1px solid #e1e8f5; font-weight:700; font-size:12px;
    }

    footer{text-align:center; padding:18px 0; color:var(--muted); font-size:14px}

    canvas{width:100% !important; height:360px !important; display:block}
  </style>
</head>
<body>

<!-- ===== Header (from home.php style) ===== -->
<header>
  <div class="logo-section">
    <img src="images/au.png" alt="Logo" />
    <div class="title">ONLINE ELECTION VOTING SYSTEM<br /><small>Phinma Araullo University</small></div>
  </div>

  <nav>
    <div class="nav-item"><a href="home.php"><i class="fas fa-home"></i> Home</a></div>

    <div class="nav-item">
      <a href="#"><i class="fas fa-list-ul"></i> Menu</a>
      <div class="dropdown">
        <a href="voter_list.php">Voters</a>
        <div class="divider"></div>
        <div class="has-submenu">
          <a href="#" role="button" aria-expanded="false">
            Admin Actions <i class="fa fa-chevron-right chev" aria-hidden="true"></i>
          </a>
          <div class="submenu">
            <a href="result.php"><i class="fa fa-table"></i> Election Result</a>
            <a href="dashboard.php"><i class="fa fa-chart-bar"></i> Analytics</a>
            <a href="canvassing_report.php"><i class="fa fa-table"></i> Vote Count Report</a>
            <a href="voter_verification.php"><i class="fa fa-id-badge"></i> Voter Verification</a>
          </div>
        </div>
      </div>
    </div>

    <div class="nav-item">
      <a href="#"><i class="fas fa-user-circle"></i> Profile</a>
      <div class="dropdown"><a href="profile.php">View Profile</a></div>
    </div>
    <div class="nav-item"><a href="about.php"><i class="fas fa-circle-info"></i> About</a></div>
    <div class="nav-item logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
  </nav>
</header>

<main>
  <div class="container">

    <!-- Tools row -->
    <div class="tools">
      <div class="filter-wrap">
        <button class="btn" id="filterBtn" type="button">
          <i class="fa fa-filter"></i> Filter By Position <i class="fa fa-caret-down" aria-hidden="true"></i>
        </button>
        <div class="filter-menu" id="filterMenu" role="menu" aria-label="Filter positions">
          <label class="filter-option">
            <input type="radio" name="posFilter" value="__ALL__" checked />
            <div class="fo-text">
              <span class="fo-title">All</span>
              <span class="fo-sub">Show all positions</span>
            </div>
          </label>
          <?php foreach ($positions as $p): ?>
            <label class="filter-option">
              <input type="radio" name="posFilter" value="<?php echo htmlspecialchars($p); ?>" />
              <div class="fo-text">
                <span class="fo-title"><?php echo htmlspecialchars($p); ?></span>
                <span class="fo-sub">Only <?php echo htmlspecialchars($p); ?></span>
              </div>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="grid">
      <!-- Left: Voted vs Unvoted (Percent) -->
      <section class="card">
        <h3>Voted vs Unvoted (Percent)</h3>
        <div class="line"></div>
        <div class="chips">
          <span class="chip">Voted: <?php echo number_format($voted_pct,2); ?>% (<?php echo (int)$voted_counts['Voted']; ?> of <?php echo (int)$total_voters; ?>)</span>
          <span class="chip">Unvoted: <?php echo number_format($unvoted_pct,2); ?>% (<?php echo (int)$voted_counts['Unvoted']; ?> of <?php echo (int)$total_voters; ?>)</span>
          <span class="chip">Voters %</span>
        </div>
        <canvas id="votedChart"></canvas>
      </section>

      <!-- Right: Candidates by Position (Percent of Total) -->
      <section class="card">
        <h3>Candidates by Position (Percent of Total)</h3>
        <div class="line"></div>
        <div class="chips">
          <span class="chip" id="scopeChip">All positions</span>
          <span class="chip">Total: 100% (<?php echo (int)$total_candidates; ?> candidates)</span>
          <span class="chip">Candidates %</span>
        </div>
        <canvas id="candidatesChart"></canvas>
      </section>
    </div>

    <footer>© 2025 Online Election Voting System</footer>
  </div>
</main>

<script>
  // --- Filter dropdown toggle ---
  (function(){
    const btn = document.getElementById('filterBtn');
    const menu = document.getElementById('filterMenu');
    btn.addEventListener('click', (e)=>{ e.stopPropagation(); menu.style.display = menu.style.display==='block'?'none':'block'; });
    document.addEventListener('click', (e)=>{ if(!e.target.closest('.filter-wrap')) menu.style.display='none'; });
  })();

  // --- Data from PHP ---
  const positions        = <?php echo json_encode($positions); ?>;
  const posPercents      = <?php echo json_encode($pos_percents); ?>; // {pos: pct}
  const votedCounts      = <?php echo json_encode($voted_counts); ?>;
  const totalVoters      = <?php echo (int)$total_voters; ?>;

  // --- Chart 1: Voted vs Unvoted (line, percent) ---
  const votedCtx = document.getElementById('votedChart').getContext('2d');
  const votedPct = totalVoters ? (votedCounts.Voted / totalVoters) * 100 : 0;
  const unvotedPct = totalVoters ? (votedCounts.Unvoted / totalVoters) * 100 : 0;

  new Chart(votedCtx, {
    type: 'line',
    data: {
      labels: ['Voted','Unvoted'],
      datasets: [{
        label: 'Voters %',
        data: [votedPct, unvotedPct],
        borderColor: '#0a2e5c',
        backgroundColor: 'rgba(10,46,92,0.12)',
        pointBackgroundColor: '#0a2e5c',
        pointRadius: 5,
        tension: 0.2
      }]
    },
    options: {
      responsive:true, maintainAspectRatio:false,
      scales:{
        y:{ beginAtZero:true, max:100, ticks:{ callback:(v)=>v+'%' }, grid:{ color:'#e6ebf4' } },
        x:{ grid:{ color:'#edf2f7' } }
      },
      plugins:{ legend:{ display:true, labels:{ color:'#0d2343', font:{ weight:700 } } } }
    }
  });

  // --- Chart 2: Candidates by Position (line, percent) ---
  const candCtx = document.getElementById('candidatesChart').getContext('2d');
  const allLabels  = positions.slice();
  const allData    = positions.map(p => posPercents[p] ?? 0);

  const candChart = new Chart(candCtx, {
    type: 'line',
    data: {
      labels: allLabels,
      datasets: [{
        label: 'Candidates %',
        data: allData,
        borderColor: '#0a2e5c',
        backgroundColor: 'rgba(10,46,92,0.12)',
        pointBackgroundColor: '#0a2e5c',
        pointRadius: 5,
        tension: 0.2
      }]
    },
    options: {
      responsive:true, maintainAspectRatio:false,
      scales:{
        y:{ beginAtZero:true, max:100, ticks:{ callback:(v)=>v+'%' }, grid:{ color:'#e6ebf4' } },
        x:{ grid:{ color:'#edf2f7' }, ticks:{ maxRotation:30, minRotation:30 } }
      },
      plugins:{ legend:{ display:true, labels:{ color:'#0d2343', font:{ weight:700 } } } }
    }
  });

  // --- Filter drives the right chart + chip text ---
  (function(){
    const radios = document.querySelectorAll('input[name="posFilter"]');
    const scopeChip = document.getElementById('scopeChip');

    function apply(){
      const val = document.querySelector('input[name="posFilter"]:checked')?.value || '__ALL__';
      if (val === '__ALL__') {
        candChart.data.labels = allLabels;
        candChart.data.datasets[0].data = allData;
        scopeChip.textContent = 'All positions';
      } else {
        candChart.data.labels = [val];
        candChart.data.datasets[0].data = [posPercents[val] ?? 0];
        scopeChip.textContent = val;
      }
      candChart.update();
      // close the menu
      document.getElementById('filterMenu').style.display='none';
    }
    radios.forEach(r => r.addEventListener('change', apply));
  })();
</script>
</body>
</html>
