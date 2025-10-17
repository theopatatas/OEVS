<?php
session_start();
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Recover Password</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f9;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .wrapper {
      display: flex;
      max-width: 1000px;
      width: 100%;
      background: #fff;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(0,0,0,0.05);
    }

    /* Left panel with login gradient */
    .left-side {
      flex: 1;
      background: linear-gradient(135deg,#2a0944 0%,#6a1b4d 35%,#b13a28 70%,#f0a500 100%);
      color: #fff;
      text-align: center;
      padding: 50px 30px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      position: relative;
      overflow: hidden;
    }
    .left-side::before {
      content: "";
      position: absolute;
      inset: 0;
      background:
        radial-gradient(circle at top left, rgba(255,255,255,.06), transparent 70%),
        radial-gradient(circle at bottom right, rgba(0,0,0,.2), transparent 70%);
    }
    .left-side > * { position: relative; z-index: 1; }

    .left-side img { width: 120px; margin-bottom: 20px; }
    .left-side h2 { font-size: 24px; margin-bottom: 10px; }
    .left-side p { font-size: 14px; line-height: 1.6; }

    .right-side {
      flex: 1;
      padding: 50px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .right-side .logo { width: 180px; display: block; margin: 0 auto 30px; }
    .form-title { font-size: 22px; text-align: center; margin-bottom: 25px; color: #002f6c; letter-spacing: 1px; }

    .input-group { position: relative; margin-bottom: 15px; }
    .input-group input { width: 100%; padding: 10px 40px 10px 40px; font-size: 16px; border-radius: 10px; border: 1px solid #ccc; outline: none; }
    .input-group i.fas { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #999; pointer-events: none; }
    .input-group.password .fa-eye,
    .input-group.password .fa-eye-slash { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #999; }

    .btn { width: 100%; background: #002f6c; color: #fff; border: none; padding: 12px; font-size: 15px; border-radius: 8px; cursor: pointer; transition: background 0.3s; }
    .btn:hover { background: #001d44; }

    .recover { text-align: right; margin-top: 10px; }
    .recover a { font-size: 13px; color: #002f6c; text-decoration: none; font-weight: 500; }
    .recover a:hover { text-decoration: underline; }

    .error-result, .success-main { padding: 15px 20px; margin-bottom: 15px; border-radius: 6px; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .error-result { background-color: #721c24; color: #fff; border: 1px solid #721c24; }
    .success-main { background-color: #002f6c; color: #fff; border: 1px solid #002f6c; }

    @media (max-width: 768px) {
      .wrapper { flex-direction: column; border-radius: 0; box-shadow: none; height: 100vh; }
      .left-side, .right-side { padding: 30px; width: 100%; }
    }
  </style>
</head>
<body>
<div class="wrapper">
  <!-- Left Side -->
  <div class="left-side">
    <img src="images/au.png" alt="AU Logo">
    <h2>Araullo University</h2>
    <p>Maharlika Highway, Brgy. Bitas,<br>Cabanatuan City, 3100 Nueva Ecija, Philippines</p>
  </div>

  <!-- Right Side -->
  <div class="right-side">
    <img src="images/phinma.png" alt="PHINMA Logo" class="logo">
    <h1 class="form-title">Administrator Recover Password</h1>

    <?php if ($success): ?>
      <div class="success-main"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (isset($errors['user_exist'])): ?>
      <div class="error-result"><?= htmlspecialchars($errors['user_exist']) ?></div>
    <?php endif; ?>

    <form method="POST" action="recover_account1.php">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="firstname" placeholder="First Name" required>
      </div>
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="lastname" placeholder="Last Name" required>
      </div>
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="username" placeholder="Username" required>
      </div>
      <div class="input-group password">
        <i class="fas fa-lock"></i>
        <input type="password" id="password" name="password" placeholder="New Password" required>
        <i class="fa fa-eye" id="togglePassword1"></i>
      </div>
      <div class="input-group password">
        <i class="fas fa-lock"></i>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
        <i class="fa fa-eye" id="togglePassword2"></i>
      </div>

      <p class="recover">
        <a href="index.php">Sign In</a>
      </p>

      <input type="submit" class="btn" value="Recover" name="signup">
    </form>
  </div>
</div>

<script>
  const togglePassword1 = document.getElementById("togglePassword1");
  const passwordField1 = document.getElementById("password");
  togglePassword1.addEventListener("click", function () {
    passwordField1.type = passwordField1.type === "password" ? "text" : "password";
    this.classList.toggle("fa-eye");
    this.classList.toggle("fa-eye-slash");
  });

  const togglePassword2 = document.getElementById("togglePassword2");
  const passwordField2 = document.getElementById("confirm_password");
  togglePassword2.addEventListener("click", function () {
    passwordField2.type = passwordField2.type === "password" ? "text" : "password";
    this.classList.toggle("fa-eye");
    this.classList.toggle("fa-eye-slash");
  });
</script>
</body>
</html>

<?php
unset($_SESSION['errors'], $_SESSION['success']);
session_write_close();
?>
