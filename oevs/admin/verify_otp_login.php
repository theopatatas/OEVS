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
    /* Additional styling for OTP verification form */

    body {
         /* Background Image */
    background: url('pic/1.jpg') no-repeat center center fixed; 
    background-size: cover;

    /* Black Overlay */
    position: relative;
    }

    #otp-verification {
      max-width: 400px;
      margin: 50px auto;
      background: #fff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      text-align: center;
    }

    .logo1-container img.logo {
      margin-bottom: 20px;
      border-radius: 50%;
      box-shadow: 0 0 10px rgba(25, 111, 56, 0.3);
    }

    h1 {
      font-size: 1.6rem;
      color: #196F38;
      margin-bottom: 25px;
      font-weight: 600;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    input[type="text"] {
      padding: 12px 15px;
      font-size: 1rem;
      border: 2px solid #196F38;
      border-radius: 6px;
      outline: none;
      transition: border-color 0.3s ease;
    }

    input[type="text"]:focus {
      border-color: #0e3d1a;
      box-shadow: 0 0 5px #196F38;
    }

    button[name="verify_otp"] {
      background-color: #196F38;
      color: white;
      border: none;
      padding: 12px;
      font-size: 1.1rem;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button[name="verify_otp"]:hover {
      background-color: #0e3d1a;
    }

    /* Error message styling */
    p[style*="color:red"] {
      background-color: #ffe6e6;
      color: #cc0000;
      padding: 10px 15px;
      border-radius: 6px;
      margin-bottom: 20px;
      font-weight: 600;
      box-shadow: 0 0 5px rgba(204, 0, 0, 0.2);
    }
  </style>
</head>
<body>

  <div class="container" id="otp-verification">
    <div class="logo1-container">
      <img src="pic/au.png" alt="Logo" class="logo" style="width: 180px; height: 180px; display: block; margin: 0 auto;">
    </div>

    <h1>Enter OEVS OTP sent to your email</h1>

    <?php if (!empty($_SESSION['otp_error'])): ?>
      <p style="color:red;">
        <?php
          echo $_SESSION['otp_error'];
          unset($_SESSION['otp_error']);
        ?>
      </p>
    <?php endif; ?>

    <form method="POST" action="user_account.php" autocomplete="off">
      <input type="text" name="otp" maxlength="6" placeholder="Enter OTP" required autofocus>
      <button type="submit" name="verify_otp"><i class="fas fa-check-circle"></i> Verify OTP</button>
    </form>
  </div>

</body>
</html>
