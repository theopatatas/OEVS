<?php
include('session.php');
include('dbcon.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Home - Online Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <style>
    :root{
      --primary:#002f6c;
      --accent:#0056b3;
      --bg:#f4f6f8;
      --white:#fff;
      --ink:#0d2343;
      --muted:#6c7b90;
      --border:#e6ebf4;
      --shadow:0 6px 18px rgba(0,0,0,.08);
      --ring:#9ec5ff;
      --transition:all .2s ease;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      background:var(--bg); margin:0; color:var(--ink)
    }
    a{text-decoration:none;color:inherit}

    /* ===== Layout (page content only) ===== */
    main{padding:24px 16px}
    .container{max-width:1100px; margin:0 auto}
    .panel-grid{display:grid; grid-template-columns:1.35fr .95fr; gap:18px}
    @media (max-width:960px){ .panel-grid{grid-template-columns:1fr} }

    /* ===== Card-style gallery ===== */
    .gallery-card{
      background:#fff; border:1px solid var(--border); border-radius:20px; box-shadow:var(--shadow);
      padding:12px; display:flex; flex-direction:column; gap:10px;
    }
    .preview{position:relative; width:100%; aspect-ratio:16/9; overflow:hidden; border-radius:16px; background:#e9eff8;}
    .preview img{width:100%; height:100%; object-fit:cover; display:block}
    .thumbs{display:flex; gap:10px; overflow-x:auto; padding-bottom:6px; -webkit-overflow-scrolling:touch; scroll-snap-type:x proximity;}
    .thumbs::-webkit-scrollbar{height:8px}
    .thumbs::-webkit-scrollbar-thumb{background:#c7d7f5; border-radius:8px}
    .thumb{flex:0 0 clamp(90px, calc((100% - 20px)/3), 140px); border:1px solid var(--border); border-radius:12px; overflow:hidden; background:#f3f6fc; scroll-snap-align:start;}
    .thumb button{width:100%; aspect-ratio:1/1; display:block; border:0; padding:0; background:transparent; cursor:pointer}
    .thumb img{width:100%; height:100%; object-fit:cover; display:block; transition:transform .2s ease}
    .thumb button:hover img{transform:scale(1.03)}
    .thumb[aria-selected="true"]{outline:3px solid var(--ring); outline-offset:2px}

    /* ===== Side cards ===== */
    .side-stack{display:flex; flex-direction:column; gap:18px}
    .card-lite{background:#f2f6ff; border-radius:12px; padding:16px; color:#0b1b36; border:1px solid #dbe7ff;}
    .card-lite h3{margin:0 0 6px; font-size:18px}
    .card-lite p{margin:0 0 10px}
    .btn-info{display:inline-flex; align-items:center; gap:8px; background:#0a2e5c; color:#fff; padding:8px 12px; border-radius:8px; text-decoration:none; transition:var(--transition);}
    .btn-info:hover{background:#0d3a72}

    /* ===== Modals (legacy styling kept) ===== */
    .modal.hide{display:none}
    .modal .modal-header{padding:10px 12px; border-bottom:1px solid var(--border)}
    .modal .modal-footer{padding:10px 12px; border-top:1px solid var(--border)}
    .btn{display:inline-block; padding:8px 12px; border-radius:8px; background:#e9edf7}
    .btn:hover{background:#dfe7ff}

    footer{text-align:center; padding:20px 0; color:var(--muted); font-size:14px}
  </style>
</head>
<body>

  <?php include 'header.php'; ?>

  <!-- ===== Content ===== -->
  <main>
    <div class="container">
      <div class="panel-grid">
        <!-- Left: Card-style gallery -->
        <section class="gallery-card" aria-label="Campus gallery">
          <div class="preview">
            <img id="previewImg" src="images/1.png" alt="Selected photo" />
          </div>

          <div class="thumbs" role="listbox" aria-label="Choose photo">
            <?php
              $imgs = ['1.png','2.png','3.png','4.png','5.png','6.png','7.png'];
              foreach ($imgs as $i => $img): ?>
              <div class="thumb" aria-selected="<?php echo $i===0 ? 'true':'false'; ?>">
                <button type="button" data-src="images/<?php echo $img; ?>" aria-label="Photo <?php echo $i+1; ?>">
                  <img src="images/<?php echo $img; ?>" alt="" loading="lazy" />
                </button>
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <!-- Right: Mission & Vision -->
        <aside class="side-stack">
          <div class="card-lite">
            <h3>Mission</h3>
            <p>“To make lives better through education.”</p>
            <a class="btn-info" data-toggle="modal" href="#mission">Read More</a>
          </div>
          <div class="card-lite">
            <h3>Vision</h3>
            <p><strong>PHINMA Education</strong></p>
            <p>“We envision PHINMA Araullo University as a dynamic institution of learning.”</p>
            <a class="btn-info" data-toggle="modal" href="#vision">Read More</a>
          </div>
        </aside>
      </div>
    </div>
  </main>

  <!-- ===== Modals ===== -->
  <div class="modal hide fade" id="mission">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">×</button>
      <h3>About PHINMA Education</h3>
    </div>
    <div class="modal-body">
      <p><font color="black">
        For more than a decade, PHINMA built its reputation on transforming existing educational institutions to better serve Filipino students.
        PHINMA Education begins this process by strategically selecting a school from a key growth area and thoroughly transforming its academics,
        operations, and student community to ensure success for Filipino youth from low-income families.
      </font></p>
    </div>
    <div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Close</a></div>
  </div>

  <div class="modal hide fade" id="vision">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">×</button>
      <h3>Fulfilling Our Mission Through Education</h3>
    </div>
    <div class="modal-body">
      <p><font color="black">
        Despite consistently high enrollment, attrition rates remain significant. Out of four students who enter first grade,
        only one will finish a tertiary degree. Seeing these statistics, PHINMA’s leaders focused on improving the country’s state of education.
        In 2004, they committed to education to fully advance their mission—introducing reforms and innovations across partner schools.
      </font></p>
    </div>
    <div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Close</a></div>
  </div>

  <footer>© 2025 Online Election Voting System</footer>

  <!-- ===== JS: gallery swap (page-specific) ===== -->
  <script>
    (function(){
      const preview = document.getElementById('previewImg');
      const thumbs = document.querySelectorAll('.thumb button');

      thumbs.forEach(btn => {
        btn.addEventListener('click', () => {
          const src = btn.getAttribute('data-src');
          if (!src || preview.src.endsWith(src)) return;
          preview.src = src;

          document.querySelectorAll('.thumb').forEach(t => t.setAttribute('aria-selected','false'));
          btn.parentElement.setAttribute('aria-selected','true');
        });
      });
    })();
  </script>
</body>
</html>
