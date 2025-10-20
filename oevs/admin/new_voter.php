<?php
include('session.php');
include('dbcon.php');

/* ---------- Master Departments ---------- */
$MASTER_DEPTS = ['CMA','CELA','CCJE','COE','CAHS','CIT','CAS'];

/* ---------- Canonical Department → Courses map ---------- */
$DEPT_COURSES = [
  'CMA'  => [
    'Bachelor of Science in Accountancy',
    'Bachelor of Science in Business Administration',
    'Bachelor of Science in Management Accounting',
    'Bachelor of Science in Accounting Information System',
    'Bachelor of Science in Entrepreneurship',
    'Bachelor of Science in Hospitality Management',
    'Bachelor of Science in Tourism Management',
  ],
  'CELA' => [
    'Bachelor of Science in Elementary Education',
    'Bachelor of Science in Secondary Education',
    'Bachelor of Science in Political Science',
  ],
  'CCJE' => [
    'Bachelor of Science in Criminology',
  ],
  'COE'  => [
    'Bachelor of Science in Civil Engineering',
  ],
  'CAHS' => [
    'Bachelor of Science in Nursing',
    'Bachelor of Science in Pharmacy',
    'Bachelor of Science in Midwifery',
  ],
  'CIT'  => [
    'Bachelor of Science in Information Technology',
  ],
  // CAS shows ALL courses (we'll fill via ALL_COURSES below)
  'CAS'  => [],
];

/* Build ALL_COURSES and fill CAS = ALL */
$ALL_COURSES = [];
foreach ($DEPT_COURSES as $list) {
  foreach ($list as $c) { if ($c !== '' && !in_array($c, $ALL_COURSES, true)) $ALL_COURSES[] = $c; }
}
$DEPT_COURSES['CAS'] = $ALL_COURSES;

/* ---------- Campus (required) ---------- */
$CAMPUS_OPTIONS = ['Au Main','Au South','Au San Jose'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Multiple Voters - Online Election Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root{ --primary-color:#002f6c; --accent-color:#0056b3; --bg-color:#f4f6f8; --white:#fff;
           --shadow:0 4px 12px rgba(0,0,0,.1); --transition:.25s ease; --font:'Inter',sans-serif; --radius:14px; }
    *,*::before,*::after{ box-sizing:border-box; }
    body{font-family:var(--font);background:var(--bg-color);margin:0;color:#1f2937}
    .page-wrap{max-width:1040px;margin:28px auto;padding:0 16px}
    .top-row{display:flex;align-items:center;justify-content:flex-end;margin-bottom:10px}
    .back-btn{display:inline-flex;align-items:center;gap:8px;background:#fff;color:#2563eb;border:2px solid #2563eb;padding:8px 12px;border-radius:8px;text-decoration:none;font-weight:700;box-shadow:0 0 6px rgba(0,0,0,.08)}
    .back-btn:hover{background:#eff6ff}
    .card{background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow);padding:22px}
    .card-header{display:flex;align-items:center;gap:10px;margin-bottom:14px;color:var(--primary-color)}
    .card-header h1{font-size:22px;margin:0;font-weight:800}
    .card-sub{color:#6b7280;font-size:13px;margin-top:-6px;margin-bottom:6px}
    .grid{display:grid;grid-template-columns:repeat(12, minmax(0,1fr));gap:14px;align-items:start}
    .col-6{ grid-column: span 6; min-width:0; display:flex; flex-direction:column; }
    .col-12{ grid-column: span 12; min-width:0; display:flex; flex-direction:column; }
    .voter-block{border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin:14px 0;background:#fafcff}
    .voter-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
    .voter-title{font-weight:700;color:#374151}
    .remove-btn{display:inline-flex;align-items:center;gap:6px;background:#fff;color:#dc2626;border:1.5px solid #fca5a5;padding:6px 10px;border-radius:8px;cursor:pointer;font-weight:700}
    .remove-btn:hover{background:#fff5f5}
    label{display:block;font-weight:600;font-size:14px;margin-bottom:6px;color:#374151}
    input[type="text"],input[type="email"],select{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-family:var(--font);font-size:14px;outline:none;transition:var(--transition);background:#fff}
    input:focus,select:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.15)}
    .actions{display:flex;gap:10px;justify-content:flex-start;padding:12px 0 6px}
    .btn-primary{background:#1d4ed8;color:#fff;border:1px solid #1e40af;padding:10px 14px;border-radius:8px;font-weight:800;cursor:pointer}
    .btn-primary:hover{background:#1e40af}
    .btn-info{background:#0ea5e9;color:#fff;border:1px solid #0284c7;padding:10px 14px;border-radius:8px;font-weight:800;cursor:pointer}
    .btn-info:hover{background:#0284c7}
    @media (max-width:768px){ .grid{grid-template-columns:repeat(1,minmax(0,1fr))} .col-6,.col-12{grid-column:span 1}
      .top-row{justify-content:stretch} .back-btn{width:100%;justify-content:center} }
  </style>
</head>
<body>

  <?php $activePage = 'voters'; include 'header.php'; ?>

  <div class="page-wrap">
    <div class="top-row">
      <a class="back-btn" href="voters.php"><i class="fa-solid fa-arrow-left"></i> Back to Voters</a>
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
                <!-- original name 'Section[]' maps to MiddleName in your save script -->
                <input type="text" name="Section[]" class="Section" placeholder="Santos (optional)">
              </div>
              <div class="col-6">
                <label>Year Level <span style="color:#dc2626">*</span></label>
                <select name="Year[]" class="Year" required>
                  <option value="" disabled selected>Select year level</option>
                  <option>1st year</option><option>2nd year</option><option>3rd year</option><option>4th year</option>
                </select>
              </div>

              <!-- Department REQUIRED -->
              <div class="col-6">
                <label>Department <span style="color:#dc2626">*</span></label>
                <select name="Department[]" class="Department" required>
                  <option value="" disabled selected>Select department</option>
                  <?php foreach ($MASTER_DEPTS as $d): ?>
                    <option value="<?php echo htmlspecialchars($d,ENT_QUOTES); ?>"><?php echo htmlspecialchars($d); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Course REQUIRED; depends on Department (CAS => ALL courses) -->
              <div class="col-6">
                <label>Course <span style="color:#dc2626">*</span></label>
                <select name="Course[]" class="Course" required>
                  <option value="" disabled selected>Select course</option>
                  <?php foreach ($ALL_COURSES as $c): ?>
                    <option value="<?php echo htmlspecialchars($c,ENT_QUOTES); ?>"><?php echo htmlspecialchars($c); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Campus REQUIRED -->
              <div class="col-6">
                <label>Campus <span style="color:#dc2626">*</span></label>
                <select name="Campus[]" class="Campus" required>
                  <option value="" disabled selected>Select campus</option>
                  <?php foreach ($CAMPUS_OPTIONS as $c): ?>
                    <option value="<?php echo htmlspecialchars($c,ENT_QUOTES); ?>"><?php echo htmlspecialchars($c); ?></option>
                  <?php endforeach; ?>
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
    // Canonical map from PHP
    const BASE_MAP = <?php echo json_encode($DEPT_COURSES, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
    const ALL_COURSES = <?php echo json_encode($ALL_COURSES, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;

    window.showNotification = window.showNotification || function(o){ alert(o?.message || 'Done'); };

    function setupBlock(root){
      const $root = $(root);
      const $dept = $root.find('select.Department');
      const $course = $root.find('select.Course');

      function rebuildCourses(keepCurrent){
        const current = keepCurrent ? $course.val() : '';
        const d = $dept.val();
        const list = (d && BASE_MAP[d] && BASE_MAP[d].length) ? BASE_MAP[d] : ALL_COURSES;

        $course.empty();
        $course.append(new Option('Select course','', true, true)).prop('required', true).find('option:first').prop('disabled', true);
        list.forEach(c => $course.append(new Option(c, c)));

        if (current && list.includes(current)) $course.val(current);
      }

      $dept.off('change._dc').on('change._dc', ()=>rebuildCourses(false));
      rebuildCourses(true);
    }

    $(function(){
      const now=new Date(),p=n=>String(n).padStart(2,'0');
      $(".pc_date").val(`${p(now.getMonth()+1)}/${p(now.getDate())}/${now.getFullYear()}`);
      $(".pc_time").val(`${p(now.getHours())}:${p(now.getMinutes())}:${p(now.getSeconds())}`);

      setupBlock($('.voter-block').first());

      $('#addVoterBtn').on('click', function(){
        const $clone = $('.voter-block').first().clone(true, false);
        $clone.find('input').val('');
        $clone.find('select').each(function(){
          this.value = '';
          this.selectedIndex = 0; // land on placeholder for required selects
        });

        const next = $('#voterContainer .voter-block').length + 1;
        $clone.attr('data-index', next).find('.voter-num').text(next);
        $clone.find('.remove-btn').show();

        $('#voterContainer').append($clone);
        setupBlock($clone);
        $('.voter-block .remove-btn').toggle($('#voterContainer .voter-block').length>1);
      });

      $('#voterContainer').on('click','.remove-btn', function(){
        $(this).closest('.voter-block').remove();
        $('#voterContainer .voter-block').each(function(i){
          $(this).attr('data-index', i+1).find('.voter-num').text(i+1);
        });
        $('.voter-block .remove-btn').toggle($('#voterContainer .voter-block').length>1);
      });
      $('.voter-block .remove-btn').toggle($('#voterContainer .voter-block').length>1);

      // Required check now includes Department, Course, and Campus
      $('#save_voters_form').on('submit', function(e){
        e.preventDefault();
        const $btn = $('#save_voter').prop('disabled', true);

        let ok=true;
        $('#voterContainer .voter-block').each(function(){
          const req = $(this).find(
            '[name="FirstName[]"],[name="LastName[]"],[name="Year[]"],[name="Department[]"],[name="Course[]"],[name="Campus[]"],[name="UserName[]"],[name="SchoolID[]"],[name="Password[]"]'
          );
          for (const el of req) {
            if (!el.value.trim()) { el.focus(); ok=false; return false; }
          }
          if (!ok) return false;
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
              setTimeout(()=> location.href='voters.php', 1200);
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
          complete: function(){ $btn.prop('disabled', false); }
        });
      });
    });
  </script>
</body>
</html>
