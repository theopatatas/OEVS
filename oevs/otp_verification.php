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
  <title>Register - OTP Verification</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body>
  <div class="container" id="signup">
      <div class="logo1-container">
      <div style="text-align: center; margin-bottom: 20px;">
        <img src="pic/au.png" alt="Logo" class="logo" style="width: 180px; height: 180px; display: block; margin: 0 auto;">
      </div>
    </div>
    <h1 class="form-title">OEVS Register - Enter Email for OTP</h1>

    <?php if ($success): ?>
      <div class="success-main"><p><?= htmlspecialchars($success) ?></p></div>
    <?php endif; ?>

    <?php if (isset($errors['email'])): ?>
      <div class="error-main"><p><?= htmlspecialchars($errors['email']) ?></p></div>
    <?php endif; ?>

    <form method="POST" action="otp_code.php">
      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="username" placeholder="Enter your AU Email" required>
      </div>
      <input type="submit" class="btn" value="Send OTP" name="send_otp">
    </form>

    <div class="links">
      <p>Already Have Account?</p>
      <a href="index.php" style="color: #196F38; font-weight: bold;">Sign In</a>
    </div>
  </div>
</body>
</html>

<?php
unset($_SESSION['errors'], $_SESSION['success']);
?>
