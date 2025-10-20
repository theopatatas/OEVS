<?php
// submit_vote.php — department-scoped tally + mark voter as Voted
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

session_start();
if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }
if (empty($_SESSION['ballot_started'])) { header('Location: home.php?msg=ballot_not_started'); exit; }

require __DIR__ . '/dbcon.php';

/* db helpers */
function db_driver(){ foreach (['pdo','conn','con'] as $v) if (isset($GLOBALS[$v])) return $v; throw new RuntimeException('No DB handle'); }
function db_all($sql,$params=[]){
  $d=db_driver();
  if ($d==='pdo' && $GLOBALS['pdo'] instanceof PDO){ $st=$GLOBALS['pdo']->prepare($sql); $st->execute($params); return $st->fetchAll(PDO::FETCH_ASSOC); }
  $m=$GLOBALS[$d]; if($params){$st=$m->prepare($sql);$types=str_repeat('s',count($params));$st->bind_param($types,...$params);$st->execute();$res=$st->get_result();} else {$res=$m->query($sql);}
  return $res?$res->fetch_all(MYSQLI_ASSOC):[];
}
function db_exec($sql,$params=[]){
  $d=db_driver();
  if ($d==='pdo' && $GLOBALS['pdo'] instanceof PDO){ $st=$GLOBALS['pdo']->prepare($sql); $st->execute($params); return $st->rowCount(); }
  $m=$GLOBALS[$d]; if($params){$st=$m->prepare($sql);$types=str_repeat('s',count($params));$st->bind_param($types,...$params);$ok=$st->execute(); return $ok ? $st->affected_rows : 0; }
  $ok=$m->query($sql); return $ok ? ($m->affected_rows ?? 0) : 0;
}
function db_begin(){ $d=db_driver(); if($d==='pdo') $GLOBALS[$d]->beginTransaction(); else $GLOBALS[$d]->begin_transaction(); }
function db_commit(){ $d=db_driver(); if($d==='pdo') $GLOBALS[$d]->commit(); else $GLOBALS[$d]->commit(); }
function db_rollback(){ $d=db_driver(); if($d==='pdo') $GLOBALS[$d]->rollBack(); else $GLOBALS[$d]->rollback(); }

/* inputs */
$user       = $_SESSION['user'];
$schoolId   = $user['SchoolID'] ?? $user['school_id'] ?? null;
$email      = $user['Email']    ?? $user['email']    ?? null;
$dept       = $user['Department'] ?? $user['dept'] ?? null;
$campus     = $_POST['campus'] ?? ($user['Campus'] ?? $user['campus'] ?? null);
$selections = (isset($_POST['pos']) && is_array($_POST['pos'])) ? $_POST['pos'] : [];
if (!$selections) { header('Location: home.php?msg=missing_data'); exit; }

try {
  db_begin();

  foreach ($selections as $position => $candidateId) {
    $cid = (string)$candidateId;

    // Guard: candidate must be in the same department (server-side)
    $c = db_all("SELECT Department FROM candidate WHERE CandidateID = ? LIMIT 1", [ $cid ]);
    if (!$c) { continue; }
    $candDept = $c[0]['Department'] ?? null;
    if ($dept && strcasecmp((string)$candDept, (string)$dept) !== 0) {
      continue; // ignore invalid selection
    }

    // Tally table pattern: votes(CandidateID, votes)
    $affected = db_exec("UPDATE votes SET votes = votes + 1 WHERE CandidateID = ?", [ $cid ]);
    if ($affected === 0) {
      db_exec("INSERT INTO votes (CandidateID, votes) VALUES (?, 1)", [ $cid ]);
    }

    // Optional mirror to candidate table (if you added VoteCount)
    try { db_exec("UPDATE candidate SET VoteCount = COALESCE(VoteCount,0) + 1 WHERE CandidateID = ? LIMIT 1", [ $cid ]); } catch (Throwable $e) {}
  }

  // Flip voter -> Voted
  $done = 0;
  if ($schoolId) {
    $done = db_exec("UPDATE voters SET Status='Voted', DateVoted=CURDATE(), TimeVoted=CURTIME() WHERE SchoolID = ? LIMIT 1", [ (string)$schoolId ]);
  }
  if ($done === 0 && $email) {
    db_exec("UPDATE voters SET Status='Voted', DateVoted=CURDATE(), TimeVoted=CURTIME() WHERE Email = ? LIMIT 1", [ (string)$email ]);
  }

  db_commit();
} catch (Throwable $e) {
  db_rollback();
}

/* refresh session from DB for UI */
try {
  if ($schoolId) {
    $fresh = db_all("SELECT * FROM voters WHERE SchoolID = ? LIMIT 1", [ (string)$schoolId ]);
    if ($fresh) {
      $_SESSION['user']['Status']    = $fresh[0]['Status'];
      $_SESSION['user']['status']    = $fresh[0]['Status'];
      $_SESSION['user']['DateVoted'] = $fresh[0]['DateVoted'];
      $_SESSION['user']['TimeVoted'] = $fresh[0]['TimeVoted'];
      $_SESSION['user']['campus']    = $_SESSION['user']['campus'] ?? $fresh[0]['Campus'];
      $_SESSION['user']['course']    = $_SESSION['user']['course'] ?? $fresh[0]['Course'];
      $_SESSION['user']['dept']      = $_SESSION['user']['dept']   ?? $fresh[0]['Department'];
      $_SESSION['user']['year']      = $_SESSION['user']['year']   ?? $fresh[0]['Year'];
    } else {
      $_SESSION['user']['Status'] = $_SESSION['user']['status'] = 'Voted';
    }
  } else {
    $_SESSION['user']['Status'] = $_SESSION['user']['status'] = 'Voted';
  }
} catch (Throwable $e) {
  $_SESSION['user']['Status'] = $_SESSION['user']['status'] = 'Voted';
}

/* close gate + back home */
unset($_SESSION['ballot_started'], $_SESSION['ballot_started_at'], $_SESSION['ballot_campus']);
header("Location: home.php?msg=vote_saved");
exit;
