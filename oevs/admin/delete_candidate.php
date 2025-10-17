<?php
// admin/candidate_delete.php
declare(strict_types=1);
session_start();
ini_set('display_errors','1'); error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once __DIR__ . '/dbcon.php';

/* Normalize mysqli from dbcon.php */
$mysqli = null;
if (isset($mysqli) && $mysqli instanceof mysqli) { /* ok */ }
elseif (isset($conn) && $conn instanceof mysqli) { $mysqli = $conn; }
elseif (isset($con)  && $con  instanceof mysqli) { $mysqli = $con; }
else { die('DB connection missing in dbcon.php'); }
$mysqli->set_charset('utf8mb4');

/* CSRF */
if (empty($_POST['token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['token'])) {
  http_response_code(400);
  exit('Invalid request (token).');
}

/* ID */
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
  header('Location: candidate_list.php?ok=0&msg='.urlencode('Invalid candidate id.'));
  exit;
}

/* Get candidate (for photo + history text) */
$sel = $mysqli->prepare('SELECT `FirstName`,`LastName`,`Photo` FROM `candidate` WHERE `CandidateID`=?');
$sel->bind_param('i', $id);
$sel->execute();
$cand = $sel->get_result()->fetch_assoc();
$sel->close();

if (!$cand) {
  header('Location: candidate_list.php?ok=0&msg='.urlencode('Candidate not found.'));
  exit;
}

/* Delete votes (no FKs in MyISAM) */
$dv = $mysqli->prepare('DELETE FROM `votes` WHERE `CandidateID`=?');
$dv->bind_param('i', $id);
$dv->execute();
$dv->close();

/* Delete candidate */
$dc = $mysqli->prepare('DELETE FROM `candidate` WHERE `CandidateID`=?');
$dc->bind_param('i', $id);
$dc->execute();
$affected = $dc->affected_rows;
$dc->close();

if ($affected > 0) {
  /* Delete photo (try ../upload then ./upload) */
  $rel = ltrim((string)$cand['Photo'], '/');
  $roots = [realpath(__DIR__ . '/../'), realpath(__DIR__ . '/')];
  foreach ($roots as $root) {
    if (!$root) continue;
    $abs = realpath($root . '/' . $rel);
    if ($abs && str_starts_with($abs, $root) && is_file($abs)) { @unlink($abs); break; }
  }

  /* Optional history log */
  try {
    if ($mysqli->query("SHOW TABLES LIKE 'history'")->num_rows) {
      $user = $_SESSION['username'] ?? 'Admin';
      $data = trim($cand['FirstName'].' '.$cand['LastName']);
      $date = date('n/j/Y G:i:s'); // similar to your dump format
      $act  = 'Deleted Candidate';
      $hs = $mysqli->prepare("INSERT INTO `history` (`data`,`action`,`date`,`user`) VALUES (?,?,?,?)");
      $hs->bind_param('ssss', $data, $act, $date, $user);
      $hs->execute(); $hs->close();
    }
  } catch (Throwable $e) { /* ignore logging errors */ }

  header('Location: candidate_list.php?ok=1&msg='.urlencode('Candidate deleted: '.$cand['FirstName'].' '.$cand['LastName']));
  exit;
}

header('Location: candidate_list.php?ok=0&msg='.urlencode('Delete failed.'));
exit;
