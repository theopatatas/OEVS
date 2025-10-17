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
    <a href="candidate_list.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> All
    </a>
  </li>
  <li>
    <a href="President.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> President
    </a>
  </li>
  <li>
    <a href="Vice-President.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> Vice-President
    </a>
  </li>
  <li>
    <a href="Governor.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> Governor
    </a>
  </li>
  <li>
    <a href="Vice-Governor.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> Vice-Governor
    </a>
  </li>
  <li>
    <a href="Secretary.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> Secretary
    </a>
  </li>
  <li>
    <a href="Treasurer.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> Treasurer
    </a>
  </li>
  <li>
    <a href="Socialmediaofficer.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
      <i class="icon-table icon-large" style="margin-right: 8px;"></i> Social-Media Officer
    </a>
  </li>
  <li>
    <a href="Representative.php" style="color: #000;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#000'">
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
    <li><a href="new_candidate.php"><font color="white"><i class="icon-plus icon-large"></i>Add Candidates</font></a></li>
    </ul>
	</div>
	<table class="users-table">


<div class="demo_jui">
		<table cellpadding="0" cellspacing="0" border="0" class="display" id="log" class="jtable">
			<thead>
				<tr>
				<th>Position</th>
				<th>FirstName</th>
				<th>LastName</th>
				<th>Year</th>
				<th>Photo</th>
				<th>Actions</th>
				
				</tr>
			</thead>
			<tbody>

<?php $candidate_query=mysqli_query($conn,"select  * from candidate where Position='Social-Media Officer'");
		while($candidate_rows=mysqli_fetch_array($candidate_query)){ $id=$candidate_rows['CandidateID'];
		$fl=$candidate_rows['FirstName'];
	
		?>

<tr class="del<?php echo $id ?>">
	<td align="center"><?php echo $candidate_rows['Position']; ?></td>
	<td><?php echo $candidate_rows['FirstName']; ?></td>
	<td><?php echo $candidate_rows['LastName']; ?></td>
	<td align="center"><?php echo $candidate_rows['Year']; ?></td>
	<td align="center"><img class="pic" width="40" height="30" src="<?php echo $candidate_rows['Photo'];?>" border="0" onmouseover="showtrail('<?php echo $candidate_rows['Photo'];?>','<?php echo $candidate_rows['FirstName']." ".$candidate_rows['LastName'];?> ',200,5)" onmouseout="hidetrail()"></a></td>
	<td width="240" align="center">
	<a class="btn btn-Success" href="edit_candidate.php<?php echo '?id='.$id; ?>"><i class="icon-edit icon-large"></i>&nbsp;Edit</a>&nbsp;
	<a class="btn btn-info"  data-toggle="modal" href="#<?php echo $id; ?>" ><i class="icon-list icon-large"></i>&nbsp;View</a>
	<a class="btn btn-danger1" id="<?php echo $id; ?>"><i class="icon-trash icon-large"></i>&nbsp;Delete</a>&nbsp;
	</td>

<div class="modal hide fade" id="<?php echo htmlspecialchars($id); ?>">
  <div class="modal-header" style="background-color: #202c61; color: white;">
    <button type="button" class="close" data-dismiss="modal" style="color:white;">×</button>
    <h1 style="margin: 0; font-size: 24px;">Candidate Information</h1>
  </div>  
  <div class="modal-body" style="
    background-color: #202c61; 
    color: white; 
    display: flex; 
    gap: 20px; 
    align-items: flex-start; 
    max-height: 400px; 
    overflow-y: auto; 
    padding: 20px;">
    
    <div style="flex-shrink: 0;">
      <img 
        src="<?php echo htmlspecialchars($candidate_rows['Photo']); ?>" 
        alt="Candidate Photo" 
        style="max-height: 200px; width: auto; border-radius: 8px; object-fit: contain;">
    </div>
    
    <div style="flex-grow: 1; overflow-wrap: break-word;">
      <p><strong>Party:</strong> <?php echo htmlspecialchars($candidate_rows['Party']); ?></p>
      <p><strong>Qualification:</strong><br>
        <?php echo nl2br(htmlspecialchars($candidate_rows['Qualification'])); ?>
      </p>
    </div>
    
  </div>
  
  <div class="modal-footer" style="background-color: #202c61; text-align: right; padding: 10px 20px;">
    <a href="#" class="btn btn-secondary" data-dismiss="modal" style="color: border: 1px solid white;">Close</a>
  </div>
</div>

		</div>
		</div>

	
	
	
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
<script type="text/javascript">
	$(document).ready( function() {
	
	var myDate = new Date();
var pc_date = (myDate.getMonth()+1) + '/' + (myDate.getDate()) + '/' + myDate.getFullYear();
var pc_time = myDate.getHours()+':'+myDate.getMinutes()+':'+myDate.getSeconds();
jQuery(".pc_date").val(pc_date);
jQuery(".pc_time").val(pc_time);
	
	
	$('.btn-danger1').click( function() {
		
		var id = $(this).attr("id");
		var pc_date = $('.pc_date').val();
		var pc_time = $('.pc_time').val();
		var data_name = $('.data_name'+id).val();
		var user_name = $('.user_name').val();
		
		if(confirm("Are you sure you want to delete this Candidate?")){
			
		
			$.ajax({
			type: "POST",
			url: "delete_candidate.php",
			data: ({id: id,pc_time:pc_time,pc_date:pc_date,data_name:data_name,user_name:user_name}),
			cache: false,
			success: function(html){
			$(".del"+id).fadeOut('slow'); 
			} 
			}); 
			}else{
			return false;}
		});				
	});

</script>

