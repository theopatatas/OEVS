<?php
include('session.php');
include('dbcon.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Home - Online Voting System</title>
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

    body {
      font-family: var(--font);
      background-color: var(--bg-color);
      margin: 0;
      padding: 0;
    }

    header {
      background-color: var(--white);
      box-shadow: var(--shadow);
      padding: 10px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      position: relative;
      z-index: 10;
    }

    .logo-section {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo-section img {
      height: 40px;
    }

    .logo-section .title {
      font-weight: 700;
      font-size: 18px;
      color: var(--primary-color);
      line-height: 1.2;
    }

    nav {
      display: flex;
      align-items: center;
      gap: 25px;
    }

    .nav-item {
      position: relative;
    }

    .nav-item > a {
      text-decoration: none;
      color: var(--primary-color);
      font-weight: 600;
      padding: 8px 12px;
      border-radius: 6px;
      display: inline-block;
      transition: var(--transition);
    }

    .nav-item > a:hover {
      background-color: var(--primary-color);
      color: white;
    }

    .dropdown {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      background-color: var(--white);
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

    .dropdown a:hover {
      background-color: var(--accent-color);
      color: white;
    }

    .nav-item:hover > .dropdown {
      display: block;
    }

    .submenu {
      display: none;
      position: absolute;
      top: 0;
      left: 100%;
      background-color: var(--white);
      box-shadow: var(--shadow);
      border-radius: 6px;
      min-width: 220px;
      padding: 8px 0;
    }

    .submenu a {
      padding: 10px 20px;
    }

    .has-submenu {
      position: relative;
    }

    .has-submenu > a {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .has-submenu > a i.fa-chevron-right {
      font-size: 12px;
      margin-left: 8px;
    }

    .has-submenu:hover > .submenu {
      display: block;
    }

    .main-content {
      text-align: center;
      padding: 60px 20px;
    }

    .main-content img {
      max-width: 200px;
    }

    .main-content p {
      font-size: 20px;
      font-weight: 600;
      color: var(--primary-color);
    }

    footer {
      text-align: center;
      padding: 20px 0;
      color: #666;
      font-size: 14px;
    }

    @media screen and (max-width: 768px) {
      header {
        flex-direction: column;
        align-items: flex-start;
      }

      nav {
        flex-direction: column;
        width: 100%;
        gap: 0;
      }

      .nav-item {
        width: 100%;
      }

      .nav-item > a {
        width: 100%;
        box-sizing: border-box;
      }

      .dropdown, .submenu {
        position: relative;
        box-shadow: none;
        left: 0;
      }
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

          <!-- Admin Actions with submenu -->
          <div class="has-submenu">
            <a href="#">Admin Actions <i class="fa fa-chevron-right"></i></a>
            <div class="submenu">
              <a href="result.php"><i class="fa fa-table" style="margin-right: 8px;"></i> Election Result</a>
              <a href="winningresult.php"><i class="fa fa-trophy" style="margin-right: 8px;"></i> Final Result</a>
              <a href="backupnreset.php"><i class="fa fa-database" style="margin-right: 8px;"></i> Backup and Reset</a>
              <a href="dashboard.php"><i class="fa fa-chart-bar" style="margin-right: 8px;"></i> Analytics</a>
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
        <a href="logout.php" style="color: red;"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </nav>
  </header>

  <div class="main-content">
    <img src="images/au.png" alt="Araullo University Logo">
    <p>Araullo University</p>
  </div>

  <footer>
    © 2025 Online Election Voting System
  </footer>

</body>
</html>
