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
	
	
	<div id="element" class="hero-body">
	
	<form method="POST" action="save_officer.php" class="form-horizontal">
    <fieldset>
          <legend style="display: flex; justify-content: space-between; align-items: center; color: white;">
    <span>Add Election Officer</span>
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
                            <label class="control-label">First Name:</label>
                            <div class="controls">
                                <input type="text" name="FirstName" class="firstname" required>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Last Name:</label>
                            <div class="controls">
                                <input type="text" name="LastName" class="lastname" required>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Username:</label>
                            <div class="controls">
                                <input type="text" name="UserName" class="username" required>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Password:</label>
                            <div class="controls">
                                <input type="password" name="Password" class="password" required>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Position:</label>
                            <div class="controls">
                                <select name="Position" class="position" required>
                                    <option value="" disabled selected>Select Position</option>
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
                                <button class="btn btn-primary" name="save"><i class="icon-save icon-large"></i> Save</button>
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
	  