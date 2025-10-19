<?php
session_start();
if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }
$user = $_SESSION['user'];

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
$first = $user['first_name'] ?: ($user['name'] ? preg_split('/\s+/', $user['name'], 2)[0] : $user['school_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Home | OEVS Voting 2025</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    :root{--bg:#f4f6f9; --ink:#333; --brand:#002f6c; --card:#fff; --soft:rgba(0,0,0,.06);}
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:var(--ink);font-family:'Segoe UI',system-ui,-apple-system,Roboto,Arial,sans-serif}
    .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:28px}
    .card{background:var(--card);border-radius:16px;box-shadow:0 20px 40px var(--soft);padding:28px;max-width:900px;width:95%}
    .header{display:flex;gap:16px;align-items:center;margin-bottom:18px}
    .header img{height:60px}
    h1{margin:0;font-size:22px;color:var(--brand);letter-spacing:.3px}
    .hello{font-size:18px;margin:4px 0 16px}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:14px;margin-top:8px}
    .tile{background:#fbfbfb;border:1px solid #eaeaea;border-radius:12px;padding:14px}
    .label{font-size:12px;color:#666;margin-bottom:4px;text-transform:uppercase;letter-spacing:.6px}
    .value{font-size:16px;color:#111}
    .actions{display:flex;gap:10px;margin-top:18px;flex-wrap:wrap}
    .btn{background:var(--brand);color:#fff;border:none;border-radius:10px;padding:10px 14px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px}
    .btn:hover{background:#001d44}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="header">
        <img src="pic/phinma.png" alt="PHINMA">
        <div>
          <!-- Updated heading -->
          <h1>Online Election Voting System</h1>
          <div class="hello">Hi, <strong><?= h($first) ?></strong> 👋</div>
        </div>
      </div>

      <div class="grid">
        <div class="tile"><div class="label">Student Number</div><div class="value"><?= h($user['school_id']) ?></div></div>
        <div class="tile"><div class="label">Full Name</div><div class="value"><?= h($user['name'] ?: '—') ?></div></div>
        <div class="tile"><div class="label">Department</div><div class="value"><?= h($user['dept'] ?: '—') ?></div></div>
        <div class="tile"><div class="label">Course</div><div class="value"><?= h($user['course'] ?: '—') ?></div></div>
        <div class="tile"><div class="label">Year Level</div><div class="value"><?= h($user['year'] ?: '—') ?></div></div>
        <div class="tile"><div class="label">Campus</div><div class="value"><?= h($user['campus'] ?: '—') ?></div></div>
        <div class="tile"><div class="label">Status</div><div class="value"><?= h($user['status'] ?: '—') ?></div></div>
      </div>

      <div class="actions">
        <a class="btn" href="vote.php"><i class="fa-solid fa-check-to-slot"></i> Go to Ballot</a>
        <a class="btn" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </div>
    </div>
  </div>
</body>
</html>
