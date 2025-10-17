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

    <?php include('homesidebar.php'); ?>           <!-- 🔹 Navbar -->

    <div id="element" class="hero-body">
      <div class="demo_jui">
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="log" class="jtable">
          <thead>
            <tr>
              <th>Date</th>
              <th>Action</th>
              <th>Data</th>
              <th>User</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $history_query = mysqli_query($conn, "SELECT * FROM history");
            while ($history_rows = mysqli_fetch_array($history_query)) { 
              $id = $history_rows['history_id'];
              $date_raw = $history_rows['date'];
              $date_formatted = (!empty($date_raw)) ? date("F j, Y, g:i A", strtotime($date_raw)) : 'N/A';
            ?>
            <tr class="del<?php echo $id; ?>">
              <td>&nbsp;<?php echo $date_formatted; ?></td>
              <td><?php echo $history_rows['action']; ?></td>
              <td><?php echo $history_rows['data']; ?></td>
              <td>&nbsp;<?php echo $history_rows['user']; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php include('footer.php'); ?>
  </div>
</div>
</body>
</html>
