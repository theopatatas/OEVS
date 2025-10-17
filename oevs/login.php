<?php
// /voting/oevs/login.php
session_start();
require_once 'dbcon.php';

/* show errors while you’re wiring things up */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.php');
  exit;
}

$identifier = trim($_POST['SchoolID'] ?? '');   // can be SchoolID OR Username OR Email
$pass       = (string)($_POST['Password'] ?? '');

if ($identifier === '' || $pass === '') {
  $_SESSION['login_error'] = 'Enter your username/ID and password.';
  header('Location: index.php'); exit;
}

/* Find voter by SchoolID OR Username OR Email */
$sql  = "SELECT VoterID, FirstName, MiddleName, LastName,
                Username, Password, Email, Year, Department, Campus,
                Status, SchoolID, Verified, OTP, OTPExpiry
         FROM voters
         WHERE SchoolID = ? OR Username = ? OR Email = ?
         LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  $_SESSION['login_error'] = 'DB error (prepare): '.$conn->error;
  header('Location: index.php'); exit;
}
$stmt->bind_param('sss', $identifier, $identifier, $identifier);
$stmt->execute();
$res  = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if (!$user) {
  $_SESSION['login_error'] = 'Invalid credentials.';
  header('Location: index.php'); exit;
}

/* Password check
   Your table currently stores plaintext. If you later migrate to hashes,
   this will auto-detect bcrypt ($2y$) and verify properly. */
$stored = (string)$user['Password'];
$ok = false;
if (preg_match('/^\$2[aby]\$/', $stored)) {
  // hashed (bcrypt)
  $ok = password_verify($pass, $stored);
} else {
  // plaintext (matches your current data)
  $ok = hash_equals($stored, $pass);
}

if (!$ok) {
  $_SESSION['login_error'] = 'Invalid credentials.';
  header('Location: index.php'); exit;
}

/* Must be verified before proceeding */
if (strcasecmp($user['Verified'] ?? '', 'Verified') !== 0) {
  // If you want to push them to OTP flow:
  $_SESSION['pending_email'] = $user['Email'];
  $_SESSION['login_error']   = 'Please verify your account to continue.';
  header('Location: verify_otp.php'); exit;
}

/* Success — log them in */
session_regenerate_id(true);
$_SESSION['voter'] = [
  'id'        => (int)$user['VoterID'],
  'name'      => trim(($user['FirstName'] ?? '').' '.($user['LastName'] ?? '')),
  'email'     => $user['Email'],
  'school_id' => $user['SchoolID'],
  'dept'      => $user['Department'],
  'campus'    => $user['Campus'],
  'status'    => $user['Status'],  // 'Voted' or 'Unvoted'
];

/* Where to go next:
   - If already voted, send to a “thank you / results” page
   - If not yet voted, send to your ballot page
   Change these filenames to your actual pages. */
$next = (strcasecmp($user['Status'], 'Voted') === 0) ? 'thank_you.php' : 'ballot.php';
header("Location: $next");
exit;
