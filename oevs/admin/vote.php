<?php
ob_start(); // Prevent output before headers
include('session.php');
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
    $votes = [];

    // Sanitize room input
    $room = mysqli_real_escape_string($conn, $_POST['room'] ?? '');

    // Get current date and time
    date_default_timezone_set('Asia/Manila'); // Set your timezone here
    $dateVoted = date('Y-m-d');
    $timeVoted = date('H:i:s');

    // Collect votes
    foreach ($positions as $pos) {
        $candidateID = $_POST[$pos] ?? '';
        if (!empty($candidateID)) {
            $votes[] = mysqli_real_escape_string($conn, $candidateID);
        }
    }

    $error = false;
    foreach ($votes as $candidateID) {
        if (!mysqli_query($conn, "INSERT INTO votes (CandidateID) VALUES ('$candidateID')")) {
            $error = true;
            break;
        }
    }

    if (!$error) {
        $updateQuery = "
            UPDATE voters 
            SET 
                Status='Voted', 
                Room='$room', 
                DateVoted='$dateVoted', 
                TimeVoted='$timeVoted' 
            WHERE VoterID='$session_id'
        ";

        if (mysqli_query($conn, $updateQuery)) {
            header("Location: thankyou.php");
            exit;
        }
    }

    echo "<script>alert('An error occurred while submitting your vote. Please try again.');</script>";
}

// Function to get candidate name
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
<body>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="brand">
                <img src="admin/images/au.png" width="60" height="60">
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
        <div class="vote_wise" onmouseover="this.style.color='#ffd400'" onmouseout="this.style.color='white'" style="color: white; font-size: 36px;">
            "Official Ballot Confirmation"
        </div>

        <div class="back">
            <a class="btn btn-info" href="voting.php"><i class="icon-arrow-left icon-large"></i>&nbsp;Back to Voting</a>
        </div>
    </div>

  <form method="POST">
    <div class="ballot" style="max-width:600px;margin:30px auto;padding:20px;border:2px solid #196F38;border-radius:10px;background-color:#f9f9f9;box-shadow:0 2px 8px #196F38;">
       
        <?php
        foreach ($positions as $pos) {
            echo '<div class="cent" style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #ddd;">';
            $display_name = ucwords(str_replace('_', ' ', $pos));
            echo '<p style="margin:0;font-weight:bold;color:#333;">' . $display_name . ':&nbsp;&nbsp;</p>';

            $candidateID = $_POST[$pos] ?? '';
            if (empty($candidateID)) {
                echo '<i style="color:#888;">No Candidate Selected</i>';
            } else {
                echo '<span style="color:#333;">' . getCandidateName($conn, $candidateID) . '</span>';
                echo '<input type="hidden" name="' . htmlspecialchars($pos) . '" value="' . htmlspecialchars($candidateID) . '" />';
            }
            echo '</div><br>';
        }
        ?>

        <!-- ✅ Room input goes here, inside the ballot -->
        <div class="cent" style="padding:10px 0;">
            <label for="room" style="font-weight:bold;color:#000;">Enter Your Room:</label><br>
            <input type="text" name="room" id="room" required style="width:100%;padding:8px;margin-top:5px;border:1px solid #ccc;border-radius:5px;">
        </div>

    </div>
     
<form method="POST">
    <!-- your ballot content here -->

    <div class="hero-body-456" style="display: flex; justify-content: center; margin-top: 20px;">
        <div class="ok_vote">
            <button class="btn btn-success" name="final_submit" type="submit" style="padding: 10px 20px; font-size: 16px;">
                <i class="icon-save icon-large"></i>&nbsp;Submit Final Votes
            </button>
        </div>
    </div>
</form>


    <?php include('footer1.php') ?>
</div>
</body>
</html>
