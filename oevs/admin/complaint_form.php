<?php
include('session.php');  // starts session and checks login
include('dbcon.php');
include('header.php');

if (!isset($_SESSION['id'])) {
    die("You must be logged in to submit a complaint.");
}

$voterID = $_SESSION['id']; // Your session id corresponds to VoterID in voters table

$query = "SELECT Username, SchoolID, Year FROM voters WHERE VoterID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $voterID);
$stmt->execute();
$result = $stmt->get_result();
$voter = $result->fetch_assoc();

if (!$voter) {
    die("Voter data not found.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Complaint Form</title>
<link rel="stylesheet" type="text/css" href="admin/css/style.css" />
<script src="jquery.iphone-switch.js" type="text/javascript"></script>
<style>
  /* Form container */
  .complaint-form {
    max-width: 600px;
    margin: 30px auto;
    background: #196F38;
    padding: 25px 30px;
    border-radius: 8px;
    box-shadow: 0 0 10px #202c61;
    color: white;
    font-family: Arial, sans-serif;
  }
  .complaint-form label {
    display: block;
    font-weight: bold;
    color: #fff;
  }
  .complaint-form input[type="text"],
  .complaint-form input[type="number"],
  .complaint-form textarea {
    width: 100%;
    padding: 10px 12px;
    border: none;
    border-radius: 5px;
    margin-bottom: 20px;
    font-size: 16px;
    font-family: Arial, sans-serif;
  }
  .complaint-form input[readonly] {
    background-color: #202c61;
    color: #fff;
  }
  .complaint-form textarea {
    resize: vertical;
  }
  .complaint-form button {
    background-color: #ffd400;
    color: #fff;
    padding: 12px 25px;
    font-size: 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  .complaint-form button:hover {
    background-color: #e6c200;
  }
</style>
</head>
<body>
<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand">
        <img src="admin/images/au.png" width="60" height="60" alt="Logo">
      </a>
    <a class="brand">
                <h2 style="color: white; transition: color 0.3s;" onmouseover="this.style.color='#ffd400'" onmouseout="this.style.color='white'">ONLINE ELECTION VOTING SYSTEM</h2>
                <div class="chmsc_nav" style="font-size: 18px; color: #fff; transition: color 0.3s; cursor: pointer;" onmouseover="this.style.color='#ffd400'" onmouseout="this.style.color='#fff'">Phinma Araullo University - South</div>
            </a>
      <?php include('head.php'); ?>
    </div>
  </div>
</div>

<div class="wrapper">
  <div class="hero-body-voting">
    <div class="vote_wise" style="color: white; font-size: 36px;" 
         onmouseover="this.style.color='#ffd400'" onmouseout="this.style.color='white'">
      Complaint Form
    </div>

    <div class="help">
      <a class="btn btn-info" id="help" href="voting.php">
        <i class="icon-info-sign icon-large"></i>&nbsp;Back
      </a>
    </div>
  </div>

  <hr>

  <div class="complaint-form">
      <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
    <div style="background-color: #202c61; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-weight: bold;">
      Your complaint has been submitted successfully!
    </div>
  <?php endif; ?>
    <form action="submit_complaint.php" method="POST" style="max-width:600px; margin:auto;">
      <div class="form-group">
        <label for="Username">AU Email</label>
        <input type="text" id="Username" name="Username" class="form-control" required
               value="<?php echo htmlspecialchars($voter['Username']); ?>" readonly>
      </div>

      <div class="form-group">
        <label for="SchoolID">AU School ID</label>
        <input type="text" id="SchoolID" name="SchoolID" class="form-control" required
               value="<?php echo htmlspecialchars($voter['SchoolID']); ?>" readonly>
      </div>

     <div class="form-group">
  <label for="Year">Year</label>
  <input type="text" id="Year" name="Year" class="form-control" required
         value="<?php echo htmlspecialchars($voter['Year']); ?>" readonly>
</div>


      <div class="form-group">
        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" class="form-control" required placeholder="Complaint subject">
      </div>

      <div class="form-group">
        <label for="description">Complaint Message</label>
        <textarea id="description" name="description" class="form-control" rows="5" required placeholder="Write your complaint here..."></textarea>
      </div>

      <button type="submit" class="btn btn-primary">Send</button>
    </form>
  </div>


<div class="foot">
  <?php include('footer1.php'); ?>
</div>

</body>
</html>
