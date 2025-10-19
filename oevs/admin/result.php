<?php
include('session.php');
include('dbcon.php');

// ------- helpers -------
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function int0($v){ return is_numeric($v) ? (int)$v : 0; }

// Current filter
$filterPos = isset($_GET['pos']) ? trim($_GET['pos']) : '';

// Positions for menu
$posRes = mysqli_query($conn, "SELECT DISTINCT `Position` FROM `candidate` ORDER BY `Position` ASC");
$positions = [];
while ($r = mysqli_fetch_assoc($posRes)) $positions[] = $r['Position'];

// Loop set
$posLoop = $filterPos && in_array($filterPos, $positions, true) ? [$filterPos] : $positions;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Election Result - Online Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- If header.php already loads these, you can remove them here -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root{
      --primary:#002f6c;
      --accent:#0056b3;
      --bg:#f9f9f9;
      --white:#fff;
      --shadow:0 5px 15px rgba(0,0,0,.10);
      --muted:#6b7280;
      --danger:#c1121f;
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial;background:var(--bg);color:#0b1324}

    /* ===== Page chrome ===== */
    .content{max-width:1200px;margin:24px auto;padding:0 16px}

    /* Toolbar (Download removed) */
    .toolbar{
      background:var(--white);box-shadow:var(--shadow);border-radius:12px;padding:14px;display:flex;flex-wrap:wrap;
      gap:10px;align-items:center;justify-content:space-between;margin-bottom:16px
    }
    .left-tools{display:flex;gap:10px;align-items:center}
    .right-tools{display:flex;gap:10px;align-items:center}

    /* Filter Button — outline only */
    .filter-wrap{position:relative;display:inline-block}
    .btn-filter{
      background: transparent;
      color: var(--primary);
      border:1px solid var(--primary);
      border-radius:10px;
      padding:10px 14px;
      font-weight:700;
      display:inline-flex;align-items:center;gap:10px;
      cursor:pointer;box-shadow:none;
    }
    .btn-filter i{font-size:14px;color:var(--primary)}
    .btn-filter:hover{background:#f0f6ff}
    .caret{border: solid var(--primary);border-width:0 2px 2px 0;display:inline-block;padding:3px;transform:rotate(45deg)}
    .filter-menu{
      position:absolute;top:110%;left:0;background:#fff;border:1px solid #e5e7eb;border-radius:12px;min-width:240px;
      box-shadow:0 12px 28px rgba(0,0,0,.12);padding:8px 0;display:none;z-index:30
    }
    .filter-menu.open{display:block}
    .filter-item{display:flex;align-items:center;gap:10px;padding:10px 14px;color:#0b1324;text-decoration:none;cursor:pointer}
    .filter-item:hover{background:#f3f6ff}
    .dot{width:16px;height:16px;border:2px solid #9aa1ac;border-radius:999px;display:inline-flex;align-items:center;justify-content:center}
    .dot .checked{width:8px;height:8px;background:#0b4a9f;border-radius:999px;display:none}
    .filter-item.active .dot{border-color:#0b4a9f}
    .filter-item.active .dot .checked{display:block}
    .filter-label{font-weight:600}
    .filter-caption{font-size:12px;color:#6b7280}

    /* Search */
    .search{display:flex;align-items:center;background:#fff;border:1px solid #d0d7de;border-radius:8px;padding:0 10px}
    .search input{border:0;outline:0;padding:9px 8px;font-size:14px;width:220px}

    /* ===== Chart grid ===== */
    .section{background:var(--white);box-shadow:var(--shadow);border-radius:12px;padding:24px;margin-bottom:18px}
    .section h2{margin:0 0 14px;text-align:center;color:#000}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px}
    .card{
      background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px;display:flex;flex-direction:column;align-items:center;
      justify-content:space-between;min-height:320px
    }
    .pos{font-size:12px;font-weight:700;color:#333;margin-bottom:6px}
    .bar-wrap{position:relative;height:160px;width:34px;background:rgba(0,0,0,.06);border-radius:6px;display:flex;align-items:flex-end;justify-content:center;overflow:hidden}
    .bar{width:100%;background:var(--primary);border-radius:6px 6px 0 0}
    .tick{position:absolute;left:0;width:100%;height:1px;background:rgba(0,0,0,.2)}
    .tick.t25{bottom:25%}.tick.t50{bottom:50%}.tick.t75{bottom:75%}.tick.t100{bottom:100%}
    .votes{margin-top:6px;font-size:12px;font-weight:800}
    .avatar{margin-top:10px;width:64px;height:64px;border-radius:50%;object-fit:cover;border:2px solid #000;background:#f3f4f6}
    .name{margin-top:6px;font-size:13px;font-weight:800;text-align:center}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border:1px solid #d0d7de;border-radius:8px;background:#eef4ff;color:#163d7a;font-weight:700;cursor:pointer;text-decoration:none}
    .btn.xs{font-size:12px}

    /* Modal */
    .modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:20px}
    .modal.open{display:flex}
    .modal-card{background:#fff;border-radius:12px;max-width:720px;width:100%;box-shadow:var(--shadow)}
    .modal-h{padding:14px 16px;border-bottom:1px solid #eee;display:flex;align-items:center;justify-content:space-between}
    .modal-b{padding:16px}
    .modal-f{padding:12px 16px;border-top:1px solid #eee;text-align:right}
    .close{background:#eee;border:0;border-radius:8px;padding:6px 10px;cursor:pointer}

    footer{color:#666;text-align:center;padding:20px 0}

    @media (max-width:768px){
      .search input{width:140px}
    }
  </style>
</head>
<body>

  <?php
    // Optional: set active page for header highlighting
    $activePage = 'results';
    include 'header.php';
  ?>

  <div class="content">

    <!-- ===== Toolbar (Filter button without background) ===== -->
    <div class="toolbar">
      <div class="left-tools">
        <div class="filter-wrap">
          <button class="btn-filter" id="filterBtn" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-filter"></i> Filter By Position <span class="caret"></span>
          </button>

          <div class="filter-menu" id="filterMenu" role="menu" aria-labelledby="filterBtn">
            <!-- All option -->
            <a class="filter-item <?php echo $filterPos===''?'active':''; ?>" data-value="" role="menuitem">
              <span class="dot"><span class="checked"></span></span>
              <div>
                <div class="filter-label">All</div>
                <div class="filter-caption">Show all positions</div>
              </div>
            </a>
            <?php foreach ($positions as $p): ?>
              <a class="filter-item <?php echo ($filterPos===$p)?'active':''; ?>" data-value="<?php echo h($p); ?>" role="menuitem">
                <span class="dot"><span class="checked"></span></span>
                <div>
                  <div class="filter-label"><?php echo h($p); ?></div>
                  <div class="filter-caption">Only <?php echo h($p); ?></div>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="right-tools">
        <div class="search">
          <i class="fa fa-search" style="color:#9aa1ac"></i>
          <input type="text" id="search" placeholder="Search candidate...">
        </div>
      </div>
    </div>

    <!-- ===== Results per position ===== -->
    <?php
    if (!$posLoop) echo '<div class="section"><h2>No positions found</h2></div>';

    foreach ($posLoop as $position):
      $safe = mysqli_real_escape_string($conn, $position);
      $q = mysqli_query($conn, "
        SELECT c.CandidateID, c.FirstName, c.LastName, c.Year, c.Position, c.Photo, c.Qualification, c.Party,
               (SELECT COUNT(*) FROM votes v WHERE v.CandidateID = c.CandidateID) AS vote_count
        FROM candidate c
        WHERE c.Position = '$safe'
        ORDER BY vote_count DESC, c.LastName ASC
      ");
      $cands = [];
      while ($row = mysqli_fetch_assoc($q)) $cands[] = $row;
      if (!$cands) continue;
      $maxVotes = max(array_map('int0', array_column($cands, 'vote_count')));
    ?>
      <section class="section">
        <h2><?php echo h($position); ?></h2>
        <div class="grid" data-position="<?php echo h($position); ?>">
          <?php foreach ($cands as $c):
            $votes = int0($c['vote_count']);
            $pct = $maxVotes > 0 ? round(($votes / $maxVotes) * 100, 2) : 0;
            $name = $c['FirstName'].' '.$c['LastName'];
            $photoPath = $c['Photo'];
            $photo = (is_string($photoPath) && $photoPath !== '' && file_exists($photoPath)) ? $photoPath : 'images/default-avatar.png';
          ?>
          <div class="card candidate-card" data-name="<?php echo h(strtolower($name)); ?>">
            <div class="pos"><?php echo h($position); ?></div>
            <div class="bar-wrap">
              <div class="tick t25"></div>
              <div class="tick t50"></div>
              <div class="tick t75"></div>
              <div class="tick t100"></div>
              <div class="bar" style="height:<?php echo $pct; ?>%"></div>
            </div>
            <div class="votes"><?php echo $votes; ?> votes</div>
            <img class="avatar" src="<?php echo h($photo); ?>" alt="<?php echo h($name); ?>">
            <div class="name"><?php echo h($name); ?></div>
            <div>
              <button class="btn xs" data-open="#m_<?php echo $c['CandidateID']; ?>">View</button>
            </div>
          </div>

          <!-- Modal -->
          <div class="modal" id="m_<?php echo $c['CandidateID']; ?>">
            <div class="modal-card">
              <div class="modal-h">
                <strong>Candidate Information</strong>
                <button class="close" data-close="#m_<?php echo $c['CandidateID']; ?>">Close</button>
              </div>
              <div class="modal-b">
                <div style="display:flex;flex-wrap:wrap;gap:20px;align-items:flex-start">
                  <div style="flex:0 0 180px;text-align:center">
                    <img src="<?php echo h($photo); ?>" alt="Photo" style="width:100%;max-width:180px;border-radius:10px;border:3px solid #fff;box-shadow:0 4px 10px rgba(0,0,0,.1)">
                    <p style="margin-top:10px;font-weight:700"><?php echo h($name); ?></p>
                  </div>
                  <div style="flex:1">
                    <p><strong>Position:</strong> <?php echo h($c['Position']); ?></p>
                    <p><strong>Year:</strong> <?php echo h($c['Year']); ?></p>
                    <p><strong>Party:</strong> <?php echo h($c['Party']); ?></p>
                    <p><strong>Total Votes:</strong> <?php echo $votes; ?></p>
                    <p><strong>Qualification:</strong><br><?php echo nl2br(h($c['Qualification'])); ?></p>
                  </div>
                </div>
              </div>
              <div class="modal-f">
                <button class="close" data-close="#m_<?php echo $c['CandidateID']; ?>">Close</button>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>

    <footer>© 2025 Online Election Voting System</footer>
  </div>

  <script>
    // Filter dropdown open/close
    const btn = document.getElementById('filterBtn');
    const menu = document.getElementById('filterMenu');
    btn.addEventListener('click', (e)=>{
      e.preventDefault();
      const open = menu.classList.toggle('open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    document.addEventListener('click', (e)=>{
      if (!menu.contains(e.target) && !btn.contains(e.target)) {
        menu.classList.remove('open'); btn.setAttribute('aria-expanded','false');
      }
    });
    // Navigate when selecting a filter option
    menu.querySelectorAll('.filter-item').forEach(it=>{
      it.addEventListener('click', ()=>{
        const val = it.getAttribute('data-value') || '';
        const url = new URL(window.location.href);
        if (val) url.searchParams.set('pos', val); else url.searchParams.delete('pos');
        window.location.href = url.toString();
      });
    });

    // Open/close modals
    document.querySelectorAll('[data-open]').forEach(b=>{
      b.addEventListener('click',()=>{
        const id = b.getAttribute('data-open');
        const m = document.querySelector(id);
        if(m){ m.classList.add('open'); }
      });
    });
    document.querySelectorAll('[data-close]').forEach(b=>{
      b.addEventListener('click',()=>{
        const id = b.getAttribute('data-close');
        const m = document.querySelector(id);
        if(m){ m.classList.remove('open'); }
      });
    });
    document.addEventListener('click',e=>{
      if(e.target.classList.contains('modal')) e.target.classList.remove('open');
    });

    // Client-side search
    const search = document.getElementById('search');
    if (search){
      search.addEventListener('input', ()=>{
        const q = search.value.trim().toLowerCase();
        document.querySelectorAll('.candidate-card').forEach(card=>{
          const name = card.getAttribute('data-name') || '';
          card.style.display = name.includes(q) ? '' : 'none';
        });
      });
    }
  </script>
</body>
</html>
