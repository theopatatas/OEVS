<?php
include('session.php');
include('header.php');
include('dbcon.php');
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

<section>

	
	<div id="element" class="hero-body">
	<div class="pagination">
        <ul>
         <li><a href="new_officer.php"><font color="white"><i class="icon-plus icon-large"></i>Add Election Officer</font></a></li>	
</ul>
        </div>
	<table class="users-table">


<div class="demo_jui">
    <table cellpadding="0" cellspacing="0" border="0" class="display jtable" id="log">
        <thead>
            <tr>
                <!-- Removed User ID column -->
                <th>FirstName</th>
                <th>LastName</th>
                <th>UserName</th>
                <th>Password</th> <!-- Changed User Type header to Password -->
                <th>Position</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $user_query = mysqli_query($conn, "SELECT * FROM users");
            while ($user = mysqli_fetch_array($user_query)) {
                $id = $user['User_id'];
                $fullName = $user['FirstName'] . " " . $user['LastName'];
            ?>
                <tr class="del<?php echo $id; ?>">
                    <!-- Removed User ID <td> -->
                    <td><?php echo htmlspecialchars($user['FirstName']); ?></td>
                    <td><?php echo htmlspecialchars($user['LastName']); ?></td>
                    <td><?php echo htmlspecialchars($user['UserName']); ?></td>
                    <td><?php echo htmlspecialchars($user['Password']); ?></td> <!-- Display password here -->
                    <td><?php echo htmlspecialchars($user['Position']); ?></td>
                    <td align="center" width="240">
                        <a class="btn btn" href="edit_officer.php?id=<?php echo $id; ?>"><i class="icon-edit icon-large"></i>&nbsp;Edit</a>&nbsp;
                        <a class="btn btn-info" data-toggle="modal" href="#modal<?php echo $id; ?>"><i class="icon-list icon-large"></i>&nbsp;View</a>
                        <a class="btn btn-danger1 btn-delete-officer" data-id="<?php echo $id; ?>"><i class="icon-trash icon-large"></i>&nbsp;Delete</a>

                    </td>
                </tr>

                <!-- Modal for user details -->
                <div class="modal hide fade" id="modal<?php echo $id; ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3>User Information</h3>
                    </div>
                    <div class="modal-body" style="background-color: #fff; color: 000;">
                        <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['FirstName']); ?></p>
                        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['LastName']); ?></p>
                        <p><strong>User Name:</strong> <?php echo htmlspecialchars($user['UserName']); ?></p>
                        <p><strong>User Type:</strong> <?php echo htmlspecialchars($user['User_Type']); ?></p>
                        <p><strong>Position:</strong> <?php echo htmlspecialchars($user['Position']); ?></p>
                        <!-- Do not display password in modal as per original -->
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn" data-dismiss="modal">Close</a>
                    </div>
                </div>

                <input type="hidden" name="data_name" class="data_name<?php echo $id; ?>" value="<?php echo htmlspecialchars($fullName); ?>"/>
            <?php
            }
            ?>
            <input type="hidden" name="user_name" class="user_name" value="<?php echo htmlspecialchars($_SESSION['User_Type']); ?>"/>
        </tbody>
    </table>
</div>


<?php include('footer.php')?>
	
</div>
<input type="hidden" class="pc_date" name="pc_date"/>
<input type="hidden" class="pc_time" name="pc_time"/>
</body>
</html>
<script type="text/javascript">
$('.btn-delete-officer').click(function () {
    var id = $(this).data("id");  // or attr("data-id")
    var pc_date = $('.pc_date').val();
    var pc_time = $('.pc_time').val();
    var data_name = $('.data_name' + id).val();
    var user_name = $('.user_name').val();

    if (confirm("Are you sure you want to delete this Officer?")) {
        $.ajax({
            type: "POST",
            url: "delete_officer.php",
            data: {
                id: id,
                pc_date: pc_date,
                pc_time: pc_time,
                data_name: data_name,
                user_name: user_name
            },
            cache: false,
            success: function (response) {
                console.log("Server response: " + response);
                if(response.trim() === "success"){
                    $(".del" + id).fadeOut('slow');
                } else {
                    alert("Delete failed: " + response);
                }
            }
        });
    } else {
        return false;
    }
});

</script>


