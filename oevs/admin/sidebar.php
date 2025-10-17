<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Dropdown Navbar Example</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 3 CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <!-- Optional: Font Awesome (for icons if needed) -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

  <style>
    .nav-pills > li > a {
      color: #fff;
      background-color: #337ab7;
      margin-right: 5px;
    }
    .nav-pills > li.active > a,
    .nav-pills > li > a:hover {
      background-color: #23527c;
    }
    .dropdown-menu > li > a {
      color: #333;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Election Management System</h2>

  <ul class="nav nav-pills">
    <li class="active">
      <a href="home.php"><i class="fa fa-home"></i> Dashboard</a>
    </li>

    <li class="dropdown">
      <a class="dropdown-toggle" data-toggle="dropdown" href="#">
        <i class="fa fa-cogs"></i> Manage <span class="caret"></span>
      </a>
      <ul class="dropdown-menu">
        <li><a href="candidate_list.php"><i class="fa fa-users"></i> Candidates List</a></li>
        <li><a href="voter_list.php"><i class="fa fa-user"></i> Voters List</a></li>
        <li><a href="election_officer_list.php"><i class="fa fa-user-secret"></i> Election Officer List</a></li>
      </ul>
    </li>

    <li class="dropdown">
      <a class="dropdown-toggle" data-toggle="dropdown" href="#">
        <i class="fa fa-bar-chart"></i> Reports <span class="caret"></span>
      </a>
      <ul class="dropdown-menu">
        <li><a href="canvassing_report.php"><i class="fa fa-book"></i> Vote Count Report</a></li>
        <li><a href="history.php"><i class="fa fa-history"></i> History Log</a></li>
        <li><a href="voter_verification.php"><i class="fa fa-check-circle"></i> Voters Verification</a></li>
      </ul>
    </li>

    <li class="dropdown">
      <a class="dropdown-toggle" data-toggle="dropdown" href="#">
        <i class="fa fa-ellipsis-h"></i> Others <span class="caret"></span>
      </a>
      <ul class="dropdown-menu">
        <li><a href="complaint.php"><i class="fa fa-exclamation-circle"></i> Complaint</a></li>
        <li><a href="profile.php"><i class="fa fa-user"></i> Profile</a></li>
      </ul>
    </li>
  </ul>
</div>

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Bootstrap 3 JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</body>
</html>
