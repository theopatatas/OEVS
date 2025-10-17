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
  <!-- Candidate-related items -->
  <li>
    <a href="canvassing_report.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> All
    </a>
  </li>
  <li>
    <a href="C_President.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> President
    </a>
  </li>
  <li>
    <a href="C_Vice-President.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> Vice-President
    </a>
  </li>
  <li>
    <a href="C_Governor.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> Governor
    </a>
  </li>
  <li>
    <a href="C_Vice-Governor.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> Vice-Governor
    </a>
  </li>
  <li>
    <a href="C_Secretary.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> Secretary
    </a>
  </li>
  <li>
    <a href="C_Treasurer.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> Treasurer
    </a>
  </li>
  <li>
    <a href="C_Socialmediaofficer.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> Social-Media Officer
    </a>
  </li>
  <li>
    <a href="C_Representative.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> Representative
    </a>
  </li>
    </ul>
  </div>
</section>

<section>
	<div id="element" class="hero-body">
	    <div class="pagination">
    <ul>

   
 
  
    </ul>
    </div>
	<?php
	$query=mysqli_query($conn,"select  * from candidate");
	$row=mysqli_fetch_array($query); $id_excel=$row['CandidateID'];	
	?>
	
		<form method="POST" action="canvassing_excel.php">
	<input type="hidden" name="id_excel" value="<?php echo $id_excel; ?>">
	<button id="save_voter" class="btn btn-success" name="save"><i class="icon-download icon-large"></i>Download Excel File</button>
	</form>
	<table class="users-table">


<div class="demo_jui">
		<table cellpadding="0" cellspacing="0" border="0" class="display" id="log" class="jtable">
			<thead>
				<tr>
				<th class="hide">Abc</th>
				<th>Position</th>
				<th>FirstName</th>
				<th>LastName</th>
				<th>Year</th>
			
				<th>Photo</th>
				<th>No. of Votes</th>
				
				</tr>
			</thead>
			<tbody>

<?php $candidate_query=mysqli_query($conn,"select  * from candidate where Position='Secretary'");
		while($candidate_rows=mysqli_fetch_array($candidate_query)){ $id=$candidate_rows['CandidateID'];
		$fl=$candidate_rows['FirstName'];
	
		?>

<tr class="del<?php echo $id ?>">
	<td align="center" class="hide"><?php echo $candidate_rows['abc']; ?></td>
	<td align="center"><?php echo $candidate_rows['Position']; ?></td>
	<td><?php echo $candidate_rows['FirstName']; ?></td>
	<td><?php echo $candidate_rows['LastName']; ?></td>
	<td align="center"><?php echo $candidate_rows['Year']; ?></td>
	
	<td align="center"><img class="pic" width="40" height="30" src="<?php echo $candidate_rows['Photo'];?>" border="0" onmouseover="showtrail('<?php echo $candidate_rows['Photo'];?>','<?php echo $candidate_rows['FirstName']." ".$candidate_rows['LastName'];?> ',200,5)" onmouseout="hidetrail()"></a></td>
		<td align="center">
	<?php $votes_query=mysqli_query($conn,"select * from votes where CandidateID='$id'");
	$vote_count=mysqli_num_rows($votes_query);
	echo $vote_count;
	?>
</td>	




	
	
	
<input type="hidden" name="data_name" class="data_name<?php echo $id ?>" value="<?php echo $candidate_rows['FirstName']." ".$candidate_rows['LastName']; ?>"/>
	<input type="hidden" name="user_name" class="user_name" value="<?php echo $_SESSION['User_Type']; ?>"/>
	
	</tr>
<?php } ?>

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
