<?php
include('session.php');
include('dbcon.php');
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$communities = [
  ['key'=>'bsit','name'=>'BSIT Community','img'=>'images/bsit.jpg','loc'=>'Phinma AU South Campus'],
  ['key'=>'cma','name'=>'CMA Community','img'=>'images/cma.jpg','loc'=>'Phinma AU South Campus'],
  ['key'=>'civil','name'=>'Civil Engineering Community','img'=>'images/civil.jpg','loc'=>'Phinma AU South Campus'],
  ['key'=>'ccje','name'=>'CCJE Community','img'=>'images/crim.jpg','loc'=>'Phinma AU South Campus'],
  ['key'=>'cahs','name'=>'CAHS Community','img'=>'images/cahs.jpg','loc'=>'Phinma AU South Campus'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About – Online Election Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root{
      --primary:#002f6c;
      --accent:#0056b3;
      --bg:#f4f6f8;
      --white:#fff;
      --shadow:0 5px 15px rgba(0,0,0,.10);
      --muted:#6b7280;
      --danger:#c1121f;
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial;background:var(--bg);color:#0b1324}

    /* Page container */
    .content{max-width:1100px;margin:24px auto;padding:0 16px}

    /* Hero */
    .hero{background:var(--white);box-shadow:var(--shadow);border-radius:12px;padding:22px;margin-bottom:18px}
    .hero h1{margin:0 0 6px;color:#111}
    .hero p{margin:2px 0;color:#333}
    .muted{color:var(--muted)}

    /* Accordion */
    .accordion{display:grid;grid-template-columns:1fr;gap:14px}
    .acc-item{background:var(--white);border:1px solid #e5e7eb;border-radius:12px;box-shadow:var(--shadow)}
    .acc-header{
      width:100%;text-align:left;background:#fff;border:0;border-radius:12px;
      padding:14px 16px;cursor:pointer;font-weight:700;display:flex;justify-content:space-between;align-items:center
    }
    .acc-header:hover{background:#f8fbff}
    .acc-header i{transition:transform .2s ease}
    .acc-item.open .acc-header i{transform:rotate(180deg)}
    .acc-panel{overflow:hidden;max-height:0;transition:max-height .25s ease}
    .acc-body{padding:0 16px 16px 16px;display:flex;gap:18px;align-items:flex-start;flex-wrap:wrap}
    .acc-body img{width:220px;max-width:100%;height:auto;border-radius:10px;border:2px solid #eee;box-shadow:0 4px 10px rgba(0,0,0,.06)}
    .acc-info{min-width:240px}
    .acc-info p{margin:6px 0}

    footer{color:#666;text-align:center;padding:20px 0;margin-top:18px}
    @media (max-width:760px){ .acc-body{flex-direction:column} }
  </style>
</head>
<body>

  <?php
    // Use the shared header (no Contact Us under About)
    $activePage = 'about';
    include 'header.php';
  ?>

  <div class="content">
    <section class="hero">
      <h1>About the System</h1>
      <p><strong>Online Election Voting System</strong></p>
      <p class="muted">For Phinma Araullo University</p>
      <p class="muted">Developed by: 4th Year BSIT</p>
    </section>

    <section class="accordion" id="accordion">
      <?php foreach ($communities as $c): ?>
        <div class="acc-item" id="item_<?php echo h($c['key']); ?>">
          <button class="acc-header" type="button" data-target="panel_<?php echo h($c['key']); ?>">
            <span><?php echo h($c['name']); ?></span>
            <i class="fa fa-chevron-down"></i>
          </button>
          <div class="acc-panel" id="panel_<?php echo h($c['key']); ?>">
            <div class="acc-body">
              <img src="<?php echo h($c['img']); ?>" alt="<?php echo h($c['name']); ?>">
              <div class="acc-info">
                <p><strong>Location:</strong> <?php echo h($c['loc']); ?></p>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </section>

    <footer>© 2025 Online Election Voting System</footer>
  </div>

  <script>
    // Vanilla accordion
    document.querySelectorAll('.acc-header').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const id = btn.getAttribute('data-target');
        const panel = document.getElementById(id);
        const item = btn.parentElement;

        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.acc-item.open').forEach(i=>{
          if(i!==item){
            i.classList.remove('open');
            const p = i.querySelector('.acc-panel');
            p.style.maxHeight = 0;
          }
        });

        if(isOpen){
          item.classList.remove('open');
          panel.style.maxHeight = 0;
        }else{
          item.classList.add('open');
          panel.style.maxHeight = panel.scrollHeight + 'px';
        }
      });
    });

    // Auto-open first item
    const first = document.querySelector('.acc-item .acc-header');
    if(first){ first.click(); }
  </script>
</body>
</html>
