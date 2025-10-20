<?php
session_start();
if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }

$user = $_SESSION['user'];
$hasVoted = strtolower((string)($user['status'] ?? '')) === 'voted';
if ($hasVoted) { header('Location: home.php?msg=already_voted'); exit; }

$campus = isset($_GET['campus']) ? trim($_GET['campus']) : ($user['campus'] ?? null);

$_SESSION['ballot_started']   = true;
$_SESSION['ballot_started_at'] = time();
if ($campus) $_SESSION['ballot_campus'] = $campus;

session_regenerate_id(true);

$target = 'vote.php';
if ($campus) { $target .= '?campus=' . urlencode($campus); }
header("Location: $target");
exit;
