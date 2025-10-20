<?php
include('session.php');
include('dbcon.php');

/* ------------ CONFIG ------------ */
$allowedPositions = [
  'President','Vice-President','Governor','Vice-Governor',
  'Secretary','Treasurer','Social-Media Officer','Representative'
];

$allowedYears = ['1st year','2nd year','3rd year','4th year'];

/* Departments + Courses (dependent select)
   - CAS is special: shows ALL courses across departments */
$deptCourses = [
  'CMA'  => [
    '__COLLEGE__' => 'College of Management and Accountancy',
    'Bachelor of Science in Accountancy',
    'Bachelor of Science in Business Administration',
    'Bachelor of Science in Management Accounting',
    'Bachelor of Science in Accounting Information System',
    'Bachelor of Science in Entrepreneurship',
    'Bachelor of Science in Hospitality Management',
    'Bachelor of Science in Tourism Management',
  ],
  'CELA' => [
    '__COLLEGE__' => 'College of Education and Liberal Arts',
    'Bachelor of Science in Elementary Education',
    'Bachelor of Science in Secondary Education',
    'Bachelor of Science in Political Science',
  ],
  'CCJE' => [
    '__COLLEGE__' => 'College of Criminal Justice Education',
    'Bachelor of Science in Criminology',
  ],
  'COE'  => [
    '__COLLEGE__' => 'College of Engineering',
    'Bachelor of Science in Civil Engineering',
  ],
  'CAHS' => [
    '__COLLEGE__' => 'College of Allied Health Sciences',
    'Bachelor of Science in Nursing',
    'Bachelor of Science in Pharmacy',
    'Bachelor of Science in Midwifery',
  ],
  'CIT'  => [
    '__COLLEGE__' => 'College of Information Technology',
    'Bachelor of Science in Information Technology',
  ],
  'CAS'  => [
    '__COLLEGE__' => 'College of Arts and Sciences',
    // course list will be generated dynamically = all courses
  ],
];

/* Build a flat, unique list of all courses (excluding markers) */
function all_courses(array $map): array {
  $all = [];
  foreach ($map as $dept => $list) {
    foreach ($list as $k => $v) {
      if ($k === '__COLLEGE__') continue;
      $all[] = $v;
    }
  }
  $seen = [];
  $uniq = [];
  foreach ($all as $c) {
    if (!isset($seen[$c])) { $uniq[] = $c; $seen[$c]=true; }
  }
  return $uniq;
}
$ALL_COURSES = all_courses($deptCourses);

/* campuses (REQUIRED) */
$campusOptions = ['Au Main', 'Au South', 'Au San Jose'];

/* ------------ HELPERS ------------ */
function slugify($s){ return strtolower(trim(preg_replace('/[^a-z0-9]+/i','-',$s),'-')); }
function is_numeric_type($t){ $t=strtolower($t); return in_array($t,['int','tinyint','smallint','mediumint','bigint','decimal','float','double','real','bit']); }
function is_datetime_type($t){ $t=strtolower($t); return in_array($t,['timestamp','datetime','date','time','year']); }
function param_type_for($t){
  $t=strtolower($t);
  if (in_array($t,['decimal','float','double','real'])) return 'd';
  if (in_array($t,['int','tinyint','smallint','mediumint','bigint','bit'])) return 'i';
  return 's';
}

/* ✅ Save OUTSIDE admin so voters can load it:
   filesystem: /oevs/images/candidates
   web path saved to DB: images/candidates/<file> */
$errors = [];
$uploadDir = dirname(__DIR__) . '/images/candidates';
if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
  $errors[] = 'Upload folder not writable: ' . htmlspecialchars($uploadDir);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  /* ---------- inputs ---------- */
  $position     = trim($_POST['position']    ?? '');
  $firstName    = trim($_POST['firstname']   ?? '');
  $lastName     = trim($_POST['lastname']    ?? '');
  $middleName   = trim($_POST['middlename']  ?? '');
  $party        = trim($_POST['party']       ?? '');
  $gender       = trim($_POST['gender']      ?? '');
  $yearText     = trim($_POST['year']        ?? '');
  $department   = trim($_POST['department']  ?? '');
  $course       = trim($_POST['course']      ?? '');
  $qualif       = trim($_POST['qualification'] ?? '');
  $campusRaw    = trim($_POST['campus'] ?? '');

  /* ---------- validate ---------- */
  $allowedPositionsMap = array_flip($allowedPositions);
  if ($position === '' || !isset($allowedPositionsMap[$position])) $errors[] = 'Please choose a valid Position.';
  if ($firstName === '') $errors[] = 'First Name is required.';
  if ($lastName  === '') $errors[] = 'Last Name is required.';
  if ($gender === '' || !in_array($gender, ['Male','Female','Other'], true)) $errors[] = 'Please select a valid Gender.';
  if ($yearText === '' || !in_array($yearText, $allowedYears, true)) $errors[] = 'Please select a valid Year level.';

  $deptKeys = array_keys($deptCourses);
  if ($department === '' || !in_array($department, $deptKeys, true)) $errors[] = 'Please choose a valid Department.';

  $validCourses = [];
  if ($department) {
    if ($department === 'CAS') {
      $validCourses = $ALL_COURSES;
    } else {
      foreach ($deptCourses[$department] as $k => $v) {
        if ($k === '__COLLEGE__') continue;
        $validCourses[] = $v;
      }
    }
    if ($course === '' || !in_array($course, $validCourses, true)) {
      $errors[] = 'Please choose a valid Course for the selected Department.';
    }
  }

  if ($campusRaw === '' || !in_array($campusRaw, $campusOptions, true)) $errors[] = 'Please choose a valid Campus.';
  $campus = $campusRaw;

  /* ---------- file upload ---------- */
  $photoPathRel = '';
  $destAbs = '';
  $tmpPath = '';

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
      if ($size > 5 * 1024 * 1024) $errors[] = 'Photo must be 5MB or smaller.';
      $info = @getimagesize($tmpPath);
      if ($info === false) {
        $errors[] = 'Uploaded file is not a valid image.';
      } else {
        $mime = $info['mime'] ?? '';
        $allowedMimes = ['image/jpeg','image/png','image/webp'];
        if (!in_array($mime, $allowedMimes, true)) $errors[] = 'Allowed image types: JPG, PNG, WEBP.';
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) {
          $ext = ($mime === 'image/png') ? 'png' : (($mime === 'image/webp') ? 'webp' : 'jpg');
        }
        $unique = date('YmdHis') . '-' . bin2hex(random_bytes(4));
        $safe   = slugify($firstName).'-'.slugify($lastName).'-'.$unique.'.'.$ext;

        $destAbs = rtrim($uploadDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$safe;  // /oevs/images/candidates/<file>
        $photoPathRel = 'images/candidates/'.$safe;                                   // saved to DB
      }
    }
  }

  if (empty($errors)) {
    if (!@move_uploaded_file($tmpPath, $destAbs)) {
      $errors[] = 'Failed to save uploaded image.';
    } else {
      @chmod($destAbs, 0644);
    }
  }

  /* ---------- INSERT ---------- */
  if (empty($errors)) {

    // read table metadata
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
      while ($r = $metaQ->fetch_assoc()) $colInfo[$r['COLUMN_NAME']] = $r;
      $metaQ->free();
    }

    if (empty($errors)) {
      $base = [
        'Position'      => $position,
        'Party'         => $party,
        'FirstName'     => $firstName,
        'LastName'      => $lastName,
        'MiddleName'    => $middleName,
        'Gender'        => $gender,
        'Year'          => $yearText,
        'Department'    => $department,   // acronym (e.g., CMA, CAS, CIT)
        'Course'        => $course,       // course full text
        'Photo'         => $photoPathRel, // web path (visible to voters)
        'Qualification' => $qualif,
        'Campus'        => $campus
      ];

      $insertData = [];
      foreach ($colInfo as $col => $info) {
        if (strpos(strtolower($info['EXTRA']), 'auto_increment') !== false) continue;

        if (array_key_exists($col, $base)) {
          $insertData[$col] = $base[$col];
          continue;
        }

        $nullable   = strtoupper($info['IS_NULLABLE']) === 'YES';
        $hasDefault = !is_null($info['COLUMN_DEFAULT']);
        if ($nullable || $hasDefault) continue;

        $dt = strtolower($info['DATA_TYPE']);
        if (is_numeric_type($dt))      $insertData[$col] = 0;
        elseif (is_datetime_type($dt)) $insertData[$col] = ($dt==='date')?date('Y-m-d'):(($dt==='time')?date('H:i:s'):date('Y-m-d H:i:s'));
        elseif ($dt === 'json')        $insertData[$col] = '{}';
        else                           $insertData[$col] = '';
      }

      if (empty($insertData)) {
        $errors[] = 'No insertable columns resolved from table metadata.';
      } else {
        $columns = array_keys($insertData);
        $placeholders = [];
        $bindValues = [];
        $bindTypes = '';

        foreach ($columns as $c) {
          $v = $insertData[$c];
          if (is_null($v)) {
            $placeholders[] = 'NULL';
          } else {
            $placeholders[] = '?';
            $bindValues[] = $v;
            $dt = isset($colInfo[$c]) ? $colInfo[$c]['DATA_TYPE'] : 'varchar';
            $bindTypes .= param_type_for($dt);
          }
        }

        $quotedCols = implode('`,`', $columns);
        $sql = "INSERT INTO `candidate` (`$quotedCols`) VALUES (" . implode(',', $placeholders) . ")";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
          $errors[] = 'Database error preparing statement.';
          if ($photoPathRel && $destAbs && file_exists($destAbs)) { @unlink($destAbs); }
        } else {
          if (!empty($bindValues)) {
            $bindParams = [];
            $bindParams[] = $bindTypes;
            for ($i=0;$i<count($bindValues);$i++) $bindParams[] = &$bindValues[$i];
            call_user_func_array([$stmt, 'bind_param'], $bindParams);
          }

          try {
            $stmt->execute();
            $stmt->close();
            header("Location: candidates.php?added=1&name=" . urlencode($firstName . ' ' . $lastName));
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
    :root{
      --primary:#0b4bb3;
      --primary-600:#0a43a1;
      --bg:#f5f7fb;
      --card:#ffffff;
      --border:#e5e7eb;
      --ink:#0f172a;
      --ink-60:#475569;
      --shadow:0 8px 24px rgba(15,23,42,.06);
      --radius:14px;
    }
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);font-family:'Inter',system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:var(--ink)}
    .wrap{max-width:1000px;margin:28px auto;padding:0 18px}
    .card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow)}
    .card-hd{display:flex;align-items:center;gap:10px;padding:18px 18px;border-bottom:1px solid var(--border)}
    .card-hd i{color:var(--primary)}
    .card-hd h1{font-size:20px;margin:0}
    .card-bd{padding:18px}
    .alert{padding:12px 14px;border-radius:12px;border:1px solid #fecaca;background:#fff1f2;color:#7f1d1d;margin-bottom:14px}
    .alert strong{display:block;margin-bottom:6px}
    .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
    @media (max-width:820px){.grid{grid-template-columns:1fr}}
    .field{display:flex;flex-direction:column;gap:8px}
    label{font-weight:600}
    .help{font-size:12px;color:var(--ink-60)}
    input[type="text"], select, textarea{
      width:100%;padding:12px 12px;border:1px solid var(--border);border-radius:10px;
      font-size:14px;outline:none;background:#fff;transition:border-color .2s ease, box-shadow .2s ease
    }
    input[type="text"]:focus, select:focus, textarea:focus{border-color:#bfd2ff;box-shadow:0 0 0 3px rgba(59,130,246,.15)}
    textarea{min-height:100px;resize:vertical}
    input[type="file"]{font-size:14px}
    .preview{margin-top:10px;max-height:120px;border-radius:10px;border:1px solid var(--border);display:none}
    .actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:11px 16px;border-radius:10px;border:1px solid transparent;font-weight:700;cursor:pointer;text-decoration:none}
    .btn-primary{background:var(--primary);color:#fff}
    .btn-primary:hover{background:var(--primary-600)}
    .btn-ghost{background:#eef2ff;color:#1e3a8a}
    .section-title{grid-column:1/-1;margin:8px 0 0;font-size:12px;color:var(--ink-60);text-transform:uppercase;letter-spacing:.08em;font-weight:700}
  </style>
</head>
<body>

<?php include('header.php'); ?>

<div class="wrap">
  <div class="card">
    <div class="card-hd">
      <i class="fa fa-user-plus"></i>
      <h1>Add Candidate</h1>
    </div>
    <div class="card-bd">

      <?php if (!empty($errors)): ?>
        <div class="alert">
          <strong>Kindly fix the following:</strong>
          <ul style="margin:6px 0 0 18px;">
            <?php foreach ($errors as $e): ?>
              <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
          </ul>
          <div class="help" style="margin-top:6px">Note: required DB-only columns are auto-filled with safe defaults.</div>
        </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="grid">
          <div class="section-title">Basic Details</div>

          <div class="field">
            <label for="position">Position</label>
            <select id="position" name="position" required>
              <option value="" disabled <?php echo empty($_POST['position'])?'selected':''; ?>>Select position</option>
              <?php foreach ($allowedPositions as $p): ?>
                <option value="<?php echo htmlspecialchars($p); ?>" <?php echo (($_POST['position'] ?? '')===$p)?'selected':''; ?>>
                  <?php echo htmlspecialchars($p); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label for="party">Party</label>
            <input type="text" id="party" name="party" value="<?php echo htmlspecialchars($_POST['party'] ?? ''); ?>" placeholder="e.g., Team 2">
            <span class="help">Leave blank if independent.</span>
          </div>

          <div class="field">
            <label for="firstname">First Name</label>
            <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>" placeholder="e.g., Juan" required>
          </div>

          <div class="field">
            <label for="lastname">Last Name</label>
            <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>" placeholder="e.g., Dela Cruz" required>
          </div>

          <div class="field">
            <label for="middlename">Middle Name (optional)</label>
            <input type="text" id="middlename" name="middlename" value="<?php echo htmlspecialchars($_POST['middlename'] ?? ''); ?>" placeholder="Optional">
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
            <label for="year">Year Level</label>
            <select id="year" name="year" required>
              <option value="" disabled <?php echo empty($_POST['year'])?'selected':''; ?>>Select year level</option>
              <?php foreach ($allowedYears as $y): ?>
                <option value="<?php echo $y; ?>" <?php echo (($_POST['year'] ?? '')===$y)?'selected':''; ?>><?php echo $y; ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="section-title">Academic Info</div>

          <div class="field">
            <label for="department">Department</label>
            <select id="department" name="department" required>
              <option value="" disabled <?php echo empty($_POST['department'])?'selected':''; ?>>Select department</option>
              <?php foreach ($deptCourses as $deptKey => $list): ?>
                <option value="<?php echo $deptKey; ?>" <?php echo (($_POST['department'] ?? '')===$deptKey)?'selected':''; ?>>
                  <?php echo $deptKey; ?>
                </option>
              <?php endforeach; ?>
            </select>
            <span class="help">Saves acronym (e.g., CMA, CAS, CIT).</span>
          </div>

          <div class="field">
            <label for="course">Course</label>
            <select id="course" name="course" required>
              <option value="" disabled <?php echo empty($_POST['course'])?'selected':''; ?>>Select course</option>
              <?php
                $selDept   = $_POST['department'] ?? '';
                $selCourse = $_POST['course'] ?? '';
                $options   = [];
                if ($selDept) {
                  if ($selDept === 'CAS') {
                    $options = $ALL_COURSES;
                  } elseif (isset($deptCourses[$selDept])) {
                    foreach ($deptCourses[$selDept] as $k => $v) {
                      if ($k === '__COLLEGE__') continue;
                      $options[] = $v;
                    }
                  }
                  foreach ($options as $c) {
                    $selected = ($selCourse === $c) ? 'selected' : '';
                    echo '<option value="'.htmlspecialchars($c).'" '.$selected.'>'.htmlspecialchars($c).'</option>';
                  }
                }
              ?>
            </select>
            <span class="help">CAS lists every course across colleges. CIT has BSIT only.</span>
          </div>

          <div class="field">
            <label for="campus">Campus</label>
            <select id="campus" name="campus" required>
              <option value="" disabled <?php echo (($_POST['campus'] ?? '') === '') ? 'selected' : ''; ?>>Select campus</option>
              <?php foreach ($campusOptions as $opt): ?>
                <option value="<?php echo htmlspecialchars($opt); ?>" <?php echo (($_POST['campus'] ?? '') === $opt) ? 'selected' : ''; ?>><?php echo htmlspecialchars($opt); ?></option>
              <?php endforeach; ?>
            </select>
            <span class="help">Choose the candidate's campus (required).</span>
          </div>

          <div class="section-title">About the Candidate</div>

          <div class="field" style="grid-column:1/-1;">
            <label for="qualification">Qualification / Bio (optional)</label>
            <textarea id="qualification" name="qualification" placeholder="Optional: short candidate bio/qualification"><?php echo htmlspecialchars($_POST['qualification'] ?? ''); ?></textarea>
          </div>

          <div class="field" style="grid-column:1/-1;">
            <label for="photo">Photo (JPG/PNG/WEBP, ≤ 5MB)</label>
            <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
            <img id="preview" class="preview" alt="">
          </div>
        </div>

        <div class="actions">
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Candidate</button>
          <a href="candidates.php" class="btn btn-ghost"><i class="fa fa-arrow-left"></i> Back to Candidates</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Build JS map using KEYS so "__COLLEGE__" doesn't leak into the course list
  const baseMap = <?php
    $jsMap = [];
    foreach ($deptCourses as $k => $arr) {
      $list = [];
      foreach ($arr as $kk => $vv) {
        if ($kk === '__COLLEGE__') continue;
        $list[] = $vv;
      }
      $jsMap[$k] = $list;
    }
    echo json_encode($jsMap, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  ?>;

  const ALL_COURSES = <?php echo json_encode($ALL_COURSES, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
  baseMap['CAS'] = ALL_COURSES; // special behavior

  const deptSel   = document.getElementById('department');
  const courseSel = document.getElementById('course');

  function refreshCourses() {
    const d = deptSel.value;
    while (courseSel.firstChild) courseSel.removeChild(courseSel.firstChild);

    const ph = document.createElement('option');
    ph.value = '';
    ph.disabled = true;
    ph.selected = true;
    ph.textContent = 'Select course';
    courseSel.appendChild(ph);

    if (!d || !baseMap[d]) return;
    baseMap[d].forEach(c => {
      const opt = document.createElement('option');
      opt.value = c; opt.textContent = c;
      courseSel.appendChild(opt);
    });
  }

  deptSel.addEventListener('change', refreshCourses);

  // Image preview
  const input = document.getElementById('photo');
  const preview = document.getElementById('preview');
  input.addEventListener('change', () => {
    const f = input.files && input.files[0];
    if (!f || !['image/jpeg','image/png','image/webp'].includes(f.type)) { preview.style.display='none'; return; }
    preview.src = URL.createObjectURL(f);
    preview.style.display = 'block';
  });
</script>
</body>
</html>
