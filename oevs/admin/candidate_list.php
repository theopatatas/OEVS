<?php
include('session.php');
include('dbcon.php');

/* Ensure session + CSRF token */
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf_token'];

/* Redirect helper — one-time session flash */
function _redir($ok, $msg) {
  if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
  $_SESSION['flash'] = ['ok' => (int)$ok, 'msg' => $msg];
  header('Location: candidate_list.php');
  exit;
}

/* DELETE handler (runs before any output) */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    _redir(0, 'Invalid request.');
  }
  $id = (int)$_POST['delete_id'];
  if ($id <= 0) _redir(0, 'Invalid candidate id.');

  if (!$stmt = mysqli_prepare($conn, "SELECT FirstName, LastName, Photo FROM candidate WHERE CandidateID = ?")) {
    _redir(0, 'DB error.');
  }
  mysqli_stmt_bind_param($stmt, 'i', $id);
  mysqli_stmt_execute($stmt);
  $res  = mysqli_stmt_get_result($stmt);
  $cand = mysqli_fetch_assoc($res);
  mysqli_stmt_close($stmt);

  if (!$cand) _redir(0, 'Candidate not found.');

  $stmt = mysqli_prepare($conn, "DELETE FROM votes WHERE CandidateID = ?");
  mysqli_stmt_bind_param($stmt, 'i', $id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  $stmt = mysqli_prepare($conn, "DELETE FROM candidate WHERE CandidateID = ?");
  mysqli_stmt_bind_param($stmt, 'i', $id);
  mysqli_stmt_execute($stmt);
  $affected = mysqli_stmt_affected_rows($stmt);
  mysqli_stmt_close($stmt);

  if ($affected > 0) {
    $rel = ltrim((string)$cand['Photo'], '/');
    $paths = [__DIR__ . '/' . $rel, realpath(__DIR__ . '/..') . '/' . $rel];
    foreach ($paths as $p) { if ($p && is_file($p)) { @unlink($p); break; } }

    $chk = mysqli_query($conn, "SHOW TABLES LIKE 'history'");
    if ($chk && mysqli_num_rows($chk)) {
      $user = $_SESSION['username'] ?? 'Admin';
      $data = trim($cand['FirstName'].' '.$cand['LastName']);
      $date = date('n/j/Y G:i:s');
      if ($hs = mysqli_prepare($conn, "INSERT INTO history (`data`,`action`,`date`,`user`) VALUES (?,?,?,?)")) {
        $act = 'Deleted Candidate';
        mysqli_stmt_bind_param($hs, 'ssss', $data, $act, $date, $user);
        mysqli_stmt_execute($hs);
        mysqli_stmt_close($hs);
      }
    }
    _redir(1, 'Candidate deleted: '.$cand['FirstName'].' '.$cand['LastName']);
  } else {
    _redir(0, 'Delete failed.');
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Candidate List - Online Voting System</title>
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
    body { font-family: var(--font); background-color: var(--bg-color); margin: 0; padding: 0; }
    header { background-color: var(--white); box-shadow: var(--shadow); padding: 10px 30px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; position:sticky; top:0; z-index:1000; }
    .logo-section{display:flex;align-items:center;gap:10px}
    .logo-section img{height:40px}
    .logo-section .title{font-weight:700;font-size:18px;color:var(--primary-color);line-height:1.2}
    nav{display:flex;align-items:center;gap:25px}
    .nav-item{position:relative}
    .nav-item>a{text-decoration:none;color:var(--primary-color);font-weight:600;padding:8px 12px;border-radius:6px;display:inline-block;transition:var(--transition)}
    .nav-item>a:hover{background-color:var(--primary-color);color:white}
    .dropdown{display:none;position:absolute;top:100%;left:0;background-color:var(--white);box-shadow:var(--shadow);border-radius:6px;min-width:200px;padding:8px 0;z-index:1100}
    .dropdown a{display:block;padding:10px 15px;text-decoration:none;color:var(--primary-color);font-weight:500;transition:var(--transition);white-space:nowrap}
    .dropdown a:hover{background-color:var(--accent-color);color:white}
    .nav-item:hover>.dropdown{display:block}
    .submenu{display:none;position:absolute;top:0;left:100%;background-color:var(--white);box-shadow:var(--shadow);border-radius:6px;min-width:220px;padding:8px 0}
    .has-submenu{position:relative}
    .has-submenu>a{display:flex;justify-content:space-between;align-items:center}
    .has-submenu>a i.fa-chevron-right{font-size:12px;margin-left:8px}
    .has-submenu:hover>.submenu{display:block}

    .content-wrapper{max-width:1400px;margin:30px auto;padding:0 20px}

    .filter-section{
      background-color: var(--white);
      padding: 16px;
      border-radius: 10px;
      margin-bottom: 14px;
      box-shadow: var(--shadow);
      display: flex;
      align-items: center;
      gap: 10px;
      justify-content: flex-start;
      flex-wrap: wrap;
    }

    .add-btn{
      background-color:#0056b3;
      color:#fff;
      border:1px solid #004a9a;
      padding:9px 14px;
      border-radius:6px;
      font-weight:700;
      display:flex; align-items:center; gap:8px;
      transition:var(--transition);
      font-size:14px;
      box-shadow:none;
    }
    .add-btn:hover{ background:#004a9a; transform:none; }

    .table-container{background-color:var(--white);border-radius:10px;box-shadow:var(--shadow);overflow:hidden;margin-top:20px}
    .table-controls{padding:20px;display:flex;justify-content:space-between;align-items:center;border-bottom:2px solid #f0f0f0;flex-wrap:wrap;gap:15px;background-color:#fafbfc}
    .items-per-page{display:flex;align-items:center;gap:10px;font-size:14px;color:#666}
    .items-per-page select{padding:8px 12px;border:1px solid #ddd;border-radius:6px;font-size:14px;background-color:white;cursor:pointer;transition:var(--transition)}
    .items-per-page select:hover{border-color:var(--accent-color)}
    .search-box{display:flex;align-items:center;gap:10px;font-size:14px;color:#666}
    .search-box input{padding:8px 15px;border:1px solid #ddd;border-radius:6px;font-size:14px;min-width:250px;transition:var(--transition)}
    .search-box input:focus{outline:none;border-color:var(--accent-color);box-shadow:0 0 0 3px rgba(0,86,179,.1)}
    table{width:100%;border-collapse:collapse}
    thead{background:linear-gradient(135deg,var(--primary-color) 0%,var(--accent-color) 100%)}
    th{padding:16px;text-align:left;font-weight:600;color:white;font-size:14px;text-transform:uppercase;letter-spacing:.5px}
    td{padding:16px;border-bottom:1px solid #f0f0f0;font-size:14px;color:#333}
    tbody tr{transition:var(--transition)}
    tbody tr:hover{background-color:#f8f9fa;transform:scale(1.001)}
    tbody tr:last-child td{border-bottom:none}
    .candidate-photo{width:50px;height:50px;border-radius:50%;object-fit:cover;border:3px solid #e0e0e0;transition:var(--transition)}
    .candidate-photo:hover{transform:scale(1.1);border-color:var(--accent-color)}
    .action-buttons{display:flex;gap:8px;flex-wrap:wrap}
    .btn{padding:8px 14px;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;transition:var(--transition);text-decoration:none;box-shadow:0 2px 4px rgba(0,0,0,.1)}
    .btn-edit{background-color:var(--primary-color);color:white}
    .btn-edit:hover{background-color:var(--accent-color);transform:translateY(-2px);box-shadow:0 6px 12px rgba(0,86,179,.3)}
    .btn-view{background-color:var(--accent-color);color:white}
    .btn-view:hover{background-color:#003d82;transform:translateY(-2px);box-shadow:0 6px 12px rgba(0,47,108,.3)}
    .btn-delete{background-color:#dc3545;color:white}
    .btn-delete:hover{background-color:#bd2130;transform:translateY(-2px);box-shadow:0 6px 12px rgba(220,53,69,.3)}

    .btn-success{
      background-color:#16a34a;
      border:1px solid #138a3e;
      color:#fff;
      padding:9px 14px;
      border-radius:6px;
      font-weight:700;
      display:inline-flex; align-items:center; gap:8px;
      box-shadow:none !important;
      cursor:pointer;
      font-size:14px;
    }
    .btn-success:hover{ background:#138a3e; transform:none; }

    footer{text-align:center;padding:20px 0;color:#666;font-size:14px}
    @media (max-width:768px){
      nav{flex-direction:column;width:100%;gap:0}
      .nav-item{width:100%}
      .nav-item>a{width:100%;box-sizing:border-box}
      .dropdown,.submenu{position:relative;box-shadow:none;left:0}
      .table-container{overflow-x:auto}
      table{min-width:900px}
      .filter-section{flex-direction:column;align-items:stretch}
      .add-btn,.btn-success{width:100%;justify-content:center}
    }
    .hidden{display:none!important}

    /* Flash */
    #flash{ position:relative; opacity:1; transition:opacity .35s ease, transform .35s ease; }
    #flash.fade-out{ opacity:0; transform:translateY(-6px); }
    .flash-close{ position:absolute; top:8px; right:10px; background:transparent; border:0; font-size:18px; line-height:1; cursor:pointer; color:inherit; }

    /* ===== Modal (View) ===== */
    .modal-overlay{
      position:fixed; inset:0; background:rgba(0,0,0,.45);
      display:none; align-items:center; justify-content:center;
      z-index:2000; padding: 20px;
    }
    .modal-overlay.show{ display:flex; }
    .modal{
      background:#fff; width:min(880px,96vw); border-radius:12px; box-shadow:var(--shadow); overflow:hidden;
    }
    .modal-header{
      display:flex; align-items:center; justify-content:space-between;
      padding:14px 18px; background:linear-gradient(135deg,var(--primary-color) 0%,var(--accent-color) 100%); color:#fff;
    }
    .modal-close{ background:transparent; border:0; color:#fff; font-size:22px; cursor:pointer; }
    .modal-body{ padding:18px; max-height:70vh; overflow:auto; }
    .modal-grid{ display:grid; grid-template-columns:140px 1fr; gap:20px; }
    .modal-photo img{ width:120px; height:120px; object-fit:cover; border-radius:12px; border:3px solid #e0e0e0; }
    .field{ margin-bottom:12px; }
    .field label{ display:block; font-size:12px; color:#666; text-transform:uppercase; letter-spacing:.4px; margin-bottom:4px; }
    .field .value{ font-size:15px; color:#222; font-weight:600; }
    .badge{ display:inline-block; padding:3px 8px; border-radius:999px; background:#eef2ff; color:#1f3a93; font-weight:600; font-size:12px; }

    /* === Pretty Filter (pill + sheet) === */
    .filter-wrap{position:relative;display:inline-block}
    .filter-pill{
      background:#fff;color:#0b1324;border:1.5px solid var(--primary-color);
      border-radius:12px;padding:10px 14px;font-weight:700;cursor:pointer;
      display:inline-flex;align-items:center;gap:10px;box-shadow:none
    }
    .filter-pill:hover{background:#f0f6ff}
    .filter-pill i{color:var(--primary-color)}
    .filter-pill .caret{border:solid var(--primary-color);border-width:0 2px 2px 0;display:inline-block;padding:3px;transform:rotate(45deg)}

    .filter-panel{
      position:absolute;top:110%;left:0;min-width:260px;max-width:320px;
      background:#fff;border:1px solid #e5e7eb;border-radius:14px;
      box-shadow:0 14px 34px rgba(0,0,0,.18);padding:8px 0;z-index:50;display:none
    }
    .filter-panel.open{display:block}

    .filter-item{
      display:flex;gap:12px;align-items:flex-start;padding:10px 14px;
      color:#0b1324;text-decoration:none
    }
    .filter-item:hover{background:#f3f6ff}
    .filter-item .dot{
      width:18px;height:18px;border:2px solid #9aa1ac;border-radius:999px;
      display:inline-flex;align-items:center;justify-content:center;margin-top:2px
    }
    .filter-item .dot .checked{width:8px;height:8px;background:#0b4a9f;border-radius:999px;display:none}
    .filter-item.active .dot{border-color:#0b4a9f}
    .filter-item.active .dot .checked{display:block}
    .filter-label{font-weight:700}
    .filter-caption{font-size:12px;color:#6b7280;margin-top:2px}
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
    <div class="nav-item">
      <a href="home.php"><i class="fas fa-home"></i> Home</a>
    </div>

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
      <div class="dropdown"><a href="about.php">System Info</a><a href="contact.php">Contact Us</a></div>
    </div>

    <div class="nav-item">
      <a href="logout.php" style="color:red;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </nav>
</header>

<div class="content-wrapper">

  <!-- Flash -->
  <?php
  $flash = $_SESSION['flash'] ?? null;
  if ($flash) {
    unset($_SESSION['flash']);
    $ok  = !empty($flash['ok']);
    $msg = $flash['msg'] ?? '';
  ?>
    <div id="flash"
         style="margin:10px 0;padding:12px 44px 12px 14px;border-radius:8px;
                background:<?= $ok ? '#e6f4ea' : '#fde8e8' ?>;
                color:<?= $ok ? '#0f5132' : '#842029' ?>;
                border:1px solid <?= $ok ? '#badbcc' : '#f5c2c7' ?>;">
      <?= htmlspecialchars($msg, ENT_QUOTES) ?>
      <button type="button" class="flash-close" aria-label="Close">&times;</button>
    </div>
  <?php } ?>

  <!-- ⬇️ Toolbar row (Filter • Add • Download) -->
  <div class="filter-section">
    <?php $self = basename($_SERVER['PHP_SELF']); ?>
    <div class="filter-wrap">
      <button class="filter-pill" id="filterTrigger" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-filter"></i> Filter By Position <span class="caret"></span>
      </button>

      <div class="filter-panel" id="filterPanel" role="menu" aria-labelledby="filterTrigger">
        <a class="filter-item <?php echo ($self==='canvassing_report.php') ? 'active' : ''; ?>" href="canvassing_report.php">
          <span class="dot"><span class="checked"></span></span>
          <div>
            <div class="filter-label">All</div>
            <div class="filter-caption">Show all positions</div>
          </div>
        </a>

        <a class="filter-item <?php echo ($self==='C_Governor.php') ? 'active' : ''; ?>" href="C_Governor.php">
          <span class="dot"><span class="checked"></span></span>
          <div><div class="filter-label">Governor</div><div class="filter-caption">Only Governor</div></div>
        </a>
        <a class="filter-item <?php echo ($self==='C_President.php') ? 'active' : ''; ?>" href="C_President.php">
          <span class="dot"><span class="checked"></span></span>
          <div><div class="filter-label">President</div><div class="filter-caption">Only President</div></div>
        </a>
        <a class="filter-item <?php echo ($self==='C_Representative.php') ? 'active' : ''; ?>" href="C_Representative.php">
          <span class="dot"><span class="checked"></span></span>
          <div><div class="filter-label">Representative</div><div class="filter-caption">Only Representative</div></div>
        </a>
        <a class="filter-item <?php echo ($self==='C_Secretary.php') ? 'active' : ''; ?>" href="C_Secretary.php">
          <span class="dot"><span class="checked"></span></span>
          <div><div class="filter-label">Secretary</div><div class="filter-caption">Only Secretary</div></div>
        </a>
        <a class="filter-item <?php echo ($self==='C_Socialmediaofficer.php') ? 'active' : ''; ?>" href="C_Socialmediaofficer.php">
          <span class="dot"><span class="checked"></span></span>
          <div><div class="filter-label">Social-Media Officer</div><div class="filter-caption">Only Social-Media Officer</div></div>
        </a>
        <a class="filter-item <?php echo ($self==='C_Treasurer.php') ? 'active' : ''; ?>" href="C_Treasurer.php">
          <span class="dot"><span class="checked"></span></span>
          <div><div class="filter-label">Treasurer</div><div class="filter-caption">Only Treasurer</div></div>
        </a>
        <a class="filter-item <?php echo ($self==='C_Vice-Governor.php') ? 'active' : ''; ?>" href="C_Vice-Governor.php">
          <span class="dot"><span class="checked"></span></span>
          <div><div class="filter-label">Vice-Governor</div><div class="filter-caption">Only Vice-Governor</div></div>
        </a>
        <a class="filter-item <?php echo ($self==='C_Vice-President.php') ? 'active' : ''; ?>" href="C_Vice-President.php">
          <span class="dot"><span class="checked"></span></span>
          <div><div class="filter-label">Vice-President</div><div class="filter-caption">Only Vice-President</div></div>
        </a>
      </div>
    </div>

    <button class="add-btn" onclick="window.location.href='add_candidate.php'">
      <i class="fas fa-plus"></i> Add Candidate
    </button>

    <?php
      $query = mysqli_query($conn, "SELECT CandidateID FROM candidate LIMIT 1");
      $row = mysqli_fetch_array($query);
      $id_excel = $row ? $row['CandidateID'] : '';
    ?>
    <form method="POST" action="canvassing_excel.php" style="display:inline">
      <input type="hidden" name="id_excel" value="<?php echo $id_excel; ?>">
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

    <table id="candidateTable">
      <thead>
        <tr>
          <th>Position</th>
          <th>Party</th>
          <th>FirstName</th>
          <th>LastName</th>
          <th>Year</th>
          <th>Photo</th>
          <th>No. of Votes</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $candidate_query = mysqli_query($conn, "SELECT * FROM candidate");
        while($candidate_rows = mysqli_fetch_array($candidate_query)) { 
          $id = (int)$candidate_rows['CandidateID'];

          $votes_query = mysqli_query($conn, "SELECT 1 FROM votes WHERE CandidateID='$id'");
          $vote_count  = mysqli_num_rows($votes_query);

          $d = function($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
        ?>
        <tr class="del<?php echo $id ?>">
          <td><?php echo $candidate_rows['Position']; ?></td>
          <td><?php echo $candidate_rows['Party']; ?></td>
          <td><?php echo $candidate_rows['FirstName']; ?></td>
          <td><?php echo $candidate_rows['LastName']; ?></td>
          <td><?php echo $candidate_rows['Year']; ?></td>
          <td>
            <img class="candidate-photo" src="<?php echo $candidate_rows['Photo'];?>"
                 alt="<?php echo $candidate_rows['FirstName']." ".$candidate_rows['LastName'];?>"
                 title="<?php echo $candidate_rows['FirstName']." ".$candidate_rows['LastName'];?>">
          </td>
          <td><?php echo $vote_count; ?></td>
          <td>
            <div class="action-buttons">
              <button type="button" class="btn btn-edit" onclick="window.location.href='edit_candidate.php?id=<?php echo $id; ?>'">
                <i class="fas fa-edit"></i> Edit
              </button>

              <button
                type="button"
                class="btn btn-view js-view"
                data-id="<?php echo $id; ?>"
                data-firstname="<?php echo $d($candidate_rows['FirstName']); ?>"
                data-middlename="<?php echo $d($candidate_rows['MiddleName']); ?>"
                data-lastname="<?php echo $d($candidate_rows['LastName']); ?>"
                data-gender="<?php echo $d($candidate_rows['Gender']); ?>"
                data-year="<?php echo $d($candidate_rows['Year']); ?>"
                data-position="<?php echo $d($candidate_rows['Position']); ?>"
                data-party="<?php echo $d($candidate_rows['Party']); ?>"
                data-photo="<?php echo $d($candidate_rows['Photo']); ?>"
                data-qualification="<?php echo $d($candidate_rows['Qualification']); ?>"
                data-votes="<?php echo $vote_count; ?>"
              >
                <i class="fas fa-eye"></i> View
              </button>

              <form method="POST" style="display:inline;"
                    onsubmit="return confirm('Are you sure you want to delete this candidate? This also removes their votes and photo.');">
                <input type="hidden" name="delete_id" value="<?php echo $id; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                <button type="submit" class="btn btn-delete">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<footer>© 2025 Online Election Voting System</footer>

<!-- ===== View Modal Markup ===== -->
<div id="viewModal" class="modal-overlay" role="dialog" aria-modal="true" aria-hidden="true">
  <div class="modal" role="document">
    <div class="modal-header">
      <h3 style="margin:0;font-size:18px;">Candidate Details</h3>
      <button type="button" class="modal-close" aria-label="Close">&times;</button>
    </div>
    <div class="modal-body">
      <div class="modal-grid">
        <div class="modal-photo">
          <img id="vmPhoto" src="" alt="Candidate photo">
        </div>
        <div>
          <div class="field"><label>Name</label><div class="value" id="vmName">—</div></div>
          <div class="field"><label>Party</label><div class="value" id="vmParty">—</div></div>
          <div class="field"><label>Position</label><div class="value" id="vmPosition" class="badge">—</div></div>
          <div class="field"><label>Gender</label><div class="value" id="vmGender">—</div></div>
          <div class="field"><label>Year Level</label><div class="value" id="vmYear">—</div></div>
          <div class="field"><label>Votes</label><div class="value" id="vmVotes">0</div></div>
        </div>
      </div>
      <div class="field" style="margin-top:16px;">
        <label>Qualification</label>
        <div class="value" id="vmQual" style="white-space:pre-wrap; font-weight:500;">—</div>
      </div>
    </div>
  </div>
</div>

<input type="hidden" class="pc_date" name="pc_date"/>
<input type="hidden" class="pc_time" name="pc_time"/>

<script>
/* Search + "no results" row */
(function () {
  const searchInput = document.getElementById('searchInput');
  const table = document.getElementById('candidateTable');
  const tbody = table.querySelector('tbody');
  const COLS = table.tHead.rows[0].cells.length;

  function debounce(fn, wait) {
    let t;
    return function (...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), wait); };
  }

  function getNoResultsRow() {
    let row = tbody.querySelector('tr.no-results-row');
    if (!row) {
      row = document.createElement('tr');
      row.className = 'no-results-row';
      const cell = document.createElement('td');
      cell.colSpan = COLS;
      cell.style.textAlign = 'center';
      cell.style.padding = '24px';
      cell.style.color = '#666';
      cell.textContent = 'No matching candidates found.';
      row.appendChild(cell);
      tbody.appendChild(row);
    } else {
      row.firstElementChild.colSpan = COLS;
    }
    return row;
  }

  function filterRows(query) {
    const q = query.trim().toLowerCase();
    const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => !r.classList.contains('no-results-row'));
    let visibleCount = 0;

    rows.forEach(row => {
      const cells = row.querySelectorAll('td');
      const indices = [0,1,2,3,4,6]; // position, party, first, last, year, votes
      const haystack = indices.map(i => cells[i]?.textContent?.toLowerCase() ?? '').join(' | ');
      const match = q === '' || haystack.includes(q);
      row.style.display = match ? '' : 'none';
      if (match) visibleCount++;
    });

    const noRow = getNoResultsRow();
    noRow.style.display = (visibleCount === 0) ? '' : 'none';
  }

  filterRows('');
  searchInput.addEventListener('input', debounce((e) => filterRows(e.target.value), 120));
})();

/* Flash auto-dismiss */
(function () {
  const flash = document.getElementById('flash');
  if (!flash) return;
  const btn = flash.querySelector('.flash-close');
  if (btn) btn.addEventListener('click', () => flash.remove());
  setTimeout(() => { flash.classList.add('fade-out'); setTimeout(() => flash.remove(), 400); }, 3000);
})();

/* ===== View Modal logic ===== */
(function () {
  const overlay = document.getElementById('viewModal');
  const closeBtn = overlay.querySelector('.modal-close');
  const table = document.getElementById('candidateTable');

  const el = (id) => document.getElementById(id);

  function openModalFromButton(btn) {
    const ds = btn.dataset;
    const name = [ds.firstname, ds.middlename, ds.lastname].filter(Boolean).join(' ');
    el('vmName').textContent = name || '—';
    el('vmParty').textContent = ds.party || '—';
    el('vmPosition').textContent = ds.position || '—';
    el('vmGender').textContent = ds.gender || '—';
    el('vmYear').textContent = ds.year || '—';
    el('vmVotes').textContent = ds.votes || '0';
    el('vmQual').textContent = ds.qualification || '—';

    const src = ds.photo && ds.photo.trim() !== '' ? ds.photo : 'images/user.png';
    const img = el('vmPhoto');
    img.src = src;
    img.alt = name ? (name + ' photo') : 'Candidate photo';

    overlay.classList.add('show');
    overlay.setAttribute('aria-hidden', 'false');
  }
  function closeModal() {
    overlay.classList.remove('show');
    overlay.setAttribute('aria-hidden', 'true');
  }

  table.addEventListener('click', function(e){
    const btn = e.target.closest('.js-view');
    if (!btn) return;
    e.preventDefault();
    openModalFromButton(btn);
  });

  closeBtn.addEventListener('click', closeModal);
  overlay.addEventListener('click', (e) => { if (e.target === overlay) closeModal(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
})();

/* === Filter pill toggle + click outside === */
(function(){
  const trigger = document.getElementById('filterTrigger');
  const panel   = document.getElementById('filterPanel');
  if(!trigger || !panel) return;

  trigger.addEventListener('click', (e)=>{
    e.preventDefault();
    const open = panel.classList.toggle('open');
    trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
  });

  document.addEventListener('click', (e)=>{
    if (!panel.contains(e.target) && !trigger.contains(e.target)) {
      panel.classList.remove('open');
      trigger.setAttribute('aria-expanded','false');
    }
  });
})();
</script>
</body>
</html>
