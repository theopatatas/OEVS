<?php
session_start();
require_once 'dbcon.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    $mail->Subject = 'Your new OEVS Verification Code';
    $safeName = htmlspecialchars($toName, ENT_QUOTES, 'UTF-8');
    $safeOtp  = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');
    $mail->Body    = "<p>Hi <b>{$safeName}</b>,</p>
                      <p>Your new OTP is: <b style='font-size:20px;letter-spacing:3px;'>{$safeOtp}</b><br>
                      It expires in <b>10 minutes</b>.</p>";
    $mail->AltBody = "Hi {$toName},\n\nYour new OTP is {$otp}. It expires in 10 minutes.\n";
    $mail->send(); return true;
  } catch (Throwable $e) { error_log('Mail send error: '.$e->getMessage()); return false; }
}

$email = trim($_GET['id'] ?? ($_SESSION['pending_email'] ?? ''));
if ($email === '') {
  $_SESSION['verify_error'] = 'Missing email for resend.';
  header('Location: verify_otp.php'); exit;
}

$usingPDO    = isset($pdo) && $pdo instanceof PDO;
$usingMysqli = isset($conn) && ($conn instanceof mysqli);
if (!$usingPDO && !$usingMysqli) {
  $_SESSION['verify_error'] = 'Database connection missing.';
  header('Location: verify_otp.php'); exit;
}

try {
  // Confirm account exists + get name
  if ($usingPDO) {
    $st = $pdo->prepare("SELECT FirstName, LastName FROM voters WHERE Email = :e LIMIT 1");
    $st->execute([':e'=>$email]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
  } else {
    $st = $conn->prepare("SELECT FirstName, LastName FROM voters WHERE Email = ? LIMIT 1");
    $st->bind_param('s',$email); $st->execute();
    $res = $st->get_result(); $row = $res ? $res->fetch_assoc() : null; $st->close();
  }
  if (!$row) {
    $_SESSION['verify_error'] = 'Account not found.';
    header('Location: verify_otp.php'); exit;
  }

  $otp    = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
  $expiry = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');

  // Update OTP
  if ($usingPDO) {
    $up = $pdo->prepare("UPDATE voters SET OTP=:o, OTPExpiry=:x WHERE Email=:e LIMIT 1");
    $up->execute([':o'=>$otp, ':x'=>$expiry, ':e'=>$email]);
  } else {
    $up = $conn->prepare("UPDATE voters SET OTP=?, OTPExpiry=? WHERE Email=? LIMIT 1");
    $up->bind_param('sss',$otp,$expiry,$email); $up->execute(); $up->close();
  }

  // Send email (non-fatal)
  $name = trim(($row['FirstName'] ?? '').' '.($row['LastName'] ?? ''));
  $sent = sendOtpEmailFlexible($email, $name ?: $email, $otp);

  $_SESSION['verify_notice'] = $sent
    ? 'A new code has been sent to your email.'
    : 'We refreshed your code, but email didn’t send. Try again later.';
  $_SESSION['pending_email'] = $email;

  header('Location: verify_otp.php'); exit;

} catch (Throwable $e) {
  $_SESSION['verify_error'] = 'Resend error: '.$e->getMessage();
  header('Location: verify_otp.php'); exit;
}
