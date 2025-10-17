<?php
include('session.php');
include('dbcon.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Vice-Governor - Canvassing Report</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root {
      --primary-color: #002f6c;
      --accent-color: #0056b3;
      --bg-color: #f4f6f8;
      --white: #fff;
      --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      --transition: all 0.3s ease;
      --font: 'Inter', sans-serif;
    }
    body { font-family: var(--font); background: var(--bg-color); margin:0; color:#333; }

    /* === Fixed, smart-hide header === */
    header{
      position: fixed;
      top:0; left:0; right:0;
      z-index:1000;
      background:var(--white);
      box-shadow:var(--shadow);
      padding:10px 30px;
      display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;
      transform:translateY(0);
      transition: transform 220ms ease-in-out, box-shadow 220ms ease-in-out, background 220ms ease-in-out;
    }
    .header--hidden{ transform: translateY(-100%); }
    .header--scrolled{ box-shadow: 0 6px 18px rgba(0,0,0,.08); background: var(--white); }

    .logo-section { display:flex; align-items:center; gap:10px; }
    .logo-section img { height:40px; }
    .logo-section .title { font-weight:700; font-size:18px; color:var(--primary-color); line-height:1.2; }

    nav { display:flex; align-items:center; gap:25px; }
    .nav-item { position:relative; }
    .nav-item > a {
      text-decoration:none; color:var(--primary-color); font-weight:600;
      padding:8px 12px; border-radius:6px; display:inline-block; transition:var(--transition);
    }
    .nav-item > a:hover { background:var(--primary-color); color:#fff; }

    .dropdown {
      display:none; position:absolute; top:100%; left:0; background:var(--white);
      box-shadow:var(--shadow); border-radius:6px; min-width:200px; padding:8px 0; z-index:99;
    }
    .nav-item:hover > .dropdown { display:block; }
    .dropdown a {
      display:block; padding:10px 15px; text-decoration:none; color:var(--primary-color);
      font-weight:500; transition:var(--transition); white-space:nowrap;
    }
    .dropdown a:hover { background:var(--accent-color); color:#fff; }

    .submenu {
      display:none; position:absolute; top:0; left:100%; background:var(--white);
      box-shadow:var(--shadow); border-radius:6px; min-width:220px; padding:8px 0;
    }
    .has-submenu { position:relative; }
    .has-submenu > a { display:flex; justify-content:space-between; align-items:center; }
    .has-submenu > a i.fa-chevron-right { font-size:12px; margin-left:8px; }
    .has-submenu:hover > .submenu { display:block; }

    /* Spacer to offset fixed header */
    #header-spacer{height:64px}

    .content-wrapper { max-width:1400px; margin:30px auto; padding:0 20px; }

    /* ===== Toolbar row (left-aligned, one line) ===== */
    .filter-section {
      background:var(--white);
      padding:15px 20px;
      border-radius:10px;
      margin-bottom:15px;
      box-shadow:var(--shadow);
      display:flex;
      align-items:center;
      gap:10px;
      justify-content:flex-start;
      flex-wrap:wrap;
    }

    .filter-dropdown { position:relative; }

    /* Outline filter (NO shadow) */
    .filter-btn {
      background:#fff;
      color:var(--primary-color);
      border:1.5px solid var(--primary-color);
      padding:9px 14px;
      border-radius:6px;
      cursor:pointer;
      font-weight:700;
      display:flex; align-items:center; gap:8px;
      transition:var(--transition);
      font-size:14px;
      box-shadow:none !important;
    }
    .filter-btn:hover { background:#eef4ff; transform:none; box-shadow:none !important; }

    .filter-dropdown-menu {
      display:none; position:absolute; top:100%; left:0; background:var(--white);
      box-shadow:var(--shadow); border-radius:6px; min-width:220px; padding:8px 0; margin-top:6px; z-index:10;
    }
    /* click-to-open (replaces old hover behavior) */
    .filter-dropdown.open .filter-dropdown-menu { display:block; }

    .filter-dropdown-menu a {
      display:flex; align-items:center; gap:10px; padding:10px 15px; color:var(--primary-color);
      text-decoration:none; font-weight:500; transition:var(--transition);
    }
    .filter-dropdown-menu a:hover { background:var(--accent-color); color:#fff; }

    /* Add = blue */
    .add-btn {
      background:#0056b3; color:#fff; border:1px solid #004a9a;
      padding:9px 14px; border-radius:6px; cursor:pointer; font-weight:700;
      display:flex; align-items:center; gap:8px; transition:var(--transition); font-size:14px; box-shadow:none;
    }
    .add-btn:hover { background:#004a9a; transform:none; }

    /* Download = green, NO shadow */
    .btn-success {
      background:#16a34a; color:#fff; border:1px solid #138a3e;
      padding:9px 14px; border-radius:6px; font-weight:700; display:inline-flex; align-items:center; gap:8px;
      font-size:14px; cursor:pointer; box-shadow:none !important;
    }
    .btn-success:hover { background:#138a3e; transform:none; }

    .table-container { background:var(--white); border-radius:10px; box-shadow:var(--shadow); overflow:hidden; margin-top:20px; }
    .table-controls {
      padding:20px; display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #f0f0f0;
      gap:15px; flex-wrap:wrap; background:#fafbfc;
    }
    .items-per-page, .search-box { display:flex; align-items:center; gap:10px; color:#666; font-size:14px; }
    .items-per-page select, .search-box input { padding:8px 12px; border:1px solid #ddd; border-radius:6px; background:#fff; }

    table { width:100%; border-collapse:collapse; min-width:800px; }
    thead { background:linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%); }
    th { padding:16px; color:#fff; text-transform:uppercase; letter-spacing:.5px; font-weight:600; font-size:14px; text-align:left; }
    td { padding:16px; border-bottom:1px solid #f0f0f0; font-size:14px; vertical-align:middle; }
    tbody tr:hover { background:#f8f9fa; transform:scale(1.001); }

    .candidate-photo { width:50px; height:50px; border-radius:50%; object-fit:cover; border:3px solid #e0e0e0; transition:var(--transition); }
    .candidate-photo:hover { transform:scale(1.1); border-color:var(--accent-color); }

    footer { text-align:center; padding:20px 0; color:#666; font-size:14px; }

    @media (max-width:768px){
      header { flex-direction:column; align-items:flex-start; }
      nav { flex-direction:column; gap:8px; width:100%; }
      .filter-section { flex-direction:column; align-items:stretch; }
      .filter-btn, .add-btn, .btn-success { width:100%; justify-content:center; }
      .table-container { overflow-x:auto; }
      table { min-width:700px; }
    }
  </style>
</head>
<body>

  <header>
    <div class="logo-section">
      <img src="images/au.png" alt="Logo">
      <div class="title">
        ONLINE ELECTION VOTING SYSTEM<br>
        <small>Phinma Araullo University</small>
      </div>
    </div>

    <nav>
      <div class="nav-item"><a href="home.php"><i class="fas fa-home"></i> Home</a></div>

      <?php if (file_exists('nav_menu_dropdown.php')) { include 'nav_menu_dropdown.php'; } else { ?>
        <div class="nav-item">
          <a href="#"><i class="fas fa-list-ul"></i> Menu</a>
          <div class="dropdown">
            <a href="candidate_list.php">Candidates</a>
            <a href="voter_list.php">Voters</a>
            <div class="has-submenu">
              <a href="#">Admin Actions <i class="fa fa-chevron-right"></i></a>
              <div class="submenu">
                <a href="result.php"><i class="fa fa-table" style="margin-right:8px;"></i> Election Result</a>
                <a href="winningresult.php"><i class="fa fa-trophy" style="margin-right:8px;"></i> Final Result</a>
                <a href="backupnreset.php"><i class="fa fa-database" style="margin-right:8px;"></i> Backup and Reset</a>
                <a href="dashboard.php"><i class="fa fa-chart-bar" style="margin-right:8px;"></i> Analytics</a>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>

      <div class="nav-item">
        <a href="#"><i class="fas fa-user-circle"></i> Profile</a>
        <div class="dropdown"><a href="profile.php">View Profile</a></div>
      </div>

      <div class="nav-item">
        <a href="#"><i class="fas fa-info-circle"></i> About</a>
        <div class="dropdown"><a href="about.php">System Info</a><a href="contact.php">Contact Us</a></div>
      </div>

      <div class="nav-item"><a href="logout.php" style="color:red;"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </nav>
  </header>

  <!-- spacer to offset fixed header height -->
  <div id="header-spacer" aria-hidden="true"></div>

  <div class="content-wrapper">
    <!-- Toolbar row: Filter • Add • Download -->
    <div class="filter-section">
      <div class="filter-dropdown">
        <button class="filter-btn" type="button" id="filterToggle" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-filter"></i> Filter By Position <i class="fas fa-chevron-down"></i>
        </button>
        <div class="filter-dropdown-menu" role="menu" aria-label="Filter by position">
          <a href="canvassing_report.php"><i class="fas fa-list"></i> All</a> 
          <a href="C_President.php"><i class="fas fa-user-tie"></i> President</a>
          <a href="C_Vice-President.php"><i class="fas fa-user"></i> Vice-President</a>
          <a href="C_Governor.php"><i class="fas fa-landmark"></i> Governor</a>
          <a href="C_Vice-Governor.php"><i class="fas fa-user-shield"></i> Vice-Governor</a>
          <a href="C_Secretary.php"><i class="fas fa-pen"></i> Secretary</a>
          <a href="C_Treasurer.php"><i class="fas fa-coins"></i> Treasurer</a>
          <a href="C_Socialmediaofficer.php"><i class="fas fa-share-alt"></i> Social-Media Officer</a>
          <a href="C_Representative.php"><i class="fas fa-users"></i> Representative</a>
        </div>
      </div>

      <button class="add-btn" onclick="window.location.href='add_candidate.php'">
        <i class="fas fa-plus"></i> Add Candidates
      </button>

      <?php
        // id for Excel export (kept same logic)
        $q = mysqli_query($conn, "SELECT CandidateID FROM candidate LIMIT 1");
        $r = mysqli_fetch_array($q);
        $id_excel = $r ? $r['CandidateID'] : '';
      ?>
      <form method="POST" action="canvassing_excel.php" style="display:inline;">
        <input type="hidden" name="id_excel" value="<?php echo htmlspecialchars($id_excel); ?>">
        <button class="btn-success" name="save" type="submit">
          <i class="fas fa-download"></i> Download Excel File
        </button>
      </form>
    </div>

    <div class="table-container">
      <div class="table-controls">
        <div class="items-per-page">
          <label>Items per page:</label>
          <select id="itemsPerPage">
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>

        <div class="search-box">
          <label>Search:</label>
          <input type="text" id="searchInput" placeholder="type here...">
        </div>
      </div>

      <table id="reportTable">
        <thead>
          <tr>
            <th>Position</th>
            <th>Party</th>
            <th>FirstName</th>
            <th>LastName</th>
            <th>Year</th>
            <th>Photo</th>
            <th>No. of Votes</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $candidate_query = mysqli_query($conn, "SELECT * FROM candidate WHERE Position='Vice-Governor'");
          while ($row = mysqli_fetch_array($candidate_query)) {
            $id = $row['CandidateID'];
          ?>
            <tr>
              <td><?php echo htmlspecialchars($row['Position']); ?></td>
              <td><?php echo htmlspecialchars($row['Party']); ?></td>
              <td><?php echo htmlspecialchars($row['FirstName']); ?></td>
              <td><?php echo htmlspecialchars($row['LastName']); ?></td>
              <td><?php echo htmlspecialchars($row['Year']); ?></td>
              <td>
                <img class="candidate-photo"
                     src="<?php echo htmlspecialchars($row['Photo']); ?>"
                     alt="<?php echo htmlspecialchars($row['FirstName'].' '.$row['LastName']); ?>"
                     title="<?php echo htmlspecialchars($row['FirstName'].' '.$row['LastName']); ?>">
              </td>
              <td style="text-align:center;">
                <?php
                  $votes_q = mysqli_query($conn, "SELECT 1 FROM votes WHERE CandidateID='$id'");
                  echo mysqli_num_rows($votes_q);
                ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <footer>© 2025 Online Election Voting System</footer>

  <!-- Search + items-per-page that cooperate -->
  <script>
    (function(){
      const searchInput = document.getElementById('searchInput');
      const itemsSelect = document.getElementById('itemsPerPage');
      const tbody = document.getElementById('reportTable').tBodies[0];

      function applyPerPage(){
        const per = parseInt(itemsSelect.value,10);
        let shown = 0;
        Array.from(tbody.rows).forEach((r)=>{
          const okBySearch = r.dataset._searchMatch !== '0'; // set in applySearch
          if (!okBySearch) { r.style.display = 'none'; return; }
          if (shown < per) { r.style.display = ''; shown++; }
          else { r.style.display = 'none'; }
        });
      }

      function applySearch(){
        const q = (searchInput.value || '').toLowerCase();
        Array.from(tbody.rows).forEach((r)=>{
          const match = r.textContent.toLowerCase().includes(q);
          r.dataset._searchMatch = match ? '1' : '0';
        });
        setTimeout(applyPerPage, 0);
      }

      if (searchInput && tbody) searchInput.addEventListener('input', applySearch);
      if (itemsSelect && tbody) itemsSelect.addEventListener('change', applyPerPage);

      // initial run
      setTimeout(()=>{ applySearch(); }, 0);
    })();
  </script>

  <!-- Smart header behavior: hide on scroll down, show on scroll up -->
  <script>
    (function () {
      const header = document.querySelector('header');
      const spacer = document.getElementById('header-spacer');

      function setSpacerHeight(){
        spacer.style.height = header.offsetHeight + 'px';
      }
      setSpacerHeight();
      window.addEventListener('resize', setSpacerHeight);

      let lastY = window.scrollY;
      let ticking = false;

      function onScroll(){
        const y = window.scrollY;
        if (y > 4) header.classList.add('header--scrolled'); else header.classList.remove('header--scrolled');
        if (y > lastY && y > header.offsetHeight) header.classList.add('header--hidden');
        else header.classList.remove('header--hidden');
        lastY = y;
        ticking = false;
      }
      window.addEventListener('scroll', function(){
        if (!ticking) { window.requestAnimationFrame(onScroll); ticking = true; }
      }, {passive:true});
    })();
  </script>

  <!-- Clickable filter dropdown (toggle, outside-click + Esc to close) -->
  <script>
    (function(){
      const dd  = document.querySelector('.filter-dropdown');
      const btn = document.getElementById('filterToggle');

      if (!dd || !btn) return;

      function closeAll(){
        dd.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
      }

      btn.addEventListener('click', function(e){
        e.stopPropagation();
        const willOpen = !dd.classList.contains('open');
        dd.classList.toggle('open', willOpen);
        btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
      });

      document.addEventListener('click', function(e){
        if (!dd.contains(e.target)) closeAll();
      });

      document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') closeAll();
      });
    })();
  </script>
</body>
</html>
