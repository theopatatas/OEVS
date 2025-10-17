<!-- NAVBAR -->
<div class="navbar navbar-fixed-top">
  <div class="navbar-inner" style="background-color: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.5);">
    <div class="navbar-container" style="max-width: 1100px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0px 0px; flex-wrap: wrap;">

      <!-- Left side: Logo and Brand -->
      <div class="brand-container" style="display: flex; align-items: center; gap: 0px;">
        <a class="brand" href="#">
          <img src="images/au.png" width="60" height="60" style="display: block;">
        </a>
        <div class="brand-text" style="text-align: left;">
          <h2 class="site-title" style="color: #000; margin: 0; font-size: 22px; transition: color 0.3s;"
              onmouseover="this.style.color='#196f38'" 
              onmouseout="this.style.color='#000'">
            ONLINE ELECTION VOTING SYSTEM
          </h2>
          <div class="chmsc_nav" style="font-size: 16px; color: #000; transition: color 0.3s; cursor: pointer;"
               onmouseover="this.style.color='#196f38'" 
               onmouseout="this.style.color='#000'">
            Phinma Araullo University
          </div>
        </div>
      </div>

      <!-- Right side: Date and Time -->
      <div class="date-time-box" style="background-color: #fff; padding: 10px 15px; border-radius: 8px; text-align: right; min-width: 180px;">
        <div style="font-weight: bold; font-size: 22px; color: #333;">
          <span id="time-now"></span>
        </div>
        <div style="font-size: 16px; color: #555;">
          <span id="date-now"></span>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Script for Date and Time -->
<script>
  function updateDateTime() {
    const now = new Date();
    const date = now.toLocaleDateString('en-PH', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
    const time = now.toLocaleTimeString('en-PH', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    });

    document.getElementById('date-now').textContent = date;
    document.getElementById('time-now').textContent = time;
  }

  updateDateTime();
  setInterval(updateDateTime, 1000);
</script>

<!-- Responsive Design -->
<style>
  @media (max-width: 768px) {
    .navbar-container {
      flex-direction: column;
      align-items: center !important;
      text-align: center !important;
    }

    .brand-container {
      flex-direction: column;
      align-items: center !important;
      text-align: center !important;
    }

    .brand-text {
      text-align: center !important;
    }

    .site-title {
      font-size: 18px !important;
      text-align: center !important;
    }

    .chmsc_nav {
      font-size: 14px !important;
      text-align: center !important;
      width: 100% !important;
    }

    .date-time-box {
      margin-top: 10px;
      text-align: center !important;
      min-width: unset !important;
    }
  }
</style>
