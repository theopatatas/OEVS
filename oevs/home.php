<?php
session_start();
if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }
$user = $_SESSION['user'];
require __DIR__.'/dbcon.php';

/* helpers */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function photo_url($val){
  $v = trim((string)$val);
  if ($v === '') return 'pic/avatar.svg';

  // absolute web URLs or absolute server paths
  if (preg_match('~^https?://~i', $v)) return $v;
  if ($v[0] === '/') return $v;

  // normalize legacy admin prefixes saved in DB
  //   admin/upload/xxx.jpg                -> upload/xxx.jpg
  //   admin/images/candidates/xxx.jpg     -> images/candidates/xxx.jpg
  $v = preg_replace('~^admin/+(upload|images/candidates)/~i', '$1/', $v);

  // if the path (as saved) exists, use it (with cache-buster)
  $fs = __DIR__ . '/' . ltrim($v, '/');
  if (is_file($fs)) {
    $ver = @filemtime($fs);
    return $v . ($ver ? '?v='.$ver : '');
  }

  // try common locations; supports DB values that are just filenames
  $basename = basename($v);
  $candidates = [
    // preferred current locations
    "images/candidates/$basename",
    "upload/$basename",

    // also try the value as subpath under common roots
    "images/candidates/$v",
    "upload/$v",

    // other historical/fallback folders
    "admin/images/candidates/$basename",
    "admin/upload/$basename",
    "photos/$basename",
    "images/$basename",
    "pic/$basename",
    $basename,
    $v,
  ];

  foreach ($candidates as $rel) {
    $path = __DIR__ . '/' . ltrim($rel, '/');
    if (is_file($path)) {
      $ver = @filemtime($path);
      return $rel . ($ver ? '?v='.$ver : '');
    }
  }

  // final fallback
  return 'pic/avatar.svg';
}

/* tiny DB layer */
function db_driver(){ foreach (['pdo','conn','con'] as $v) if (isset($GLOBALS[$v])) return $v; throw new RuntimeException('No DB handle found.'); }
function db_fetch_value($sql,$params=[]){
  $d=db_driver();
  if ($d==='pdo' && $GLOBALS['pdo'] instanceof PDO){ $st=$GLOBALS['pdo']->prepare($sql); $st->execute($params); return $st->fetchColumn(); }
  $m=$GLOBALS[$d]; if($params){$st=$m->prepare($sql);$types=str_repeat('s',count($params));$st->bind_param($types,...$params);$st->execute();$res=$st->get_result();} else {$res=$m->query($sql);}
  $row=$res?$res->fetch_array():null; return $row?$row[0]:null;
}
function db_fetch_all($sql,$params=[]){
  $d=db_driver();
  if ($d==='pdo' && $GLOBALS['pdo'] instanceof PDO){ $st=$GLOBALS['pdo']->prepare($sql); $st->execute($params); return $st->fetchAll(PDO::FETCH_ASSOC); }
  $m=$GLOBALS[$d]; if($params){$st=$m->prepare($sql);$types=str_repeat('s',count($params));$st->bind_param($types,...$params);$st->execute();$res=$st->get_result();} else {$res=$m->query($sql);}
  return $res?$res->fetch_all(MYSQLI_ASSOC):[];
}

/* filters (campus toggle; dept ALWAYS applied) */
$campusFilter   = $user['campus'] ?? $user['Campus'] ?? null;
$deptFilter     = $user['dept']   ?? $user['Department'] ?? null;
$showAll        = isset($_GET['all']);
$campusForQuery = $showAll ? null : $campusFilter;

/* stats & data — limited to department */
$where  = "WHERE Department = ?";
$params = [$deptFilter];
if ($campusForQuery){ $where .= " AND Campus = ?"; $params[] = $campusForQuery; }

$totalCandidates  = (int) db_fetch_value("SELECT COUNT(*) FROM candidate $where", $params);
$campusCandidates = $totalCandidates;

$byPosition = db_fetch_all("SELECT Position, COUNT(*) c FROM candidate $where GROUP BY Position ORDER BY Position", $params);
$byParty    = db_fetch_all("SELECT Party, COUNT(*) c FROM candidate $where GROUP BY Party ORDER BY Party", $params);

$list = db_fetch_all(
  "SELECT CandidateID, Position, Party, FirstName, LastName, MiddleName, Gender, Year, Department, Course, Photo, Qualification, Campus
     FROM candidate $where
   ORDER BY Position, LastName, FirstName", $params
);

/* group by position */
$groups = [];
foreach ($list as $row) { $pos = trim($row['Position'] ?: 'Unspecified'); $groups[$pos][] = $row; }

/* voted? (accept both status/Status) */
$hasVoted = in_array(strtolower((string)($user['status'] ?? $user['Status'] ?? '')), ['voted','yes'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Home | OEVS Voting 2025</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    :root{ --bg:#f6f8fc; --ink:#0f2653; --muted:#5b6b86; --card:#fff; --line:#e7edf6; --soft:rgba(12,27,64,.06);
           --r:14px; --pad:18px; --gap:14px; --site-width:1320px; }
    *{box-sizing:border-box} html,body{height:100%; overflow-x:hidden;}
    body{margin:0;background:var(--bg);color:var(--ink);font:14px/1.55 Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif}
    a{color:inherit;text-decoration:none}
    .visually-hidden{position:absolute !important;clip:rect(1px,1px,1px,1px);clip-path:inset(50%);height:1px;width:1px;overflow:hidden;white-space:nowrap}

    .container-body{max-width:var(--site-width);margin:0 auto;padding:0 20px}
    .card{background:var(--card);border:1px solid var(--line);border-radius:var(--r);box-shadow:0 10px 28px var(--soft);padding:var(--pad)}
    .tile{border:1px dashed var(--line);border-radius:12px;padding:12px;background:#fff;min-height:68px;display:flex;flex-direction:column;justify-content:center}
    .label{font-size:11px;text-transform:uppercase;letter-spacing:.4px;color:var(--muted);margin-bottom:4px}
    .value{font-size:15px;word-break:break-word}

    .profile-grid{display:grid;gap:var(--gap);grid-template-columns:repeat(auto-fit,minmax(180px,1fr));}
    .stats-grid{display:grid;gap:var(--gap);grid-template-columns:repeat(auto-fit,minmax(220px,1fr));margin-top:14px}
    .stat{background:#fff;border:1px solid var(--line);border-radius:12px;padding:14px}
    .stat .sm{font-size:12px;color:var(--muted)} .stat .num{font-size:22px;font-weight:800;margin-top:4px}
    .chips{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px}
    .chip{border:1px solid var(--line);border-radius:999px;padding:6px 10px;font-size:12px;background:#f3f6ff;white-space:nowrap}

    .toolbar{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:18px}
    .search{position:relative;flex:1 1 320px;min-width:220px}
    .search input{width:100%;border:1px solid var(--line);border-radius:10px;padding:10px 36px 10px 12px;background:#fff}
    .search i{position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#7183a3}

    .section{margin-top:22px} .section-title{font-size:16px;margin:0 0 10px;color:#0e2444}
    .list{display:grid;gap:var(--gap);grid-template-columns:repeat(auto-fit,minmax(260px,1fr));}
    .cand{background:#fff;border:1px solid var(--line);border-radius:12px;padding:12px;display:flex;gap:12px;transition:box-shadow .15s ease}
    .cand:hover{box-shadow:0 10px 22px var(--soft)}
    .avatar{width:60px;height:60px;border-radius:12px;overflow:hidden;background:#eef2fb;display:flex;align-items:center;justify-content:center;border:1px solid var(--line);flex:0 0 60px}
    .avatar img{width:100%;height:100%;object-fit:cover;display:block}
    .c-name{font-weight:700}.meta{font-size:12px;color:var(--muted);margin-top:2px}
    .badges{display:flex;gap:6px;flex-wrap:wrap;margin-top:8px}
    .badge{border:1px solid var(--line);border-radius:999px;padding:3px 8px;font-size:11px;background:#f8fafc}
    .qual{font-size:12px;color:var(--muted);margin-top:8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
    .empty{padding:18px;border:1px dashed var(--line);border-radius:12px;background:#fff}
    .foot{margin:24px 0 60px;color:#6b7a99;font-size:12px;text-align:center}
    @media (max-width:640px){ .avatar{width:54px;height:54px} }
  </style>
</head>
<body>

<?php require __DIR__.'/header.php'; ?>

<main class="container-body" role="main">
  <!-- profile -->
  <section aria-labelledby="profile-heading" class="card">
    <h2 id="profile-heading" class="visually-hidden">Student Profile</h2>
    <div class="profile-grid">
      <div class="tile"><div class="label">Student Number</div><div class="value"><?= h($user['school_id'] ?? $user['SchoolID'] ?? '—') ?></div></div>
      <div class="tile"><div class="label">Full Name</div><div class="value"><?= h($user['name'] ?? '—') ?></div></div>
      <div class="tile"><div class="label">Department</div><div class="value"><?= h($user['dept'] ?? $user['Department'] ?? '—') ?></div></div>
      <div class="tile"><div class="label">Course</div><div class="value"><?= h($user['course'] ?? $user['Course'] ?? '—') ?></div></div>
      <div class="tile"><div class="label">Year</div><div class="value"><?= h($user['year'] ?? $user['Year'] ?? '—') ?></div></div>
      <div class="tile"><div class="label">Campus</div><div class="value"><?= h($user['campus'] ?? $user['Campus'] ?? '—') ?></div></div>
      <div class="tile" style="grid-column:1/-1"><div class="label">Status</div><div class="value"><?= h($user['status'] ?? $user['Status'] ?? '—') ?></div></div>
    </div>
  </section>

  <!-- stats -->
  <section class="stats-grid" aria-label="Summary stats">
    <div class="stat"><div class="sm">Candidates (Your Dept)</div><div class="num"><?= number_format($totalCandidates) ?></div></div>
    <div class="stat"><div class="sm"><?= $showAll ? 'Shown (All campuses)' : 'Shown (Your campus)' ?></div><div class="num"><?= number_format($campusCandidates) ?></div></div>
    <div class="stat">
      <div class="sm">Positions</div>
      <div class="chips">
        <?php foreach ($byPosition as $p): ?>
          <span class="chip"><?= h($p['Position'] ?: 'Unspecified') ?>: <b><?= (int)$p['c'] ?></b></span>
        <?php endforeach; if (!$byPosition): ?><span class="chip">None</span><?php endif; ?>
      </div>
    </div>
    <div class="stat">
      <div class="sm">Parties</div>
      <div class="chips">
        <?php foreach ($byParty as $p): ?>
          <span class="chip"><?= h($p['Party'] ?: 'Independent') ?>: <b><?= (int)$p['c'] ?></b></span>
        <?php endforeach; if (!$byParty): ?><span class="chip">None</span><?php endif; ?>
      </div>
    </div>
  </section>

  <!-- search + chips -->
  <div class="toolbar">
    <div class="search">
      <input id="search" type="search" placeholder="Search candidates by name, party, course…" aria-label="Search candidates">
      <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
    </div>
    <div class="chip">Department: <?= h($deptFilter ?? '—') ?></div>
    <?php if ($showAll): ?>
      <div class="chip">Viewing: All campuses</div>
    <?php else: ?>
      <div class="chip">Campus: <?= h($campusFilter ?? '—') ?></div>
    <?php endif; ?>
  </div>

  <!-- candidate lists -->
  <?php if (!$groups): ?>
    <div class="section"><div class="empty">No candidates found for your department<?= $campusForQuery ? ' in campus '.h($campusForQuery) : '' ?>.</div></div>
  <?php else: ?>
    <?php foreach ($groups as $position => $rows): ?>
      <section class="section" aria-labelledby="pos-<?= md5($position) ?>">
        <h3 id="pos-<?= md5($position) ?>" class="section-title"><?= h($position) ?></h3>
        <div class="list" data-position="<?= h($position) ?>">
          <?php foreach ($rows as $c):
            $name  = trim(preg_replace('/\s+/', ' ', ($c['FirstName'] ?? '').' '.($c['MiddleName'] ?? '').' '.($c['LastName'] ?? '')));
            $party = $c['Party'] ?: 'Independent';
            $tags  = implode(' • ', array_filter([$party, $c['Gender'] ?? null, $c['Year'] ?? null]));
            $img   = photo_url($c['Photo'] ?? '');
          ?>
            <article class="cand"
                     data-name="<?= h(strtolower($name)) ?>"
                     data-party="<?= h(strtolower($party)) ?>"
                     data-course="<?= h(strtolower($c['Course'] ?? '')) ?>">
              <div class="avatar" aria-hidden="true">
                <img src="<?= h($img) ?>" alt="<?= h($name) ?>" loading="lazy" decoding="async"
                     onerror="this.onerror=null;this.src='pic/avatar.svg';">
              </div>
              <div>
                <div class="c-name"><?= h($name ?: 'Unnamed') ?></div>
                <div class="meta"><?= h($tags ?: '—') ?></div>
                <div class="badges">
                  <span class="badge"><?= h($c['Department'] ?: 'Dept —') ?></span>
                  <span class="badge"><?= h($c['Course'] ?: 'Course —') ?></span>
                  <span class="badge"><?= h($c['Campus'] ?: 'Campus —') ?></span>
                </div>
                <?php if (!empty($c['Qualification'])): ?>
                  <div class="qual"><?= h($c['Qualification']) ?></div>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  <?php endif; ?>

  <div class="foot">Source: <code>oevs_db.candidate</code> • View limited to your Department</div>
</main>

<script>
  // search filter
  const search = document.getElementById('search');
  if (search) {
    search.addEventListener('input', () => {
      const q = search.value.trim().toLowerCase();
      document.querySelectorAll('.list .cand').forEach(card => {
        const hit = (card.dataset.name?.includes(q) || card.dataset.party?.includes(q) || card.dataset.course?.includes(q));
        card.style.display = hit ? '' : 'none';
      });
    });
  }
</script>
</body>
</html>
