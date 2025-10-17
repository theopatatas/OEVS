<?php
include('session.php');
include('dbcon.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Home - Online Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    :root{
      --primary:#002f6c;
      --accent:#0056b3;
      --bg:#f4f6f8;
      --white:#fff;
      --ink:#0d2343;
      --muted:#6c7b90;
      --border:#e6ebf4;
      --shadow:0 6px 18px rgba(0,0,0,.08);
      --ring:#9ec5ff;
      --transition:all .2s ease;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);margin:0;color:var(--ink)}
    a{text-decoration:none;color:inherit}

    /* ===== Header ===== */
    header{
      background:var(--white); box-shadow:var(--shadow); border-bottom:1px solid var(--border);
      padding:10px 22px; display:flex; justify-content:space-between; align-items:center; gap:16px;
      position:sticky; top:0; z-index:10;
    }
    .logo-section{display:flex; align-items:center; gap:10px}
    .logo-section img{height:40px}
    .logo-section .title{font-weight:700; font-size:16px; color:var(--primary); line-height:1.1}
    .logo-section .title small{font-weight:600; font-size:12px; color:var(--accent)}
    nav{display:flex; align-items:center; gap:12px}
    .nav-item{position:relative}
    .nav-item > a{
      display:inline-flex; align-items:center; gap:8px; padding:8px 14px; border-radius:10px;
      font-weight:700; color:var(--primary); transition:var(--transition);
    }
    .nav-item > a i{width:18px; text-align:center}
    .nav-item > a:hover,
    .nav-item > a:focus-visible{background:var(--primary); color:#fff; outline:none}
    .nav-item.logout > a{color:#d92d2d; font-weight:800}
    .nav-item.logout > a:hover,
    .nav-item.logout > a:focus-visible{background:#ffe9e9; color:#b61e1e}

    /* ===== Dropdowns (card style) ===== */
    .dropdown,
    .submenu{
      display:none;
      position:absolute;
      top:calc(100% - 2px);
      left:0;
      min-width:240px;
      background:#fff;
      border:1px solid #e7eef7;
      border-radius:14px;
      box-shadow:0 10px 30px rgba(13,35,67,.12), 0 2px 6px rgba(13,35,67,.06);
      padding:6px;
      z-index:999;
    }
    .nav-item:hover > .dropdown,
    .nav-item:focus-within > .dropdown{display:block}

    .dropdown a,.submenu a{
      display:flex; align-items:center; gap:10px;
      padding:10px 12px; border-radius:10px;
      color:var(--primary); font-weight:600; white-space:nowrap;
      transition:background .16s ease, color .16s ease, transform .06s ease;
    }
    .dropdown a:hover,.submenu a:hover,
    .dropdown a:focus-visible,.submenu a:focus-visible{
      background:var(--accent); color:#fff; outline:none;
    }
    .dropdown .divider{height:1px; background:#e9eff7; margin:6px 4px}

    /* ===== Submenu becomes a DROP-DOWN (not fly-out) ===== */
    .has-submenu{position:relative}
    .submenu{
      position:static;
      top:auto; left:auto;
      min-width:auto;
      border:none;
      box-shadow:none;
      padding:4px 0 0 0;
      margin:0;
      display:none;
    }
    .has-submenu:hover > .submenu,
    .has-submenu:focus-within > .submenu,
    .has-submenu.open > .submenu{display:block}
    .submenu a{padding-left:36px}
    .has-submenu > a .chev{margin-left:auto; transition:transform .2s ease}
    .has-submenu.open > a .chev{transform:rotate(90deg)}

    /* ===== Layout ===== */
    main{padding:24px 16px}
    .container{max-width:1100px; margin:0 auto}
    .panel-grid{display:grid; grid-template-columns:1.35fr .95fr; gap:18px}
    @media (max-width:960px){ .panel-grid{grid-template-columns:1fr} }

    /* ===== Card-style gallery ===== */
    .gallery-card{
      background:#fff; border:1px solid var(--border); border-radius:20px; box-shadow:var(--shadow);
      padding:12px; display:flex; flex-direction:column; gap:10px;
    }
    .preview{position:relative; width:100%; aspect-ratio:16/9; overflow:hidden; border-radius:16px; background:#e9eff8;}
    .preview img{width:100%; height:100%; object-fit:cover; display:block}
    .thumbs{display:flex; gap:10px; overflow-x:auto; padding-bottom:6px; -webkit-overflow-scrolling:touch; scroll-snap-type:x proximity;}
    .thumbs::-webkit-scrollbar{height:8px}
    .thumbs::-webkit-scrollbar-thumb{background:#c7d7f5; border-radius:8px}
    .thumb{flex:0 0 clamp(90px, calc((100% - 20px)/3), 140px); border:1px solid var(--border); border-radius:12px; overflow:hidden; background:#f3f6fc; scroll-snap-align:start;}
    .thumb button{width:100%; aspect-ratio:1/1; display:block; border:0; padding:0; background:transparent; cursor:pointer}
    .thumb img{width:100%; height:100%; object-fit:cover; display:block; transition:transform .2s ease}
    .thumb button:hover img{transform:scale(1.03)}
    .thumb[aria-selected="true"]{outline:3px solid var(--ring); outline-offset:2px}

    /* ===== Side cards ===== */
    .side-stack{display:flex; flex-direction:column; gap:18px}
    .card-lite{background:#f2f6ff; border-radius:12px; padding:16px; color:#0b1b36; border:1px solid #dbe7ff;}
    .card-lite h3{margin:0 0 6px; font-size:18px}
    .card-lite p{margin:0 0 10px}
    .btn-info{display:inline-flex; align-items:center; gap:8px; background:#0a2e5c; color:#fff; padding:8px 12px; border-radius:8px; text-decoration:none; transition:var(--transition);}
    .btn-info:hover{background:#0d3a72}

    .modal.hide{display:none}
    .modal .modal-header{padding:10px 12px; border-bottom:1px solid var(--border)}
    .modal .modal-footer{padding:10px 12px; border-top:1px solid var(--border)}
    .btn{display:inline-block; padding:8px 12px; border-radius:8px; background:#e9edf7}
    .btn:hover{background:#dfe7ff}

    footer{text-align:center; padding:20px 0; color:var(--muted); font-size:14px}

    @media (max-width:768px){
      header{flex-direction:column; align-items:stretch; gap:8px}
      nav{flex-direction:column; gap:6px}
      .nav-item{width:100%}
      .nav-item > a{width:100%}
      .dropdown{position:relative; top:0; left:0; margin:6px 0 0 0; box-shadow:none}
      .submenu{position:relative; padding-left:0}
    }
  </style>
</head>
<body>
  <!-- ===== Header ===== -->
  <header>
    <div class="logo-section">
      <img src="images/au.png" alt="Logo" />
      <div class="title">
        ONLINE ELECTION VOTING SYSTEM<br />
        <small>Phinma Araullo University</small>
      </div>
    </div>

    <nav>
      <!-- Home -->
      <div class="nav-item">
        <a href="home.php"><i class="fas fa-home"></i> Home</a>
      </div>

      <!-- Menu -->
      <div class="nav-item">
        <a href="#"><i class="fas fa-list-ul"></i> Menu</a>
        <div class="dropdown">
          <a href="voter_list.php">Voters</a>
          <div class="divider"></div>

          <!-- Admin Actions (submenu drops down) -->
          <div class="has-submenu">
            <a href="#" role="button" aria-expanded="false">
              Admin Actions
              <i class="fa fa-chevron-right chev" aria-hidden="true"></i>
            </a>
            <div class="submenu">
              <a href="result.php"><i class="fa fa-table"></i> Election Result</a>
              <a href="dashboard.php"><i class="fa fa-chart-bar"></i> Analytics</a>
              <!-- NEW under Admin Actions -->
              <a href="canvassing_report.php"><i class="fa fa-table"></i> Vote Count Report</a>
              <a href="voter_verification.php"><i class="fa fa-id-badge"></i> Voter Verification</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Profile -->
      <div class="nav-item">
        <a href="#"><i class="fas fa-user-circle"></i> Profile</a>
        <div class="dropdown"><a href="profile.php">View Profile</a></div>
      </div>

      <!-- About (NEW) -->
      <div class="nav-item">
        <a href="about.php"><i class="fas fa-circle-info"></i> About</a>
      </div>

      <!-- Logout -->
      <div class="nav-item logout">
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </nav>
  </header>

  <!-- ===== Content ===== -->
  <main>
    <div class="container">
      <div class="panel-grid">
        <!-- Left: Card-style gallery -->
        <section class="gallery-card" aria-label="Campus gallery">
          <div class="preview">
            <img id="previewImg" src="images/1.png" alt="Selected photo" />
          </div>

          <div class="thumbs" role="listbox" aria-label="Choose photo">
            <?php
              $imgs = ['1.png','2.png','3.png','4.png','5.png','6.png','7.png'];
              foreach ($imgs as $i => $img): ?>
              <div class="thumb" aria-selected="<?php echo $i===0 ? 'true':'false'; ?>">
                <button type="button" data-src="images/<?php echo $img; ?>" aria-label="Photo <?php echo $i+1; ?>">
                  <img src="images/<?php echo $img; ?>" alt="" loading="lazy" />
                </button>
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <!-- Right: Mission & Vision -->
        <aside class="side-stack">
          <div class="card-lite">
            <h3>Mission</h3>
            <p>“To make lives better through education.”</p>
            <a class="btn-info" data-toggle="modal" href="#mission">Read More</a>
          </div>
          <div class="card-lite">
            <h3>Vision</h3>
            <p><strong>PHINMA Education</strong></p>
            <p>“We envision PHINMA Araullo University as a dynamic institution of learning.”</p>
            <a class="btn-info" data-toggle="modal" href="#vision">Read More</a>
          </div>
        </aside>

      </div>
    </div>
  </main>

  <!-- ===== Modals ===== -->
  <div class="modal hide fade" id="mission">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">×</button>
      <h3>About PHINMA Education</h3>
    </div>
    <div class="modal-body">
      <p><font color="black">
        For more than a decade, PHINMA built its reputation on transforming existing educational institutions to better serve Filipino students.
        PHINMA Education begins this process by strategically selecting a school from a key growth area and thoroughly transforming its academics,
        operations, and student community to ensure success for Filipino youth from low-income families.
      </font></p>
    </div>
    <div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Close</a></div>
  </div>

  <div class="modal hide fade" id="vision">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">×</button>
      <h3>Fulfilling Our Mission Through Education</h3>
    </div>
    <div class="modal-body">
      <p><font color="black">
        Despite consistently high enrollment, attrition rates remain significant. Out of four students who enter first grade,
        only one will finish a tertiary degree. Seeing these statistics, PHINMA’s leaders focused on improving the country’s state of education.
        In 2004, they committed to education to fully advance their mission—introducing reforms and innovations across partner schools.
      </font></p>
    </div>
    <div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Close</a></div>
  </div>

  <footer>© 2025 Online Election Voting System</footer>

  <!-- ===== JS: gallery swap ===== -->
  <script>
    (function(){
      const preview = document.getElementById('previewImg');
      const thumbs = document.querySelectorAll('.thumb button');

      thumbs.forEach(btn => {
        btn.addEventListener('click', () => {
          const src = btn.getAttribute('data-src');
          if (!src || preview.src.endsWith(src)) return;
          preview.src = src;

          document.querySelectorAll('.thumb').forEach(t => t.setAttribute('aria-selected','false'));
          btn.parentElement.setAttribute('aria-selected','true');
        });
      });
    })();
  </script>

  <!-- ===== JS: make the submenu a dropdown (hover + tap) ===== -->
  <script>
    (function(){
      const wrappers = document.querySelectorAll('.has-submenu');

      wrappers.forEach(w => {
        const trigger = w.querySelector(':scope > a');
        if (!trigger) return;

        trigger.addEventListener('click', (e) => {
          e.preventDefault();
          const willOpen = !w.classList.contains('open');
          w.parentElement.querySelectorAll('.has-submenu.open').forEach(other => {
            if (other !== w) other.classList.remove('open');
          });
          w.classList.toggle('open', willOpen);
          trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });
      });

      document.addEventListener('click', (e) => {
        const openOnes = document.querySelectorAll('.has-submenu.open');
        openOnes.forEach(w => {
          if (!w.closest('.dropdown')?.contains(e.target)) {
            w.classList.remove('open');
            const t = w.querySelector(':scope > a');
            if (t) t.setAttribute('aria-expanded','false');
          }
        });
      });
    })();
  </script>
</body>
</html>
