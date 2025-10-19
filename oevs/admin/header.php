<?php
// Set $activePage (e.g., 'home', 'candidates', 'voters') before including this file.
$active = isset($activePage) ? strtolower($activePage) : '';
?>
<style>
  :root {
    --primary-color: #002f6c;
    --accent-color: #0056b3;
    --white: #fff;
    --shadow: 0 4px 12px rgba(0,0,0,.1);
    --transition: all .3s ease;
    --font: 'Inter', sans-serif;
  }

  /* Header + Nav */
  header {
    background: var(--white);
    box-shadow: var(--shadow);
    padding: 10px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    position: sticky;          /* 🔒 stick on scroll */
    position: -webkit-sticky;  /* Safari */
    top: 0;
    z-index: 1000;
  }
  .logo-section { display: flex; align-items: center; gap: 10px; }
  .logo-section img { height: 40px; }
  .logo-section .title { font-weight: 700; font-size: 18px; color: var(--primary-color); line-height: 1.2; }

  nav { display: flex; align-items: center; gap: 25px; }
  .nav-item { position: relative; }

  /* Base */
  .nav-item > a {
    text-decoration: none;
    color: var(--primary-color);
    font-weight: 600;
    padding: 8px 12px;
    border-radius: 6px;
    display: inline-block;
    transition: var(--transition);
  }

  /* ACTIVE: show as normal text (no pill, no underline) */
  .nav-item > a.active {
    background: transparent;
    color: var(--primary-color);
    box-shadow: none;
  }

  /* HOVER: blue pill */
  .nav-item > a:hover,
  .nav-item > a.active:hover {
    background: var(--primary-color);
    color: #fff;
  }

  .dropdown {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: var(--white);
    box-shadow: var(--shadow);
    border-radius: 6px;
    min-width: 200px;
    padding: 8px 0;
    z-index: 99;
  }
  .dropdown a {
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    color: var(--primary-color);
    font-weight: 500;
    transition: var(--transition);
    white-space: nowrap;
  }
  .dropdown a:hover { background: var(--accent-color); color: #fff; }
  .nav-item:hover > .dropdown { display: block; }

  .submenu {
    display: none;
    position: absolute;
    top: 0;
    left: 100%;
    background: var(--white);
    box-shadow: var(--shadow);
    border-radius: 6px;
    min-width: 220px;
    padding: 8px 0;
  }
  .submenu a { padding: 10px 20px; }
  .has-submenu { position: relative; }
  .has-submenu > a { display: flex; justify-content: space-between; align-items: center; }
  .has-submenu > a i.fa-chevron-right { font-size: 12px; margin-left: 8px; }
  .has-submenu:hover > .submenu { display: block; }

  @media (max-width: 768px) {
    header { flex-direction: column; align-items: flex-start; }
    nav { flex-direction: column; width: 100%; gap: 0; }
    .nav-item { width: 100%; }
    .nav-item > a { width: 100%; box-sizing: border-box; }
    .dropdown, .submenu { position: relative; left: 0; box-shadow: none; }
  }
</style>

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
      <a href="home.php" class="<?php echo $active==='home'?'active':''; ?>">
        <i class="fas fa-home"></i> Home
      </a>
    </div>

    <!-- MENU (with Admin Actions submenu) -->
    <div class="nav-item">
      <a href="#"><i class="fas fa-list-ul"></i> Menu</a>
      <div class="dropdown">
        <a href="candidates.php" class="<?php echo $active==='candidates'?'active':''; ?>">Candidates</a>
        <a href="voters.php" class="<?php echo $active==='voters'?'active':''; ?>">Voters</a>

        <div class="has-submenu">
          <a href="#">Admin Actions <i class="fa fa-chevron-right"></i></a>
          <div class="submenu">
            <a href="result.php"><i class="fa fa-table" style="margin-right:8px;"></i> Election Result</a>
            <a href="winningresult.php"><i class="fa fa-trophy" style="margin-right:8px;"></i> Final Result</a>
            <a href="backupnreset.php"><i class="fa fa-database" style="margin-right:8px;"></i> Backup and Reset</a>
            <a href="history.php"><i class="fa fa-clock-rotate-left" style="margin-right:8px;"></i> History</a>
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
      </div>
    </div>

    <!-- LOGOUT -->
    <div class="nav-item">
      <a href="logout.php" style="color:red;">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </div>
  </nav>
</header>
