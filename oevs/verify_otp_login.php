<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>OTP Verification</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
  <style>
    /* Heading for OTP section */
    #otp-verification h1 {
      font-size: 24px;
      margin-bottom: 20px;
      text-align: center;
    }

    /* Input styling */
    input[type="text"] {
      width: 100%;
      padding: 12px 15px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-bottom: 20px;
      box-sizing: border-box;
      transition: border-color 0.3s;
    }

    input[type="text"]:focus {
      border-color: #002f6c;
      outline: none;
    }

    /* Button styling */
    button[type="submit"] {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      color: #fff;
      background-color: #002f6c;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button[type="submit"]:hover {
      background-color: #002f6c;
    }

    button i {
      margin-right: 8px;
    }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="left-side">
    <img src="pic/au.png" alt="AU Logo" class="au-logo">
    <h2>Araullo University</h2>
    <p>Maharlika Highway, Brgy. Bitas,<br>Cabanatuan City, 3100 Nueva Ecija, Philippines</p>
  </div>

  <div class="right-side">
    <div class="container" id="otp-verification">
      <div class="logo1-container" style="text-align: center; margin-bottom: 10px;">
        <img src="pic/phinma.png" alt="Logo" class="logo" style="width: 300px; height: 100px; margin: 0 auto;">
      </div>

      <h1>Enter OEVS OTP sent to your email</h1>

      <!-- ✅ Display OTP error if any -->
      <?php if (!empty($_SESSION['otp_error'])): ?>
        <p style="color:red; text-align:center;">
          <?php
            echo $_SESSION['otp_error'];
            unset($_SESSION['otp_error']);
          ?>
        </p>
      <?php endif; ?>

      <!-- ✅ Display the email (username) OTP was sent to -->
      <?php if (!empty($_SESSION['login_username'])): ?>
        <p style="text-align:center; font-size:16px; margin-bottom: 10px;">
          OTP sent to: <strong><?php echo htmlspecialchars($_SESSION['login_username']); ?></strong>
        </p>
      <?php endif; ?>

      <!-- ✅ OTP input form -->
      <form method="POST" action="user_account.php" autocomplete="off">
        <input type="text" name="otp" maxlength="6" placeholder="Enter OTP" required autofocus>
        <button type="submit" name="verify_otp"><i class="fas fa-check-circle"></i> Verify OTP</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
