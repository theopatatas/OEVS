<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<style>
  :root{
    --nav-bg:#ffffff;
    --nav-navy:#0a2e5c;      /* text & icons */
    --nav-sub:#133b72;       /* slightly darker for icons */
    --nav-accent:#3f0a6d;    /* top thin purple line */
    --border:#e6ecf5;
    --danger:#d62828;
  }

  /* ===== BAR WRAPPER ===== */
  .topbar-v2{ position:sticky; top:0; z-index:1000; background:var(--nav-bg); }
  .topbar-v2::before{
    content:""; display:block; height:6px; background:var(--nav-accent);
  }
  .topbar-row{
    max-width:1280px; margin:0 auto; padding:12px 18px;
    display:flex; align-items:center; justify-content:space-between;
  }

  /* ===== LEFT: BRAND (logo + 2 lines) ===== */
  .brand-v2{ display:flex; align-items:center; gap:12px; }
  .brand-v2 img{ width:40px; height:40px; object-fit:contain; }
  .brand-text{ line-height:1.05; }
  .brand-title{
    font-size:22px; font-weight:800; color:var(--nav-navy); letter-spacing:.2px;
    text-transform:uppercase;
  }
  .brand-sub{ font-size:18px; font-weight:700; color:var(--nav-navy); }

  /* ===== RIGHT: NAV LINKS ===== */
  .nav-v2{ display:flex; align-items:center; gap:22px; }
  .nav-item{ position:relative; }
  .nav-link{
    display:flex; align-items:center; gap:10px; text-decoration:none;
    color:var(--nav-navy); font-weight:700; font-size:20px;
  }

  /* circular icon chip (blue) */
  .chip{
    width:28px; height:28px; border-radius:50%;
    display:grid; place-items:center; background:var(--nav-sub); color:#fff;
  }
  .chip svg{ width:16px; height:16px; }

  /* Logout styling (red text + red icon) */
  .nav-link.logout{ color:var(--danger); }
  .nav-link.logout .chip{ background:var(--danger); }

  /* ===== DROPDOWN ===== */
  .dropdown-toggle::after{
    content:""; margin-left:8px; border:6px solid transparent; border-top-color:currentColor;
    transform:translateY(2px);
  }
  .dropdown-menu{
    position:absolute; top:115%; right:0; min-width:240px; background:#fff;
    border:1px solid var(--border); border-radius:12px; padding:6px; display:none; z-index:1200;
    box-shadow:0 10px 28px rgba(0,0,0,.08);
  }
  .dropdown-menu.show{ display:block; }
  .drop-link{
    display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px;
    text-decoration:none; color:#0b0b0b; font-size:14px; font-weight:600;
  }
  .drop-link:hover{ background:#eef4ff; color:var(--nav-navy); }
  .drop-divider{ height:1px; background:var(--border); margin:6px; }

  /* Responsive: shrink brand a bit on small screens */
  @media (max-width:1024px){
    .brand-title{ font-size:18px; }
    .brand-sub{ font-size:14px; }
    .nav-link{ font-size:18px; }
  }
  @media (max-width:780px){
    .nav-v2{ gap:14px; }
    .brand-sub{ display:none; }
  }
</style>

<header class="topbar-v2">
  <div class="topbar-row">
    <!-- LEFT: Brand -->
    <div class="brand-v2">
      <img src="images/au.png" alt="AU">
      <div class="brand-text">
        <div class="brand-title">ONLINE ELECTION VOTING SYSTEM</div>
        <div class="brand-sub">Phinma Araullo University</div>
      </div>
    </div>

    <!-- RIGHT: Nav -->
    <nav class="nav-v2" aria-label="Main">
      <div class="nav-item">
        <a class="nav-link" href="home.php">
          <span class="chip">
            <!-- Home icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>
            </svg>
          </span>
          <span>Home</span>
        </a>
      </div>

      <!-- MENU (dropdown) -->
      <div class="nav-item" id="menuDrop">
        <a href="#" class="nav-link dropdown-toggle" aria-expanded="false">
          <span class="chip">
            <!-- List icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
            </svg>
          </span>
          <span>Menu</span>
        </a>
        <div class="dropdown-menu" role="menu" aria-label="Menu">
          <a class="drop-link" href="result.php">Election Result</a>
          <a class="drop-link" href="dashboard.php">Analytics</a>
          <div class="drop-divider"></div>
          <a class="drop-link" href="candidates.php">Candidates</a>
          <a class="drop-link" href="positions.php">Positions</a>
          <a class="drop-link" href="parties.php">Parties</a>
          <a class="drop-link" href="voters.php">Voters</a>
        </div>
      </div>

      <div class="nav-item">
        <a class="nav-link" href="profile.php">
          <span class="chip">
            <!-- User icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="8" r="4"/><path d="M6 21v-2a6 6 0 0112 0v2"/>
            </svg>
          </span>
          <span>Profile</span>
        </a>
      </div>

      <div class="nav-item">
        <a class="nav-link" href="about.php">
          <span class="chip">
            <!-- Info icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>
            </svg>
          </span>
          <span>About</span>
        </a>
      </div>

      <div class="nav-item">
        <a class="nav-link logout" href="logout.php">
          <span class="chip">
            <!-- Logout icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
              <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>
            </svg>
          </span>
          <span>Logout</span>
        </a>
      </div>
    </nav>
  </div>
</header>

<script>
  // Simple dropdown (no dependencies)
  (function(){
    var wrap = document.getElementById('menuDrop');
    if(!wrap) return;
    var toggle = wrap.querySelector('.dropdown-toggle');
    var menu = wrap.querySelector('.dropdown-menu');

    function close(){ menu.classList.remove('show'); toggle.setAttribute('aria-expanded','false'); }
    toggle.addEventListener('click', function(e){
      e.preventDefault();
      var open = menu.classList.toggle('show');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    document.addEventListener('click', function(e){
      if(!wrap.contains(e.target)) close();
    });
    document.addEventListener('keydown', function(e){ if(e.key === 'Escape') close(); });
  })();
</script>
s