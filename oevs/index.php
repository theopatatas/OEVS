<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | OEVS Voting 2025</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', sans-serif; background: #f4f6f9; color: #333;
      min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px;
    }
    .container {
      width: 90%; max-width: 1000px; background: #fff; display: flex; border-radius: 15px; overflow: hidden;
      box-shadow: 0 20px 40px rgba(0,0,0,.05);
    }
    .left {
      flex: 1; color:#fff; padding:40px; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;
      background: linear-gradient(135deg,#2a0944 0%,#6a1b4d 35%,#b13a28 70%,#f0a500 100%); position:relative; overflow:hidden;
    }
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
    form{width:100%;}

    /* Inputs */
    .input-group{position:relative;margin-bottom:15px;}
    .input-group input,.input-group select{
      width:100%;
      padding:10px 44px 10px 40px;
      font-size:16px; border-radius:10px; border:1px solid #ccc; outline:none; background:#fff;
    }
    .input-group input:focus{border-color:#002f6c}

    /* Left icons */
    .input-group .left-icon{
      position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#888; pointer-events:none;
    }

    /* Eye toggle button (right side) */
    .input-group .eye-btn{
      position:absolute; right:8px; top:50%; transform:translateY(-50%);
      border:none; background:transparent; cursor:pointer; padding:4px; line-height:0; color:#888;
    }
    .input-group .eye-btn:focus{outline:2px solid #cfe3ff; outline-offset:2px; border-radius:6px}

    .btn{
      width:100%; background:#002f6c; color:#fff; border:none; padding:12px; font-size:15px;
      border-radius:8px; cursor:pointer; transition:background .3s; margin-top:10px;
    }
    .btn:hover{background:#001d44;}
    .switch{text-align:center;margin-top:15px;}
    .switch a{color:#002f6c;font-weight:500;text-decoration:none;}
    .switch a:hover{text-decoration:underline;}

    /* Flash auto-hide + close */
    .flash{ position:relative; display:flex; align-items:center; gap:10px;
      padding:10px 40px 10px 12px; border-radius:6px; margin-bottom:15px;
      transition: opacity .35s ease, transform .35s ease, max-height .35s ease, margin .35s ease, padding .35s ease;
      opacity:1; transform:translateY(0); max-height:200px;
    }
    .flash.hide{ opacity:0; transform:translateY(-6px); max-height:0; margin:0; padding-top:0; padding-bottom:0; }
    .flash-close{
      position:absolute; right:10px; top:50%; transform:translateY(-50%);
      border:none; background:transparent; cursor:pointer; font-size:20px; line-height:1; color:inherit;
    }
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

      <?php if (!empty($_SESSION['login_error'])): ?>
        <div class="error-msg flash" role="alert" aria-live="polite">
          <span><?php echo $_SESSION['login_error']; ?></span>
          <button type="button" class="flash-close" aria-label="Dismiss">&times;</button>
        </div>
        <?php unset($_SESSION['login_error']); ?>
      <?php endif; ?>

      <?php if (!empty($_SESSION['register_success'])): ?>
        <div class="success-msg flash" role="status" aria-live="polite">
          <span><?php echo $_SESSION['register_success']; ?></span>
          <button type="button" class="flash-close" aria-label="Dismiss">&times;</button>
        </div>
        <?php unset($_SESSION['register_success']); ?>
      <?php endif; ?>

      <!-- Login -->
      <form id="loginForm" action="user_account.php" method="POST">
        <div class="input-group">
          <i class="fas fa-id-card left-icon"></i>
          <input type="text" name="SchoolID" placeholder="Enter Username" required>
        </div>
        <div class="input-group">
          <i class="fas fa-lock left-icon"></i>
          <input type="password" name="Password" id="password" placeholder="Password" required>
          <button type="button" class="eye-btn" data-target="#password" aria-label="Show password">
            <i class="fas fa-eye"></i>
          </button>
        </div>
        <button type="submit" class="btn" name="Login">Login</button>

        <!-- Forgot password -->
        <div style="text-align:right; margin-top:8px;">
          <a href="recoverpassword.php" style="font-size:14px; color:#002f6c; text-decoration:none;">Forgot Password?</a>
        </div>

        <div class="switch"><a href="#" id="showRegister">Create an account</a></div>
      </form>

      <!-- Register -->
      <form id="registerForm" action="register_account.php" method="POST" style="display:none;">
        <div class="input-group">
          <i class="fas fa-id-card left-icon"></i>
          <input type="text" name="SchoolID" placeholder="School ID / Username" required>
        </div>

        <div class="input-group">
          <i class="fas fa-user left-icon"></i>
          <input type="text" name="FullName" placeholder="Full Name" required>
        </div>

        <div class="input-group">
          <i class="fas fa-envelope left-icon"></i>
          <input type="email" name="Email" placeholder="School Email" required>
        </div>

        <div class="input-group">
          <i class="fas fa-building left-icon"></i>
          <select name="Department" required>
            <option value="">Select Department</option>
            <option value="CMA">CMA</option>
            <option value="CIT">CIT</option>
            <option value="COE">COE</option>
            <option value="CCJE">CCJE</option>
            <option value="CASH">CASH</option>
          </select>
        </div>

        <div class="input-group">
          <i class="fas fa-location-dot left-icon"></i>
          <select name="Campus" required>
            <option value="">Select Campus</option>
            <option value="AU South">AU South</option>
            <option value="AU Main">AU Main</option>
            <option value="AU San Jose">AU San Jose</option>
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
    // switch forms
    const showRegister = document.getElementById('showRegister');
    const showLogin = document.getElementById('showLogin');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    showRegister?.addEventListener('click', e => { e.preventDefault(); loginForm.style.display='none'; registerForm.style.display='block'; });
    showLogin?.addEventListener('click', e => { e.preventDefault(); registerForm.style.display='none'; loginForm.style.display='block'; });

    // Eye toggle (works for login + both register passwords)
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

    // confirm password
    const regPassword = document.getElementById('regPassword');
    const regConfirm = document.getElementById('regConfirm');
    registerForm.addEventListener('submit', e => {
      if (regPassword && regConfirm && regPassword.value !== regConfirm.value) {
        e.preventDefault();
        alert('Passwords do not match.');
        regConfirm.focus();
      }
    });

    // ==== FLASH AUTO-HIDE (4s) + CLOSE ====
    (function(){
      const flashes = document.querySelectorAll('.flash');
      flashes.forEach(f => {
        // close button
        const btn = f.querySelector('.flash-close');
        if (btn) btn.addEventListener('click', () => {
          f.classList.add('hide');
          setTimeout(() => f.remove(), 400);
        });
        // auto hide after 4s
        setTimeout(() => {
          f.classList.add('hide');
          setTimeout(() => f.remove(), 400);
        }, 4000);
      });
    })();
  </script>
</body>
</html>
