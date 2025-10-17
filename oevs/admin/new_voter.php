<?php
include('session.php');
include('dbcon.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Multiple Voters - Online Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root{
      --primary-color:#002f6c; --accent-color:#0056b3; --bg-color:#f4f6f8; --white:#fff;
      --shadow:0 4px 12px rgba(0,0,0,.1); --transition:.25s ease; --font:'Inter',sans-serif; --radius:14px;
    }
    *,*::before,*::after{ box-sizing:border-box; }

    body{font-family:var(--font);background:var(--bg-color);margin:0;color:#1f2937}

    /* ===== HEADER (from home.php) ===== */
    header{background:var(--white);box-shadow:var(--shadow);padding:10px 30px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;position:relative;z-index:10}
    .logo-section{display:flex;align-items:center;gap:10px}
    .logo-section img{height:40px}
    .logo-section .title{font-weight:700;font-size:18px;color:var(--primary-color);line-height:1.2}
    nav{display:flex;align-items:center;gap:25px}
    .nav-item{position:relative}
    .nav-item>a{text-decoration:none;color:var(--primary-color);font-weight:600;padding:8px 12px;border-radius:6px;display:inline-block;transition:var(--transition)}
    .nav-item>a:hover{background:var(--primary-color);color:#fff}
    .dropdown{display:none;position:absolute;top:100%;left:0;background:var(--white);box-shadow:var(--shadow);border-radius:6px;min-width:200px;padding:8px 0;z-index:99}
    .dropdown a{display:block;padding:10px 15px;text-decoration:none;color:var(--primary-color);font-weight:500;transition:var(--transition);white-space:nowrap}
    .dropdown a:hover{background:var(--accent-color);color:#fff}
    .nav-item:hover>.dropdown{display:block}
    .submenu{display:none;position:absolute;top:0;left:100%;background:var(--white);box-shadow:var(--shadow);border-radius:6px;min-width:220px;padding:8px 0}
    .submenu a{padding:10px 20px}
    .has-submenu{position:relative}
    .has-submenu>a{display:flex;justify-content:space-between;align-items:center}
    .has-submenu>a i.fa-chevron-right{font-size:12px;margin-left:8px}
    .has-submenu:hover>.submenu{display:block}

    /* ===== PAGE ===== */
    .page-wrap{max-width:1040px;margin:28px auto;padding:0 16px}
    .top-row{display:flex;align-items:center;justify-content:flex-end;margin-bottom:10px}
    .back-btn{display:inline-flex;align-items:center;gap:8px;background:#fff;color:#2563eb;border:2px solid #2563eb;padding:8px 12px;border-radius:8px;text-decoration:none;font-weight:700;box-shadow:0 0 6px rgba(0,0,0,.08)}
    .back-btn:hover{background:#eff6ff}

    .card{background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow);padding:22px}
    .card-header{display:flex;align-items:center;gap:10px;margin-bottom:14px;color:var(--primary-color)}
    .card-header h1{font-size:22px;margin:0;font-weight:800}
    .card-sub{color:#6b7280;font-size:13px;margin-top:-6px;margin-bottom:6px}

    /* ===== FIXED GRID (prevents overlap) ===== */
    .grid{
      display:grid;
      grid-template-columns:repeat(12, minmax(0,1fr));
      gap:14px;
      align-items:start;
    }
    .col-6{ grid-column: span 6; min-width:0; display:flex; flex-direction:column; }
    .col-12{ grid-column: span 12; min-width:0; display:flex; flex-direction:column; }

    .voter-block{border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin:14px 0;background:#fafcff}
    .voter-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
    .voter-title{font-weight:700;color:#374151}
    .remove-btn{display:inline-flex;align-items:center;gap:6px;background:#fff;color:#dc2626;border:1.5px solid #fca5a5;padding:6px 10px;border-radius:8px;cursor:pointer;font-weight:700}
    .remove-btn:hover{background:#fff5f5}

    label{display:block;font-weight:600;font-size:14px;margin-bottom:6px;color:#374151}
    input[type="text"],input[type="email"],select,textarea{
      width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;
      font-family:var(--font);font-size:14px;outline:none;transition:var(--transition);background:#fff
    }
    input:focus,select:focus,textarea:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.15)}
    .hint{color:#6b7280;font-size:12px;margin-top:4px;display:block}

    .actions{display:flex;gap:10px;justify-content:flex-start;padding:12px 0 6px}
    .btn-primary{background:#1d4ed8;color:#fff;border:1px solid #1e40af;padding:10px 14px;border-radius:8px;font-weight:800;cursor:pointer}
    .btn-primary:hover{background:#1e40af}
    .btn-info{background:#0ea5e9;color:#fff;border:1px solid #0284c7;padding:10px 14px;border-radius:8px;font-weight:800;cursor:pointer}
    .btn-info:hover{background:#0284c7}

    @media (max-width:768px){
      .grid{grid-template-columns:repeat(1,minmax(0,1fr))}
      .col-6,.col-12{grid-column:span 1}
      .top-row{justify-content:stretch}
      .back-btn{width:100%;justify-content:center}
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header>
    <div class="logo-section">
      <img src="images/au.png" alt="Logo">
      <div class="title">
        ONLINE ELECTION VOTING SYSTEM<br>
        <small>Phinma Araullo University</small>
      </div>
    </div>

    <nav>
      <div class="nav-item"><a href="home.php"><i class="fas fa-home"></i> Home</a></div>
      <div class="nav-item">
        <a href="#"><i class="fas fa-list-ul"></i> Menu</a>
        <div class="dropdown">
          <a href="candidate_list.php">Candidates</a>
          <a href="voter_list.php">Voters</a>
          <div class="has-submenu">
            <a href="#">Admin Actions <i class="fa fa-chevron-right"></i></a>
            <div class="submenu">
              <a href="result.php"><i class="fa fa-table" style="margin-right:8px;"></i> Election Result</a>
              <a href="winningresult.php"><i class="fa fa-trophy" style="margin-right:8px;"></i> Final Result</a>
              <a href="backupnreset.php"><i class="fa fa-database" style="margin-right:8px;"></i> Backup and Reset</a>
              <a href="dashboard.php"><i class="fa fa-chart-bar" style="margin-right:8px;"></i> Analytics</a>
            </div>
          </div>
        </div>
      </div>
      <div class="nav-item">
        <a href="#"><i class="fas fa-user-circle"></i> Profile</a>
        <div class="dropdown"><a href="profile.php">View Profile</a></div>
      </div>
      <div class="nav-item">
        <a href="#"><i class="fas fa-info-circle"></i> About</a>
        <div class="dropdown">
          <a href="about.php">System Info</a>
          <a href="contact.php">Contact Us</a>
        </div>
      </div>
      <div class="nav-item"><a href="logout.php" style="color:red;"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </nav>
  </header>
  <!-- /Header -->

  <div class="page-wrap">
    <div class="top-row">
      <a class="back-btn" href="voter_list.php"><i class="fa-solid fa-arrow-left"></i> Back to Voters</a>
    </div>

    <div class="card">
      <div class="card-header">
        <i class="fa-solid fa-user-plus"></i><h1>Add Multiple Voters</h1>
      </div>
      <p class="card-sub">Fill out the details below. Use “Add Another Voter” to insert more rows, then click “Save All”.</p>

      <form id="save_voters_form">
        <input type="hidden" class="pc_date" name="pc_date"/>
        <input type="hidden" class="pc_time" name="pc_time"/>
        <input type="hidden" name="user_name" class="user_name" value="<?php echo htmlspecialchars($_SESSION['User_Type'] ?? ''); ?>"/>

        <div id="voterContainer">
          <div class="voter-block" data-index="1">
            <div class="voter-head">
              <div class="voter-title">Voter #<span class="voter-num">1</span></div>
              <button type="button" class="remove-btn" style="display:none;"><i class="fa-solid fa-trash"></i> Remove</button>
            </div>

            <!-- Equal 2-column layout -->
            <div class="grid">
              <div class="col-6">
                <label>First Name <span style="color:#dc2626">*</span></label>
                <input type="text" name="FirstName[]" class="FirstName" placeholder="Juan" required>
              </div>
              <div class="col-6">
                <label>Last Name <span style="color:#dc2626">*</span></label>
                <input type="text" name="LastName[]" class="LastName" placeholder="Dela Cruz" required>
              </div>

              <div class="col-6">
                <label>Middle Name</label>
                <!-- Keep original name 'Section[]' for compatibility -->
                <input type="text" name="Section[]" class="Section" placeholder="Santos (optional)">
              </div>
              <div class="col-6">
                <label>Year Level <span style="color:#dc2626">*</span></label>
                <select name="Year[]" class="Year" required>
                  <option value="" disabled selected>Select year level</option>
                  <option>1st year</option><option>2nd year</option><option>3rd year</option><option>4th year</option>
                </select>
              </div>

              <div class="col-6">
                <label>Phinma Email <span style="color:#dc2626">*</span></label>
                <input type="email" name="UserName[]" class="UserName" placeholder="juan.delacruz@phinma.edu.ph" required>
              </div>
              <div class="col-6">
                <label>Student ID <span style="color:#dc2626">*</span></label>
                <input type="text" name="SchoolID[]" class="SchoolID" placeholder="e.g., 22-12345" required>
              </div>

              <div class="col-6">
                <label>Password <span style="color:#dc2626">*</span></label>
                <input type="text" name="Password[]" class="Password" placeholder="temporary password" required>
              </div>
              <div class="col-6" aria-hidden="true"></div>
            </div>
          </div>
        </div>

        <div class="actions">
          <button type="button" id="addVoterBtn" class="btn-info"><i class="fa-solid fa-plus"></i> Add Another Voter</button>
          <button type="submit" id="save_voter" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save All</button>
        </div>
      </form>
    </div>
  </div>

  <footer style="text-align:center;padding:20px 0;color:#6b7280;font-size:14px">© 2025 Online Election Voting System</footer>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    // fallback notification
    window.showNotification = window.showNotification || function(o){ alert(o?.message || 'Done'); };

    $(function(){
      // timestamp
      const now=new Date(),p=n=>String(n).padStart(2,'0');
      $(".pc_date").val(`${p(now.getMonth()+1)}/${p(now.getDate())}/${now.getFullYear()}`);
      $(".pc_time").val(`${p(now.getHours())}:${p(now.getMinutes())}:${p(now.getSeconds())}`);

      // add block
      $('#addVoterBtn').on('click', function(){
        const $clone = $('.voter-block').first().clone(true,true);
        $clone.find('input').val('');
        $clone.find('select').prop('selectedIndex',0);
        const next = $('#voterContainer .voter-block').length + 1;
        $clone.attr('data-index', next).find('.voter-num').text(next);
        $clone.find('.remove-btn').show();
        $('#voterContainer').append($clone);
      });

      // remove block
      $('#voterContainer').on('click','.remove-btn', function(){
        $(this).closest('.voter-block').remove();
        $('#voterContainer .voter-block').each(function(i){
          $(this).attr('data-index', i+1).find('.voter-num').text(i+1);
          $(this).find('.remove-btn').toggle($('#voterContainer .voter-block').length>1);
        });
      });
      $('.voter-block .remove-btn').toggle($('#voterContainer .voter-block').length>1);

      // submit
      $('#save_voters_form').on('submit', function(e){
        e.preventDefault();

        // Prevent double-submit
        const $btn = $('#save_voter').prop('disabled', true);

        // Basic required check
        let ok=true;
        $('#voterContainer .voter-block [required]').each(function(){
          if(!this.value.trim()){ this.focus(); ok=false; return false; }
        });
        if(!ok){
          $btn.prop('disabled', false);
          showNotification({message:'Please complete all required fields.',type:'error',autoClose:true,duration:5});
          return;
        }

        $.ajax({
          type: 'POST',
          url: 'save_voter.php',
          data: $(this).serialize(),
          dataType: 'json',
          success: function(res){
            if(res && res.success){
              showNotification({ message: `Saved ${res.saved} voter(s).`, type: "success", autoClose: true, duration: 4 });
              setTimeout(()=> location.href='voter_list.php', 1200);
            } else {
              const msg = (res && (res.message || res.error)) ? (res.message || res.error) : 'Save failed.';
              const list = (res && res.errors && res.errors.length) ? `\n\nDetails:\n• `+res.errors.join('\n• ') : '';
              alert(msg + list);
            }
          },
          error: function(xhr){
            const detail = xhr.responseText ? `\n\nServer says:\n${xhr.responseText}` : '';
            alert(`Request failed (${xhr.status} ${xhr.statusText}).` + detail);
          },
          complete: function(){
            $btn.prop('disabled', false);
          }
        });
      });
    });
  </script>
</body>
</html>
