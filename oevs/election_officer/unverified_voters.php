<?php
include('session.php');
include('dbcon.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Unverified Voters - Online Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    :root{
      --primary:#002f6c; --accent:#0056b3; --bg:#f4f6f8; --white:#fff; --ink:#0d2343;
      --muted:#6c7b90; --border:#e6ebf4; --shadow:0 8px 24px rgba(0,0,0,.08); --ring:#9ec5ff;
      --success:#e6f6ec; --success-ink:#127c41; --table-head:#0a2e5c;
      --warn:#fff7e6; --warn-ink:#9a6b00; --warn-br:#ffe0a3;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);margin:0;color:var(--ink)}
    a{text-decoration:none;color:inherit}

    /* ===== Header (smart sticky) ===== */
    header.smart-header{
      background:var(--white); box-shadow:var(--shadow); border-bottom:1px solid var(--border);
      padding:10px 22px; display:flex; justify-content:space-between; align-items:center; gap:16px;
      position:sticky; top:0; z-index:1000; transition:transform .25s ease; will-change:transform;
    }
    header.smart-header.header-hide{ transform:translateY(-120%); }
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

    /* Dropdowns */
    .dropdown,.submenu{
      display:none; position:absolute; top:calc(100% - 2px); left:0; min-width:240px; background:#fff;
      border:1px solid #e7eef7; border-radius:14px; box-shadow:0 10px 30px rgba(13,35,67,.12), 0 2px 6px rgba(13,35,67,.06);
      padding:6px; z-index:999;
    }
    .nav-item:hover > .dropdown,.nav-item:focus-within > .dropdown{display:block}
    .dropdown a,.submenu a{display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; color:var(--primary); font-weight:600; white-space:nowrap}
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
    .card{background:#fff; border:1px solid var(--border); border-radius:16px; box-shadow:var(--shadow); padding:14px; overflow:visible}

    /* Toolbar */
    .toolbar{display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding:6px 8px 12px}
    .left-actions{display:flex; align-items:center; gap:10px}
    .right-actions{display:flex; align-items:center; gap:10px}
    .btn{display:inline-flex; align-items:center; gap:8px; border:1px solid var(--border); background:#f7f9fc; color:#0b1b36; font-weight:700; border-radius:10px; padding:9px 12px; cursor:pointer}
    .btn:hover{background:#eef4ff}

    .filter-wrap{position:relative}
    .filter-menu{display:none; position:absolute; top:calc(100% + 6px); left:0; background:#fff; border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow); min-width:240px; padding:6px; z-index:5}
    .filter-menu a{display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; font-weight:600; color:#0b1b36}
    .filter-menu a:hover{background:var(--accent); color:#fff}

    .control{display:flex; align-items:center; gap:10px}
    select,input[type="search"]{height:36px; border:1px solid var(--border); border-radius:10px; padding:0 10px; font:inherit; outline:none}
    select:focus,input[type="search"]:focus{box-shadow:0 0 0 3px var(--ring); border-color:#b6d6ff}

    table{width:100%; border-collapse:collapse}
    thead th{background:var(--table-head); color:#fff; text-align:left; font-weight:700; padding:12px 14px; font-size:14px}
    tbody td{padding:14px; border-bottom:1px solid #edf1f7; vertical-align:middle; font-size:14px}
    tbody tr:hover{background:#fbfdff}

    .badge{display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; font-weight:700; font-size:12px}
    .badge-success{background:var(--success); color:var(--success-ink); border:1px solid #bfe9cf}
    .badge-warn{background:var(--warn); color:var(--warn-ink); border:1px solid var(--warn-br)}

    .btn-verify{display:inline-flex; align-items:center; gap:6px; padding:8px 12px; background:#e6f6ec; color:#127c41; border:1px solid #bfe9cf; border-radius:999px; font-weight:700}
    .btn-verify i{width:14px}

    .table-footer{display:flex; align-items:center; justify-content:flex-end; gap:10px; padding:12px 8px 4px; color:#445c7a; font-size:14px}
    .pager{display:inline-flex; align-items:center; gap:6px}
    .pager button{width:34px; height:34px; border:1px solid var(--border); border-radius:8px; background:#fff; cursor:pointer}
    .pager button:hover{background:#f1f6ff}

    footer{text-align:center; padding:20px 0; color:var(--muted); font-size:14px}

    @media (max-width:768px){
      header.smart-header{flex-direction:column; align-items:stretch; gap:8px}
      nav{flex-direction:column; gap:6px}
      .nav-item{width:100%}
      .nav-item > a{width:100%}
      .dropdown{position:relative; top:0; left:0; margin:6px 0 0 0; box-shadow:none}
      .submenu{position:relative}
      .toolbar{flex-direction:column; align-items:stretch}
      .right-actions{justify-content:space-between}
    }
  </style>
</head>
<body>

<!-- ===== Header ===== -->
<header class="smart-header">
  <div class="logo-section">
    <img src="images/au.png" alt="Logo" />
    <div class="title">
      ONLINE ELECTION VOTING SYSTEM<br />
      <small>Phinma Araullo University</small>
    </div>
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
    <div class="card">
      <!-- ===== Toolbar ===== -->
      <div class="toolbar">
        <div class="left-actions">
          <div class="filter-wrap">
            <button class="btn" id="filterBtn" type="button">
              <i class="fa fa-filter"></i> Filter By Position <i class="fa fa-caret-down" aria-hidden="true"></i>
            </button>
            <div class="filter-menu" id="filterMenu">
              <a href="voter_verification.php"><i class="fa fa-table"></i> All</a>
              <a href="verified_voters.php"><i class="fa fa-circle-check"></i> Verified Voters</a>
              <a href="unverified_voters.php"><i class="fa fa-circle"></i> Unverified Voters</a>
            </div>
          </div>
        </div>

        <div class="right-actions">
          <div class="control">
            <label for="pageSize">Items per page:</label>
            <select id="pageSize">
              <option>5</option><option>10</option><option selected>15</option><option>25</option><option>50</option>
            </select>
          </div>
          <input type="search" id="tableSearch" placeholder="Search..." />
        </div>
      </div>

      <!-- ===== Table ===== -->
      <div class="demo_jui">
        <table id="votersTable">
          <thead>
            <tr>
              <th>FirstName</th>
              <th>LastName</th>
              <th>MiddleName</th>
              <th>Phinma Email</th>
              <th>Student ID</th>
              <th>Year</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="tableBody">
            <?php
              $voter_query = mysqli_query($conn, "SELECT * FROM voters WHERE Verified='Not Verified'");
              while ($voter_rows = mysqli_fetch_array($voter_query)):
                $id = $voter_rows['VoterID'];
            ?>
            <tr class="del<?php echo $id; ?>">
              <td><?php echo htmlspecialchars($voter_rows['FirstName']); ?></td>
              <td><?php echo htmlspecialchars($voter_rows['LastName']); ?></td>
              <td><?php echo htmlspecialchars($voter_rows['MiddleName']); ?></td>
              <td><?php echo htmlspecialchars($voter_rows['Username']); ?></td>
              <td><?php echo htmlspecialchars($voter_rows['SchoolID']); ?></td>
              <td align="center"><?php echo htmlspecialchars($voter_rows['Year']); ?></td>
              <td align="center"><span class="badge badge-warn">Not Verified</span></td>
              <td align="center">
                <a class="btn-verify verify-btn" data-id="<?php echo $id; ?>">
                  <i class="fa fa-check"></i> Verify
                </a>
                <input type="hidden" name="data_name" class="data_name<?php echo $id; ?>" value="<?php echo htmlspecialchars($voter_rows['FirstName'].' '.$voter_rows['LastName']); ?>"/>
                <input type="hidden" name="user_name" class="user_name" value="<?php echo htmlspecialchars($_SESSION['User_Type']); ?>"/>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <!-- ===== Footer / Pagination ===== -->
      <div class="table-footer">
        <div id="showingText">Showing 0–0 of 0</div>
        <div class="pager">
          <button id="prevBtn" aria-label="Previous"><i class="fa fa-chevron-left"></i></button>
          <button id="nextBtn" aria-label="Next"><i class="fa fa-chevron-right"></i></button>
        </div>
      </div>
    </div>

    <footer>© 2025 Online Election Voting System</footer>
  </div>
</main>

<input type="hidden" class="pc_date" name="pc_date"/>
<input type="hidden" class="pc_time" name="pc_time"/>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
  // Smart header
  (function(){
    const header = document.querySelector('header.smart-header');
    let lastY = window.pageYOffset || 0;
    window.addEventListener('scroll', () => {
      const y = window.pageYOffset || document.documentElement.scrollTop;
      if (y > 80 && y > lastY) header.classList.add('header-hide');
      else header.classList.remove('header-hide');
      lastY = y <= 0 ? 0 : y;
    }, { passive:true });
  })();

  // Filter dropdown
  (function(){
    const btn = document.getElementById('filterBtn');
    const menu = document.getElementById('filterMenu');
    btn.addEventListener('click', (e)=>{ e.stopPropagation(); menu.style.display = menu.style.display==='block'?'none':'block'; });
    document.addEventListener('click', (e)=>{ if(!e.target.closest('.filter-wrap')) menu.style.display='none'; });
  })();

  // Client-side search + pagination
  (function(){
    const tbody = document.getElementById('tableBody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const search = document.getElementById('tableSearch');
    const pageSizeSel = document.getElementById('pageSize');
    const showingText = document.getElementById('showingText');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    let page = 1;
    const getSize = () => parseInt(pageSizeSel.value,10);
    const filtered = () => {
      const q = search.value.trim().toLowerCase();
      if(!q) return rows;
      return rows.filter(tr => tr.textContent.toLowerCase().includes(q));
    };
    function render(){
      const list = filtered();
      const size = getSize();
      const total = list.length;
      const pages = Math.max(1, Math.ceil(total/size));
      if(page>pages) page = pages;

      rows.forEach(tr => tr.style.display='none');
      const start = (page-1)*size;
      const end = Math.min(total, start+size);
      for(let i=start;i<end;i++) list[i].style.display='table-row';

      showingText.textContent = total ? `Showing ${start+1}–${end} of ${total}` : 'Showing 0–0 of 0';
      prevBtn.disabled = page<=1;
      nextBtn.disabled = page>=pages;
    }
    search.addEventListener('input', ()=>{ page=1; render(); });
    pageSizeSel.addEventListener('change', ()=>{ page=1; render(); });
    prevBtn.addEventListener('click', ()=>{ if(page>1){ page--; render(); }});
    nextBtn.addEventListener('click', ()=>{ page++; render(); });
    render();
  })();

  // Timestamp helpers
  $(function(){
    const d = new Date();
    $(".pc_date").val(`${d.getMonth()+1}/${d.getDate()}/${d.getFullYear()}`);
    $(".pc_time").val(`${d.getHours()}:${d.getMinutes()}:${d.getSeconds()}`);
  });

  // Verify voter
  $('.demo_jui').on('click', '.verify-btn', function(e){
    e.preventDefault();
    const id = $(this).data('id');
    if(!confirm("Are you sure you want to verify this voter?")) return;

    $.post('verify_voter.php', {
      id,
      pc_time: $('.pc_time').val(),
      pc_date: $('.pc_date').val(),
      data_name: $('.data_name'+id).val(),
      user_name: $('.user_name').val()
    }, function(resp){
      // remove row after success (simple UX)
      $(".del"+id).fadeOut('slow');
    });
  });
</script>
</body>
</html>
