<?php
ob_start(); // Prevent "headers already sent" error
include('session.php');
include('header.php');
include('dbcon.php');

$get_id = $_GET['id'] ?? null;
if (!$get_id) {
    die("Invalid user ID.");
}

// Fetch existing user data
$result = mysqli_query($conn, "SELECT * FROM users WHERE User_id='$get_id'") or die(mysqli_error($conn));
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("User not found.");
}

// Handle form submission
if (isset($_POST['save'])) {
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    
    // Update password only if provided
    if (!empty($_POST['password'])) {
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $password_sql = ", Password='$password'";
    } else {
        $password_sql = "";
    }

    $update_sql = "UPDATE users SET 
        FirstName='$firstname',
        LastName='$lastname',
        UserName='$username',
        Position='$position'
        $password_sql
        WHERE User_id='$get_id'";

    if (mysqli_query($conn, $update_sql)) {
        header("Location: election_officer_list.php");
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>
</head>
<body>
<?php include('nav_top.php'); ?>
<div class="wrapper">
<div class="home_body">
<?php include('menusidebar.php'); ?>
<section style="margin-top: 20px;">
 <section style="margin-top: 20px;">
  <div class="dropdown">
    <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"
  #000; border: none;>
  <i class="icon-table icon-large" style="margin-right: 8px;"></i> Filter By Position
  <span class="caret" style="border-top-color: #000; margin-left: 6px;"></span>
</button>

    <ul class="dropdown-menu" style="background-color: #fff;">
      <li>
        <a href="election_officer_list.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
          <i class="icon-table icon-large" style="margin-right: 8px;"></i> All
        </a>
      </li>
     
    </ul>
  </div>
</section>
	
	
	<div id="element" class="hero-body">
	
	<form method="POST" class="form-horizontal" enctype="multipart/form-data">
    <input type="hidden" name="user_name" class="user_name" value="<?php echo $_SESSION['User_Type']; ?>"/>
    <fieldset>
        <legend style="display: flex; justify-content: space-between; align-items: center; color: white;">
    <span>Edit Election Officer</span>
    <a href="election_officer_list.php" 
       class="btn"
       style="background-color: #ffffff; color: #007bff; border: 2px solid #007bff; 
              padding: 8px 14px; text-decoration: none; border-radius: 5px; 
              font-weight: bold; box-shadow: 0 0 5px rgba(0,0,0,0.3);">
        <i class="icon-arrow-left icon-large"></i> Back
    </a>
</legend>
        <br>
        <div class="candidate_margin">
            <ul class="thumbnails_new_voter">
                <li class="span3">
                    <div class="thumbnail_new_voter">

                        <div class="control-group">
                            <label class="control-label" for="firstname">First Name:</label>
                            <div class="controls">
                                <input type="text" name="firstname" class="firstname" value="<?php echo $row['FirstName']; ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="lastname">Last Name:</label>
                            <div class="controls">
                                <input type="text" name="lastname" class="lastname" value="<?php echo $row['LastName']; ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="username">Username:</label>
                            <div class="controls">
                                <input type="text" name="username" class="username" value="<?php echo $row['UserName']; ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="password">Password:</label>
                            <div class="controls">
                                <input type="text" name="password" class="password" placeholder="Leave blank to keep current password">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="position">Position:</label>
                            <div class="controls">
                                <select name="position" class="position" id="span90">
                                    <option selected><?php echo $row['Position']; ?></option>
                                    <option>Election Officer 1</option>
                                    <option>CSDL Officer</option>
                                    <option>Faculty Officer</option>
                                    <option>Admin</option>
                                    <option>Secretary Officer</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button class="btn btn-primary" name="save"><i class="icon-edit icon-large"></i>Edit</button>
                            </div>
                        </div>

                    </div>
                </li>
            </ul>
        </div>
    </fieldset>
</form>

	
	</div>
	<?php include('footer.php')?>	
</div>
</div>
</div>
</body>
</html>


	  