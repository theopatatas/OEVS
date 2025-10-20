<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('session.php');
include('dbcon.php');

/* ----------------------- helpers ----------------------- */
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function db_name(mysqli $c){ $r=$c->query("SELECT DATABASE() AS d"); return $r?($r->fetch_assoc()['d']??''):''; }

function col_exists(mysqli $c,$t,$col){
  $db = db_name($c);
  $s = $c->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=? AND COLUMN_NAME=? LIMIT 1");
  $s->bind_param('sss',$db,$t,$col); $s->execute();
  $ok = $s->get_result()->num_rows>0; $s->close(); return $ok;
}

/* Distinct values from candidate */
function distinct_from_candidate(mysqli $c, string $col): array {
  $safe = preg_replace('/[^A-Za-z0-9_]/','',$col);
  $sql  = "SELECT DISTINCT `$safe` AS v
           FROM `candidate`
           WHERE `$safe` IS NOT NULL AND TRIM(`$safe`) <> ''
           ORDER BY `$safe` ASC";
  $out = [];
  if ($res = $c->query($sql)) { while ($r = $res->fetch_assoc()) $out[] = $r['v']; }
  return $out;
}

/* Distinct department->courses mapping from candidate */
function dept_course_map(mysqli $c): array {
  $sql = "SELECT DISTINCT `Department`, `Course`
          FROM `candidate`
          WHERE `Department` IS NOT NULL AND TRIM(`Department`) <> ''
            AND `Course`     IS NOT NULL AND TRIM(`Course`)     <> ''";
  $map = [];
  if ($res = $c->query($sql)) {
    while ($r = $res->fetch_assoc()) {
      $d = (string)$r['Department']; $k = (string)$r['Course'];
      if (!isset($map[$d])) $map[$d] = [];
      if (!in_array($k, $map[$d], true)) $map[$d][] = $k;
    }
  }
  // sort courses per department
  foreach ($map as $d => $arr) { sort($map[$d], SORT_NATURAL | SORT_FLAG_CASE); }
  ksort($map, SORT_NATURAL | SORT_FLAG_CASE);
  return $map;
}

/* ----------------------- load record ----------------------- */
$get_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($get_id <= 0) { header("Location: candidate_list.php"); exit; }

$TABLE = 'candidate';

$cols = [
  'abc'          => col_exists($conn,$TABLE,'abc'),
  'Position'     => col_exists($conn,$TABLE,'Position'),
  'Party'        => col_exists($conn,$TABLE,'Party'),
  'FirstName'    => col_exists($conn,$TABLE,'FirstName'),
  'LastName'     => col_exists($conn,$TABLE,'LastName'),
  'MiddleName'   => col_exists($conn,$TABLE,'MiddleName'),
  'Gender'       => col_exists($conn,$TABLE,'Gender'),
  'Year'         => col_exists($conn,$TABLE,'Year'),
  'Department'   => col_exists($conn,$TABLE,'Department'),
  'Course'       => col_exists($conn,$TABLE,'Course'),
  'Photo'        => col_exists($conn,$TABLE,'Photo'),
  'Qualification'=> col_exists($conn,$TABLE,'Qualification'),
  'Campus'       => col_exists($conn,$TABLE,'Campus'),
];

$st = $conn->prepare("SELECT * FROM `$TABLE` WHERE CandidateID=?");
$st->bind_param('i',$get_id);
$st->execute();
$row = $st->get_result()->fetch_assoc();
$st->close();
if (!$row) { header("Location: candidate_list.php"); exit; }

/* -------------------- reference lists -------------------- */
$positions = ['President','Vice-President','Governor','Vice-Governor','Secretary','Treasurer','Representative','Social-Media Officer'];
$genders   = ['Male','Female'];
$years     = ['1st year','2nd year','3rd year','4th year'];

/* Departments list + mapping for dependent Course */
$departments = $cols['Department'] ? distinct_from_candidate($conn,'Department') : [];
$dc_map      = ($cols['Department'] && $cols['Course']) ? dept_course_map($conn) : [];

// Ensure current values show up even if not in the lists yet
$curDept   = $cols['Department'] ? (string)($row['Department'] ?? '') : '';
$curCourse = $cols['Course']     ? (string)($row['Course'] ?? '')     : '';

if ($cols['Department'] && $curDept !== '' && !in_array($curDept, $departments, true)) {
  array_unshift($departments, $curDept);
}
if ($cols['Department'] && $cols['Course'] && $curDept !== '' && $curCourse !== '') {
  if (!isset($dc_map[$curDept])) $dc_map[$curDept] = [];
  if (!in_array($curCourse, $dc_map[$curDept], true)) array_unshift($dc_map[$curDept], $curCourse);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Candidate - Online Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root{ --primary:#002f6c; --accent:#0056b3; --bg:#f4f6f8; --white:#fff; --ink:#0b1324; --muted:#6b7280; --shadow:0 6px 16px rgba(0,0,0,.08); --radius:14px; }
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial;background:var(--bg);color:var(--ink)}
    .wrap{max-width:1100px;margin:26px auto;padding:0 16px}
    .back{display:inline-flex;gap:8px;align-items:center;padding:10px 14px;border:2px solid #0b55c2;color:#0b55c2;border-radius:10px;text-decoration:none;font-weight:700}
    .back:hover{background:#eff6ff}
    h1{font-size:22px;margin:14px 0 16px}
    .card{background:#fff;border:1px solid #e8eef7;border-radius:var(--radius);box-shadow:var(--shadow);padding:20px}
    .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
    .full{grid-column:1 / -1}
    label{display:block;font-weight:700;color:#093065;margin-bottom:6px}
    input[type=text],select,textarea{width:100%;padding:10px 12px;border:1px solid #d6e0ef;border-radius:10px;height:44px;font-size:14px}
    textarea{min-height:120px;height:auto}
    input:focus,select:focus,textarea:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(0,86,179,.12)}
    .photo{display:flex;gap:16px;align-items:center}
    .thumb{width:120px;height:120px;border-radius:12px;object-fit:cover;border:3px solid #e6edf6;background:#f7f9fc}
    .help{font-size:12px;color:var(--muted)}
    .actions{display:flex;gap:12px;margin-top:8px}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:10px;font-weight:700;cursor:pointer;text-decoration:none}
    .primary{background:var(--accent);color:#fff;border:none}
    .ghost{background:#fff;color:#374151;border:1px solid #d1d5db}
    @media (max-width: 840px){ .grid{grid-template-columns:1fr} }
  </style>
</head>
<body>
<?php include 'header.php'; ?>
<div class="wrap">
  <div style="display:flex;justify-content:flex-end"><a class="back" href="candidates.php"><i class="fa-solid fa-arrow-left"></i> Back to list</a></div>
  <h1>Edit Candidate</h1>

  <div class="card">
    <form method="POST" enctype="multipart/form-data" class="grid" autocomplete="off">
      <input type="hidden" name="user_name" value="<?php echo h($_SESSION['User_Type'] ?? 'admin'); ?>"/>


      <?php if ($cols['Position']): ?>
      <div>
        <label for="Position">Position</label>
        <select id="Position" name="Position" required>
          <?php foreach ($positions as $p): $sel=strcasecmp($row['Position']??'',$p)===0?'selected':''; ?>
            <option value="<?php echo h($p); ?>" <?php echo $sel; ?>><?php echo h($p); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>

      <?php if ($cols['Party']): ?>
      <div>
        <label for="Party">Party</label>
        <input type="text" id="Party" name="Party" value="<?php echo h($row['Party'] ?? ''); ?>" placeholder="e.g., Team 2">
      </div>
      <?php endif; ?>

      <?php if ($cols['FirstName']): ?>
      <div>
        <label for="FirstName">First Name</label>
        <input type="text" id="FirstName" name="FirstName" value="<?php echo h($row['FirstName'] ?? ''); ?>" required>
      </div>
      <?php endif; ?>

      <?php if ($cols['LastName']): ?>
      <div>
        <label for="LastName">Last Name</label>
        <input type="text" id="LastName" name="LastName" value="<?php echo h($row['LastName'] ?? ''); ?>" required>
      </div>
      <?php endif; ?>

      <?php if ($cols['MiddleName']): ?>
      <div>
        <label for="MiddleName">Middle Name (optional)</label>
        <input type="text" id="MiddleName" name="MiddleName" value="<?php echo h($row['MiddleName'] ?? ''); ?>">
      </div>
      <?php endif; ?>

      <?php if ($cols['Gender']): ?>
      <div>
        <label for="Gender">Gender</label>
        <select id="Gender" name="Gender" required>
          <?php foreach ($genders as $g): $sel=strcasecmp($row['Gender']??'',$g)===0?'selected':''; ?>
            <option value="<?php echo h($g); ?>" <?php echo $sel; ?>><?php echo h($g); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>

      <?php if ($cols['Year']): ?>
      <div>
        <label for="Year">Year Level</label>
        <select id="Year" name="Year" required>
          <?php foreach ($years as $y): $sel=strcasecmp($row['Year']??'',$y)===0?'selected':''; ?>
            <option value="<?php echo h($y); ?>" <?php echo $sel; ?>><?php echo h($y); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>

      <?php if ($cols['Department']): ?>
      <div>
        <label for="Department">Department</label>
        <select id="Department" name="Department" required>
          <?php
            $deps = $departments;
            if (!$deps) $deps = [''];
            foreach ($deps as $d):
              $sel = (strcasecmp($curDept, $d) === 0) ? 'selected' : '';
          ?>
            <option value="<?php echo h($d); ?>" <?php echo $sel; ?>>
              <?php echo $d !== '' ? h($d) : '— Select Department —'; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>

      <?php if ($cols['Course']): ?>
      <div>
        <label for="Course">Course</label>
        <select id="Course" name="Course" required>
          <!-- Options will be filled by JS based on selected Department -->
        </select>
      </div>
      <?php endif; ?>

      <?php if ($cols['Campus']): ?>
      <div>
        <label for="Campus">Campus</label>
        <input type="text" id="Campus" name="Campus" value="<?php echo h($row['Campus'] ?? ''); ?>" placeholder="Campus">
      </div>
      <?php endif; ?>

      <?php if ($cols['Qualification']): ?>
      <div class="full">
        <label for="Qualification">Qualification</label>
        <textarea id="Qualification" name="Qualification" rows="6" placeholder="Enter candidate qualification..."><?php echo h($row['Qualification'] ?? ''); ?></textarea>
      </div>
      <?php endif; ?>

      <?php if ($cols['Photo']): ?>
      <div class="full">
        <label>Photo</label>
        <div class="photo">
          <?php if (!empty($row['Photo'])): ?>
            <img class="thumb" src="<?php echo h($row['Photo']); ?>" alt="Candidate Photo">
          <?php else: ?>
            <div class="thumb" style="display:flex;align-items:center;justify-content:center;color:#9aa4b2;">No photo</div>
          <?php endif; ?>
          <div>
            <input type="file" name="image" accept="image/*">
            <div class="help">Uploading a new photo will replace the current one.</div>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <div class="actions full">
        <button type="submit" name="save" class="btn primary"><i class="fa-solid fa-floppy-disk"></i> Save</button>
        <a href="candidates.php" class="btn ghost"><i class="fa-solid fa-xmark"></i> Cancel</a>
      </div>
    </form>
  </div>
</div>
<footer style="text-align:center;color:#667;font-size:14px;padding:18px 0">© 2025 Online Election Voting System</footer>

<?php if ($cols['Department'] && $cols['Course']): ?>
<script>
(function(){
  // PHP → JS data
  const dcMap   = <?php echo json_encode($dc_map, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
  const curDept = <?php echo json_encode($curDept, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
  const curCourse = <?php echo json_encode($curCourse, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;

  const deptSel = document.getElementById('Department');
  const courseSel = document.getElementById('Course');

  function setCoursesFor(dept, keepCurrent=false){
    // wipe
    while (courseSel.firstChild) courseSel.removeChild(courseSel.firstChild);

    const courses = (dept && dcMap[dept]) ? dcMap[dept].slice() : [];
    if (!courses.length) {
      const opt = document.createElement('option');
      opt.value = '';
      opt.textContent = '— Select Course —';
      courseSel.appendChild(opt);
      courseSel.value = '';
      return;
    }

    // ensure current course included when rendering for initial load
    if (keepCurrent && curCourse && !courses.includes(curCourse)) {
      courses.unshift(curCourse);
    }

    courses.forEach(c=>{
      const opt = document.createElement('option');
      opt.value = c;
      opt.textContent = c;
      courseSel.appendChild(opt);
    });

    // select desired value
    const want = keepCurrent ? curCourse : '';
    if (want && courses.includes(want)) {
      courseSel.value = want;
    } else {
      courseSel.selectedIndex = 0;
    }
  }

  // Initial paint
  setCoursesFor(deptSel ? deptSel.value : curDept, true);

  // When department changes, repopulate courses
  if (deptSel) {
    deptSel.addEventListener('change', () => setCoursesFor(deptSel.value, false));
  }
})();
</script>
<?php endif; ?>
</body>
</html>

<?php
/* ======================= SAVE HANDLER ======================= */
if (isset($_POST['save'])) {
  $user_name = $_POST['user_name'] ?? 'admin';

  // collect only existing columns
  $fields = [];
  foreach (['abc','Position','Party','FirstName','LastName','MiddleName','Gender','Year','Department','Course','Qualification','Campus'] as $cname) {
    if ($cols[$cname]) $fields[$cname] = $_POST[$cname] ?? '';
  }

  // photo
  if ($cols['Photo'] && !empty($_FILES['image']['tmp_name'])) {
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_name = time().'_'.preg_replace('/[^A-Za-z0-9_.-]/','_',basename($_FILES['image']['name']));
    $upload_dir = "upload/"; if (!is_dir($upload_dir)) @mkdir($upload_dir,0777,true);
    $target = $upload_dir.$image_name;
    if (move_uploaded_file($image_tmp,$target)) {
      $fields['Photo'] = $target;
    } else {
      die("Failed to upload image.");
    }
  }

  if ($fields) {
    $sets = []; $types=''; $params=[];
    foreach ($fields as $k=>$v){ $sets[]="`$k`=?"; $types.='s'; $params[]=$v; }
    $types.='i'; $params[]=$get_id;

    $sql = "UPDATE `$TABLE` SET ".implode(', ',$sets)." WHERE CandidateID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
      // log history
      $fullname = trim(($fields['FirstName'] ?? $row['FirstName']).' '.($fields['LastName'] ?? $row['LastName']));
      $action   = "Edit Candidate";
      $hs = $conn->prepare("INSERT INTO history (`data`,`action`,`date`,`user`) VALUES (?,?,NOW(),?)");
      if ($hs) { $hs->bind_param("sss",$fullname,$action,$user_name); $hs->execute(); $hs->close(); }
      header("Location: candidates.php"); exit;
    } else {
      die("Error updating candidate: ".$conn->error);
    }
  } else {
    header("Location: candidates.php"); exit;
  }
}
