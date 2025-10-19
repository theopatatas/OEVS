<?php
include('session.php');
include('dbcon.php');

// Fetch current user
$query = mysqli_query($conn, "SELECT * FROM users WHERE User_id = '$id_session'") or die(mysqli_error($conn));
$row   = mysqli_fetch_assoc($query);

// Update handler
$successMsg = $errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
  $firstname = trim($_POST['firstname'] ?? '');
  $lastname  = trim($_POST['lastname'] ?? '');
  $username  = trim($_POST['username'] ?? '');
  $password  = trim($_POST['password'] ?? '');

  if ($firstname === '' || $lastname === '' || $username === '') {
    $errorMsg = 'Please complete the required fields.';
  } else {
    if ($password !== '') {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("UPDATE users SET FirstName=?, LastName=?, UserName=?, Password=? WHERE User_id=?");
      $stmt->bind_param("ssssi", $firstname, $lastname, $username, $hash, $id_session);
    } else {
      $stmt = $conn->prepare("UPDATE users SET FirstName=?, LastName=?, UserName=? WHERE User_id=?");
      $stmt->bind_param("sssi", $firstname, $lastname, $username, $id_session);
    }

    if ($stmt && $stmt->execute()) {
      $successMsg = 'Profile updated successfully.';
      $query = mysqli_query($conn, "SELECT * FROM users WHERE User_id = '$id_session'") or die(mysqli_error($conn));
      $row   = mysqli_fetch_assoc($query);
    } else {
      $errorMsg = 'Update failed. Please try again.';
    }
    if ($stmt) $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile — Online Election Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root{
      --primary:#002f6c;
      --accent:#0b57cf;
      --bg:#f5f7fb;
      --panel:#ffffff;
      --ink:#0f172a;
      --muted:#64748b;
      --line:#e6e9ef;
      --success-bg:#e9f9ee; --success-ink:#0f7a38; --success-line:#c6f1d6;
      --danger-bg:#fdecec;  --danger-ink:#a42828;  --danger-line:#f7c9c9;
      --radius:12px;
      --shadow:0 6px 18px rgba(2, 32, 71, .06);
      --focus:0 0 0 4px rgba(11, 87, 207, .15);
    }
    *{box-sizing:border-box}
    html,body{margin:0;padding:0}
    body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--ink)}

    /* Layout */
    .page{max-width:1120px;margin:36px auto;padding:0 16px}
    .page-title{display:flex;align-items:center;gap:10px;color:var(--primary)}
    .page-title h1{margin:0;font-size:22px}
    .grid{display:grid;grid-template-columns:360px 1fr;gap:20px;margin-top:16px}
    @media (max-width:980px){.grid{grid-template-columns:1fr}}

    /* Cards */
    .card{background:var(--panel);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow)}
    .card-header{padding:14px 18px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:10px;background:#fafbfd}
    .card-body{padding:18px}

    /* Summary */
    .summary{display:flex;align-items:center;gap:14px}
    .avatar{height:72px;width:72px;border-radius:50%;display:grid;place-items:center;
      background:radial-gradient(120px 120px at 30% 30%, #2f6cdf, #1842a0);
      color:#fff;font-weight:700;font-size:28px;box-shadow:0 6px 16px rgba(24,66,160,.25)
    }
    .summary h2{margin:0 0 2px 0;font-size:18px}
    .summary small{color:var(--muted)}
    .meta{margin-top:14px;display:grid;gap:8px}
    .meta-row{display:flex;align-items:center;gap:10px;color:#111827}
    .meta-row i{color:var(--accent)}

    /* Form */
    .form-grid{display:grid;grid-template-columns:1fr;gap:12px}
    .form-group{display:grid;gap:6px}
    label{font-weight:600;color:#0b1220}
    .hint{font-size:12px;color:var(--muted)}
    input.form-control{
      height:44px;border:1px solid var(--line);border-radius:10px;background:#fff;
      padding:10px 12px;font:inherit;transition:border .15s, box-shadow .15s
    }
    input.form-control:focus{outline:none;border-color:var(--accent);box-shadow:var(--focus)}
    input[readonly]{background:#f8fafc;color:#0f172a}

    .actions{display:flex;gap:10px;margin-top:6px}
    .btn{border:1px solid transparent;border-radius:10px;height:40px;padding:0 14px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:8px}
    .btn-primary{background:var(--accent);color:#fff}
    .btn-primary:hover{filter:brightness(.96)}
    .btn-ghost{background:#fff;border-color:var(--line);color:var(--ink)}
    .btn-ghost:hover{background:#f6f8fc}

    /* Alerts */
    .alert{padding:10px 12px;border-radius:10px;margin-bottom:12px;border:1px solid}
    .alert-success{background:var(--success-bg);color:var(--success-ink);border-color:var(--success-line)}
    .alert-danger{background:var(--danger-bg);color:var(--danger-ink);border-color:var(--danger-line)}

    footer{text-align:center;color:#6b7280;font-size:14px;margin:28px 0}
    @media (max-width:520px){
      .actions{flex-direction:column}
      .btn{justify-content:center}
    }
  </style>
</head>
<body>
  <?php
    // Use the shared header (no Contact Us, correct hover)
    $activePage = 'profile';
    include 'header.php';
  ?>

  <!-- Content -->
  <div class="page">
    <div class="page-title">
      <i class="fa-solid fa-id-card-clip"></i>
      <h1>My Profile</h1>
    </div>

    <div class="grid">
      <!-- Profile summary -->
      <section class="card">
        <div class="card-header">
          <i class="fa-solid fa-user"></i><strong>Profile Summary</strong>
        </div>
        <div class="card-body">
          <div class="summary">
            <div class="avatar">
              <?php echo strtoupper(substr(($row['FirstName'] ?? 'U'), 0, 1)); ?>
            </div>
            <div>
              <h2><?php echo htmlspecialchars(($row['FirstName'] ?? '').' '.($row['LastName'] ?? '')); ?></h2>
              <small>Username: <?php echo htmlspecialchars($row['UserName'] ?? ''); ?></small>
            </div>
          </div>

          <div class="meta">
            <div class="meta-row"><i class="fa-solid fa-briefcase"></i> <span>Position: <strong><?php echo htmlspecialchars($row['Position'] ?? '—'); ?></strong></span></div>
            <div class="meta-row"><i class="fa-solid fa-user-shield"></i> <span>User Type: <strong><?php echo htmlspecialchars($row['User_Type'] ?? '—'); ?></strong></span></div>
            <div class="meta-row"><i class="fa-solid fa-hashtag"></i> <span>User ID: <strong><?php echo htmlspecialchars($row['User_id'] ?? '—'); ?></strong></span></div>
          </div>
        </div>
      </section>

      <!-- Edit form -->
      <section class="card">
        <div class="card-header">
          <i class="fa-solid fa-pen-to-square"></i><strong>Edit Your Profile</strong>
        </div>
        <div class="card-body">
          <?php if ($successMsg): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($successMsg); ?></div>
          <?php endif; ?>
          <?php if ($errorMsg): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($errorMsg); ?></div>
          <?php endif; ?>

          <form method="post" class="form-grid" autocomplete="off">
            <div class="form-group">
              <label>First Name</label>
              <input type="text" name="firstname" value="<?php echo htmlspecialchars($row['FirstName'] ?? ''); ?>" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Last Name</label>
              <input type="text" name="lastname" value="<?php echo htmlspecialchars($row['LastName'] ?? ''); ?>" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Username</label>
              <input type="text" name="username" value="<?php echo htmlspecialchars($row['UserName'] ?? ''); ?>" class="form-control" required>
            </div>

            <div class="form-group">
              <label>New Password <span class="hint">(leave blank to keep current)</span></label>
              <input type="password" name="password" class="form-control" placeholder="••••••••">
              <div class="hint">Strong passwords use 8+ characters with letters, numbers, and symbols.</div>
            </div>

            <div class="form-group">
              <label>Position</label>
              <input type="text" value="<?php echo htmlspecialchars($row['Position'] ?? ''); ?>" class="form-control" readonly>
            </div>

            <div class="actions">
              <button type="submit" name="save" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
              <a href="home.php" class="btn btn-ghost">Cancel</a>
            </div>
          </form>
        </div>
      </section>
    </div>

    <footer>© 2025 Online Election Voting System</footer>
  </div>
</body>
</html>
