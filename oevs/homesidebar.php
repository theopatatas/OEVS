<!-- ========== CUSTOM CSS ========== -->
<style>
/* Sidebar Styles */
#rightSidebar {
  position: fixed;
  top: 0;
  right: 0;
  width: 250px;
  height: 100%;
  background-color: #f8f9fa;
  box-shadow: -2px 0 5px rgba(0,0,0,0.2);
  transform: translateX(100%);
  transition: transform 0.3s ease;
  z-index: 1060;
  padding: 20px;
  display: none;
   pointer-events: auto;
}

/* Sidebar content as list */
#rightSidebar ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

#rightSidebar li {
  margin-bottom: 10px;
}

#rightSidebar a,
#rightSidebar button {
  width: 100%;
  display: block;
  text-align: left;
  padding: 10px 12px;
  border: none;
  background-color: #fff;
  color: #000;
  border-radius: 4px;
  text-decoration: none;
  font-size: 14px;
}

#rightSidebar a:hover,
#rightSidebar button:hover {
  background-color: #002f6c;
}

/* Close button inside sidebar */
.close-sidebar {
  position: absolute !important;
  top: 10px !important;
  right: 10px !important;
  font-size: 20px !important;
  background: transparent !important;
  border: none !important;
  cursor: pointer !important;
  color: #555 !important;
  width: 30px !important;
  height: 30px !important;
  border-radius: 4px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  transition: background-color 0.2s ease, color 0.2s ease !important;
  z-index: 9999 !important;
}

.close-sidebar:hover {
  background-color: #e81123 !important; /* Chrome red */
  color: #fff !important;
}

/* Overlay Styles */
#overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.4);
  display: none;
  z-index: 1050;
  pointer-events: auto;
}

/* Hamburger button */
.navbar2 {
  width: 100%;
  max-width: 100%;
  box-sizing: border-box;
  overflow-x: visible; /* Prevent cutoff on X axis */
  padding: 0;
}

.navbar-inner {
  width: 100%;
  max-width: 100%;
  box-sizing: border-box;
}

.navbar2 .container {
  width: 100% !important;
  max-width: 100%;
  box-sizing: border-box;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  overflow-x: visible;
  padding: 0 15px;
}

/* Keep nav on the left */
.navbar2 .nav {
  display: flex;
  flex-direction: row;
  margin: 0;
  padding: 0;
  flex-wrap: wrap;
}

/* Nav links spacing */
.navbar2 .nav > li > a {
  padding-right: 12px;
  padding-left: 12px;
  margin-right: 2px;
  line-height: 14px;
  white-space: nowrap;
}

/* Hamburger button style */
.hamburger {
  font-size: 24px;
  background: transparent;
  border: none;
  color: #000;
  cursor: pointer;
  padding: 4px 8px;
  margin-left: auto; /* Pushes it to the far right */
}

@media (max-width: 768px) {
 
  .navbar2 {
    width: 90vw;
    max-width: 100vw;
    overflow-x: visible;
  }

  .navbar2 .container {
    flex-wrap: nowrap;
    flex-direction: row;
    justify-content: flex-start;
    align-items: center;
    overflow-x: visible;
  }

  .hamburger {
    margin-left: auto; /* keep at right */
  }
}


</style>




<!-- ========== NAVBAR ========== -->
<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Main pages and their labels
$main_labels = [
  'voting.php' => 'Voting Page',
  'complaint_form.php' => 'Complaint Form',
];

// Subpages under the main pages
$sub_labels = [
  'vote.php' => 'Ballot Confirmation',
  'thank_you.php' => 'Thank You Page',
];

// Mapping subpages to their parent main pages
$main_page_map = [
  'vote.php' => 'voting.php',
  'thank_you.php' => 'voting.php',
];

// Mapping second-level subpages (like thank_you.php) to their immediate subpage parents
$subpage_parent_map = [
  'thank_you.php' => 'vote.php',
];

// Determine the main page key
$main_page_key = $main_page_map[$current_page] ?? $current_page;
$main_label = $main_labels[$main_page_key] ?? '';

// Determine first-level subpage
$subpage_label = $sub_labels[$current_page] ?? '';

// Determine second-level subpage if applicable
$subpage_key = $subpage_parent_map[$current_page] ?? null;
$sub_subpage_label = $subpage_key ? ($sub_labels[$subpage_key] ?? '') : '';
?>


<div class="navbar2">
  <div class="navbar-inner">
    <div class="container">
      <ul class="nav nav-pills">
        <li class="dropdown active">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#" style="color: #000;">
            <i class="icon-home icon-large"></i> Home
            <?php if ($main_label): ?>
              <span style="font-size: 12px; font-weight: normal; color: #000;">
                &nbsp;>&nbsp;<?php echo $main_label; ?>
                <?php if ($subpage_label): ?>
                  &nbsp;>&nbsp;<?php echo $subpage_label; ?>
                <?php endif; ?>
              </span>
            <?php endif; ?>
            <b class="caret" style="border-top-color: #000;"></b>
          </a>
          <ul class="dropdown-menu">
            <li class="<?php echo ($current_page == 'voting.php') ? 'active' : ''; ?>">
              <a href="voting.php" style="color: #000;"><i class="icon-home icon-large"></i> Voting Page</a>
            </li>
           
          </ul>
        </li>
      </ul>

      <!-- Hamburger Button -->
      <button class="hamburger pull-right" onclick="toggleSidebar()">&#9776;</button>
    </div>
  </div>
</div>

<!-- Overlay and Sidebar -->
 <div id="overlay" onclick="toggleSidebar()"></div>

<div id="rightSidebar">
  <button class="close-sidebar" onclick="toggleSidebar()">&times;</button>
    <div style="text-align: center; margin-bottom: 15px;">
    <img src="pic/au.png" alt="Logo" style="max-width: 40%; height: auto;">
  </div>

  <?php 
    if (!isset($_SESSION)) {
        session_start();
    }

    if (!isset($_SESSION['id'])) {
        header("Location: index.php");
        exit;
    }

    $id = $_SESSION['id'];
    include('dbcon.php'); // Make sure database is connected

    $query = mysqli_query($conn, "SELECT * FROM voters WHERE VoterID = '$id'");
    $voter = mysqli_fetch_assoc($query);
  ?>

  <!-- Welcome Message -->
  <h4 style="margin-top: 20px;">
    <i class="icon-user-md"></i> 
    Welcome: <?php echo htmlspecialchars($voter['FirstName'] . ' ' . $voter['LastName']); ?>
  </h4>

  <hr>

  <!-- Menu -->
  <ul>
    <li>
      <a href="voting.php">
        <i class="icon-home"></i> Voting Page
      </a>
    </li>
    <li>
      <button onclick="$('#profileModal').modal('show')" class="btn btn-link" style="padding: 0;">
        <i class="icon-user"></i> Profile
      </button>
    </li>
    <li>
  <button onclick="$('#disclaimerModal').modal('show')" class="btn btn-link" style="padding: 0;">
    <i class="icon-info-sign"></i> Disclaimer
  </button>
</li>
    <li>
      <button class="btn btn-danger" data-toggle="modal" data-target="#logoutModal">
        <i class="icon-off"></i> Logout
      </button>
    </li>
  </ul>
</div>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Logout</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to log out?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
        <a href="logout.php" class="btn btn-primary">Yes</a>
      </div>
    </div>
  </div>
</div>

<!-- Load About Content -->
<script>
function loadAbout() {
  const xhr = new XMLHttpRequest();
  xhr.open("GET", "about.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      document.getElementById("aboutContent").innerHTML = xhr.responseText;
    }
  };
  xhr.send();
}
</script>

<!-- Toggle Sidebar Script -->
<script>
function toggleSidebar() {
  const sidebar = document.getElementById("rightSidebar");
  const overlay = document.getElementById("overlay");

  if (sidebar.style.display === "block") {
    sidebar.style.transform = "translateX(100%)";
    overlay.style.display = "none";
    setTimeout(() => {
      sidebar.style.display = "none";
    }, 300);
  } else {
    sidebar.style.display = "block";
    overlay.style.display = "block"; // ensure visible
    setTimeout(() => {
      sidebar.style.transform = "translateX(0)";
    }, 10);
  }
}
// Close sidebar automatically when a modal is opened
$(document).on('show.bs.modal', function () {
  const sidebar = document.getElementById("rightSidebar");
  const overlay = document.getElementById("overlay");

  if (sidebar.style.display === "block") {
    sidebar.style.transform = "translateX(100%)";
    overlay.style.display = "none";
    setTimeout(() => {
      sidebar.style.display = "none";
    }, 300);
  }
});
</script>

<!-- Disclaimer Modal -->
<div class="modal fade" id="disclaimerModal" tabindex="-1" role="dialog" aria-labelledby="disclaimerModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="disclaimerModalLabel">How to Vote</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ol style="line-height: 1.8;">
          <li><strong>Login</strong> using your Student ID and Password.</li>
          <li>On the <strong>Voting Page</strong>, select your candidate for each position and click <strong>Submit</strong>.</li>
          <li>On the <strong>Ballot Confirmation</strong> page:
            <ul>
              <li>Review your selected candidates.</li>
              <li>Input your <strong>assigned voting room</strong>.</li>
              <li>Click <strong>Submit Result</strong> to finalize your vote.</li>
            </ul>
          </li>
          <li>After submitting, you will see a <strong>confirmation message</strong> indicating that you have successfully voted.</li>
          <li><strong>Note:</strong> Once you have voted, you <u>cannot log in again</u>. Thank you!</li>
        </ol>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Profile Modal -->
<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width: 600px;">
    <div class="modal-content hero-profile">
      <div class="modal-header">
        <h5 class="modal-title" id="profileModalLabel">Edit Your Profile</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php
        if (!isset($_SESSION)) session_start();
        $id = $_SESSION['id'];
        include('dbcon.php');

        // Fetch voter data
        $query = mysqli_query($conn, "SELECT * FROM voters WHERE VoterID = '$id'");
        $row = mysqli_fetch_assoc($query);

        // Handle form submission
        if (isset($_POST['save'])) {
          $fname = mysqli_real_escape_string($conn, $_POST['firstname']);
          $mname = mysqli_real_escape_string($conn, $_POST['middlename']);
          $lname = mysqli_real_escape_string($conn, $_POST['lastname']);
          $password = mysqli_real_escape_string($conn, $_POST['password']);
          $year = mysqli_real_escape_string($conn, $_POST['year']);

          mysqli_query($conn, "UPDATE voters SET 
            FirstName = '$fname',
            MiddleName = '$mname',
            LastName = '$lname',
            Password = '$password',
            Year = '$year'
            WHERE VoterID = '$id'") or die(mysqli_error($conn));

          echo "<div class='alert alert-success'>Profile updated successfully.</div>";

          // Refresh data
          $query = mysqli_query($conn, "SELECT * FROM voters WHERE VoterID = '$id'");
          $row = mysqli_fetch_assoc($query);
        }
        ?>
        <form method="post">
          <div class="form-group">
            <label>First Name</label>
            <input type="text" name="firstname" value="<?php echo htmlspecialchars($row['FirstName']); ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Middle Name</label>
            <input type="text" name="middlename" value="<?php echo htmlspecialchars($row['MiddleName']); ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="lastname" value="<?php echo htmlspecialchars($row['LastName']); ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Password</label>
            <input type="text" name="password" value="<?php echo htmlspecialchars($row['Password']); ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Year Level</label>
            <input type="text" name="year" value="<?php echo htmlspecialchars($row['Year']); ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Username</label>
            <input type="text" value="<?php echo htmlspecialchars($row['Username']); ?>" class="form-control" readonly>
          </div>

          <div class="form-group">
            <label>School ID</label>
            <input type="text" value="<?php echo htmlspecialchars($row['SchoolID']); ?>" class="form-control" readonly>
          </div>

          <input type="submit" name="save" value="Save" class="btn btn-primary">
        </form>
      </div>
    </div>
  </div>
</div>
