<?php
session_start();
include('dbcon.php');

$errors = [];

if (isset($_POST['Login'])) {
    $UserName = mysqli_real_escape_string($conn, $_POST['UserName']);
    $Password = mysqli_real_escape_string($conn, $_POST['Password']);

    $login_query = mysqli_query($conn, "SELECT * FROM users WHERE UserName='$UserName' AND Password='$Password'");

    if ($login_query && mysqli_num_rows($login_query) > 0) {
        $row = mysqli_fetch_assoc($login_query);
        $_SESSION['id'] = $row['User_id'];
        $_SESSION['User_Type'] = $row['User_Type'];
        $f = $row['FirstName'];
        $l = $row['LastName'];
        $type = $row['User_Type'];

        mysqli_query($conn, "INSERT INTO history (data, action, date, user) VALUES ('$f $l', 'Login', NOW(), '$type')") or die(mysqli_error($conn));
        header("Location: home.php");
        exit;
    } else {
        $errors['login'] = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login | OEVS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f9;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .wrapper {
      display: flex;
      width: 1000px;
      max-width: 100%;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
      background: #fff;
    }

    /* Left Panel with login gradient */
    .left-side {
      flex: 1;
      background: linear-gradient(135deg,#2a0944 0%,#6a1b4d 35%,#b13a28 70%,#f0a500 100%);
      color: #fff;
      padding: 50px 30px;
      text-align: center;
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
    .left-side p { font-size: 14px; line-height: 1.5; }

    .right-side {
      flex: 1;
      padding: 50px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .logo { display: block; margin: 0 auto 20px; width: 220px; }
    .form-title { font-size: 22px; text-align: center; margin-bottom: 25px; color: #002f6c; }

    .input-group { position: relative; margin-bottom: 15px; }
    .input-group input { width: 100%; padding: 10px 40px 10px 40px; font-size: 16px; border-radius: 10px; border: 1px solid #ccc; outline: none; }
    .input-group i.fas.fa-user,
    .input-group i.fas.fa-lock,
    .input-group i.fas.fa-id-card { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #888; }
    .input-group.password .fa-eye,
    .input-group.password .fa-eye-slash { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #888; }

    .btn { width: 100%; background: #002f6c; color: #fff; border: none; padding: 12px; font-size: 15px; border-radius: 8px; cursor: pointer; transition: background 0.3s; }
    .btn:hover { background: #001d44; }

    .recover { text-align: right; margin-top: 10px; }
    .recover a { font-size: 13px; color: #002f6c; font-weight: bold; text-decoration: none; }
    .recover a:hover { text-decoration: underline; }

    .error-main {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
      padding: 15px 20px;
      margin-bottom: 15px;
      border-radius: 5px;
      font-size: 14px;
    }

    @media (max-width: 768px) {
      .wrapper { flex-direction: column; height: auto; border-radius: 0; }
      .left-side, .right-side { padding: 30px; width: 100%; }
      .logo { width: 180px; }
    }
  </style>
</head>
<body>

<div class="wrapper">
  <!-- Left Panel -->
  <div class="left-side">
    <img src="images/au.png" alt="AU Logo">
    <h2>Araullo University</h2>
    <p>Maharlika Highway, Brgy. Bitas,<br>Cabanatuan City, 3100 Nueva Ecija, Philippines</p>
  </div>

  <!-- Right Panel -->
  <div class="right-side">
    <img src="images/phinma.png" alt="PHINMA Logo" class="logo">
    <h1 class="form-title">OEVS CSDL Election 2025</h1>

    <?php if (!empty($errors['login'])): ?>
      <div class="error-main"><?php echo htmlspecialchars($errors['login']); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="UserName" id="username" placeholder="Username" required />
      </div>

      <div class="input-group password">
        <i class="fas fa-lock"></i>
        <input type="password" name="Password" id="password" placeholder="Password" required />
        <i id="eye" class="fa fa-eye" onclick="togglePassword()"></i>
      </div>

      <p class="recover"><a href="recoverpassword.php">Recover Password</a></p>

      <input type="submit" name="Login" class="btn" value="Sign In" />
    </form>
  </div>
</div>

<script>
  function togglePassword() {
    const passwordField = document.getElementById("password");
    const eyeIcon = document.getElementById("eye");
    if (passwordField.type === "password") {
      passwordField.type = "text";
      eyeIcon.classList.remove("fa-eye");
      eyeIcon.classList.add("fa-eye-slash");
    } else {
      passwordField.type = "password";
      eyeIcon.classList.remove("fa-eye-slash");
      eyeIcon.classList.add("fa-eye");
    }
  }
</script>
</body>
</html>
