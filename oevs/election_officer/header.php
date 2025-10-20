<?php
// header.php (sticky/fixed version)
?>

<!-- ===== Header Assets ===== -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
  :root{
    --primary:#002f6c;
    --accent:#0056b3;
    --white:#fff;
    --ink:#0d2343;
    --muted:#6c7b90;
    --border:#e6ebf4;
    --shadow:0 6px 18px rgba(0,0,0,.08);
    --ring:#9ec5ff;
    --transition:all .2s ease;
  }

  /* ===== Header (fixed) ===== */
  header.site-header{
    background:var(--white); box-shadow:var(--shadow); border-bottom:1px solid var(--border);
    padding:10px 22px; display:flex; justify-content:space-between; align-items:center; gap:16px;
    position:fixed; top:0; left:0; right:0; z-index:1000;  /* <— fixed & on top */
    color:var(--ink); font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
  }
  header.site-header a{text-decoration:none; color:inherit}

  .logo-section{display:flex; align-items:center; gap:10px}
  .logo-section img{height:40px}
  .logo-section .title{font-weight:700; font-size:16px; color:var(--primary); line-height:1.1}
  .logo-section .title small{font-weight:600; font-size:12px; color:var(--accent)}

  nav.main-nav{display:flex; align-items:center; gap:12px}
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

  /* ===== Submenu behaves as stacked dropdown (not fly-out) ===== */
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

  /* ===== Responsive ===== */
  @media (max-width:768px){
    header.site-header{flex-direction:column; align-items:stretch; gap:8px}
    nav.main-nav{flex-direction:column; gap:6px}
    .nav-item{width:100%}
    .nav-item > a{width:100%}
    .dropdown{position:relative; top:0; left:0; margin:6px 0 0 0; box-shadow:none}
    .submenu{position:relative; padding-left:0}
  }
</style>

<header class="site-header">
  <div class="logo-section">
    <img src="images/au.png" alt="Logo" />
    <div class="title">
      ONLINE ELECTION VOTING SYSTEM<br />
      <small>Phinma Araullo University</small>
    </div>
  </div>

  <nav class="main-nav" aria-label="Primary">
    <div class="nav-item">
      <a href="home.php"><i class="fas fa-home"></i> Home</a>
    </div>

    <div class="nav-item">
      <a href="#" aria-haspopup="true" aria-expanded="false"><i class="fas fa-list-ul"></i> Menu</a>
      <div class="dropdown" role="menu">
        <a href="voters.php">Voters</a>
        <div class="divider" role="separator" aria-hidden="true"></div>

        <div class="has-submenu">
          <a href="#" role="button" aria-expanded="false">
            Admin Actions
            <i class="fa fa-chevron-right chev" aria-hidden="true"></i>
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
      <a href="#" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-circle"></i> Profile</a>
      <div class="dropdown" role="menu"><a href="profile.php">View Profile</a></div>
    </div>

    <div class="nav-item">
      <a href="about.php"><i class="fas fa-circle-info"></i> About</a>
    </div>

    <div class="nav-item logout">
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </nav>
</header>

<script>
  // Add body top padding equal to header height so content doesn't jump under the fixed header
  (function fixBodyOffset(){
    const header = document.querySelector('header.site-header');
    const setPad = () => document.body.style.paddingTop = header.offsetHeight + 'px';
    window.addEventListener('load', setPad);
    window.addEventListener('resize', setPad);
  })();

  // Keep the "Admin Actions" submenu usable on tap/click
  (function stickyDropdown(){
    const wrappers = document.querySelectorAll('.has-submenu');

    wrappers.forEach(w => {
      const trigger = w.querySelector(':scope > a');
      if (!trigger) return;

      trigger.addEventListener('click', (e) => {
        e.preventDefault();
        const willOpen = !w.classList.contains('open');

        // Close other open submenus within same dropdown
        w.parentElement.querySelectorAll('.has-submenu.open').forEach(other => {
          if (other !== w) other.classList.remove('open');
        });

        w.classList.toggle('open', willOpen);
        trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
      });
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
      document.querySelectorAll('.has-submenu.open').forEach(w => {
        if (!w.closest('.dropdown')?.contains(e.target)) {
          w.classList.remove('open');
          const t = w.querySelector(':scope > a');
          if (t) t.setAttribute('aria-expanded','false');
        }
      });
    });
  })();
</script>
