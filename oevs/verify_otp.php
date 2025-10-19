<?php
session_start();
require_once 'dbcon.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* which email are we verifying? */
$pendingEmail = trim($_SESSION['pending_email'] ?? '');
if (isset($_GET['id']) && $_GET['id'] !== '') {
  $pendingEmail = trim($_GET['id']);
}

/* no context -> back to login */
if ($pendingEmail === '') {
  $_SESSION['login_error'] = 'Verification session expired. Please log in or register again.';
  header('Location: index.php'); exit;
}

/* handle submit */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $otp = trim($_POST['otp'] ?? '');

  if ($otp === '' || strlen($otp) !== 6 || !ctype_digit($otp)) {
    $_SESSION['verify_error'] = 'Enter the 6-digit code.';
    header('Location: verify_otp.php'); exit;
  }

  $usingPDO    = isset($pdo) && $pdo instanceof PDO;
  $usingMysqli = isset($conn) && ($conn instanceof mysqli);
  if (!$usingPDO && !$usingMysqli) {
    $_SESSION['verify_error'] = 'Database connection missing.';
    header('Location: verify_otp.php'); exit;
  }

  try {
    // fetch OTP & expiry
    if ($usingPDO) {
      $st = $pdo->prepare("SELECT OTP, OTPExpiry FROM voters WHERE Email = :e LIMIT 1");
      $st->execute([':e'=>$pendingEmail]);
      $row = $st->fetch(PDO::FETCH_ASSOC);
    } else {
      $st = $conn->prepare("SELECT OTP, OTPExpiry FROM voters WHERE Email = ? LIMIT 1");
      $st->bind_param('s', $pendingEmail);
      $st->execute();
      $res = $st->get_result();
      $row = $res ? $res->fetch_assoc() : null;
      $st->close();
    }

    if (!$row) {
      $_SESSION['login_error'] = 'Account not found. Please register again.';
      header('Location: index.php'); exit;
    }

    $dbOtp    = (string)($row['OTP'] ?? '');
    $dbExpiry = (string)($row['OTPExpiry'] ?? '');

    if ($dbOtp === '' || $dbExpiry === '') {
      $_SESSION['verify_error'] = 'No active OTP. Please resend a new code.';
      header('Location: verify_otp.php'); exit;
    }

    $now = new DateTime('now');
    $exp = new DateTime($dbExpiry);
    if ($now > $exp) {
      $_SESSION['verify_error'] = 'Your code expired. Please resend a new one.';
      header('Location: verify_otp.php'); exit;
    }

    if (!hash_equals($dbOtp, $otp)) {
      $_SESSION['verify_error'] = 'Incorrect code. Try again.';
      header('Location: verify_otp.php'); exit;
    }

    // success -> mark verified, clear OTP
    if ($usingPDO) {
      $up = $pdo->prepare("UPDATE voters SET Verified='Verified', OTP=NULL, OTPExpiry=NULL WHERE Email = :e LIMIT 1");
      $up->execute([':e'=>$pendingEmail]);
    } else {
      $up = $conn->prepare("UPDATE voters SET Verified='Verified', OTP=NULL, OTPExpiry=NULL WHERE Email = ? LIMIT 1");
      $up->bind_param('s', $pendingEmail);
      $up->execute();
      $up->close();
    }

    unset($_SESSION['pending_email']);
    $_SESSION['register_success'] = 'Email verified. You can now log in.';
    header('Location: index.php'); exit;

  } catch (Throwable $e) {
    $_SESSION['verify_error'] = 'Verification error: '.$e->getMessage();
    header('Location: verify_otp.php'); exit;
  }
}

/* render form */
$err   = $_SESSION['verify_error'] ?? '';
$note  = $_SESSION['verify_notice'] ?? '';
unset($_SESSION['verify_error'], $_SESSION['verify_notice']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify OTP | OEVS</title>
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <style>
    body{font-family:Segoe UI,system-ui,-apple-system,Roboto,Arial,sans-serif;background:#f4f6f9;margin:0;display:flex;min-height:100vh;align-items:center;justify-content:center}
    .card{background:#fff;padding:24px 22px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.06);width:min(420px,92vw)}
    h1{font-size:20px;color:#002f6c;margin:0 0 12px}
    p{color:#555;margin:.25rem 0 1rem}
    .error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:10px 12px;border-radius:8px;margin-bottom:12px}
    .note{background:#eff6ff;border:1px solid #bfdbfe;color:#1e3a8a;padding:10px 12px;border-radius:8px;margin-bottom:12px}
    input[type=text]{width:100%;padding:12px;border:1px solid #ccc;border-radius:8px;font-size:16px}
    button{width:100%;margin-top:12px;padding:12px;border:none;border-radius:8px;background:#002f6c;color:#fff;font-size:15px;cursor:pointer}
    button:hover{background:#001d44}
    .hint{margin-top:10px;font-size:14px}
    a{color:#002f6c;text-decoration:none}
    a:hover{text-decoration:underline}
  </style>
</head>
<body>
  <form class="card" method="POST" action="verify_otp.php">
    <h1>Verify your email</h1>
    <p>We sent a 6-digit code to <b><?= htmlspecialchars($pendingEmail) ?></b>.</p>
    <?php if ($note): ?><div class="note"><?= htmlspecialchars($note) ?></div><?php endif; ?>
    <?php if ($err):  ?><div class="error"><?= htmlspecialchars($err)  ?></div><?php endif; ?>
    <input type="text" name="otp" placeholder="Enter 6-digit code" maxlength="6" autofocus required>
    <button type="submit">Verify</button>
    <div class="hint">
      Didn’t get it? <a href="resend_otp.php?id=<?= urlencode($pendingEmail) ?>">Resend code</a> |
      <a href="index.php">Back to login</a>
    </div>
  </form>
</body>
</html>
