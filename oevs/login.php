<?php
// login.php — Student Number–only login (SchoolID), with OTP gate
session_start();
require_once 'dbcon.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$schoolId = trim($_POST['SchoolID'] ?? '');
$password = $_POST['Password'] ?? '';

if ($schoolId === '' || $password === '') {
  $_SESSION['login_error']   = 'Enter your Student Number and password.';
  $_SESSION['prefill_login'] = $schoolId;
  header('Location: index.php'); exit;
}
if (!preg_match('/^[0-9-]+$/', $schoolId)) {
  $_SESSION['login_error']   = 'Student Number must contain only digits and dashes.';
  $_SESSION['prefill_login'] = $schoolId;
  header('Location: index.php'); exit;
}

/* table pick (PDO assumed in dbcon.php as $pdo) */
function tableExists(PDO $pdo, string $name): bool {
  $stmt = $pdo->prepare("SHOW TABLES LIKE :t");
  $stmt->execute([':t' => $name]);
  return (bool)$stmt->fetchColumn();
}
$candidates = ['voters','users','tbl_voters','voter','oevs_voters'];
$table = null;
foreach ($candidates as $t) { if (tableExists($pdo, $t)) { $table = $t; break; } }
if (!$table) {
  $_SESSION['login_error']   = 'Login is unavailable: voters/users table not found.';
  $_SESSION['prefill_login'] = $schoolId;
  header('Location: index.php'); exit;
}

/* map columns; require SchoolID + Password */
$cols    = $pdo->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_COLUMN);
$cols_lc = array_map('strtolower', $cols);
$find = function(array $cands) use ($cols, $cols_lc) {
  foreach ($cands as $c) { $i = array_search(strtolower($c), $cols_lc, true); if ($i !== false) return $cols[$i]; }
  return null;
};
$map = [
  'SchoolID'   => ['SchoolID','school_id','schoolid','student_id','stud_id','idnumber','id_number','id_no'],
  'Password'   => ['PasswordHash','password_hash','passwd_hash','pass_hash','Password','password','passwd','pass','pwd'],
  'Verified'   => ['Verified','verified','email_verified','is_verified'],
  'Status'     => ['Status','status','account_status','is_active','active'],
  'FullName'   => ['FullName','full_name','name','fullname','complete_name'],
  'FirstName'  => ['FirstName','first_name','given_name','fname'],
  'LastName'   => ['LastName','last_name','surname','lname'],
  'Department' => ['Department','department','dept'],
  'Campus'     => ['Campus','campus','branch','site'],
  'Year'       => ['Year','year','level','yr','grade_year'],
  'Email'      => ['Email','email','email_address','mail'],
  'Course'     => ['Course','course','program','degree']
];
$resolved = [];
foreach ($map as $key => $cands) { $resolved[$key] = $find($cands); }

if (!$resolved['SchoolID'] || !$resolved['Password']) {
  $_SESSION['login_error']   = 'Login requires Student Number and Password columns in the database.';
  $_SESSION['prefill_login'] = $schoolId; header('Location: index.php'); exit;
}

/* select by SchoolID only */
$selectParts = [];
foreach (['SchoolID','Password','Verified','Status','FullName','FirstName','LastName','Department','Campus','Year','Email','Course'] as $k) {
  if ($resolved[$k]) $selectParts[] = "`{$resolved[$k]}` AS `$k`";
}
$sql = "SELECT ".implode(', ', $selectParts)." FROM `$table` WHERE `{$resolved['SchoolID']}` = :sid LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':sid' => $schoolId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  $_SESSION['register_error'] = 'No account found for that Student Number. Create one below.';
  $_SESSION['prefill'] = ['SchoolID' => $schoolId];
  $_SESSION['show_register'] = true;
  header('Location: index.php'); exit;
}

/* password check */
$stored = (string)$user['Password'];
$looksHashed = fn($s) => is_string($s) && (str_starts_with($s,'$2y$')||str_starts_with($s,'$2a$')||str_starts_with($s,'$argon2')||strlen($s)>=50);
$ok = $looksHashed($stored) ? password_verify($password, $stored) : hash_equals($stored, (string)$password);
if (!$ok) {
  $_SESSION['login_error']   = 'Incorrect password. Try again or use “Forgot Password”.';
  $_SESSION['prefill_login'] = $schoolId; header('Location: index.php'); exit;
}

/* OTP gate */
$verifiedStr = strtolower((string)($user['Verified'] ?? ''));
if ($verifiedStr === '' || in_array($verifiedStr, ['unverified','pending','no','0'], true)) {
  $_SESSION['pending_email'] = $user['Email'] ?? '';
  $_SESSION['verify_notice'] = 'Please verify your email to continue.';
  header('Location: verify_otp.php'); exit;
}

/* session payload using first name + course */
$full = trim($user['FullName'] ?? '');
$first = $user['FirstName'] ?? '';
if ($first === '') { $first = $full ? preg_split('/\s+/', $full, 2)[0] : $schoolId; }

$_SESSION['user'] = [
  'id'          => $user['SchoolID'],
  'first_name'  => $first,
  'name'        => $full ?: trim(($user['FirstName']??'').' '.($user['LastName']??'')),
  'school_id'   => $user['SchoolID'],
  'dept'        => $user['Department'] ?? '',
  'campus'      => $user['Campus'] ?? '',
  'status'      => $user['Status'] ?? '',
  'year'        => $user['Year'] ?? '',
  'course'      => $user['Course'] ?? ''
];
header('Location: home.php'); exit;
