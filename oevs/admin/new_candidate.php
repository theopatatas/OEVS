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
	
	
	<div id="element" class="hero-body" style="position: relative;">
    <a href="candidate_list.php" 
       class="btn"
       style="position: absolute; top: 15px; right: 20px;
              background-color: #ffffff; color: #007bff; border: 2px solid #007bff; 
              padding: 8px 14px; text-decoration: none; border-radius: 5px; 
              font-weight: bold; box-shadow: 0 0 5px rgba(0,0,0,0.3);">
        <i class="icon-arrow-left icon-large"></i> Back
    </a>
    <h2 style="text-align:center; color:#fff; margin-bottom:25px; margin-top:15px;">
    <i class="" style="margin-right:10px;"></i> Add Candidate
  </h2>
	<form method="POST" action="save_candidate.php" class="form-horizontal" enctype="multipart/form-data">
	<input type="hidden" name="user_name" class="user_name" value="<?php echo $_SESSION['User_Type']; ?>"/>
    <fieldset>
   	       <div class="pagination">
    <ul>
  
    </ul>
	

    </div>

	<div class="pagination">
	
	</div>
	<div class="candidate_margin">
	<ul class="thumbnails_new_voter">
    <li class="span3">
    <div class="thumbnail_new_voter">
   
	<div class="control-group">
    <label class="control-label" for="input01">FirstName:</label>
    <div class="controls">
    <input type="text" name="rfirstname" class="rfirstname">
    </div>
    </div>
	
	<div class="control-group">
    <label class="control-label" for="input01">LastName:</label>
    <div class="controls">
    <input type="text" name="rlastname" class="rlastname">
    </div>
    </div>
	
	<div class="control-group">
    <label class="control-label" for="input01">Gender:</label>
    <div class="controls">
   <select name="rgender" class="rgender" id="span2">
	<option>Male</option>
	<option>Female</option>
	
	</select>
    </div>
    </div>
	
	<div class="control-group">
    <label class="control-label" for="input01">Year Level:</label>
    <div class="controls">
   <select name="ryear" class="ryear" id="span2">
	<option>1st year</option>
	<option>2nd year</option>
	<option>3rd year</option>
	<option>4th year</option>
	</select>
    </div>
    </div>
	
	<div class="control-group">
    <label class="control-label" for="input01">MiddleName:</label>
    <div class="controls">
    <input type="text" name="rmname" class="rmnane">
    </div>
    </div>
	

	
	<div class="control-group">
    <label class="control-label" for="input01">Position:</label>
    <div class="controls">
   <select name="rposition" class="rposition" id="span22">
	<option>President</option>
	<option>Vice-President</option>
	<option>Governor</option>
	<option>Vice-Governor</option>
	<option>Secretary</option>
	<option>Treasurer</option>
	<option>Social-Media Officer</option>
	<option>Representative</option>
	
	</select>
    </div>
    </div>
	
	<div class="control-group">
    <label class="control-label" for="input01">Party:</label>
    <div class="controls">
    <input type="text" name="party" class="party">
    </div>
    </div>
	
	<div class="control-group">
    <label class="control-label" for="qualification">Qualification</label>
    <div class="controls">
        <textarea id="qualification" name="qualification" class="font" rows="5" cols="50" placeholder="Enter candidate qualification..."></textarea>
    </div>
</div>


	<div class="control-group">
	<label class="control-label" for="input01">Image:</label>
    <div class="controls">
	<input type="file" name="image" class="font"> 
    </div>
    </div>

	
	
	<div class="control-group">
    <div class="controls">
		<button class="btn btn-primary" name="save"><i class="icon-save icon-large"></i>Save</button>
    </div>
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
	  