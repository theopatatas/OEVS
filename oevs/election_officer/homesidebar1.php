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
  font-size: 24px;
  background: none;
  border: none;
  cursor: pointer;
  color: #333;
  float: right;
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

// Main page label (like Dashboard, History Log)
$main_labels = [
  'analytics.php' => 'Dashboard',
  'history.php' => 'History Log',
];

// Sub-actions under Admin Actions
$sub_labels = [
  'result.php' => 'Election Result',
  'winningresult.php' => 'Winning Result',
  'backupnreset.php' => 'Backup and Reset',
  'dashboard.php' => 'Analytics',
];

$main_page_map = [
  // Candidate subpages
   'result.php' => 'analytics.php',
  'winningresult.php' => 'analytics.php',
  'backupnreset.php' => 'analytics.php',
  'dashboard.php' => 'analytics.php',
  

];
$main_page_key = isset($main_page_map[$current_page]) ? $main_page_map[$current_page] : $current_page;

// Get labels
$main_label = isset($main_labels[$main_page_key]) ? $main_labels[$main_page_key] : '';
$subpage_label = isset($sub_labels[$current_page]) ? $sub_labels[$current_page] : '';
?>

<div class="navbar2">
  <div class="navbar-inner">
    <div class="container">
      <ul class="nav nav-pills">
        <li class="dropdown active">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#" style="color: #000;">
            <i class="icon-home icon-large"></i> 
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
    <img src="images/au.png" alt="Logo" style="max-width: 40%; height: auto;">
  </div>
  <?php 
    $result = mysqli_query($conn, "SELECT * FROM users WHERE User_id='$id_session'");
    $row = mysqli_fetch_array($result);
  ?>
  <h4 style="margin-top: 20px;"><i class="icon-user-md"></i> Welcome: <?php echo $row['User_Type']; ?></h4>
  <hr>
  <ul>
      <li>
    <a href="home.php">
      <i class="icon-home"></i> Homepage
    </a>
  </li>
    <li>
  <button onclick="$('#profileModal').modal('show')">
    <i class="icon-user"></i> Profile
  </button>
</li>

    </li>
    <li>
      <button class="btn-danger" data-toggle="modal" data-target="#logoutModal">
        <i class="icon-off"></i> Logout
      </button>
    </li>
  </ul>
</div>

<!-- Logout Modal -->
<div class="modal hide fade" id="logoutModal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3>Logout</h3>
  </div>
  <div class="modal-body">
    <p style="color: gray;">Are you sure you want to log out?</p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">No</a>
    <a href="logout.php" class="btn btn-primary">Yes</a>
  </div>
</div>

<!-- About Modal -->
<div class="modal hide fade" id="aboutModal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3>About the System</h3>
  </div>
  <div class="modal-body" id="aboutContent">
    <p>Loading...</p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal">Close</a>
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
<!-- Profile Modal -->
<!-- Profile Modal -->
<div class="modal hide fade" id="profileModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" style="max-width: 600px;">
    <div class="modal-content hero-profile">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Edit Your Profile</h3>
      </div>
      <div class="modal-body">
        <?php
        // Fetch user data
        $query = mysqli_query($conn, "SELECT * FROM users WHERE User_id = '$id_session'");
        $row = mysqli_fetch_assoc($query);

        // Handle form submission
        if (isset($_POST['save'])) {
          $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
          $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
          $username = mysqli_real_escape_string($conn, $_POST['username']);
          $password = mysqli_real_escape_string($conn, $_POST['password']); // Plain text password
          $user_type = 'admin';

          mysqli_query($conn, "UPDATE users SET 
            FirstName = '$firstname',
            LastName = '$lastname',
            UserName = '$username',
            Password = '$password',
            User_Type = '$user_type'
            WHERE User_id = '$id_session'") or die(mysqli_error($conn));

          echo "<div class='alert alert-success'>Profile updated successfully.</div>";

          // Refresh data
          $query = mysqli_query($conn, "SELECT * FROM users WHERE User_id = '$id_session'");
          $row = mysqli_fetch_assoc($query);
        }
        ?>
        <form method="post">
          <div class="form-group">
            <label>First Name</label>
            <input type="text" name="firstname" value="<?php echo $row['FirstName']; ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="lastname" value="<?php echo $row['LastName']; ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo $row['UserName']; ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label>New Password</label>
            <input type="text" name="password" value="<?php echo $row['Password']; ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Position</label>
            <input type="text" value="<?php echo $row['Position']; ?>" class="form-control" readonly>
          </div>

          <input type="submit" name="save" value="Save" class="btn btn-primary">
        </form>
      </div>
    </div>
  </div>
</div>
