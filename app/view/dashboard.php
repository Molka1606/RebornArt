<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - RebornArt</title>

  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  
  <link rel="stylesheet" href="/res_event/assets/style.css">
  <link rel="stylesheet" href="/res_event/assets/css/modern-styles.css">

  <style>
    
    .sidebar {
      height: 100vh;
      width: 240px;
      background: #111;
      color: white;
      position: fixed;
      left: 0;
      top: 0;
      padding: 20px;
    }
    .sidebar h3 {
      font-weight: 700;
      color: #008037;
      margin-bottom: 30px;
    }
    .sidebar a {
      display: block;
      color: #eee;
      padding: 12px 10px;
      margin-bottom: 8px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 16px;
      transition: 0.3s;
    }
    .sidebar a:hover {
      background: #008037;
      color: #fff;
    }

    
    .main {
      margin-left: 260px;
      padding: 25px;
    }

    .dash-card {
      border-radius: 14px;
      padding: 25px;
      color: white;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .bg1 { background: #008037; }
    .bg2 { background: #0d6efd; }
    .bg3 { background: #d9534f; }
    .bg4 { background: #f0ad4e; }

    .chart-box {
      height: 350px;
      border-radius: 15px;
      background: white;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 20px;
      margin-top: 30px;
    }
  </style>
</head>

<body>

  
  <div class="sidebar">
    <h3>RebornArt</h3>

    <a href="/res_event/public/index.php?action=dashboard"><i class="fa fa-chart-line me-2"></i> Dashboard</a>
    <a href="/res_event/public/index.php?action=list"><i class="fa fa-table me-2"></i> Reservations</a>
    <a href="/res_event/public/index.php?action=event_list"><i class="fa fa-calendar me-2"></i> Events</a>
    <a href="/res_event/public/index.php?action=home"><i class="fa fa-home me-2"></i> Back to Home</a>
  </div>


  <div class="main fade-in">

    <h2 class="fw-bold mb-4 slide-in-left">Admin Dashboard</h2>

    <div class="row g-4">

      <div class="col-md-3">
        <a href="/res_event/public/index.php?action=list" style="text-decoration: none; color: inherit;">
          <div class="dash-card bg1" style="cursor: pointer;">
            <h4>Total Reservations</h4>
            <h2><?= $total_reservations ?? 0 ?></h2>
          </div>
        </a>
      </div>

      <div class="col-md-3">
        <a href="/res_event/public/index.php?action=event_list" style="text-decoration: none; color: inherit;">
          <div class="dash-card bg2" style="cursor: pointer;">
            <h4>Total Events</h4>
            <h2><?= $total_events ?? 0 ?></h2>
          </div>
        </a>
      </div>

      <div class="col-md-3">
        <a href="/res_event/public/index.php?action=list" style="text-decoration: none; color: inherit;">
          <div class="dash-card bg3" style="cursor: pointer;">
            <h4>Pending Messages</h4>
            <h2><?= $pending_messages ?? 0 ?></h2>
          </div>
        </a>
      </div>

      <div class="col-md-3">
        <div class="dash-card bg4">
          <h4>Today Visits</h4>
          <h2>124</h2>
        </div>
      </div>

    </div>

    
    <div class="chart-box mt-4">
      <h4 class="mb-3 fw-bold"><i class="fa fa-chart-area me-2"></i> Activity Overview</h4>
      <canvas id="statsChart"></canvas>
    </div>

  </div>

  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    
    const ctx = document.getElementById('statsChart');

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        datasets: [{
          label: 'Reservations per Day',
          data: [5, 3, 7, 8, 6, 9, 4],
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        tension: 0.3
      }
    });
  </script>

</body>
</html>
