<?php
include('session.php');
include('dbcon.php');

// collect distinct positions for the filter
$pos_res = mysqli_query($conn, "SELECT DISTINCT Position FROM candidate ORDER BY Position ASC");
$positions = [];
while ($r = mysqli_fetch_assoc($pos_res)) { $positions[] = $r['Position']; }

// pull candidates grouped by position
function fetchCandidatesByPosition($conn, $position){
  $safe = mysqli_real_escape_string($conn, $position);
  $q = mysqli_query($conn, "
    SELECT c.CandidateID, c.FirstName, c.LastName, c.Year, c.Position, c.Photo, c.Qualification, c.Party,
           (SELECT COUNT(*) FROM votes v WHERE v.CandidateID = c.CandidateID) AS vote_count
    FROM candidate c
    WHERE c.Position = '$safe'
    ORDER BY vote_count DESC, c.LastName ASC
  ");
  $rows = [];
  while($row = mysqli_fetch_assoc($q)){ $rows[] = $row; }
  return $rows;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Election Result - Online Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    :root{
      --primary:#002f6c; --accent:#0056b3; --bg:#f4f6f8; --white:#fff; --ink:#0d2343;
      --muted:#6c7b90; --border:#e6ebf4; --shadow:0 8px 24px rgba(0,0,0,.08); --ring:#9ec5ff;
      --table-head:#0a2e5c;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);margin:0;color:var(--ink)}
    a{text-decoration:none;color:inherit}

    /* ===== Header (same pattern as other pages) ===== */
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

    /* header dropdowns */
    .dropdown,.submenu{
      display:none; position:absolute; top:calc(100% - 2px); left:0; min-width:240px; background:#fff; border:1px solid #e7eef7;
      border-radius:14px; box-shadow:0 10px 30px rgba(13,35,67,.12), 0 2px 6px rgba(13,35,67,.06); padding:6px; z-index:999;
    }
    .nav-item:hover > .dropdown,.nav-item:focus-within > .dropdown{display:block}
    .dropdown a,.submenu a{display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; color:var(--primary); font-weight:600}
    .dropdown a:hover,.submenu a:hover,.dropdown a:focus-visible,.submenu a:focus-visible{background:var(--accent); color:#fff}
    .dropdown .divider{height:1px; background:#e9eff7; margin:6px 4px}
    .has-submenu{position:relative}
    .submenu{position:static; border:none; box-shadow:none; padding:4px 0 0 0}
    .submenu a{padding-left:36px}
    .has-submenu > a .chev{margin-left:auto; transition:transform .2s}
    .has-submenu:hover > .submenu,.has-submenu:focus-within > .submenu{display:block}
    .has-submenu:hover > a .chev,.has-submenu:focus-within > a .chev{transform:rotate(90deg)}

    /* ===== Page ===== */
    main{padding:22px 16px}
    .container{max-width:1100px; margin:0 auto}

    /* top tools row (filter + search) */
    .tools{
      display:flex; align-items:center; justify-content:space-between; gap:12px; background:#fff; border:1px solid var(--border);
      border-radius:12px; box-shadow:var(--shadow); padding:12px; margin-bottom:14px;
    }
    .btn{
      display:inline-flex; align-items:center; gap:8px; border:1px solid var(--border); background:#f7f9fc; color:#0b1b36;
      font-weight:700; border-radius:10px; padding:9px 12px; cursor:pointer;
    }
    .btn:hover{background:#eef4ff}
    .search-wrap{flex:1; display:flex; justify-content:flex-end}
    .search-wrap input{
      width:min(420px, 100%); height:38px; border:1px solid var(--border); border-radius:10px; padding:0 12px; font:inherit; outline:none;
    }
    .search-wrap input:focus{box-shadow:0 0 0 3px var(--ring); border-color:#b6d6ff}

    /* Filter dropdown (like screenshot) */
    .filter-wrap{position:relative}
    .filter-menu{
      display:none; position:absolute; top:calc(100% + 8px); left:0; background:#fff; border:1px solid var(--border);
      border-radius:12px; box-shadow:var(--shadow); min-width:260px; padding:8px; z-index:5;
    }
    .filter-option{
      display:flex; gap:10px; align-items:flex-start; padding:10px; border-radius:10px; cursor:pointer;
    }
    .filter-option:hover{background:#f3f6ff}
    .filter-option input{margin-top:2px}
    .fo-text{display:flex; flex-direction:column}
    .fo-title{font-weight:700; color:#102b54}
    .fo-sub{font-size:12px; color:#6c7b90}

    /* Section + grid */
    .section{background:#fff; border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow); padding:18px; margin-bottom:18px}
    .section h2{margin:0 0 14px; text-align:center; color:#102b54}
    .grid{display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:14px}
    .card{
      border:1px solid var(--border); border-radius:12px; background:#fff; padding:14px; display:flex; flex-direction:column; align-items:center; gap:10px;
    }
    .pos-label{font-size:12px; color:#6b7892; font-weight:700}
    .bar-rail{position:relative; height:160px; width:30px; background:#edf2f7; border-radius:6px; overflow:hidden; display:flex; align-items:flex-end; justify-content:center}
    .bar-fill{width:100%; background:#0a2e5c; border-radius:6px 6px 0 0}
    .bar-tick{position:absolute; left:0; width:100%; height:1px; background:rgba(0,0,0,.12)}
    .bar-tick.t1{bottom:25%} .bar-tick.t2{bottom:50%} .bar-tick.t3{bottom:75%} .bar-tick.t4{bottom:100%}
    .votes{font-size:12px; font-weight:700}
    .avatar{width:60px; height:60px; border-radius:50%; object-fit:cover; border:2px solid #0a2e5c}
    .name{font-weight:700; text-align:center}
    .pill{display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; border:1px solid #e7c8cc; background:#ffe8ea; color:#b61e1e; font-weight:700; cursor:pointer}
    .pill i{width:14px; text-align:center}

    /* Modal overlay (real overlay, not inline) */
    .modal-overlay{position:fixed; inset:0; display:none; align-items:center; justify-content:center; padding:16px; background:rgba(0,0,0,.35); z-index:1000}
    .modal-overlay.show{display:flex}
    .modal-box{background:#fff; width:min(860px, 96vw); max-height:90vh; overflow:auto; border-radius:16px; border:1px solid var(--border); box-shadow:var(--shadow)}
    .modal-head{display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--border)}
    .modal-body{padding:16px}
    .close-btn{border:1px solid var(--border); background:#f6f8fc; border-radius:8px; width:30px; height:30px; cursor:pointer}

    footer{text-align:center; padding:18px 0; color:var(--muted); font-size:14px}

    @media (max-width:768px){
      header{flex-direction:column; align-items:stretch; gap:8px}
      nav{flex-direction:column; gap:6px}
      .nav-item{width:100%}
      .nav-item > a{width:100%}
      .dropdown{position:relative; top:0; left:0; margin:6px 0 0 0; box-shadow:none}
      .submenu{position:relative}
    }
  </style>
</head>
<body>

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

    <!-- tools row -->
    <div class="tools">
      <div class="filter-wrap">
        <button class="btn" id="filterBtn" type="button">
          <i class="fa fa-filter"></i> Filter By Position <i class="fa fa-caret-down" aria-hidden="true"></i>
        </button>
        <div class="filter-menu" id="filterMenu" role="menu" aria-label="Filter positions">
          <!-- All -->
          <label class="filter-option">
            <input type="radio" name="posFilter" value="__ALL__" checked />
            <div class="fo-text">
              <span class="fo-title">All</span>
              <span class="fo-sub">Show all positions</span>
            </div>
          </label>
          <?php foreach($positions as $p): ?>
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

      <div class="search-wrap">
        <input type="search" id="searchBox" placeholder="Search candidate..." />
      </div>
    </div>

    <?php
      // Render sections for each position
      foreach ($positions as $position):
        $cands = fetchCandidatesByPosition($conn, $position);
        if (!$cands) continue;
        $max_votes = max(array_column($cands, 'vote_count'));
    ?>
      <section class="section position-section" data-position="<?php echo htmlspecialchars($position); ?>">
        <h2><?php echo htmlspecialchars($position); ?></h2>
        <div class="grid">
          <?php foreach($cands as $cand):
            $h = ($max_votes > 0) ? round(($cand['vote_count'] / $max_votes) * 100, 2) : 0;
            $fullName = $cand['FirstName'].' '.$cand['LastName'];
          ?>
            <article class="card candidate-card"
                     data-name="<?php echo htmlspecialchars(strtolower($fullName)); ?>">
              <div class="pos-label"><?php echo htmlspecialchars($position); ?></div>
              <div class="bar-rail">
                <div class="bar-tick t1"></div>
                <div class="bar-tick t2"></div>
                <div class="bar-tick t3"></div>
                <div class="bar-tick t4"></div>
                <div class="bar-fill" style="height: <?php echo $h; ?>%;"></div>
              </div>
              <div class="votes"><?php echo (int)$cand['vote_count']; ?> votes</div>
              <img class="avatar" src="<?php echo $cand['Photo']; ?>" alt="<?php echo htmlspecialchars($fullName); ?>" />
              <div class="name"><?php echo htmlspecialchars($fullName); ?></div>

              <button class="pill view-btn"
                data-photo="<?php echo htmlspecialchars($cand['Photo']); ?>"
                data-name="<?php echo htmlspecialchars($fullName); ?>"
                data-position="<?php echo htmlspecialchars($cand['Position']); ?>"
                data-year="<?php echo htmlspecialchars($cand['Year']); ?>"
                data-party="<?php echo htmlspecialchars($cand['Party']); ?>"
                data-votes="<?php echo (int)$cand['vote_count']; ?>"
                data-qual="<?php echo htmlspecialchars($cand['Qualification']); ?>">
                <i class="fa fa-eye"></i> View
              </button>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>

    <footer>© 2025 Online Election Voting System</footer>
  </div>
</main>

<!-- Modal -->
<div id="candidateModal" class="modal-overlay" aria-hidden="true">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-head">
      <strong id="modalTitle">Candidate Information</strong>
      <button class="close-btn" id="modalClose" aria-label="Close">&times;</button>
    </div>
    <div class="modal-body" id="modalBody"></div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
  // Filter dropdown open/close
  (function(){
    const btn = document.getElementById('filterBtn');
    const menu = document.getElementById('filterMenu');
    btn.addEventListener('click', (e)=>{ e.stopPropagation(); menu.style.display = menu.style.display==='block'?'none':'block'; });
    document.addEventListener('click', (e)=>{ if(!e.target.closest('.filter-wrap')) menu.style.display='none'; });
  })();

  // Search + position filter (like screenshot behavior)
  (function(){
    const search = document.getElementById('searchBox');
    const radios = document.querySelectorAll('input[name="posFilter"]');
    const sections = Array.from(document.querySelectorAll('.position-section'));

    function apply(){
      const q = search.value.trim().toLowerCase();
      const selected = document.querySelector('input[name="posFilter"]:checked')?.value || '__ALL__';

      sections.forEach(sec => {
        const pos = sec.dataset.position;
        let any = false;
        // filter each candidate card inside
        sec.querySelectorAll('.candidate-card').forEach(card => {
          const matchText = !q || card.dataset.name.includes(q);
          const show = matchText;
          card.style.display = show ? '' : 'none';
          if(show) any = true;
        });
        // hide section if no card matches OR position mismatched
        const posOk = (selected === '__ALL__' || selected === pos);
        sec.style.display = (any && posOk) ? '' : 'none';
      });
    }
    search.addEventListener('input', apply);
    radios.forEach(r => r.addEventListener('change', () => {
      document.getElementById('filterMenu').style.display='none';
      apply();
    }));
    apply();
  })();

  // Modal behavior
  (function(){
    const overlay = document.getElementById('candidateModal');
    const closeBtn = document.getElementById('modalClose');
    const body = document.getElementById('modalBody');

    function openModal(data){
      body.innerHTML = `
        <div style="display:flex; flex-wrap:wrap; gap:20px; align-items:flex-start;">
          <div style="flex:0 0 200px; text-align:center;">
            <img src="${data.photo}" alt="Photo" style="width:100%; max-width:200px; height:auto; border-radius:12px; border:3px solid #e9eff7;">
            <p style="margin-top:10px; font-weight:800; color:#0a2e5c;">${data.name}</p>
          </div>
          <div style="flex:1; min-width:240px;">
            <p><strong>Position:</strong> ${data.position || '-'}</p>
            <p><strong>Year:</strong> ${data.year || '-'}</p>
            <p><strong>Party:</strong> ${data.party || '-'}</p>
            <p><strong>Total Votes:</strong> ${data.votes || 0}</p>
            <p><strong>Qualification:</strong><br>${(data.qual||'').replace(/\n/g,'<br>')}</p>
          </div>
        </div>`;
      overlay.classList.add('show');
      overlay.setAttribute('aria-hidden','false');
    }
    function closeModal(){
      overlay.classList.remove('show');
      overlay.setAttribute('aria-hidden','true');
    }

    document.addEventListener('click', (e)=>{
      const btn = e.target.closest('.view-btn');
      if(btn){
        e.preventDefault();
        openModal({
          photo: btn.dataset.photo || '',
          name: btn.dataset.name || '',
          position: btn.dataset.position || '',
          year: btn.dataset.year || '',
          party: btn.dataset.party || '',
          votes: btn.dataset.votes || '0',
          qual: btn.dataset.qual || ''
        });
      }
    });
    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', (e)=>{ if(e.target === overlay) closeModal(); });
    document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape') closeModal(); });
  })();
</script>
</body>
</html>
