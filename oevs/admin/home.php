<?php
include('session.php');
include('dbcon.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Home - Online Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --bg-color: #f4f6f8;
      --primary-color: #002f6c; /* used in main content text color */
      --font: 'Inter', sans-serif;
    }
    body { font-family: var(--font); background: var(--bg-color); margin: 0; }

    .main-content { text-align: center; padding: 60px 20px; }
    .main-content img { max-width: 200px; }
    .main-content p { font-size: 20px; font-weight: 600; color: var(--primary-color); }

    footer { text-align: center; padding: 20px 0; color: #666; font-size: 14px; }
  </style>
</head>
<body>
  <?php
    $activePage = 'home'; // underline Home without filling it
    include 'header.php';
  ?>

  <div class="main-content">
    <img src="images/au.png" alt="Araullo University Logo">
    <p>Araullo University</p>
  </div>

  <footer>
    © 2025 Online Election Voting System
  </footer>
</body>
</html>
