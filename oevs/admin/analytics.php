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
    <?php include('homesidebar.php'); ?>

    <section style="margin-top: 20px;">
      <!-- Admin Actions Dropdown -->
      <div class="admin-actions-dropdown">
        <button class="admin-actions-btn" type="button">
          <i class="icon-table icon-large" style="margin-right: 8px;"></i> Admin Actions
          <span class="caret"></span>
        </button>
        <ul class="admin-dropdown-menu">
          <li>
            <a href="result.php">
              <i class="icon-table icon-large" style="margin-right: 8px;"></i> Election Result
            </a>
          </li>
          <li>
            <a href="winningresult.php">
              <i class="icon-table icon-large" style="margin-right: 8px;"></i> Final Result
            </a>
          </li>
          <li>
            <a href="backupnreset.php">
              <i class="icon-table icon-large" style="margin-right: 8px;"></i> Backup and Reset
            </a>
          </li>
          <li>
            <a href="dashboard.php">
              <i class="icon-table icon-large" style="margin-right: 8px;"></i> Analytics
            </a>
          </li>
        </ul>
      </div>
    </section>

    <!-- 📅 Calendar Section -->
    <?php
    $month = date('n');
    $year = date('Y');
    $today = date('j');
    $first_day = mktime(0, 0, 0, $month, 1, $year);
    $days_in_month = date('t', $first_day);
    $start_day = date('w', $first_day);
    $month_name = date('F', $first_day);
    ?>

    <style>
      /* Admin Actions Dropdown */
      .admin-actions-dropdown {
        position: relative;
        display: inline-block;
        margin-left: 20px;
      }

      .admin-actions-btn {
        background-color: #002f6c;
        color: #fff;
        border: none;
        padding: 12px 24px;
        font-size: 18px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        user-select: none;
        transition: background-color 0.3s ease;
      }
      .admin-actions-btn .caret {
        border-top: 6px solid #fff;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        display: inline-block;
        margin-left: 8px;
      }
      .admin-actions-btn:hover {
        background-color: #0056b3;
      }

      .admin-dropdown-menu {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        background-color: #fff;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        border-radius: 10px;
        min-width: 220px;
        padding: 8px 0;
        z-index: 999;
      }

      .admin-dropdown-menu li {
        list-style: none;
      }

      .admin-dropdown-menu li a {
        display: flex;
        align-items: center;
        color: #002f6c;
        padding: 12px 24px;
        text-decoration: none;
        font-weight: 600;
        transition: background-color 0.3s, color 0.3s;
        border-radius: 8px;
      }

      .admin-dropdown-menu li a i {
        font-size: 18px;
      }

      .admin-dropdown-menu li a:hover {
        background-color: #002f6c;
        color: #fff;
      }

      /* Show dropdown on hover */
      .admin-actions-dropdown:hover .admin-dropdown-menu {
        display: block;
      }

      /* Calendar styling */
      .calendar-wrapper {
        max-width: 600px;
        margin: 40px auto 80px;
        padding: 25px;
        background-color: #fefefe;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      }

      .calendar-title {
        text-align: center;
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #002f6c;
      }

      .calendar {
        width: 100%;
        border-collapse: separate;
        border-spacing: 10px;
      }

      .calendar th {
        background-color: #002f6c;
        color: white;
        padding: 12px;
        font-size: 16px;
        border-radius: 8px 8px 0 0;
      }

      .calendar td {
        width: 14.28%;
        height: 70px;
        text-align: center;
        vertical-align: middle;
        font-size: 16px;
        color: #002f6c;
        background-color: #e8f0fe;
        border-radius: 12px;
        transition: background-color 0.3s, color 0.3s;
        cursor: pointer;
        user-select: none;
      }

      .calendar td:hover {
        background-color: #0056b3;
        color: #fff;
      }

      .calendar .today {
        background-color: #0056b3;
        color: #fff;
        font-weight: bold;
        border: 2px solid #001d3d;
        box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.15);
      }
    </style>

    <div class="calendar-wrapper">
      <div class="calendar-title"><?php echo "$month_name $year"; ?></div>
      <table class="calendar">
        <tr>
          <th>Sun</th>
          <th>Mon</th>
          <th>Tue</th>
          <th>Wed</th>
          <th>Thu</th>
          <th>Fri</th>
          <th>Sat</th>
        </tr>
        <tr>
          <?php
          // Empty cells before first day
          for ($i = 0; $i < $start_day; $i++) {
            echo "<td></td>";
          }

          for ($day = 1; $day <= $days_in_month; $day++) {
            $is_today = ($day == $today) ? 'today' : '';
            echo "<td class='$is_today'>$day</td>";
            if (($start_day + $day) % 7 == 0 && $day != $days_in_month) {
              echo "</tr><tr>";
            }
          }

          // Empty cells after last day
          $remaining = (7 - (($start_day + $days_in_month) % 7)) % 7;
          for ($i = 0; $i < $remaining; $i++) {
            echo "<td></td>";
          }
          ?>
        </tr>
      </table>
    </div>

    <?php include('footer.php'); ?>
  </div>
</div>

</body>
</html>
