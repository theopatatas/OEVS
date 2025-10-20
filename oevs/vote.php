<?php
// (dev) show errors while wiring
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

session_start();
if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }
if (empty($_SESSION['ballot_started'])) { header('Location: home.php?msg=ballot_not_started'); exit; }

$user   = $_SESSION['user'];
$campus = $_GET['campus'] ?? ($_SESSION['ballot_campus'] ?? ($user['campus'] ?? $user['Campus'] ?? null));
$dept   = $user['dept'] ?? $user['Department'] ?? null;

require __DIR__ . '/dbcon.php';

/* db helpers */
function db_driver(){ foreach (['pdo','conn','con'] as $v) if (isset($GLOBALS[$v])) return $v; throw new RuntimeException('No DB handle'); }
function db_all($sql,$params=[]){
  $d=db_driver();
  if ($d==='pdo' && $GLOBALS['pdo'] instanceof PDO){ $st=$GLOBALS['pdo']->prepare($sql); $st->execute($params); return $st->fetchAll(PDO::FETCH_ASSOC); }
  $m=$GLOBALS[$d]; if($params){$st=$m->prepare($sql);$types=str_repeat('s',count($params));$st->bind_param($types,...$params);$st->execute();$res=$st->get_result();} else {$res=$m->query($sql);}
  return $res?$res->fetch_all(MYSQLI_ASSOC):[];
}

/* load positions + candidates — ALWAYS limited by department (and campus if given) */
$where  = "WHERE Department = ?";
$params = [$dept];
if ($campus){ $where .= " AND Campus = ?"; $params[] = $campus; }

$rows = db_all("SELECT CandidateID, Position, Party, FirstName, MiddleName, LastName, Course, Gender, Year
                FROM candidate $where ORDER BY Position, LastName, FirstName", $params);

/* group for rendering */
$groups = [];
foreach ($rows as $r) { $pos = trim($r['Position'] ?: 'Unspecified'); $groups[$pos][] = $r; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Ballot | OEVS Voting 2025</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    :root{ --bg:#f6f8fc; --ink:#0f2653; --muted:#5b6b86; --card:#fff; --line:#e7edf6; --soft:rgba(12,27,64,.06);
           --r:14px; --pad:18px; --gap:14px; --site-width:960px; }
    *{ box-sizing:border-box } html,body{ height:100% }
    body{ margin:0; background:var(--bg); color:var(--ink); font:14px/1.55 Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif }
    .wrap{ max-width:var(--site-width); margin:0 auto; padding:18px; }
    .card{ background:var(--card); border:1px solid var(--line); border-radius:var(--r); box-shadow:0 10px 28px var(--soft); padding:var(--pad) }
    h1{ font-size:20px; margin:0 0 6px } .muted{ color:var(--muted); font-size:12.5px }
    .pos{ margin-top:18px } .pos h2{ font-size:16px; margin:0 0 10px }
    .grid{ display:grid; gap:var(--gap); grid-template-columns:repeat(auto-fit, minmax(260px,1fr)); }
    .cand{ display:flex; gap:10px; align-items:center; padding:10px 12px; border:1px solid var(--line); border-radius:10px; background:#fff }
    .cand + .cand{ margin-top:10px } .btn{ display:inline-flex; gap:8px; align-items:center; border-radius:10px; border:1px solid var(--line); padding:10px 14px; font-weight:700; background:#fff }
    .btn.primary{ background:#1c3770; border-color:#1c3770; color:#fff } .actions{ display:flex; gap:10px; margin-top:20px }
  </style>
</head>
<body>
  <?php require __DIR__.'/header.php'; ?>

  <main class="wrap">
    <form class="card" method="post" action="submit_vote.php">
      <h1>Ballot</h1>
      <div class="muted">
        <?= htmlspecialchars($user['name'] ?? $user['SchoolID'] ?? 'Student', ENT_QUOTES, 'UTF-8') ?>
        • Department: <?= htmlspecialchars($dept ?? '—', ENT_QUOTES, 'UTF-8') ?>
        <?= $campus ? ' • Campus: '.htmlspecialchars($campus, ENT_QUOTES, 'UTF-8') : '' ?>
      </div>

      <?php if (!$groups): ?>
        <p class="muted" style="margin-top:8px">No candidates found for your department.</p>
      <?php else: foreach ($groups as $position => $cands): ?>
        <section class="pos">
          <h2><?= htmlspecialchars($position, ENT_QUOTES, 'UTF-8') ?></h2>
          <div class="grid">
            <?php foreach ($cands as $c):
              $name = trim(preg_replace('/\s+/', ' ', ($c['FirstName'] ?? '').' '.($c['MiddleName'] ?? '').' '.($c['LastName'] ?? '')));
              $label = $name ?: ('Candidate #'.$c['CandidateID']);
              $meta  = implode(' • ', array_filter([ $c['Party'] ?: 'Independent', $c['Course'] ?? null, $c['Gender'] ?? null, $c['Year'] ?? null ]));
              $input = 'pos['.$position.']'; $id='p'.md5($position).'_'.$c['CandidateID'];
            ?>
              <label class="cand" for="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
                <input type="radio" id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
                       name="<?= htmlspecialchars($input, ENT_QUOTES, 'UTF-8') ?>"
                       value="<?= (int)$c['CandidateID'] ?>" required>
                <div><div style="font-weight:700"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></div>
                <small style="color:#5b6b86"><?= htmlspecialchars($meta, ENT_QUOTES, 'UTF-8') ?></small></div>
              </label>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; endif; ?>

      <div class="actions">
        <button class="btn" type="button" onclick="history.back()"><i class="fa-solid fa-arrow-left"></i>Back</button>
        <button class="btn primary" type="submit"><i class="fa-solid fa-paper-plane"></i>Submit Ballot</button>
      </div>
      <input type="hidden" name="campus" value="<?= htmlspecialchars($campus ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </form>
  </main>
</body>
</html>
