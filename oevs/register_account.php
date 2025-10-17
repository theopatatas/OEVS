<?php
session_start();
require_once 'dbcon.php';

function endsWith($haystack, $needle) {
  $len = strlen($needle);
  if ($len === 0) return true;
  return substr($haystack, -$len) === $needle;
}
function isPhinmaedEmail($email) {
  $e = strtolower(trim($email));
  return filter_var($e, FILTER_VALIDATE_EMAIL)
      && (endsWith($e, '@phinmaed.com') || endsWith($e, '@phinmaed.edu.ph'));
}
function splitFullName($full) {
  $full = trim(preg_replace('/\s+/', ' ', $full));
  if ($full === '') return ['','',''];
  $parts = explode(' ', $full);
  if (count($parts) === 1) return [$parts[0], '', ''];
  if (count($parts) === 2) return [$parts[0], '', $parts[1]];
  $first = array_shift($parts);
  $last  = array_pop($parts);
  $middle = implode(' ', $parts);
  return [$first, $middle, $last];
}

function sendOtpEmailFlexible($toEmail, $toName, $otp): bool {
  require __DIR__ . '/vendor/autoload.php';
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

    $mail->send();
    return true;
  } catch (Throwable $e) {
    error_log('Mailtrap send error: '.$e->getMessage());
    return false;
  }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.php');
  exit;
}

$schoolId = trim($_POST['SchoolID'] ?? '');
$fullName = trim($_POST['FullName']  ?? '');
$email    = trim($_POST['Email']     ?? '');
$dept     = trim($_POST['Department']?? '');
$campus   = trim($_POST['Campus']    ?? '');
$pass     = $_POST['Password']       ?? '';
$confirm  = $_POST['ConfirmPassword']?? '';

if ($schoolId==='' || $fullName==='' || $email==='' || $dept==='' || $campus==='' || $pass==='' || $confirm==='') {
  $_SESSION['login_error'] = 'Please fill in all fields.';
  header('Location: index.php'); exit;
}
if ($pass !== $confirm) {
  $_SESSION['login_error'] = 'Passwords do not match.';
  header('Location: index.php'); exit;
}
if (strlen($pass) < 8) {
  $_SESSION['login_error'] = 'Password must be at least 8 characters.';
  header('Location: index.php'); exit;
}
if (!isPhinmaedEmail($email)) {
  $_SESSION['login_error'] = 'Only PHINMAED emails are allowed.';
  header('Location: index.php'); exit;
}

$allowedDepts = ['CMA','CIT','COE','CCJE','CASH'];
$allowedCamp  = ['AU South','AU Main','AU San Jose'];
if (!in_array($dept, $allowedDepts, true) || !in_array($campus, $allowedCamp, true)) {
  $_SESSION['login_error'] = 'Invalid Department or Campus.';
  header('Location: index.php'); exit;
}

$stmt = $conn->prepare("SELECT VoterID FROM voters WHERE Email = ? OR SchoolID = ? LIMIT 1");
$stmt->bind_param('ss', $email, $schoolId);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
  $_SESSION['login_error'] = 'Account already exists with that email or School ID.';
  $stmt->close();
  header('Location: index.php'); exit;
}
$stmt->close();

list($first, $middle, $last) = splitFullName($fullName);
$username = $email;
$status   = 'Unvoted';
$year     = '';
$verified = 'Unverified';
$otp      = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$expiry   = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');

$passwordToStore = $pass;

$placeholderDate = '1970-01-01';
$placeholderTime = '00:00:00';
$placeholderRoom = '';

$sql = "INSERT INTO voters
  (FirstName, MiddleName, LastName, Username, Password, Email,
   Status, Year, SchoolID, Verified, Department, Campus, OTP, OTPExpiry,
   DateVoted, TimeVoted, Room)
  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

function doInsert($conn, $sql, $first, $middle, $last, $username, $passwordToStore, $email,
                  $status, $year, $schoolId, $verifiedValue, $dept, $campus, $otp, $expiry,
                  $placeholderDate, $placeholderTime, $placeholderRoom) {
  $stmt2 = $conn->prepare($sql);
  if (!$stmt2) {
    throw new RuntimeException('DB error (prepare failed): '.$conn->error);
  }
  $stmt2->bind_param(
    'sssssssssssssssss',
    $first, $middle, $last, $username, $passwordToStore, $email,
    $status, $year, $schoolId, $verifiedValue, $dept, $campus, $otp, $expiry,
    $placeholderDate, $placeholderTime, $placeholderRoom
  );
  if (!$stmt2->execute()) {
    $err = $stmt2->error;
    $stmt2->close();
    throw new RuntimeException('DB error: '.$err);
  }
  $stmt2->close();
}

try {
  doInsert($conn, $sql, $first, $middle, $last, $username, $passwordToStore, $email,
           $status, $year, $schoolId, $verified, $dept, $campus, $otp, $expiry,
           $placeholderDate, $placeholderTime, $placeholderRoom);
} catch (Throwable $e) {
  $msg = $e->getMessage();
  if (stripos($msg, 'Verified') !== false && stripos($msg, 'truncated') !== false) {
    $verifiedFallback = 'Pending';
    doInsert($conn, $sql, $first, $middle, $last, $username, $passwordToStore, $email,
             $status, $year, $schoolId, $verifiedFallback, $dept, $campus, $otp, $expiry,
             $placeholderDate, $placeholderTime, $placeholderRoom);
  } else {
    $_SESSION['login_error'] = $msg;
    header('Location: index.php'); exit;
  }
}

$sent = sendOtpEmailFlexible($email, trim("$first $last"), $otp);
if (!$sent) {
  $_SESSION['register_success'] =
    'Account created. We tried to send your OTP, but email didn’t go through. You can use “Resend OTP” on the next page.';
}

$_SESSION['pending_email'] = $email;
header('Location: verify_otp.php');
exit;
