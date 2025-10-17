<?php
include('session.php');
include('dbcon.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Voter List - Online Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root{
      --primary-color:#002f6c;   /* same palette as home.php */
      --accent-color:#0056b3;
      --bg-color:#f4f6f8;
      --white:#fff;
      --muted:#6b7280;
      --danger:#e11d48;
      --success:#16a34a;
      --shadow:0 4px 12px rgba(0,0,0,.1);
      --radius:10px;
      --transition:all .25s ease;
      --font:'Inter',sans-serif;
    }
    body{font-family:var(--font);background:var(--bg-color);margin:0}
    /* ====== Header (copied from home.php structure) ====== */
    header{
      background:var(--white);
      box-shadow:var(--shadow);
      padding:10px 30px;
      display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;position:relative;z-index:10
    }
    .logo-section{display:flex;align-items:center;gap:10px}
    .logo-section img{height:40px}
    .logo-section .title{font-weight:700;font-size:18px;color:var(--primary-color);line-height:1.2}
    nav{display:flex;align-items:center;gap:25px}
    .nav-item{position:relative}
    .nav-item>a{text-decoration:none;color:var(--primary-color);font-weight:600;padding:8px 12px;border-radius:6px;display:inline-block;transition:var(--transition)}
    .nav-item>a:hover{background:var(--primary-color);color:#fff}
    .dropdown{display:none;position:absolute;top:100%;left:0;background:var(--white);box-shadow:var(--shadow);border-radius:8px;min-width:220px;padding:8px 0;z-index:99}
    .dropdown a{display:block;padding:10px 15px;text-decoration:none;color:var(--primary-color);font-weight:500;transition:var(--transition);white-space:nowrap}
    .dropdown a:hover{background:var(--accent-color);color:#fff}
    .nav-item:hover>.dropdown{display:block}
    .submenu{display:none;position:absolute;top:0;left:100%;background:var(--white);box-shadow:var(--shadow);border-radius:8px;min-width:240px;padding:8px 0}
    .has-submenu{position:relative}
    .has-submenu>a{display:flex;justify-content:space-between;align-items:center}
    .has-submenu>a i{font-size:12px;margin-left:8px}
    .has-submenu:hover>.submenu{display:block}
    @media (max-width:768px){
      header{flex-direction:column;align-items:flex-start}
      nav{flex-direction:column;width:100%;gap:0}
      .nav-item{width:100%}
      .nav-item>a{width:100%;box-sizing:border-box}
      .dropdown,.submenu{position:relative;box-shadow:none;left:0}
    }

    /* ====== Page frame ====== */
    .page{max-width:1200px;margin:24px auto;padding:0 16px}
    .card{
      background:var(--white);
      border-radius:var(--radius);
      box-shadow:var(--shadow);
      overflow:hidden
    }
    .toolbar{
      display:flex;gap:12px;align-items:center;justify-content:space-between;
      padding:18px; border-bottom:1px solid #e5e7eb; flex-wrap:wrap
    }
    .left-tools,.right-tools{display:flex;gap:12px;align-items:center;flex-wrap:wrap}
    .btn{
      display:inline-flex;align-items:center;gap:8px;
      border:1px solid #d1d5db;background:#fff;color:#111827;
      padding:10px 14px;border-radius:8px;cursor:pointer;font-weight:600;
      transition:var(--transition); text-decoration:none
    }
    .btn:hover{box-shadow:var(--shadow)}
    .btn-primary{background:var(--primary-color);color:#fff;border-color:var(--primary-color)}
    .btn-primary:hover{transform:translateY(-1px);background:#08306b}
    .btn-success{background:#10b981;color:#fff;border-color:#10b981}
    .btn-danger{background:var(--danger);color:#fff;border-color:var(--danger)}
    .btn-outline{background:#fff;color:var(--primary-color);border-color:var(--primary-color)}
    .btn .caret{font-size:12px;opacity:.8}

    /* Filter dropdown (button-triggered) */
    .filter-wrap{position:relative}
    .filter-menu{
      position:absolute;top:110%;left:0;min-width:220px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;
      box-shadow:var(--shadow);padding:8px;display:none;z-index:50
    }
    .filter-item{display:block;padding:10px 12px;border-radius:8px;color:#111827;text-decoration:none;font-weight:500}
    .filter-item:hover{background:var(--bg-color);color:#111827}

    /* Table */
    .table-wrap{padding:0 18px 18px}
    table{width:100%;border-collapse:separate;border-spacing:0}
    thead th{
      background:var(--primary-color);color:#fff;text-align:left;
      padding:12px;border-right:1px solid rgba(255,255,255,.1); font-weight:700
    }
    thead th:last-child{border-right:none}
    tbody td{background:#fff;padding:12px;border-bottom:1px solid #e5e7eb}
    tbody tr:hover td{background:#f9fafb}
    .badge{
      display:inline-block;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:700
    }
    .badge-green{background:#ecfdf5;color:#065f46}
    .badge-gray{background:#f3f4f6;color:#374151}

    /* Search + page size */
    .search-input{
      border:1px solid #d1d5db;border-radius:8px;padding:10px 12px;min-width:260px;outline:none
    }
    .select{border:1px solid #d1d5db;border-radius:8px;padding:10px 12px;background:#fff}
    .table-footer{display:flex;justify-content:flex-end;gap:12px;align-items:center;margin-top:12px;color:var(--muted)}
    footer{ text-align:center; padding:20px 0; color:#666; font-size:14px }

    /* utility */
    .hidden{display:none}
  </style>
</head>
<body>

  <!-- ===== Header from home.php ===== -->
  <header>
    <div class="logo-section">
      <img src="images/au.png" alt="Logo">
      <div class="title">
        ONLINE ELECTION VOTING SYSTEM<br>
        <small>Phinma Araullo University</small>
      </div>
    </div>
    <nav>
      <!-- HOME -->
      <div class="nav-item">
        <a href="home.php"><i class="fas fa-home"></i> Home</a>
      </div>

      <!-- MENU -->
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

      <!-- PROFILE -->
      <div class="nav-item">
        <a href="#"><i class="fas fa-user-circle"></i> Profile</a>
        <div class="dropdown">
          <a href="profile.php">View Profile</a>
        </div>
      </div>

      <!-- ABOUT -->
      <div class="nav-item">
        <a href="#"><i class="fas fa-info-circle"></i> About</a>
        <div class="dropdown">
          <a href="about.php">System Info</a>
          <a href="contact.php">Contact Us</a>
        </div>
      </div>

      <!-- LOGOUT -->
      <div class="nav-item">
        <a href="logout.php" style="color:red;"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </nav>
  </header>

  <!-- ===== Content ===== -->
  <div class="page">
    <div class="card">

      <!-- Toolbar -->
      <div class="toolbar">
        <div class="left-tools">
          <!-- Filter dropdown (styled like in 2nd pic) -->
          <div class="filter-wrap">
            <button class="btn btn-outline" id="filterBtn">
              <i class="fa-solid fa-filter"></i> Filter By Position <span class="caret"><i class="fa-solid fa-caret-down"></i></span>
            </button>
            <div class="filter-menu" id="filterMenu">
              <a href="voter_list.php" class="filter-item"><i class="fa-regular fa-rectangle-list" style="width:18px;"></i> All</a>
              <a href="Voted_voters.php" class="filter-item"><i class="fa-solid fa-check" style="width:18px;"></i> Voted Voters</a>
              <a href="Unvoted_voters.php" class="filter-item"><i class="fa-regular fa-circle" style="width:18px;"></i> Unvoted Voters</a>
            </div>
          </div>

          <!-- Add Voter -->
          <a href="new_voter.php" class="btn btn-primary">
            <i class="fa-solid fa-user-plus"></i> Add Voters
          </a>

          <!-- Download Excel -->
          <form method="POST" action="excel_voter.php" style="display:inline">
            <button id="excel" class="btn btn-success" name="save" type="submit">
              <i class="fa-solid fa-file-arrow-down"></i> Download Excel File
            </button>
          </form>
        </div>

        <div class="right-tools">
          <label for="pageSize" style="color:var(--muted);font-weight:600;">Items per page:</label>
          <select id="pageSize" class="select">
            <option value="15" selected>15</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>

          <input id="tableSearch" class="search-input" type="search" placeholder="Search..." />
        </div>
      </div>

      <!-- Table -->
      <div class="table-wrap">
        <div class="demo_jui">
          <table cellpadding="0" cellspacing="0" border="0" id="votersTable">
            <thead>
              <tr>
                <th>Date Voted</th>
                <th>Time Voted</th>
                <th>Phinma Email</th>
                <th>Student ID</th>
                <th>Year</th>
                <th>Status</th>
                <th style="width:120px">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $voter_query = mysqli_query($conn, "SELECT * FROM voters");
              while ($voter_rows = mysqli_fetch_array($voter_query)) {
                $id = $voter_rows['VoterID'];
                $status = trim(strtolower($voter_rows['Status'])) === 'voted' ? 'voted' : 'unvoted';
              ?>
                <tr class="del<?php echo $id; ?>">
                  <td><?php echo htmlspecialchars($voter_rows['DateVoted']); ?></td>
                  <td><?php echo htmlspecialchars($voter_rows['TimeVoted']); ?></td>
                  <td><?php echo htmlspecialchars($voter_rows['Username']); ?></td>
                  <td><?php echo htmlspecialchars($voter_rows['SchoolID']); ?></td>
                  <td><?php echo htmlspecialchars($voter_rows['Year']); ?></td>
                  <td>
                    <?php if ($status === 'voted'): ?>
                      <span class="badge badge-green">Voted</span>
                    <?php else: ?>
                      <span class="badge badge-gray">Unvoted</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <a class="btn btn-danger btn-delete" id="<?php echo $id; ?>">
                      <i class="fa-solid fa-trash"></i> Delete
                    </a>
                    <input type="hidden" name="data_name" class="data_name<?php echo $id; ?>" value="<?php echo htmlspecialchars($voter_rows['FirstName'] . ' ' . $voter_rows['LastName']); ?>" />
                    <input type="hidden" name="user_name" class="user_name" value="<?php echo htmlspecialchars($_SESSION['User_Type']); ?>" />
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>

        <!-- simple table footer for non-DataTables fallback -->
        <div class="table-footer hidden" id="fallbackFooter">
          <span id="rangeText"></span>
          <div>
            <button class="btn" id="prevPage"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="btn" id="nextPage"><i class="fa-solid fa-chevron-right"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer>© 2025 Online Election Voting System</footer>

  <!-- Hidden time fields preserved for your delete audit -->
  <input type="hidden" class="pc_date" name="pc_date" />
  <input type="hidden" class="pc_time" name="pc_time" />

  <script>
    // ===== Utilities: date/time for delete payload (same as your old page) =====
    (function(){
      var myDate=new Date();
      var pc_date=(myDate.getMonth()+1)+'/'+(myDate.getDate())+'/'+myDate.getFullYear();
      var pc_time=myDate.getHours()+':'+myDate.getMinutes()+':'+myDate.getSeconds();
      document.querySelector(".pc_date").value=pc_date;
      document.querySelector(".pc_time").value=pc_time;
    })();

    // ===== Filter dropdown toggle (button style like screenshot) =====
    (function(){
      const btn=document.getElementById('filterBtn');
      const menu=document.getElementById('filterMenu');
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
      });
      document.addEventListener('click', function(){ menu.style.display='none'; });
    })();

    // ===== Delete (AJAX) — jQuery or Fetch fallback =====
    (function(){
      function ajaxDelete(id, payload){
        // If jQuery is available, use it (keeps your original behavior)
        if (window.jQuery){
          jQuery.ajax({
            type:"POST", url:"delete_voter.php", data:payload, cache:false,
            success:function(){ jQuery(".del"+id).fadeOut('slow'); }
          });
          return;
        }
        // Vanilla fetch fallback
        fetch("delete_voter.php",{
          method:"POST",
          headers:{'Content-Type':'application/x-www-form-urlencoded'},
          body:new URLSearchParams(payload).toString()
        }).then(()=> {
          const row=document.querySelector(".del"+id);
          if(row){ row.style.opacity='0'; setTimeout(()=>row.remove(),300); }
        });
      }

      document.addEventListener('click', function(e){
        const btn = e.target.closest('.btn-delete');
        if(!btn) return;
        const id = btn.getAttribute('id');
        const pc_date = document.querySelector('.pc_date').value;
        const pc_time = document.querySelector('.pc_time').value;
        const data_name = document.querySelector('.data_name'+id).value;
        const user_name = document.querySelector('.user_name').value;

        if(confirm("Are you sure you want to delete this Voter?")){
          ajaxDelete(id, {id, pc_time, pc_date, data_name, user_name});
        }
      });
    })();

    // ===== Table: Use DataTables if present; otherwise provide minimal search + paging =====
    (function(){
      const table = document.getElementById('votersTable');
      const search = document.getElementById('tableSearch');
      const pageSizeSel = document.getElementById('pageSize');

      if (window.jQuery && jQuery.fn.DataTable){
        const dt = jQuery(table).DataTable({
          pageLength: parseInt(pageSizeSel.value,10) || 15,
          lengthChange:false,
          ordering:true,
          order:[],
          dom:'t<"dt-footer d-flex justify-content-end gap-2"ip>',
        });
        pageSizeSel.addEventListener('change',()=> dt.page.len(parseInt(pageSizeSel.value,10)).draw());
        search.addEventListener('input',()=> dt.search(search.value).draw());
        return;
      }

      // ---- Lightweight fallback (no external plugin required) ----
      const tbody = table.tBodies[0];
      const rows = Array.from(tbody.rows);
      const footer = document.getElementById('fallbackFooter');
      const rangeText = document.getElementById('rangeText');
      const prevBtn = document.getElementById('prevPage');
      const nextBtn = document.getElementById('nextPage');
      footer.classList.remove('hidden');

      let page = 1;
      let pageSize = parseInt(pageSizeSel.value,10) || 15;
      let filtered = rows.slice();

      function render(){
        rows.forEach(r=>r.style.display='none');
        const total = filtered.length;
        const pages = Math.max(1, Math.ceil(total/pageSize));
        if(page>pages) page=pages;
        const start = (page-1)*pageSize;
        const end = Math.min(start+pageSize, total);
        filtered.slice(start,end).forEach(r=>r.style.display='table-row');
        rangeText.textContent = total ? `Showing ${start+1}–${end} of ${total}` : 'No entries';
      }
      function applyFilter(){
        const q = search.value.toLowerCase();
        filtered = rows.filter(r => r.textContent.toLowerCase().includes(q));
        page = 1;
        render();
      }
      search.addEventListener('input', applyFilter);
      pageSizeSel.addEventListener('change', ()=>{ pageSize=parseInt(pageSizeSel.value,10)||15; page=1; render();});
      prevBtn.addEventListener('click', ()=>{ if(page>1){ page--; render(); }});
      nextBtn.addEventListener('click', ()=>{ const pages=Math.ceil(filtered.length/pageSize)||1; if(page<pages){ page++; render(); }});
      render();
    })();
  </script>
</body>
</html>
