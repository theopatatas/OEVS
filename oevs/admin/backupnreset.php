<?php
include('session.php');
include('dbcon.php');

// collect backups
$backupDir = __DIR__ . DIRECTORY_SEPARATOR . 'backup';
$files = is_dir($backupDir) ? glob($backupDir . DIRECTORY_SEPARATOR . '*.sql') : [];
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Backup & Restore - Online Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root{
      --primary:#002f6c;
      --accent:#0056b3;
      --bg:#f4f6f8;
      --white:#fff;
      --shadow:0 5px 15px rgba(0,0,0,.10);
      --danger:#c1121f;
      --muted:#6b7280;
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial;background:var(--bg);color:#0b1324}

    /* Sticky header like the other pages */
    header{
      position:sticky; top:0; z-index:1000;
      background:var(--white); box-shadow:var(--shadow);
      padding:10px 30px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;
    }
    .logo-section{display:flex;align-items:center;gap:10px}
    .logo-section img{height:40px}
    .logo-section .title{font-weight:700;font-size:18px;color:var(--primary);line-height:1.2}
    nav{display:flex;align-items:center;gap:25px}
    .nav-item{position:relative}
    .nav-item>a{
      text-decoration:none;color:var(--primary);font-weight:600;padding:8px 12px;border-radius:6px;display:inline-block;transition:.25s
    }
    .nav-item>a:hover{background:var(--primary);color:#fff}
    .dropdown{
      display:none;position:absolute;top:100%;left:0;background:var(--white);box-shadow:var(--shadow);
      border-radius:6px;min-width:220px;padding:8px 0;z-index:99
    }
    .dropdown a{display:block;padding:10px 15px;text-decoration:none;color:var(--primary);font-weight:500;white-space:nowrap}
    .dropdown a:hover{background:var(--accent);color:#fff}
    .nav-item:hover>.dropdown{display:block}
    .submenu{display:none;position:absolute;top:0;left:100%;background:var(--white);box-shadow:var(--shadow);border-radius:6px;min-width:220px;padding:8px 0}
    .submenu a{padding:10px 20px}
    .has-submenu{position:relative}
    .has-submenu>a{display:flex;justify-content:space-between;align-items:center}
    .has-submenu>a i{font-size:12px;margin-left:8px}
    .has-submenu:hover>.submenu{display:block}

    .content{max-width:1000px;margin:24px auto;padding:0 16px}

    /* Card shells */
    .section{background:var(--white);box-shadow:var(--shadow);border-radius:12px;padding:22px;margin-bottom:18px}
    .section h3{margin:0 0 14px}
    .lead{color:#111;font-weight:700;text-align:center;margin:6px 0 18px}
    .muted{color:var(--muted);font-size:13px}

    .danger-card{border-left:6px solid var(--danger)}
    .primary-card{border-left:6px solid var(--primary)}

    input[type="text"], select{
      width:100%;padding:10px 12px;border:1px solid #d0d7de;border-radius:8px;font-size:14px;outline:none
    }
    .btn{
      display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:8px;border:0;cursor:pointer;
      font-weight:700; text-decoration:none
    }
    .btn-danger{background:var(--danger);color:#fff}
    .btn-danger:hover{filter:brightness(.95)}
    .btn-primary{background:var(--primary);color:#fff}
    .btn-primary:hover{filter:brightness(.95)}

    /* Simple list of backups */
    .file-list{margin-top:8px;border:1px solid #e5e7eb;border-radius:10px;padding:10px;background:#fafafa}
    .file-item{padding:8px 10px;border-bottom:1px dashed #e5e7eb;font-size:14px;display:flex;justify-content:space-between;gap:10px}
    .file-item:last-child{border-bottom:0}

    footer{color:#666;text-align:center;padding:20px 0}
    @media (max-width:768px){
      nav{flex-direction:column;gap:0}
      .nav-item{width:100%}
      .nav-item>a{width:100%}
      .dropdown,.submenu{position:relative;left:0;box-shadow:none}
    }
  </style>
</head>
<body>

  <!-- Header copied from home.php -->
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

      <div class="nav-item">
        <a href="#"><i class="fas fa-user-circle"></i> Profile</a>
        <div class="dropdown"><a href="profile.php">View Profile</a></div>
      </div>

      <div class="nav-item">
        <a href="#"><i class="fas fa-info-circle"></i> About</a>
        <div class="dropdown">
          <a href="about.php">System Info</a>
          <a href="contact.php">Contact Us</a>
        </div>
      </div>

      <div class="nav-item">
        <a href="logout.php" style="color:var(--danger)"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </nav>
  </header>

  <div class="content">
    <h2 class="lead">Backup & Restore Options</h2>

    <!-- Reset & Backup -->
    <section class="section danger-card">
      <h3><i class="fa fa-triangle-exclamation" style="margin-right:8px;color:var(--danger)"></i> Create Backup & Reset</h3>
      <p class="muted">This will export the database to <code>/backup</code> and then reset OEVS data. Proceed only if you mean it.</p>

      <form method="post" action="reset.php"
            onsubmit="return confirm('Are you sure you want to reset all OEVS data? A backup will be created first.');">
        <label for="custom_name" class="muted" style="display:block;margin-bottom:6px">Backup name</label>
        <input id="custom_name" name="custom_name" type="text" placeholder="e.g., oevs_backup_2025_10_17" required>
        <div style="margin-top:12px">
          <button type="submit" name="reset" class="btn btn-danger">
            <i class="fa fa-database"></i> Reset & Backup
          </button>
        </div>
      </form>
    </section>

    <!-- Restore -->
    <section class="section primary-card">
      <h3><i class="fa fa-rotate-left" style="margin-right:8px;color:var(--primary)"></i> Restore From Backup</h3>
      <p class="muted">Choose a <code>.sql</code> file saved in <code>/backup</code> to restore your database.</p>

      <form method="post" action="restore_process.php"
            onsubmit="return confirm('Restore the selected backup? Current data will be overwritten.');">
        <label for="backup_file" class="muted" style="display:block;margin-bottom:6px">Backup file</label>
        <select id="backup_file" name="backup_file" required>
          <option value="">-- Select backup file --</option>
          <?php if ($files): ?>
            <?php foreach ($files as $f): $fileName = basename($f); ?>
              <option value="<?php echo h($fileName); ?>"><?php echo h($fileName); ?></option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
        <div style="margin-top:12px">
          <button type="submit" name="restore" class="btn btn-primary">
            <i class="fa fa-rotate"></i> Restore Selected Backup
          </button>
        </div>
      </form>

      <!-- Optional: quick view of available backups -->
      <div class="file-list" style="margin-top:16px">
        <?php if (!$files): ?>
          <div class="file-item" style="color:#777">No backups found in /backup yet.</div>
        <?php else: ?>
          <?php foreach ($files as $f): $fileName = basename($f); ?>
            <div class="file-item">
              <span><i class="fa fa-file-lines" style="margin-right:8px"></i><?php echo h($fileName); ?></span>
              <span class="muted"><?php echo date('Y-m-d H:i', filemtime($f)); ?></span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <footer>© 2025 Online Election Voting System</footer>
  </div>
</body>
</html>
