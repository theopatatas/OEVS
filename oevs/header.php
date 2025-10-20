<?php
// header.php
// Expects: $campusFilter, $showAll (bool), $hasVoted (bool)
$campusFilter = $campusFilter ?? null;
$showAll      = isset($showAll) ? (bool)$showAll : false;
$hasVoted     = isset($hasVoted) ? (bool)$hasVoted : false;
?>
<!-- Accent bar + sticky header + breathing gap -->
<style>
  :root{
    --brand:#1c3770; --accent:#3b2a97; --line:#e7edf6;
  }
  .accent{height:4px;background:linear-gradient(90deg,var(--accent),var(--brand));}
  header.headerbar{
    background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:20;
    padding-top:env(safe-area-inset-top, 0px);
    box-shadow:0 2px 10px rgba(12,27,64,.06);
  }
  @supports(padding: max(0px)){
    header.headerbar{ padding-top:max(8px, env(safe-area-inset-top)); }
  }
  .header-shell{max-width:none;margin:0;padding:0}
  .header-inner{
    display:flex;align-items:center;justify-content:space-between;
    padding:8px 12px 10px 12px; gap:10px;
    padding-left:calc(12px + env(safe-area-inset-left, 0px));
    padding-right:calc(12px + env(safe-area-inset-right, 0px));
  }
  .header-left{display:flex;align-items:center;gap:10px;min-width:0}
  .header-logo{height:32px;width:auto}
  .titles{min-width:0}
  .titles .t1{
    font-weight:800;font-size:18px;line-height:1.05;letter-spacing:.2px;color:var(--brand);text-transform:uppercase;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:60vw
  }
  .titles .t2{
    margin-top:1px;font-weight:700;font-size:12.5px;color:var(--brand);opacity:.9;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:60vw
  }
  .header-actions{
    display:flex;gap:8px;align-items:center;flex-wrap:nowrap;
    overflow-x:auto; -webkit-overflow-scrolling:touch; scrollbar-width:none;
    max-width:50vw;
  }
  .header-actions::-webkit-scrollbar{display:none}
  .btn{border-radius:8px;border:1px solid var(--line);background:#fff;display:inline-flex;align-items:center;gap:6px;font-weight:600;flex:0 0 auto}
  .btn i{font-size:12px}
  .btn span{font-size:12.5px}
  .btn.btn-sm{padding:6px 10px}
  .btn.primary{background:#1c3770;border-color:#1c3770;color:#fff}
  .btn[aria-disabled="true"]{opacity:.55;cursor:not-allowed}
  .page-top-gap{height:14px}

  /* Mobile: stack header cleanly */
  @media (max-width: 640px){
    .header-inner{flex-wrap:wrap;gap:8px;}
    .header-left{width:100%;justify-content:space-between;}
    .header-actions{order:2;width:100%;max-width:100%;justify-content:flex-start;}
    .titles .t2{display:none;}
    .btn.btn-sm{padding:6px 8px}
    .btn span{display:none}
    .page-top-gap{height:14px}
  }
</style>

<div class="accent" aria-hidden="true"></div>

<header class="headerbar" role="banner">
  <div class="header-shell">
    <div class="header-inner">
      <div class="header-left">
        <img class="header-logo" src="pic/au.png" alt="AU logo" loading="lazy" decoding="async">
        <div class="titles">
          <div class="t1">ONLINE ELECTION VOTING SYSTEM</div>
          <div class="t2">Phinma Araullo University</div>
        </div>
      </div>

      <nav class="header-actions" aria-label="Header actions">
        <?php if ($showAll): ?>
          <a class="btn btn-sm" href="home.php" title="Show my campus only">
            <i class="fa-solid fa-filter"></i><span>My Campus (<?= htmlspecialchars($campusFilter ?: '—', ENT_QUOTES, 'UTF-8') ?>)</span>
          </a>
        <?php else: ?>
          <a class="btn btn-sm" href="home.php?all=1" title="Show all campuses">
            <i class="fa-solid fa-list-ul"></i><span>All Campuses</span>
          </a>
        <?php endif; ?>

        <!-- Start Voting button -->
        <a class="btn btn-sm primary"
           href="<?= $hasVoted ? '#' : 'start_voting.php' ?>"
           <?= $hasVoted ? 'aria-disabled="true" tabindex="-1"' : '' ?>
           onclick="return startVoting(event)">
          <i class="fa-solid fa-check-to-slot"></i>
          <span><?= $hasVoted ? 'Already Voted' : 'Start Voting' ?></span>
        </a>

        <a class="btn btn-sm" href="logout.php" title="Logout">
          <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
        </a>
      </nav>
    </div>
  </div>
</header>

<div class="page-top-gap" aria-hidden="true"></div>

<script>
  // Flags for header actions
  const OEVS = {
    hasVoted: <?= $hasVoted ? 'true' : 'false' ?>,
    showAll:  <?= $showAll  ? 'true' : 'false' ?>,
    campus:   <?= json_encode($campusFilter ?? '') ?>
  };

  function startVoting(e){
    if (OEVS.hasVoted){
      e.preventDefault();
      alert("You’ve already voted. The ballot is locked.");
      return false;
    }
    if (!confirm("Start voting now?")){
      e.preventDefault();
      return false;
    }
    // Hit the starter endpoint so the ballot lets us in
    let url = "start_voting.php";
    if (!OEVS.showAll && OEVS.campus){
      const q = new URLSearchParams({ campus: OEVS.campus });
      url += "?" + q.toString();
    }
    window.location.href = url;
    e.preventDefault();
    return false;
  }
</script>
