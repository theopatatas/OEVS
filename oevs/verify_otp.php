<?php
session_start();
require_once 'dbcon.php';
$email = $_SESSION['pending_email'] ?? '';
if ($email === '') { header('Location: login.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $otp = trim($_POST['otp'] ?? '');
  $stmt = $conn->prepare("SELECT VoterID, OTP, OTPExpiry FROM voters WHERE Email=? AND Verified='Unverified' LIMIT 1");
  $stmt->bind_param('s',$email);
  $stmt->execute();
  $stmt->bind_result($id,$code,$exp);
  if ($stmt->fetch()) {
    $stmt->close();
    $now=new DateTime(); $expires=new DateTime($exp);
    if ($otp===$code && $now<=$expires) {
      $verified='Verified';
      $stmt2=$conn->prepare("UPDATE voters SET Verified=?, OTP=NULL, OTPExpiry=NULL WHERE VoterID=?");
      $stmt2->bind_param('si',$verified,$id);
      $stmt2->execute(); $stmt2->close();
      $_SESSION['register_success']='Your email has been verified.';
      unset($_SESSION['pending_email']);
      header('Location: login.php'); exit;
    } else {
      $_SESSION['login_error']='Invalid or expired OTP.';
    }
  } else {
    $_SESSION['login_error']='No verification record found.';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Verify Email</title></head>
<body style="font-family:Segoe UI;background:#f4f6f9;display:flex;justify-content:center;align-items:center;height:100vh">
  <form method="POST" style="background:#fff;padding:24px;border-radius:10px;box-shadow:0 0 10px rgba(0,0,0,.1)">
    <h2>Email Verification</h2>
    <p>Check your inbox for the 6-digit code sent to <b><?=htmlspecialchars($email)?></b></p>
    <input type="text" name="otp" maxlength="6" pattern="\d{6}" required placeholder="Enter OTP" style="padding:10px;margin:10px 0;width:100%">
    <button type="submit" style="width:100%;padding:10px;background:#002f6c;color:#fff;border:none;border-radius:5px">Verify</button>
    <p style="margin-top:10px;text-align:center"><a href="login.php">Back to Login</a></p>
  </form>
</body>
</html>
