<?php
include('session.php');
include('header.php');
include('dbcon.php');
?>
</head>
<style>
.hero-profile {
    max-width: 500px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px #202c61 ;
}

.hero-profile h3 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 24px;
    color: #333;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 8px;
    transition: border-color 0.3s;
}

.form-control:focus {
    border-color: #007bff;
    outline: none;
}

.btn-primary {
    width: 100%;
    height: 5%;
    padding: 10px;
    background-color: #007bff;
    color: white;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    transition: background-color 0.3s;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.alert {
    text-align: center;
    padding: 12px;
    border-radius: 8px;
    font-weight: bold;
    margin-bottom: 20px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}
</style>

<body>
<?php include('nav_top.php'); ?>
<div class="wrapper">
<div class="home_body">
<div class="navbar">
	<div class="navbar-inner">
	<div class="container">	
	<ul class="nav nav-pills">
	  <li>....</li>
	    <li  ><a href="home.php"><i class="icon-home icon-large"></i>Home</a></li>
    <li><a href="dashboard.php"><i class="icon-table icon-large"></i>Dashboard</a></li>
	  <li class=""><a  href="voter_list.php"><i class="icon-align-justify icon-large"></i>Voters List</a></li>  
		<li><a  href="canvassing_report.php"><i class="icon-book icon-large"></i>Vote Count Report</a></li>
		<li><a  href="voter_verification.php"><i class="icon-table icon-large"></i>Voters Verification</a>
  
     <li><a  href="complaint.php"><i class="icon-table icon-large"></i>Complaint</a> 
		  <li  class="active"><a  href="profile.php"><i class="icon-table icon-large"></i>Profile</a>
		   <div class="modal hide fade" id="about">
	<div class="modal-header"> 
	<button type="button" class="close" data-dismiss="modal">�</button>
	    <h3> </h3>
	  </div>
	  <div class="modal-body">
	  <?php include('about.php') ?>
	  <div class="modal-footer_about">
	    <a href="#" class="btn" data-dismiss="modal">Close</a>
		</div>
		</div>
		   <li>....</li>
	 </ul>
	<form class="navbar-form pull-right">
		<?php $result=mysqli_query($conn,"select * from users where User_id='$id_session'");
	$row=mysqli_fetch_array($result);
	?>
	<font color="white">Welcome:<i class="icon-user-md"></i><?php echo $row['User_Type']; ?></font>
	<a class="btn btn-danger" id="logout" data-toggle="modal" href="#myModal"><i class="icon-off"></i>&nbsp;Logout</a>
	<div class="modal hide fade" id="myModal">
	<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">�</button>
	    <h3> </h3>
	  </div>
	  <div class="modal-body">
	    <p><font color="gray">Are You Sure you Want to LogOut?</font></p>
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn" data-dismiss="modal">No</a>
	    <a href="logout.php" class="btn btn-primary">Yes</a>
		</div>
		</div>

	</form>
	</div>
	</div>
	</div>

    <div class="hero-profile">
 <h3 
  style="transition: color 0.3s;" 
  onmouseover="this.style.color='#ffd400';  this.style.cursor='pointer';" 
  onmouseout="this.style.color=''; this.style.textDecoration='none';"
>
  Edit Your Profile
</h3>


    <?php
    // Fetch the user data
    $query = mysqli_query($conn, "SELECT * FROM users WHERE User_id = '$id_session'");
    $row = mysqli_fetch_assoc($query);

   if (isset($_POST['save'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $password = $_POST['password']; // plain text (not hashed)
    $user_type = 'admin'; // Automatically admin

    mysqli_query($conn, "UPDATE users SET 
        FirstName = '$firstname',
        LastName = '$lastname',
        UserName = '$username',
        Password = '$password',
        User_Type = '$user_type'
        WHERE User_id = '$id_session'") or die(mysqli_error($conn));

    echo "<div class='alert alert-success'>Profile updated successfully.</div>";

    // Refresh data after update
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
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Position</label>
            <input type="text" value="<?php echo $row['Position']; ?>" class="form-control" readonly>
        </div>

        <input type="submit" name="save" value="Save" class="btn btn-primary">
    </form>
</div>
	
	<?php include('footer.php')?>	
</div>
</div>
</body>
</html>
