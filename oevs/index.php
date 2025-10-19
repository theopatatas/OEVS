<?php session_start();
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

/* Flags & prefills set by register_account.php / login.php */
$showRegister  = !empty($_SESSION['show_register']);
$regErr        = $_SESSION['register_error'] ?? null;
$prefill       = $_SESSION['prefill'] ?? [];
$prefill_login = $_SESSION['prefill_login'] ?? '';

/* Flashes */
$loginErr = $_SESSION['login_error'] ?? null;
$regOk    = $_SESSION['register_success'] ?? null;

/* Clear one-time flashes */
unset($_SESSION['show_register'], $_SESSION['register_error'], $_SESSION['prefill'],
      $_SESSION['prefill_login'], $_SESSION['login_error'], $_SESSION['register_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | OEVS Voting 2025</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; color: #333;
      min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
    .container { width: 90%; max-width: 1000px; background: #fff; display: flex; border-radius: 15px; overflow: hidden;
      box-shadow: 0 20px 40px rgba(0,0,0,.05); }
    .left { flex: 1; color:#fff; padding:40px; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;
      background: linear-gradient(135deg,#2a0944 0%,#6a1b4d 35%,#b13a28 70%,#f0a500 100%); position:relative; overflow:hidden; }
    .left::before{content:"";position:absolute;inset:0;background:
      radial-gradient(circle at top left, rgba(255,255,255,.06), transparent 70%),
      radial-gradient(circle at bottom right, rgba(0,0,0,.2), transparent 70%);}
    .left > * { position: relative; z-index: 1; }
    .left img{width:120px;margin-bottom:25px;}
    .left h2{font-size:24px;margin-bottom:10px;}
    .left p{font-size:14px;line-height:1.6;opacity:.9;}
    .right{flex:1;padding:50px 40px;background:#fff;display:flex;flex-direction:column;justify-content:center;}
    .right img{width:180px;display:block;margin:0 auto 30px;}
    .right h1{font-size:22px;color:#002f6c;text-align:center;margin-bottom:20px;letter-spacing:1px;}
    form{width:100%}
    .input-group{position:relative;margin-bottom:15px;}
    .input-group input,.input-group select{ width:100%; padding:10px 44px 10px 40px; font-size:16px; border-radius:10px; border:1px solid #ccc; outline:none; background:#fff;}
    .input-group input:focus,.input-group select:focus{border-color:#002f6c}
    .input-group .left-icon{position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#888; pointer-events:none;}
    .input-group .eye-btn{position:absolute; right:8px; top:50%; transform:translateY(-50%); border:none; background:transparent; cursor:pointer; padding:4px; line-height:0; color:#888;}
    .input-group .eye-btn:focus{outline:2px solid #cfe3ff; outline-offset:2px; border-radius:6px}
    .btn{width:100%; background:#002f6c; color:#fff; border:none; padding:12px; font-size:15px; border-radius:8px; cursor:pointer; transition:background .3s; margin-top:10px;}
    .btn:hover{background:#001d44;}
    .switch{text-align:center;margin-top:15px;}
    .switch a{color:#002f6c;font-weight:500;text-decoration:none;}
    .switch a:hover{text-decoration:underline;}
    .flash{ position:relative; display:flex; align-items:center; gap:10px; padding:10px 40px 10px 12px; border-radius:6px; margin-bottom:15px;
      transition: opacity .35s ease, transform .35s ease, max-height .35s ease, margin .35s ease, padding .35s ease; opacity:1; transform:translateY(0); max-height:200px; }
    .error-msg{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
    .success-msg{background:#ecfdf5;border:1px solid #bbf7d0;color:#065f46}
    .flash.hide{ opacity:0; transform:translateY(-6px); max-height:0; margin:0; padding-top:0; padding-bottom:0; }
    .flash-close{position:absolute; right:10px; top:50%; transform:translateY(-50%); border:none; background:transparent; cursor:pointer; font-size:20px; line-height:1; color:inherit;}
    .flash-close:focus{ outline:2px solid #cfe3ff; border-radius:6px; }
    @media (max-width:768px){.container{flex-direction:column}.left,.right{width:100%;padding:30px}.left{border-radius:15px 15px 0 0}.right{border-radius:0 0 15px 15px}}
  </style>
</head>
<body>

  <div class="container">
    <!-- Left -->
    <div class="left">
      <img src="pic/au.png" alt="Araullo University Logo">
      <h2>Araullo University</h2>
      <p>Maharlika Highway, Brgy. Bitas,<br>Cabanatuan City, 3100 Nueva Ecija,<br>Philippines</p>
    </div>

    <!-- Right -->
    <div class="right">
      <img src="pic/phinma.png" alt="PHINMA Logo">
      <h1>STUDENT ELECTION</h1>

      <!-- Login flashes -->
      <?php if (!empty($loginErr)): ?>
        <div class="error-msg flash" role="alert" aria-live="polite">
          <span><?= h($loginErr) ?></span>
          <button type="button" class="flash-close" aria-label="Dismiss">&times;</button>
        </div>
      <?php endif; ?>

      <?php if (!empty($regOk)): ?>
        <div class="success-msg flash" role="status" aria-live="polite">
          <span><?= h($regOk) ?></span>
          <button type="button" class="flash-close" aria-label="Dismiss">&times;</button>
        </div>
      <?php endif; ?>

      <!-- Login (SchoolID only) -->
      <form id="loginForm" action="login.php" method="POST" autocomplete="on"
            style="display: <?= $showRegister ? 'none' : 'block' ?>;">
        <div class="input-group">
          <i class="fas fa-id-card left-icon"></i>
          <input
            type="text"
            name="SchoolID"
            placeholder="Student Number (SchoolID only)"
            value="<?= h($prefill_login) ?>"
            required
            autocomplete="username"
            pattern="^[0-9-]+$"
            title="Use your student number (digits and dashes only)."
          >
        </div>
        <div class="input-group">
          <i class="fas fa-lock left-icon"></i>
          <input type="password" name="Password" id="password" placeholder="Password" required autocomplete="current-password">
          <button type="button" class="eye-btn" data-target="#password" aria-label="Show password">
            <i class="fas fa-eye"></i>
          </button>
        </div>
        <button type="submit" class="btn" name="Login">Login</button>

        <div style="text-align:right; margin-top:8px;">
          <a href="recoverpassword.php" style="font-size:14px; color:#002f6c; text-decoration:none;">Forgot Password?</a>
        </div>

        <div class="switch">
          <a href="register_account.php" id="showRegister" data-toggle="inline">Create an account</a>
        </div>
      </form>

      <!-- Register error -->
      <?php if (!empty($regErr)): ?>
        <div class="error-msg flash" role="alert" aria-live="polite"
             style="display: <?= $showRegister ? 'block' : 'none' ?>;">
          <span><?= h($regErr) ?></span>
          <button type="button" class="flash-close" aria-label="Dismiss">&times;</button>
        </div>
      <?php endif; ?>

      <!-- Register -->
      <form id="registerForm" action="register_account.php" method="POST"
            style="display: <?= $showRegister ? 'block' : 'none' ?>;">

        <div class="input-group">
          <i class="fas fa-id-card left-icon"></i>
          <input type="text" name="SchoolID" placeholder="Student Number"
                 value="<?= h($prefill['SchoolID'] ?? '') ?>" required
                 pattern="^[0-9-]+$" title="Use your student number (digits and dashes only).">
        </div>

        <div class="input-group">
          <i class="fas fa-user left-icon"></i>
          <input type="text" name="FullName" placeholder="Full Name"
                 value="<?= h($prefill['FullName'] ?? '') ?>" required>
        </div>

        <div class="input-group">
          <i class="fas fa-envelope left-icon"></i>
          <input type="email" name="Email" placeholder="School Email"
                 value="<?= h($prefill['Email'] ?? '') ?>" required>
        </div>

        <div class="input-group">
          <i class="fas fa-building left-icon"></i>
          <select name="Department" id="dept" required>
            <option value="">Select Department</option>
            <?php foreach (['CMA','CELA','CCJE','COE','CAHS','CIT','CAS'] as $opt): ?>
              <option value="<?= $opt ?>" <?= (($prefill['Department'] ?? '') === $opt) ? 'selected' : '' ?>><?= $opt ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="input-group">
          <i class="fas fa-location-dot left-icon"></i>
          <select name="Campus" required>
            <option value="">Select Campus</option>
            <?php foreach (['AU South','AU Main','AU San Jose'] as $opt): ?>
              <option value="<?= $opt ?>" <?= (($prefill['Campus'] ?? '') === $opt) ? 'selected' : '' ?>><?= $opt ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Year: 1st–4th -->
        <div class="input-group">
          <i class="fas fa-calendar-alt left-icon"></i>
          <select name="Year" required>
            <option value="">Select Year Level</option>
            <?php
              $years = ['1st Year','2nd Year','3rd Year','4th Year'];
              $selYear = $prefill['Year'] ?? '';
              foreach ($years as $y): ?>
                <option value="<?= $y ?>" <?= ($selYear === $y ? 'selected' : '') ?>><?= $y ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Course (auto filtered by Department; CAS = all courses) -->
        <div class="input-group">
          <i class="fas fa-graduation-cap left-icon"></i>
          <select name="Course" id="course" required>
            <option value="">Select Course</option>
          </select>
        </div>

        <div class="input-group">
          <i class="fas fa-lock left-icon"></i>
          <input type="password" name="Password" id="regPassword" placeholder="Create Password" minlength="8" required>
          <button type="button" class="eye-btn" data-target="#regPassword" aria-label="Show password">
            <i class="fas fa-eye"></i>
          </button>
        </div>

        <div class="input-group">
          <i class="fas fa-lock left-icon"></i>
          <input type="password" name="ConfirmPassword" id="regConfirm" placeholder="Confirm Password" minlength="8" required>
          <button type="button" class="eye-btn" data-target="#regConfirm" aria-label="Show password">
            <i class="fas fa-eye"></i>
          </button>
        </div>

        <button type="submit" class="btn" name="Register">Register</button>
        <div class="switch"><a href="#" id="showLogin">Back to login</a></div>
      </form>
    </div>
  </div>

  <script>
    const loginForm    = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const showRegister = document.getElementById('showRegister');
    const showLogin    = document.getElementById('showLogin');

    // Respect server flag on first paint
    (function(){
      const show = <?= $showRegister ? 'true' : 'false' ?>;
      if (show && loginForm && registerForm) {
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
      }
    })();

    // Create Account behavior (toggle inline unless user opens in new tab)
    showRegister?.addEventListener('click', (e) => {
      const inline = showRegister.dataset.toggle === 'inline' && registerForm;
      const metaOpen = e.ctrlKey || e.metaKey || e.shiftKey || e.button === 1;
      if (inline && !metaOpen) {
        e.preventDefault();
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
      }
    });

    // Back to login
    showLogin?.addEventListener('click', (e) => {
      e.preventDefault();
      registerForm.style.display = 'none';
      loginForm.style.display = 'block';
    });

    // Eye toggle (login + both register passwords)
    document.querySelectorAll('.eye-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const input = document.querySelector(btn.dataset.target);
        const icon  = btn.querySelector('i');
        if (!input) return;
        const showing = input.type === 'text';
        input.type = showing ? 'password' : 'text';
        icon.classList.toggle('fa-eye', showing);
        icon.classList.toggle('fa-eye-slash', !showing);
        btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
      });
    });

    // ---- Course filtering by Department (CAS = all courses) ----
    const deptEl   = document.getElementById('dept');
    const courseEl = document.getElementById('course');

    const COURSES_ALL = [
      'Bachelor of Science in Accountancy',
      'Bachelor of Science in Hospitality Management',
      'Bachelor of Science in Tourism Management',
      'Bachelor of Science in Entrepreneurship',
      'Bachelor of Science in Business Administration',
      'Bachelor of Science in Management Accounting',
      'Bachelor of Science in Accounting Information System',
      'Bachelor of Elementary Education',
      'Bachelor of Secondary Education',
      'Bachelor of Arts in Political Science',
      'Bachelor of Science in Criminology',
      'Bachelor of Science in Civil Engineering',
      'Bachelor of Science in Nursing',
      'Bachelor of Science in Pharmacy',
      'Bachelor of Science in Midwifery',
      'Bachelor of Science in Information Technology'
    ];

    const COURSE_MAP = {
      CMA: [
        'Bachelor of Science in Accountancy',
        'Bachelor of Science in Hospitality Management',
        'Bachelor of Science in Tourism Management',
        'Bachelor of Science in Entrepreneurship',
        'Bachelor of Science in Business Administration',
        'Bachelor of Science in Management Accounting',
        'Bachelor of Science in Accounting Information System'
      ],
      CELA: [
        'Bachelor of Elementary Education',
        'Bachelor of Secondary Education',
        'Bachelor of Arts in Political Science'
      ],
      CCJE: [
        'Bachelor of Science in Criminology'
      ],
      COE: [
        'Bachelor of Science in Civil Engineering'
      ],
      CAHS: [
        'Bachelor of Science in Nursing',
        'Bachelor of Science in Pharmacy',
        'Bachelor of Science in Midwifery'
      ],
      CIT: [
        'Bachelor of Science in Information Technology'
      ],
      CAS: COURSES_ALL // all courses
    };

    const PREF_DEPT   = <?= json_encode($prefill['Department'] ?? '') ?>;
    const PREF_COURSE = <?= json_encode($prefill['Course'] ?? '') ?>;

    function populateCourses(dept, preselect = '') {
      courseEl.innerHTML = '<option value="">Select Course</option>';
      const list = COURSE_MAP[dept] || [];
      list.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c;
        opt.textContent = c;
        if (preselect && preselect === c) opt.selected = true;
        courseEl.appendChild(opt);
      });
    }

    deptEl?.addEventListener('change', () => {
      populateCourses(deptEl.value, '');
    });

    (function initCourse(){
      const currentDept = deptEl ? (deptEl.value || PREF_DEPT) : '';
      const currentCourse = PREF_COURSE || '';
      if (currentDept) populateCourses(currentDept, currentCourse);
    })();

    // FLASH AUTO-HIDE (4s) + CLOSE
    (function(){
      const flashes = document.querySelectorAll('.flash');
      flashes.forEach(f => {
        const btn = f.querySelector('.flash-close');
        if (btn) btn.addEventListener('click', () => {
          f.classList.add('hide'); setTimeout(() => f.remove(), 400);
        });
        setTimeout(() => {
          f.classList.add('hide'); setTimeout(() => f.remove(), 400);
        }, 4000);
      });
    })();
  </script>
</body>
</html>
