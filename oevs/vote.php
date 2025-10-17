<?php
ob_start();
session_start();
include('dbcon.php');
include('header.php');


$positions = [
    'president',
    'vice_president',
    'governor',
    'vice_governor',
    'secretary',
    'treasurer',
    'social_media_officer',
    'representative'
];

if (isset($_POST['final_submit'])) {
    $session_id = $_SESSION['id'];
    $votes = [];
    $room = mysqli_real_escape_string($conn, $_POST['room'] ?? '');
    date_default_timezone_set('Asia/Manila');
    $dateVoted = date('Y-m-d');
    $timeVoted = date('H:i:s');

    $error = false;
    foreach ($positions as $pos) {
        $candidateID = $_POST[$pos] ?? '';
        if (!empty($candidateID)) {
            $candidateID = mysqli_real_escape_string($conn, $candidateID);
            if (!mysqli_query($conn, "INSERT INTO votes (CandidateID) VALUES ('$candidateID')")) {
                $error = true;
                break;
            }
        }
    }

    if (!$error) {
        $updateQuery = "
            UPDATE voters 
            SET Status='Voted', Room='$room', DateVoted='$dateVoted', TimeVoted='$timeVoted' 
            WHERE VoterID='$session_id'
        ";

        if (mysqli_query($conn, $updateQuery)) {
            header("Location: thankyou.php");
            exit;
        }
    }

    echo "<script>alert('An error occurred while submitting your vote. Please try again.'); window.history.back();</script>";
}

function getCandidateName($conn, $candidateID) {
    $candidateID = mysqli_real_escape_string($conn, $candidateID);
    $result = mysqli_query($conn, "SELECT FirstName, LastName FROM candidate WHERE CandidateID='$candidateID'");
    if ($row = mysqli_fetch_assoc($result)) {
        return htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']);
    }
    return 'Unknown Candidate';
}
?>

<link rel="stylesheet" type="text/css" href="admin/css/style.css" />
<script src="jquery.iphone-switch.js" type="text/javascript"></script>
</head>
<style>
/* Ballot Heading */
h2 {
  text-align: center;
  color: #002f6c;
  font-weight: bold;
  margin-bottom: 30px;
}

/* Ballot container */
.ballot1 {
  max-width: 700px;  /* use the larger default you had */
  margin: 30px auto;
  padding: 20px;
  border: 1px solid #000;
  border-radius: 10px;
  background-color: #f9f9f9;
  box-shadow: 0 2px 8px #000;
  box-sizing: border-box;
}

/* Grid */
.grid1 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

/* Candidate card */
.grid1 > div {
  padding: 15px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background: #fff;
}

/* Room input */
.cent1 {
  margin-top: 20px;
}

.cent1 label {
  font-weight: bold;
  color: #000;
}

.cent1 input[type="text"] {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
  box-sizing: border-box;
}

/* Submit button */
.btn-success {
  background-color: #002f6c;
  border: none;
  padding: 12px 30px;
  font-size: 18px;
  font-weight: bold;
  color: #fff;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-success:hover {
  background-color: #000;
}

/* ✅ Mobile Responsive */
@media (max-width: 600px) {
     .ballot1-heading {
    text-align: center; /* redundant but ensures no overrides */
    font-size: 24px;    /* smaller for mobile, optional */
    margin-bottom: 20px;
    display: block;     /* ensures block-level centering */
    width: 40%;        /* ensures full width to center text */
  }
  .grid1 {
    grid-template-columns: 1fr; /* stack vertically */
  }

  .ballot1 {
    max-width: 38%;   /* ✅ this is correct — wide enough for mobile */
    padding: 15px 20px;
    margin: 20px ; /* center it horizontally */
  }

  h2 {
    font-size: 20px;
    text-align: center;
  }

  .btn-success {
    width: 100%;  /* full width for easy tapping */
    font-size: 10px;
  }
}

</style>

<body>
<?php include('nav_top.php'); ?>
<div class="wrapper">
<div class="home_body">
<?php include('homesidebar.php'); ?>
 <hr class="footer-line1">

 <h2 class="ballot1-heading">BALLOT CONFIRMATION</h2>

<form method="POST">
    <div class="ballot1">

        <div class="grid1">
            <?php
            foreach ($positions as $index => $pos) {
                $display_name = ucwords(str_replace('_', ' ', $pos));
                $candidateID = $_POST[$pos] ?? '';

                echo '<div style="padding: 15px; border: 1px solid #ccc; border-radius: 8px; background: #fff;">';
                echo '<p style="margin: 0 0 5px; font-weight: bold; color: #002f6c;">' . $display_name . '</p>';

                if (empty($candidateID)) {
                    echo '<i style="color: #888;">No Candidate Selected</i>';
                } else {
                    echo '<p style="color: #000; margin: 0;">' . getCandidateName($conn, $candidateID) . '</p>';
                    echo '<input type="hidden" name="' . htmlspecialchars($pos) . '" value="' . htmlspecialchars($candidateID) . '" />';
                }

                echo '</div>';
            }
            ?>
        </div>

        <!-- Room Input -->


        <!-- Submit Button -->
        <div style="text-align: center; margin-top: 30px;">
    <button 
        name="final_submit" 
        type="submit" 
        class="btn btn-success"
        style="
            background-color: #002f6c;
            border: none;
            padding: 12px 30px;
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: background-color 0.3s ease;
        "
        onmouseover="this.style.backgroundColor='#002f6c';"
        onmouseout="this.style.backgroundColor='#002f6c';"
    >
        <i class="icon-save icon-large"></i>&nbsp; Submit Final Votes
    </button>
</div>
        </div>
    </div>
</form>

<div class="foot" style="margin-top: 40px;">
    <?php include('footer1.php'); ?>
</div>
</body>
</html>
<?php ob_end_flush(); ?>
