<?php 
include('session.php');
include('dbcon.php');
include('header.php');

$session_id = $_SESSION['id'];

// Get voter status from DB
$voter_query = mysqli_query($conn, "SELECT Status FROM voters WHERE VoterID='$session_id'") or die(mysqli_error($conn));
$voter_row = mysqli_fetch_assoc($voter_query);
$voter_status = $voter_row['Status'];

// 30 seconds time limit
// 30 mins time limit
$time_limit = 1800;


// Track last status in session
if (!isset($_SESSION['last_status'])) {
    $_SESSION['last_status'] = $voter_status;
}

// Reset timer ONLY if status changed from "Time limit is over" to "Unvoted"
if ($voter_status == 'Unvoted' && $_SESSION['last_status'] == 'Time limit is over') {
    $_SESSION['vote_start_time'] = time();
}

// If not set, initialize timer
if (!isset($_SESSION['vote_start_time'])) {
    $_SESSION['vote_start_time'] = time();
}

$elapsed_time = time() - $_SESSION['vote_start_time'];

// Update last_status tracker
$_SESSION['last_status'] = $voter_status;

// Check if time limit is over but only if still unvoted
if ($elapsed_time > $time_limit && $voter_status == 'Unvoted') {
    mysqli_query($conn, "UPDATE voters SET Status='Time limit is over' WHERE VoterID='$session_id'") or die(mysqli_error($conn));
    $voter_status = 'Time limit is over';
    $_SESSION['last_status'] = 'Time limit is over';
}

// If already expired in DB, show expired message + logout button
if ($voter_status == 'Time limit is over') {
    echo "<div style='text-align:center; margin-top:50px; font-size:20px; color:red;'>
            <strong>⏳ Your voting time has expired. You cannot vote anymore.</strong>
            <br><br>
            <a href='logout.php' class='btn btn-danger' style='padding:10px 20px; font-size:16px;'>
                <i class='icon-signout'></i> Logout
            </a>
          </div>";
    include('footer1.php');
    exit();
}
?>



<link rel="stylesheet" type="text/css" href="admin/css/style.css" />

<style>
.wrapper {
    max-width: 1500px;
    margin: auto;
}
.position-title {
    font-size: 20px;
    margin: 20px 0 10px 0;
    color: white;
    background: #002f6c;
    padding: 10px 20px;
    border-radius: 5px;
    display: inline-block;
    text-align: center;
    position: relative;
}
.position-header {
    text-align: center;
    margin-bottom: 20px;
    position: relative;
}
.position-header::after {
    content: "";
    display: block;
    height: 2px;
    width: 100%;
    background-color: #ccc;
    position: absolute;
    bottom: -10px;
    left: 0;
}
.vote-box1 {
    background-color: #ffffff;
    padding: 30px;
    max-width: 1100px;
    margin: auto;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
}
.carousel-container {
    position: relative;
    overflow-x: hidden;
    width: 100%;
    max-width: 100%;
}
.carousel-inner {
    display: flex;
    transition: transform 0.3s ease-in-out;
    gap: 20px;
    padding: 10px;
    min-width: 100%;
}
.candidate-card {
    flex: 0 0 260px;
    width: 300px;
    text-align: center;
    background: #fff;
    border-radius: 8px;
    padding: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    color: black;
    transition: transform 0.3s ease;
}
.candidate-card:hover {
    transform: scale(1.03);
}
.candidate-photo {
    width: 60%;
    height: 180px;
    border-radius: 10px;
}
.carousel-controls {
    margin: 10px 0;
    text-align: center;
}
.carousel-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: #002f6c;
    color: white;
    border: none;
    padding: 10px;
    z-index: 1;
    cursor: pointer;
    border-radius: 50%;
}
.carousel-arrow.left { left: 0; }
.carousel-arrow.right { right: 0; }

/* Mobile */
@media screen and (max-width: 768px) {
  .vote-box1 { max-width: 40%; margin: 10px 0; padding: 10px; }
  .carousel-container { overflow: hidden; position: relative; }
  .carousel-inner { display: flex; transition: transform 0.3s ease; scroll-behavior: smooth; }
  .candidate-card { flex: 0 0 100%; max-width: 100%; box-sizing: border-box; padding: 10px; margin: 0 auto; }
  .carousel-arrow { font-size: 24px; padding: 8px 12px; }
  .carousel-arrow.left { left: 5px; }
  .carousel-arrow.right { right: 5px; }
  .position-block { padding: 10px; }
  .carousel-controls { text-align: center; margin-top: 15px; }
  .carousel-controls button { width: 120px; margin: 5px; }
}
</style>
</head>
<body>
<?php include('nav_top.php'); ?>
<div class="wrapper">
<div class="home_body">
<?php include('homesidebar.php'); ?>
<hr class="footer-line1">

<!-- Countdown Timer -->
<div style="text-align:center; font-size:18px; color:#002f6c; margin-bottom:20px;">
    Time Remaining: <span id="timer"></span>
</div>

<div class="vote-box1">
<form method="post" action="vote.php" id="voteForm">
    <h2 style="text-align: center; color: #002f6c; font-weight: bold; margin-bottom: 30px;">PLSS VOTE WISELY</h2>

<?php
$positions = [
    'President','Vice-President','Governor','Vice-Governor',
    'Secretary','Treasurer','Social-Media Officer','Representative'
];
$index = 0;
foreach ($positions as $position):
    $safe_position = mysqli_real_escape_string($conn, $position);
    $query = mysqli_query($conn, "SELECT * FROM candidate WHERE Position='$safe_position'") or die(mysqli_error($conn));
    if (mysqli_num_rows($query) > 0):
?>
<div class="position-block" id="position_<?php echo $index; ?>" style="<?php echo ($index !== 0) ? 'display:none;' : ''; ?>">
    <div class="position-header">
        <div class="position-title">Candidates for <?php echo htmlspecialchars($position); ?></div>
    </div>
    <div class="carousel-container">
        <button type="button" class="carousel-arrow left" id="left_<?php echo $index; ?>" onclick="scrollCarousel('carousel_<?php echo $index; ?>', -1, <?php echo $index; ?>)">&#10094;</button>
        <div class="carousel-inner" id="carousel_<?php echo $index; ?>">
        <?php while ($row = mysqli_fetch_assoc($query)):
            $photo = $row['Photo'];
            $photo_path = 'admin/upload/' . basename($photo);
        ?>
        <div class="candidate-card">
            <div style="font-weight: bold; color: #000; margin-bottom: 5px;"><?php echo htmlspecialchars($row['Position']); ?></div>
            <img class="candidate-photo" src="<?php echo $photo_path; ?>" alt="Candidate Photo">
            <div style="margin-top:10px;"><strong><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></strong></div>
            <div style="font-size: 14px;"><?php echo htmlspecialchars($row['Party']); ?></div>
            <input type="radio" name="<?php echo strtolower(str_replace(['-', ' '], '_', $position)); ?>" value="<?php echo $row['CandidateID']; ?>" style="accent-color: #d5b60a;">
            <div style="margin-top: 5px;">
                <a class="btn btn-info btn-small" data-toggle="modal" href="#viewModal<?php echo $row['CandidateID']; ?>"><i class="icon-list icon-large"></i> View</a>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal hide fade" id="viewModal<?php echo $row['CandidateID']; ?>">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h3>Candidate Qualification</h3></div>
            <div class="modal-body" style="text-align:center; background-color:#fff; color:000;">
                <p><strong>Qualification:</strong> <?php echo htmlspecialchars($row['Qualification']); ?></p>
            </div>
            <div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Close</a></div>
        </div>
        <?php endwhile; ?>
        </div>
        <button type="button" class="carousel-arrow right" id="right_<?php echo $index; ?>" onclick="scrollCarousel('carousel_<?php echo $index; ?>', 1, <?php echo $index; ?>)">&#10095;</button>
    </div>
    <div class="carousel-controls">
        <?php if ($index > 0): ?>
            <button type="button" class="btn btn-secondary" onclick="previousPosition(<?php echo $index; ?>)">Back</button>
        <?php endif; ?>
        <?php if ($index < count($positions) - 1): ?>
            <button type="button" class="btn btn-primary" onclick="nextPosition(<?php echo $index; ?>)">Next</button>
        <?php else: ?>
            <button type="submit" class="btn btn-success"><i class="icon-thumbs-up icon-large"></i>&nbsp;Submit Vote</button>
        <?php endif; ?>
    </div>
</div>
<?php
    $index++;
    endif;
endforeach;
?>
</form>
</div> <!-- End of vote-box -->

<script>
function showPosition(index) {
    const count = <?php echo count($positions); ?>;
    for (let i = 0; i < count; i++) {
        const block = document.getElementById("position_" + i);
        if (block) block.style.display = (i === index) ? "block" : "none";
    }
    updateCarouselArrows(index);
    localStorage.setItem("lastPositionIndex", index);
}

function nextPosition(currentIndex) {
    showPosition(currentIndex + 1);
}

function previousPosition(currentIndex) {
    showPosition(currentIndex - 1);
}

function scrollCarousel(id, direction, index) {
    const container = document.getElementById(id);
    const scrollAmount = 320;
    container.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
    setTimeout(() => updateCarouselArrows(index), 300);
}

function updateCarouselArrows(index) {
    const container = document.getElementById('carousel_' + index);
    if (!container) return;
    const leftArrow = document.getElementById('left_' + index);
    const rightArrow = document.getElementById('right_' + index);
    const scrollLeft = container.scrollLeft;
    const maxScroll = container.scrollWidth - container.clientWidth;
    leftArrow.disabled = scrollLeft <= 0;
    rightArrow.disabled = scrollLeft >= maxScroll - 5;
}

window.onload = function() {
    const count = <?php echo count($positions); ?>;
    for (let i = 0; i < count; i++) updateCarouselArrows(i);

    // Restore last viewed position after reload
    const savedIndex = parseInt(localStorage.getItem("lastPositionIndex") || "0");
    showPosition(savedIndex);
};


// Countdown Timer (Logout After Time Ends)
var serverTimeLeft = <?php echo max(0, $time_limit - $elapsed_time); ?>;
var pageLoadTime = Date.now();

function formatTime(seconds) {
    var h = Math.floor(seconds / 3600);
    var m = Math.floor((seconds % 3600) / 60);
    var s = seconds % 60;
    return (h > 0 ? h + ":" : "") + 
           (m < 10 ? "0" + m : m) + ":" + 
           (s < 10 ? "0" + s : s);
}

function updateTimer() {
    // Calculate elapsed time since page load
    var now = Date.now();
    var elapsedSinceLoad = Math.floor((now - pageLoadTime) / 1000);
    var timeLeft = serverTimeLeft - elapsedSinceLoad;

    if (timeLeft <= 0) {
        document.getElementById("timer").innerHTML = "Time is up!";
        alert("⏰ Your voting time has ended. You will now be logged out.");
        document.querySelectorAll("input, button").forEach(el => el.disabled = true);
        setTimeout(function() { window.location.href = "logout.php"; }, 2000);
        return;
    }

    document.getElementById("timer").innerHTML = formatTime(timeLeft);
    requestAnimationFrame(updateTimer); // smoother refresh (60fps)
}

updateTimer();


document.addEventListener("DOMContentLoaded", function() {
    var form = document.getElementById("voteForm");
    if (form) {
        form.addEventListener("submit", function(e){
            if (timeLeft <= 0) {
                alert("You cannot submit. Voting time has expired!");
                e.preventDefault(); // Stop form from submitting
            }
        });
    }
});
// Allow unselecting a chosen candidate (toggle radio button)
document.querySelectorAll('input[type="radio"]').forEach(radio => {
    radio.addEventListener('click', function(e) {
        if (this.previousChecked) {
            this.checked = false; // unselect
        }
        // Store the checked state for next click
        this.previousChecked = this.checked;
    });
});

</script>



<div class="foot" style="margin-top: 40px;"><?php include('footer1.php'); ?></div>
</div>
</div>
</body>
</html>  