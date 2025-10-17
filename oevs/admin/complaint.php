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


<section>
	
	<div id="element" class="hero-body">
	    <div class="pagination">
    <ul>


 
  
    </ul>
	

    </div>


	<table class="users-table">


<div class="demo_jui">
    <table cellpadding="0" cellspacing="0" border="0" class="display" id="log" class="jtable">
    <thead>
  <tr>
      <th>AU Email</th>
      <th>Complaint Message</th>
      <th>Status</th>
      <th>Action</th>
  </tr>
</thead>
    <tbody>
<?php
// Handle status update
if (isset($_POST['update_status']) && isset($_POST['complaint_id']) && isset($_POST['status'])) {
    $complaint_id = intval($_POST['complaint_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);

    $allowed_statuses = ['pending', 'in_progress', 'resolved'];
    if (in_array($new_status, $allowed_statuses)) {
        $update_query = "UPDATE complaint SET status = '$new_status' WHERE complaint_id = $complaint_id";
        mysqli_query($conn, $update_query);
        echo "<script>window.location.href = 'complaint.php';</script>"; // reload to see changes
        exit;
    } else {
        echo "<script>alert('Invalid status value.');</script>";
    }
}

// Handle delete request
if (isset($_POST['delete_complaint']) && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $delete_query = "DELETE FROM complaint WHERE complaint_id = $delete_id";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Complaint deleted successfully'); window.location.href = 'complaint.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error deleting complaint: " . mysqli_error($conn) . "');</script>";
    }
}

// Fetch complaints
$query = "SELECT complaint_id, Username, subject, description, status FROM complaint ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "<tr><td colspan='5'>Error fetching data: " . mysqli_error($conn) . "</td></tr>";
} elseif (mysqli_num_rows($result) == 0) {
    echo "<tr><td colspan='5'>No complaints found.</td></tr>";
} else {
   while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['Username']) . "</td>";
    echo "<td>" . nl2br(htmlspecialchars($row['description'])) . "</td>";

    echo "<td>
            <form method='post' style='margin:0;'>
                <input type='hidden' name='complaint_id' value='" . $row['complaint_id'] . "' />
                <select name='status' onchange='this.form.submit()'>
                    <option value='pending'" . ($row['status'] == 'pending' ? " selected" : "") . ">Pending</option>
                    <option value='in_progress'" . ($row['status'] == 'in_progress' ? " selected" : "") . ">In Progress</option>
                    <option value='resolved'" . ($row['status'] == 'resolved' ? " selected" : "") . ">Resolved</option>
                </select>
                <input type='hidden' name='update_status' value='1' />
            </form>
          </td>";

    echo "<td>
            <form method='post' onsubmit='return confirm(\"Are you sure you want to delete this complaint?\");'>
                <input type='hidden' name='delete_id' value='" . $row['complaint_id'] . "' />
                <button type='submit' name='delete_complaint' class='btn btn-danger'>Delete</button>
            </form>
          </td>";
    echo "</tr>";
}

}
?>
    </tbody>
</table>
	</div>	
	</div>	
<?php include('footer.php')?>
	
</div>
<input type="hidden" class="pc_date" name="pc_date"/>
<input type="hidden" class="pc_time" name="pc_time"/>
</body>
</html>


