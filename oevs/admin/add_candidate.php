<?php
include('session.php');
include('dbcon.php');

$allowedPositions = [
  'President','Vice-President','Governor','Vice-Governor',
  'Secretary','Treasurer','Social-Media Officer','Representative'
];

$allowedYears = ['1st year','2nd year','3rd year','4th year'];

$errors  = [];
$success = false;

// ---------- helpers ----------
function slugify($s){
  return strtolower(trim(preg_replace('/[^a-z0-9]+/i','-',$s),'-'));
}
function is_numeric_type($t){
  $t = strtolower($t);
  return in_array($t, ['int','tinyint','smallint','mediumint','bigint','decimal','float','double','real','bit']);
}
function is_datetime_type($t){
  $t = strtolower($t);
  return in_array($t, ['timestamp','datetime','date','time','year']);
}
function param_type_for($t){
  $t = strtolower($t);
  if (in_array($t, ['decimal','float','double','real'])) return 'd';
  if (in_array($t, ['int','tinyint','smallint','mediumint','bigint','bit'])) return 'i';
  return 's';
}

// Upload dir (absolute path)
$uploadDir = __DIR__ . '/images/candidates';
if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
  $errors[] = 'Upload folder not writable: ' . htmlspecialchars($uploadDir);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // ---------- read inputs ----------
  $position    = trim($_POST['position']   ?? '');
  $firstName   = trim($_POST['firstname']  ?? '');
  $lastName    = trim($_POST['lastname']   ?? '');
  $middleName  = trim($_POST['middlename'] ?? '');
  $party       = trim($_POST['party']      ?? '');
  $gender      = trim($_POST['gender']     ?? '');
  $yearText    = trim($_POST['year']       ?? '');
  $qualif      = trim($_POST['qualification'] ?? '');

  // ---------- validate ----------
  if ($position === '' || !in_array($position, $allowedPositions, true)) {
    $errors[] = 'Please choose a valid Position.';
  }
  if ($firstName === '') { $errors[] = 'First Name is required.'; }
  if ($lastName === '')  { $errors[] = 'Last Name is required.'; }
  if ($gender === '' || !in_array($gender, ['Male','Female','Other'], true)) {
    $errors[] = 'Please select a valid Gender.';
  }
  if ($yearText === '' || !in_array($yearText, $allowedYears, true)) {
    $errors[] = 'Please select a valid Year (e.g., 1st year, 2nd year, 3rd year, 4th year).';
  }

  // ---------- file upload ----------
  $photoPathRel = '';
  $destAbs = '';
  if (!isset($_FILES['photo']) || (int)$_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
    $errors[] = 'Candidate photo is required.';
  } else {
    $file    = $_FILES['photo'];
    $err     = (int)$file['error'];
    $tmpPath = $file['tmp_name'] ?? '';
    $name    = $file['name'] ?? 'photo';
    $size    = (int)($file['size'] ?? 0);

    if ($err !== UPLOAD_ERR_OK) {
      $errors[] = 'Error uploading file (code: '.$err.').';
    } else {
      if ($size > 5 * 1024 * 1024) {
        $errors[] = 'Photo must be 5MB or smaller.';
      }
      $info = @getimagesize($tmpPath);
      if ($info === false) {
        $errors[] = 'Uploaded file is not a valid image.';
      } else {
        $mime = $info['mime'] ?? '';
        $allowedMimes = ['image/jpeg','image/png','image/webp'];
        if (!in_array($mime, $allowedMimes, true)) {
          $errors[] = 'Allowed image types: JPG, PNG, WEBP.';
        }
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) {
          $ext = ($mime === 'image/png') ? 'png' : (($mime === 'image/webp') ? 'webp' : 'jpg');
        }
        $unique = date('YmdHis') . '-' . bin2hex(random_bytes(4));
        $safe   = slugify($firstName).'-'.slugify($lastName).'-'.$unique.'.'.$ext;

        $destAbs = rtrim($uploadDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$safe;
        $photoPathRel = 'images/candidates/'.$safe; // relative path saved in DB
      }
    }
  }

  if (empty($errors)) {
    if (!@move_uploaded_file($_FILES['photo']['tmp_name'], $destAbs)) {
      $errors[] = 'Failed to save uploaded image.';
    }
  }

  // ---------- build INSERT that satisfies NOT NULL no-default columns ----------
  if (empty($errors)) {

    // Table columns + metadata
    $colInfo = [];
    $metaQ = $conn->query("
      SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA
      FROM INFORMATION_SCHEMA.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'candidate'
      ORDER BY ORDINAL_POSITION
    ");
    if (!$metaQ) {
      $errors[] = 'Cannot read table metadata for `candidate`.';
    } else {
      while ($r = $metaQ->fetch_assoc()) {
        $colInfo[$r['COLUMN_NAME']] = $r;
      }
      $metaQ->free();
    }

    if (empty($errors)) {
      // Base data we actually collect
      $base = [
        'Position'      => $position,
        'Party'         => $party,
        'FirstName'     => $firstName,
        'LastName'      => $lastName,
        'MiddleName'    => $middleName,
        'Gender'        => $gender,
        'Year'          => $yearText,   // back-ticked in SQL later
        'Photo'         => $photoPathRel,
        'Qualification' => $qualif,
      ];

      // Start with base, then add any other NOT NULL + no-default columns
      $insertData = [];

      foreach ($colInfo as $col => $info) {
        // skip auto-increment columns
        if (strpos(strtolower($info['EXTRA']), 'auto_increment') !== false) continue;

        if (array_key_exists($col, $base)) {
          $insertData[$col] = $base[$col];
          continue;
        }

        // If column is nullable or has a default, we can skip sending it.
        $nullable = strtoupper($info['IS_NULLABLE']) === 'YES';
        $hasDefault = !is_null($info['COLUMN_DEFAULT']);

        if ($nullable || $hasDefault) {
          // don’t send; DB will handle
          continue;
        }

        // Otherwise, it's required: provide a safe fallback by data type
        $dt = strtolower($info['DATA_TYPE']);
        if (is_numeric_type($dt)) {
          $insertData[$col] = 0;
        } elseif (is_datetime_type($dt)) {
          // current timestamp/date/time as string
          if ($dt === 'date') {
            $insertData[$col] = date('Y-m-d');
          } elseif ($dt === 'time') {
            $insertData[$col] = date('H:i:s');
          } else {
            $insertData[$col] = date('Y-m-d H:i:s');
          }
        } elseif ($dt === 'json') {
          $insertData[$col] = '{}';
        } else {
          // char/varchar/text/blob/etc.
          $insertData[$col] = '';
        }
      }

      // If somehow we still ended up with nothing to insert (shouldn’t happen), bail
      if (!$insertData) {
        $errors[] = 'No insertable columns resolved from table metadata.';
      } else {
        // Build SQL in table order so it’s predictable
        $columns = array_keys($insertData);

        // Map param types using real column data types
        $types = '';
        foreach ($columns as $c) {
          $t = isset($colInfo[$c]) ? param_type_for($colInfo[$c]['DATA_TYPE']) : 's';
          $types .= $t;
        }

        $placeholders = implode(',', array_fill(0, count($columns), '?'));
        $quotedCols = implode('`,`', $columns);
        $sql = "INSERT INTO `candidate` (`$quotedCols`) VALUES ($placeholders)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
          $errors[] = 'Database error preparing statement.';
          if ($photoPathRel && $destAbs && file_exists($destAbs)) { @unlink($destAbs); }
        } else {
          // mysqli bind_param needs references
          $values = array_values($insertData);
          $bind = [];
          $bind[] = $types;
          // create referenced array
          for ($i=0; $i<count($values); $i++) {
            $bind[] = &$values[$i];
          }
          call_user_func_array([$stmt, 'bind_param'], $bind);

          try {
            $stmt->execute();
            $stmt->close();
            header("Location: candidate_list.php?added=1&name=".urlencode($firstName.' '.$lastName));
            exit;
          } catch (Throwable $e) {
            $errors[] = 'Database error: '.htmlspecialchars($e->getMessage());
            if ($photoPathRel && $destAbs && file_exists($destAbs)) { @unlink($destAbs); }
          }
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Candidate - Online Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #002f6c;
      --accent-color: #0056b3;
      --bg-color: #f4f6f8;
      --white: #fff;
      --shadow: 0 4px 12px rgba(0,0,0,.1);
      --transition: all .3s ease;
      --font: 'Inter', sans-serif;
    }
    body { font-family: var(--font); background: var(--bg-color); margin:0; }
    header { position: sticky; top:0; z-index:1000; background: var(--white); box-shadow: var(--shadow); padding: 10px 30px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; }
    .logo-section{display:flex;align-items:center;gap:10px}
    .logo-section img{height:40px}
    .title{font-weight:700;font-size:18px;color:var(--primary-color);line-height:1.2}
    .content-wrapper{max-width:900px;margin:30px auto;padding:0 20px}
    .card{background:#fff;border-radius:12px;box-shadow:var(--shadow);padding:22px}
    h1{margin:0 0 16px;font-size:22px;color:#0d2946}
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    .field{display:flex;flex-direction:column;gap:8px}
    label{font-weight:600;color:#102a43}
    input[type="text"], select, textarea{ padding:10px 12px;border:1px solid #d0d7de;border-radius:8px;font-size:14px;outline:none; }
    input[type="file"]{font-size:14px}
    textarea{min-height:90px;resize:vertical}
    .actions{margin-top:18px;display:flex;gap:10px;flex-wrap:wrap}
    .btn{ display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:8px;border:none;cursor:pointer;font-weight:700;box-shadow:0 2px 4px rgba(0,0,0,.08);transition:var(--transition); text-decoration:none; }
    .btn-primary{background:var(--primary-color);color:#fff}
    .btn-primary:hover{background:var(--accent-color);transform:translateY(-1px)}
    .btn-ghost{background:#eef4ff;color:#163d7a}
    .alert{padding:12px 14px;border-radius:10px;margin-bottom:14px;font-size:14px}
    .alert-error{background:#ffe8e8;color:#7a1f1f;border:1px solid #ffc7c7}
    .help{font-size:12px;color:#6b7280}
    .preview{margin-top:10px;max-height:120px;border-radius:10px;border:1px solid #e5e7eb}
    @media (max-width:768px){.grid{grid-template-columns:1fr}}
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
    <a class="btn btn-ghost" href="candidate_list.php"><i class="fa fa-arrow-left"></i> Back to Candidates</a>
  </nav>
</header>

<div class="content-wrapper">
  <div class="card">
    <h1><i class="fa fa-user-plus"></i> Add Candidate</h1>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <strong>Kindly fix the following:</strong>
        <ul style="margin:8px 0 0 18px;">
          <?php foreach ($errors as $e): ?>
            <li><?php echo htmlspecialchars($e); ?></li>
          <?php endforeach; ?>
        </ul>
        <div class="help" style="margin-top:6px">
          We now auto-fill required DB-only columns (e.g., <code>abc</code>) with safe defaults.
          If you don’t want a column saved, make it NULLable or give it a DEFAULT in MySQL.
        </div>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" autocomplete="off">
      <div class="grid">
        <div class="field">
          <label for="position">Position</label>
          <select id="position" name="position" required>
            <option value="" disabled <?php echo empty($_POST['position'])?'selected':''; ?>>Select position</option>
            <?php foreach ($allowedPositions as $p): ?>
              <option value="<?php echo htmlspecialchars($p); ?>"
                <?php echo (isset($_POST['position']) && $_POST['position']===$p) ? 'selected':''; ?>>
                <?php echo htmlspecialchars($p); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <span class="help">Must match your filtering pages (e.g., C_President.php).</span>
        </div>

        <div class="field">
          <label for="party">Party</label>
          <input type="text" id="party" name="party"
                 value="<?php echo htmlspecialchars($_POST['party'] ?? ''); ?>"
                 placeholder="e.g., Team 2">
          <span class="help">Leave blank if independent.</span>
        </div>

        <div class="field">
          <label for="firstname">First Name</label>
          <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>" required>
        </div>

        <div class="field">
          <label for="lastname">Last Name</label>
          <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>" required>
        </div>

        <div class="field">
          <label for="middlename">Middle Name</label>
          <input type="text" id="middlename" name="middlename" value="<?php echo htmlspecialchars($_POST['middlename'] ?? ''); ?>">
        </div>

        <div class="field">
          <label for="gender">Gender</label>
          <select id="gender" name="gender" required>
            <option value="" disabled <?php echo empty($_POST['gender'])?'selected':''; ?>>Select gender</option>
            <?php foreach (['Male','Female','Other'] as $g): ?>
              <option value="<?php echo $g; ?>" <?php echo (($_POST['gender'] ?? '')===$g)?'selected':''; ?>><?php echo $g; ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="year">Year</label>
          <select id="year" name="year" required>
            <option value="" disabled <?php echo empty($_POST['year'])?'selected':''; ?>>Select year level</option>
            <?php foreach ($allowedYears as $y): ?>
              <option value="<?php echo $y; ?>" <?php echo (($_POST['year'] ?? '')===$y)?'selected':''; ?>><?php echo $y; ?></option>
            <?php endforeach; ?>
          </select>
          <span class="help">Matches your DB values (e.g., “4th year”).</span>
        </div>

        <div class="field" style="grid-column: 1 / -1;">
          <label for="qualification">Qualification</label>
          <textarea id="qualification" name="qualification"
            placeholder="Optional: short candidate bio/qualification"><?php echo htmlspecialchars($_POST['qualification'] ?? ''); ?></textarea>
        </div>

        <div class="field" style="grid-column: 1 / -1;">
          <label for="photo">Photo (JPG/PNG/WEBP, ≤ 5MB)</label>
          <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
          <img id="preview" class="preview" alt="" style="display:none;">
        </div>
      </div>

      <div class="actions">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Candidate</button>
        <a href="candidate_list.php" class="btn btn-ghost"><i class="fa fa-times"></i> Cancel</a>
      </div>
    </form>
  </div>
</div>

<script>
  const input = document.getElementById('photo');
  const preview = document.getElementById('preview');
  input.addEventListener('change', () => {
    const f = input.files && input.files[0];
    if (!f) { preview.style.display='none'; return; }
    if (!['image/jpeg','image/png','image/webp'].includes(f.type)) { preview.style.display='none'; return; }
    const url = URL.createObjectURL(f);
    preview.src = url;
    preview.style.display = 'block';
  });
</script>
</body>
</html>
