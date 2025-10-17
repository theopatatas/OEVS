<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('session.php');
include('dbcon.php');

$get_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($get_id <= 0) { header("Location: candidate_list.php"); exit; }

$res = mysqli_query($conn, "SELECT * FROM candidate WHERE CandidateID = $get_id") or die(mysqli_error($conn));
$row = mysqli_fetch_assoc($res);
if (!$row) { header("Location: candidate_list.php"); exit; }

function h($v){ return htmlspecialchars($v ?? '', ENT_QUOTES); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Candidate - Online Voting System</title>
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
    /* === Equal-size fields fix (strict 2-col grid) === */
    *, *::before, *::after { box-sizing: border-box; }

    body { font-family: var(--font); background-color: var(--bg-color); margin: 0; color: #1f2937; }

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
    .logo-section { display:flex; align-items:center; gap:10px; }
    .logo-section img { height: 40px; }
    .logo-section .title { font-weight: 700; font-size: 18px; color: var(--primary-color); line-height: 1.2; }

    nav { display: flex; align-items: center; gap: 25px; }
    .nav-item { position: relative; }
    .nav-item > a {
      text-decoration: none; color: var(--primary-color); font-weight: 600;
      padding: 8px 12px; border-radius: 6px; display: inline-block; transition: var(--transition);
    }
    .nav-item > a:hover { background-color: var(--primary-color); color: #fff; }

    .dropdown { display:none; position:absolute; top:100%; left:0; background:var(--white);
      box-shadow: var(--shadow); border-radius: 6px; min-width: 200px; padding: 8px 0; z-index: 99; white-space: nowrap; }
    .dropdown a { display:block; padding:10px 15px; text-decoration:none; color:var(--primary-color); font-weight:500; transition:var(--transition); }
    .dropdown a:hover { background-color: var(--accent-color); color: #fff; }
    .nav-item:hover > .dropdown { display:block; }

    .submenu { display:none; position:absolute; top:0; left:100%; background:var(--white); box-shadow:var(--shadow); border-radius:6px; min-width:220px; padding:8px 0; }
    .has-submenu { position:relative; }
    .has-submenu > a { display:flex; justify-content:space-between; align-items:center; }
    .has-submenu > a i.fa-chevron-right { font-size:12px; margin-left:8px; }
    .has-submenu:hover > .submenu { display:block; }

    .content-wrapper { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
    h1.page-title { font-size: 22px; color: #0b2b6a; margin: 12px 0 16px; font-weight: 700; }

    .top-actions { display:flex; justify-content:flex-end; }
    .btn-back {
      background:#fff; color:#0b55c2; border:2px solid #0b55c2; padding:10px 14px; border-radius:10px;
      font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:8px;
    }
    .btn-back:hover { background:#eff6ff; }

    .card { background: var(--white); border-radius: 12px; box-shadow: var(--shadow); padding: 22px; }

    /* Grid enforcing equal columns */
    .form-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
    }
    .col-6  { grid-column: span 1; }
    .col-12 { grid-column: 1 / -1; }

    .form-group label { display:block; font-weight:600; color:#092c5c; margin-bottom:6px; }
    .form-group input[type="text"],
    .form-group select,
    .form-group textarea {
      width: 100%;
      max-width: 100%;
      padding: 10px 12px;
      border: 1px solid #dcdfe4;
      border-radius: 8px;
      font-size: 14px;
      transition: border .2s ease, box-shadow .2s ease;
      background: #fff;
    }
    .form-group input[type="text"],
    .form-group select { height: 44px; }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: var(--accent-color);
      box-shadow: 0 0 0 3px rgba(0,86,179,.12);
    }

    .photo-wrap { display:flex; align-items:center; gap:16px; }
    .photo-thumb {
      width: 120px; height: 120px; border-radius: 12px; object-fit: cover;
      border: 3px solid #e6edf6; background:#f7f9fc;
    }
    .help { color:#6b7280; font-size:12px; }

    .actions { display:flex; justify-content:flex-start; gap:12px; margin-top: 4px; }
    .btn-primary {
      background: var(--accent-color); color:#fff; border:none; padding:10px 16px; border-radius:10px;
      font-weight:700; cursor:pointer;
    }
    .btn-primary:hover { filter: brightness(0.95); }

    footer { text-align:center; padding:20px 0; color:#666; font-size:14px; }

    @media (max-width: 800px) {
      .form-grid { grid-template-columns: 1fr; }
      .col-6 { grid-column: span 1; }
      .photo-wrap { flex-direction: column; align-items: flex-start; }
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

      <div class="nav-item"><a href="logout.php" style="color:red;"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </nav>
  </header>

  <div class="content-wrapper">
    <div class="top-actions">
      <a href="candidate_list.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back to list</a>
    </div>
    <h1 class="page-title">Edit Candidate</h1>

    <div class="card">
      <form method="POST" enctype="multipart/form-data" class="form-grid" autocomplete="off">
        <input type="hidden" name="user_name" value="<?php echo h($_SESSION['User_Type'] ?? 'admin'); ?>"/>

        <!-- First / Last -->
        <div class="form-group col-6">
          <label for="rfirstname">First Name</label>
          <input type="text" id="rfirstname" name="rfirstname" value="<?php echo h($row['FirstName']); ?>">
        </div>
        <div class="form-group col-6">
          <label for="rlastname">Last Name</label>
          <input type="text" id="rlastname" name="rlastname" value="<?php echo h($row['LastName']); ?>">
        </div>

        <!-- Middle / Gender -->
        <div class="form-group col-6">
          <label for="rname">Middle Name</label>
          <input type="text" id="rname" name="rname" value="<?php echo h($row['MiddleName']); ?>">
        </div>
        <div class="form-group col-6">
          <label for="rgender">Gender</label>
          <select id="rgender" name="rgender">
            <?php
              $genders = ['Male','Female'];
              foreach ($genders as $g) {
                $sel = (strcasecmp($row['Gender'],$g)===0) ? 'selected' : '';
                echo "<option $sel>".h($g)."</option>";
              }
            ?>
          </select>
        </div>

        <!-- Year / Position -->
        <div class="form-group col-6">
          <label for="ryear">Year Level</label>
          <select id="ryear" name="ryear">
            <?php
              $years = ['1st year','2nd year','3rd year','4th year'];
              foreach ($years as $y) {
                $sel = (strcasecmp($row['Year'],$y)===0) ? 'selected' : '';
                echo "<option $sel>".h($y)."</option>";
              }
            ?>
          </select>
        </div>
        <div class="form-group col-6">
          <label for="rposition">Position</label>
          <select id="rposition" name="rposition">
            <?php
              $positions = ['President','Vice-President','Governor','Vice-Governor','Secretary','Treasurer','Social-Media Officer','Representative'];
              foreach ($positions as $p) {
                $sel = (strcasecmp($row['Position'],$p)===0) ? 'selected' : '';
                echo "<option $sel>".h($p)."</option>";
              }
            ?>
          </select>
        </div>

        <!-- Party (half) + spacer to keep grid symmetry -->
        <div class="form-group col-6">
          <label for="party">Party</label>
          <input type="text" id="party" name="party" value="<?php echo h($row['Party']); ?>">
        </div>
        <div class="col-6" aria-hidden="true"></div>

        <!-- Qualification (full width) -->
        <div class="form-group col-12">
          <label for="qualification">Qualification</label>
          <textarea id="qualification" name="qualification" rows="6" placeholder="Enter candidate qualification..."><?php echo h($row['Qualification']); ?></textarea>
        </div>

        <!-- Photo (full width) -->
        <div class="form-group col-12">
          <label>Photo</label>
          <div class="photo-wrap">
            <?php if (!empty($row['Photo'])): ?>
              <img class="photo-thumb" src="<?php echo h($row['Photo']); ?>" alt="Candidate Photo">
            <?php else: ?>
              <div class="photo-thumb" style="display:flex;align-items:center;justify-content:center;color:#9aa4b2;">No photo</div>
            <?php endif; ?>
            <div>
              <input type="file" name="image" accept="image/*">
              <div class="help">Uploading a new photo will replace the current one.</div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="actions col-12">
          <button type="submit" name="save" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save</button>
        </div>
      </form>
    </div>
  </div>

  <footer>© 2025 Online Election Voting System</footer>
</body>
</html>

<?php
// ====== Update handler ======
if (isset($_POST['save'])) {
    $user_name   = $_POST['user_name'] ?? 'admin';
    $rfirstname  = mysqli_real_escape_string($conn, $_POST['rfirstname'] ?? '');
    $rlastname   = mysqli_real_escape_string($conn, $_POST['rlastname'] ?? '');
    $rgender     = mysqli_real_escape_string($conn, $_POST['rgender'] ?? '');
    $ryear       = mysqli_real_escape_string($conn, $_POST['ryear'] ?? '');
    $rposition   = mysqli_real_escape_string($conn, $_POST['rposition'] ?? '');
    $rmname      = mysqli_real_escape_string($conn, $_POST['rname'] ?? '');
    $party       = mysqli_real_escape_string($conn, $_POST['party'] ?? '');
    $qualification = mysqli_real_escape_string($conn, $_POST['qualification'] ?? '');

    $location_sql = '';
    if (!empty($_FILES['image']['tmp_name'])) {
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_name = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($_FILES['image']['name']));
        $upload_dir = "upload/";
        if (!is_dir($upload_dir)) { @mkdir($upload_dir, 0777, true); }
        $target_file = $upload_dir . $image_name;

        if (move_uploaded_file($image_tmp_name, $target_file)) {
            $location_sql = ", Photo = '" . mysqli_real_escape_string($conn, $target_file) . "'";
        } else {
            die("Failed to upload image.");
        }
    }

    $sql = "UPDATE candidate SET 
              FirstName = '$rfirstname',
              LastName = '$rlastname',
              Gender = '$rgender',
              Year = '$ryear',
              Position = '$rposition',
              MiddleName = '$rmname',
              Party = '$party',
              Qualification = '$qualification'
              $location_sql
            WHERE CandidateID = '$get_id'";

    if (mysqli_query($conn, $sql)) {
        // history log
        $fullname = trim($rfirstname . ' ' . $rlastname);
        $action = "Edit Candidate";
        $stmt = $conn->prepare("INSERT INTO history (data, action, date, user) VALUES (?, ?, NOW(), ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $fullname, $action, $user_name);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: candidate_list.php");
        exit();
    } else {
        die("Error updating candidate: " . mysqli_error($conn));
    }
}
?>
