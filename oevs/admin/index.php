<?php
session_start();
include('dbcon.php');

$errors = [];

if (isset($_POST['Login'])) {
    // Sanitize inputs
    $UserName = mysqli_real_escape_string($conn, $_POST['UserName']);
    $Password = mysqli_real_escape_string($conn, $_POST['Password']);

    // Check user and position = Admin
    $login_query = mysqli_query($conn, "SELECT * FROM users WHERE UserName='$UserName' AND Password='$Password' AND Position='Admin'");

    if ($login_query && mysqli_num_rows($login_query) > 0) {
        $row = mysqli_fetch_assoc($login_query);
        $f = $row['FirstName'];
        $l = $row['LastName'];

        $_SESSION['id'] = $row['User_id'];
        $_SESSION['User_Type'] = $row['User_Type'];
        $type = $row['User_Type'];

        mysqli_query($conn, "INSERT INTO history (data, action, date, user) VALUES ('$f $l', 'Login', NOW(), '$type')") or die(mysqli_error($conn));

        header("Location: home.php");
        exit;
    } else {
        $errors['login'] = "Invalid username, password, or you do not have admin access.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Login | OEVS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family:'Segoe UI',sans-serif;
      background:#f4f6f9;
      height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
    }
    .wrapper {
      display:flex;
      max-width:1000px;
      width:100%;
      background:#fff;
      border-radius:15px;
      overflow:hidden;
      box-shadow:0 20px 40px rgba(0,0,0,0.05);
    }

    /* Left panel with login gradient */
    .left-side {
      flex:1;
      background: linear-gradient(135deg,#2a0944 0%,#6a1b4d 35%,#b13a28 70%,#f0a500 100%);
      color:#fff;
      text-align:center;
      padding:50px 30px;
      display:flex;
      flex-direction:column;
      justify-content:center;
      align-items:center;
      position:relative;
      overflow:hidden;
    }
    .left-side::before {
      content:"";
      position:absolute;
      inset:0;
      background:
        radial-gradient(circle at top left, rgba(255,255,255,.06), transparent 70%),
        radial-gradient(circle at bottom right, rgba(0,0,0,.2), transparent 70%);
    }
    .left-side > * { position:relative; z-index:1; }

    .left-side img { width:120px; margin-bottom:20px; }
    .left-side h2 { font-size:24px; margin-bottom:10px; }
    .left-side p { font-size:14px; line-height:1.6; }

    .right-side {
      flex:1;
      padding:50px 40px;
      display:flex;
      flex-direction:column;
      justify-content:center;
    }

    .right-side .logo {
      width:180px;
      display:block;
      margin:0 auto 30px;
    }
    .form-title {
      font-size:22px;
      text-align:center;
      margin-bottom:25px;
      color:#002f6c;
      letter-spacing:1px;
    }

    .input-group {
      position:relative;
      margin-bottom:15px;
    }
    .input-group input {
      width:100%;
      padding:10px 40px 10px 40px;
      font-size:16px;
      border-radius:10px;
      border:1px solid #ccc;
      outline:none;
    }
    .input-group i.fas.fa-user,
    .input-group i.fas.fa-lock,
    .input-group i.fas.fa-id-card {
      position:absolute;
      left:12px;
      top:50%;
      transform:translateY(-50%);
      color:#888;
    }
    .input-group.password .fa-eye,
    .input-group.password .fa-eye-slash {
      position:absolute;
      right:12px;
      top:50%;
      transform:translateY(-50%);
      cursor:pointer;
      color:#888;
    }

    .btn {
      width:100%;
      background:#002f6c;
      color:#fff;
      border:none;
      padding:12px;
      font-size:15px;
      border-radius:8px;
      cursor:pointer;
      transition:background 0.3s;
    }
    .btn:hover { background:#001d44; }

    .recover {
      text-align:right;
      margin-top:10px;
    }
    .recover a {
      font-size:13px;
      color:#002f6c;
      text-decoration:none;
      font-weight:500;
    }
    .recover a:hover { text-decoration:underline; }

    .error-result {
      background-color:#721c24;
      color:#fff;
      border:1px solid #721c24;
      padding:15px 20px;
      margin-bottom:15px;
      border-radius:6px;
      font-size:14px;
      box-shadow:0 2px 4px rgba(0,0,0,0.1);
    }

    @media(max-width:768px) {
      .wrapper { flex-direction:column; border-radius:0; box-shadow:none; height:100vh; }
      .left-side, .right-side { padding:30px; width:100%; }
      .left-side { border-radius:0 0 15px 15px; }
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
    <h1 class="form-title">Administrator Login</h1>

    <?php if(!empty($errors['login'])): ?>
      <div class="error-result"><?= htmlspecialchars($errors['login']) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="UserName" id="username" placeholder="Enter Username" required />
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
function togglePassword(){
  const passwordField = document.getElementById("password");
  const eyeIcon = document.getElementById("eye");
  if(passwordField.type === "password"){
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
