<?php
session_start();
require_once 'dbcon.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* helpers */
function endsWith($haystack, $needle) { $l=strlen($needle); return $l===0 || substr($haystack,-$l)===$needle; }
function isPhinmaedEmail($email) {
  $e = strtolower(trim($email));
  return filter_var($e, FILTER_VALIDATE_EMAIL)
      && (endsWith($e,'@phinmaed.com') || endsWith($e,'@phinmaed.edu.ph'));
}
function splitFullName($full) {
  $full = trim(preg_replace('/\s+/', ' ', $full));
  if ($full==='') return ['','',''];
  $p = explode(' ', $full);
  if (count($p)===1) return [$p[0],'',''];
  if (count($p)===2) return [$p[0],'',$p[1]];
  $first = array_shift($p); $last = array_pop($p); $mid = implode(' ', $p);
  return [$first,$mid,$last];
}

/* optional mailer */
function sendOtpEmailFlexible($toEmail, $toName, $otp): bool {
  $autoload = __DIR__ . '/vendor/autoload.php';
  if (!is_file($autoload)) { error_log('Mailer skipped: vendor missing'); return false; }
  require_once $autoload;
  $mail = new PHPMailer\PHPMailer\PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host       = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth   = true;
    $mail->Username   = '01380edc4b225b';
    $mail->Password   = '255bddffeed505';
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->setFrom('no-reply@oevs.test', 'OEVS 2025');
    $mail->addAddress($toEmail, $toName);
    $mail->isHTML(true);
    $mail->Subject = 'Your OEVS Verification Code';
    $safeName = htmlspecialchars($toName, ENT_QUOTES, 'UTF-8');
    $safeOtp  = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');
    $mail->Body    = "<p>Hi <b>{$safeName}</b>,</p>
                      <p>Your OTP is: <b style='font-size:20px;letter-spacing:3px;'>{$safeOtp}</b><br>
                      It expires in <b>10 minutes</b>.</p>";
    $mail->AltBody = "Hi {$toName},\n\nYour OTP is {$otp}. It expires in 10 minutes.\n";
    $mail->send(); return true;
  } catch (Throwable $e) { error_log('Mail send error: '.$e->getMessage()); return false; }
}

/* only POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

/* inputs */
$schoolId = trim($_POST['SchoolID'] ?? '');
$fullName = trim($_POST['FullName']  ?? '');
$email    = trim($_POST['Email']     ?? '');
$dept     = trim($_POST['Department']?? '');
$campus   = trim($_POST['Campus']    ?? '');
$year     = trim($_POST['Year']      ?? '');
$course   = trim($_POST['Course']    ?? '');
$pass     = $_POST['Password']       ?? '';
$confirm  = $_POST['ConfirmPassword']?? '';

/* prefill for errors */
$_SESSION['prefill'] = [
  'SchoolID'=>$schoolId,'FullName'=>$fullName,'Email'=>$email,
  'Department'=>$dept,'Campus'=>$campus,'Year'=>$year,'Course'=>$course
];

/* validate */
if ($schoolId===''||$fullName===''||$email===''||$dept===''||$campus===''||$year===''||$course===''||$pass===''||$confirm==='') {
  $_SESSION['register_error']='Please fill in all fields.'; $_SESSION['show_register']=true; header('Location:index.php'); exit;
}
if (!preg_match('/^[0-9-]+$/', $schoolId)) {
  $_SESSION['register_error']='Student Number must contain only digits and dashes.'; $_SESSION['show_register']=true; header('Location:index.php'); exit;
}
if ($pass !== $confirm) { $_SESSION['register_error']='Passwords do not match.'; $_SESSION['show_register']=true; header('Location:index.php'); exit; }
if (strlen($pass) < 8)   { $_SESSION['register_error']='Password must be at least 8 characters.'; $_SESSION['show_register']=true; header('Location:index.php'); exit; }
if (!isPhinmaedEmail($email)) { $_SESSION['register_error']='Only PHINMAED emails are allowed.'; $_SESSION['show_register']=true; header('Location:index.php'); exit; }

$departments = ['CMA','CELA','CCJE','COE','CAHS','CIT','CAS'];
if (!in_array($dept, $departments, true)) { $_SESSION['register_error']='Invalid Department.'; $_SESSION['show_register']=true; header('Location:index.php'); exit; }

$allowedYears = ['1st Year','2nd Year','3rd Year','4th Year'];
if (!in_array($year, $allowedYears, true)) { $_SESSION['register_error']='Invalid Year. Choose 1st–4th Year.'; $_SESSION['show_register']=true; header('Location:index.php'); exit; }

$coursesAll = [
  'Bachelor of Science in Accountancy',
  'Bachelor of Science in Hospitality Management',
  'Bachelor of Science in Tourism Management',
  'Bachelor of Science in Entrepreneurship',
  'Bachelor of Science in Business Administration',
  'Bachelor of Science in Management Accounting',
  'Bachelor of Science in Accounting Information System',
  'Bachelor of Elementary Education',
  'Bachelor of Secondary Education',
  'Bachelor of Arts in Political Science',
  'Bachelor of Science in Criminology',
  'Bachelor of Science in Civil Engineering',
  'Bachelor of Science in Nursing',
  'Bachelor of Science in Pharmacy',
  'Bachelor of Science in Midwifery',
  'Bachelor of Science in Information Technology'
];
$courseMap = [
  'CMA'  => [
    'Bachelor of Science in Accountancy',
    'Bachelor of Science in Hospitality Management',
    'Bachelor of Science in Tourism Management',
    'Bachelor of Science in Entrepreneurship',
    'Bachelor of Science in Business Administration',
    'Bachelor of Science in Management Accounting',
    'Bachelor of Science in Accounting Information System'
  ],
  'CELA' => [
    'Bachelor of Elementary Education',
    'Bachelor of Secondary Education',
    'Bachelor of Arts in Political Science'
  ],
  'CCJE' => ['Bachelor of Science in Criminology'],
  'COE'  => ['Bachelor of Science in Civil Engineering'],
  'CAHS' => ['Bachelor of Science in Nursing','Bachelor of Science in Pharmacy','Bachelor of Science in Midwifery'],
  'CIT'  => ['Bachelor of Science in Information Technology'],
  'CAS'  => $coursesAll,
];
$validCourses = $courseMap[$dept] ?? [];
if (!in_array($course, $validCourses, true)) {
  $_SESSION['register_error']='Invalid Course for the selected Department.'; $_SESSION['show_register']=true; header('Location:index.php'); exit;
}

$allowedCamp = ['AU South','AU Main','AU San Jose'];
if (!in_array($campus, $allowedCamp, true)) { $_SESSION['register_error']='Invalid Campus.'; $_SESSION['show_register']=true; header('Location:index.php'); exit; }

/* DB pick */
$usingPDO    = isset($pdo) && $pdo instanceof PDO;
$usingMysqli = isset($conn) && ($conn instanceof mysqli);
if (!$usingPDO && !$usingMysqli) { $_SESSION['register_error']='Database connection missing.'; $_SESSION['show_register']=true; header('Location:index.php'); exit; }

/* duplicates */
try {
  if ($usingPDO) {
    $dup = $pdo->prepare("SELECT VoterID FROM voters WHERE Email = :e OR SchoolID = :s LIMIT 1");
    $dup->execute([':e'=>$email,':s'=>$schoolId]);
    if ($dup->fetch()) { $_SESSION['register_error']='Account already exists with that email or Student Number.'; $_SESSION['show_register']=true; header('Location:index.php'); exit; }
  } else {
    $stmt = $conn->prepare("SELECT VoterID FROM voters WHERE Email = ? OR SchoolID = ? LIMIT 1");
    $stmt->bind_param('ss',$email,$schoolId); $stmt->execute(); $stmt->store_result();
    if ($stmt->num_rows > 0) { $stmt->close(); $_SESSION['register_error']='Account already exists with that email or Student Number.'; $_SESSION['show_register']=true; header('Location:index.php'); exit; }
    $stmt->close();
  }
} catch (Throwable $e) {
  $_SESSION['register_error'] = 'DB check failed: '.$e->getMessage();
  $_SESSION['show_register'] = true; header('Location:index.php'); exit;
}

/* derived */
list($first,$middle,$last) = splitFullName($fullName);
$username = $email;
$status   = 'Unvoted';
$verified = 'Unverified';                  // require OTP before login
$otp      = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$expiry   = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');

// plaintext for now (matches your table). Recommend hashing later.
// $passwordToStore = password_hash($pass, PASSWORD_BCRYPT);
$passwordToStore = $pass;

$placeholderDate = '1970-01-01';
$placeholderTime = '00:00:00';
$placeholderRoom = '';

/* INSERT with Course + Year */
$insertSql = "INSERT INTO voters
  (FirstName, MiddleName, LastName, Username, Password, Email,
   Year, Course, Department, Campus, Status, SchoolID, Verified, OTP, OTPExpiry,
   DateVoted, TimeVoted, Room)
  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

try {
  if ($usingPDO) {
    $st = $pdo->prepare($insertSql);
    $st->execute([
      $first,$middle,$last,$username,$passwordToStore,$email,
      $year,$course,$dept,$campus,$status,$schoolId,$verified,$otp,$expiry,
      $placeholderDate,$placeholderTime,$placeholderRoom
    ]);
  } else {
    $st = $conn->prepare($insertSql);
    if (!$st) throw new RuntimeException('DB error (prepare failed): '.$conn->error);
    $st->bind_param(
      'ssssssssssssssssss',
      $first,$middle,$last,$username,$passwordToStore,$email,
      $year,$course,$dept,$campus,$status,$schoolId,$verified,$otp,$expiry,
      $placeholderDate,$placeholderTime,$placeholderRoom
    );
    if (!$st->execute()) { $err=$st->error; $st->close(); throw new RuntimeException('DB error: '.$err); }
    $st->close();
  }
} catch (Throwable $e) {
  $_SESSION['register_error'] = $e->getMessage();
  $_SESSION['show_register'] = true; header('Location:index.php'); exit;
}

/* OTP email (optional) */
$sent = sendOtpEmailFlexible($email, trim("$first $last"), $otp);
$_SESSION['register_success'] = $sent
  ? 'Account created. We emailed your OTP. Please verify within 10 minutes.'
  : 'Account created. We tried to send your OTP, but email didn’t go through. You can use “Resend OTP” on the next page.';

/* handoff to verify */
$_SESSION['pending_email'] = $email;
header('Location: verify_otp.php'); exit;
