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
  <title>Register</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<style>
    .success-main {
  background-color: #d4edda;      /* Light green background */
  color: #196F38;                 /* Dark green text */
  border: 1px solid #c3e6cb;     /* Green border */
  padding: 15px 20px;             /* Some padding */
  margin: 15px 0;                 /* Space above and below */
  border-radius: 5px;             /* Rounded corners */
  font-family: Arial, sans-serif;
  font-size: 16px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

    </style>
<body>
  <div class="container" id="signup">
    <div class="logo1-container">
      <div style="text-align: center; margin-bottom: 20px;">
        <img src="pic/au.png" alt="Logo" class="logo" style="width: 180px; height: 180px; display: block; margin: 0 auto;">
      </div>
    </div>

    <h1 class="form-title">OEVS Register</h1>

    <?php if ($success): ?>
      <div class="success-main"><p><?= $success ?></p></div>
    <?php endif; ?>

    <?php if (isset($errors['user_exist'])): ?>
      <div class="error-main"><p><?= $errors['user_exist'] ?></p></div>
    <?php endif; ?>

    <form method="POST" action="register_account.php">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="firstname" placeholder="First Name" required>
      </div>

      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="middlename" placeholder="Middle Name" required>
      </div>

      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="lastname" placeholder="Last Name" required>
      </div>

      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="username" placeholder="AU Email" required>
      </div>


      <div class="input-group">
        <i class="fas fa-id-card"></i>
        <input type="text" name="schoolid" placeholder="AU School ID" required>
      </div>

      <div class="input-group">
  <i class="fas fa-calendar"></i>
  <select name="year" required>
    <option value="" disabled selected>Select Year</option>
    <option value="1st Year">1st Year</option>
    <option value="2nd Year">2nd Year</option>
    <option value="3rd Year">3rd Year</option>
    <option value="4th Year">4th Year</option>
  </select>
</div>


      <div class="input-group password">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required>
        <i id="eye" class="fa fa-eye"></i>
      </div>

      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      </div>

      <input type="submit" class="btn" value="Sign Up" name="signup">
    </form>

    <div class="links">
      <p>Already Have Account?</p>
      <a href="index.php" style="color: #196F38; font-weight: bold;">Sign In</a>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>

<?php
unset($_SESSION['errors'], $_SESSION['success']);
?>
