<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel - Reservations</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
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
  <h2 class="mb-4 slide-in-left">Reservation List</h2>

  <?php if (empty($reservations)): ?>
    <p class="fade-in">No reservations yet.</p>
  <?php else: ?>

  <table class="table table-bordered glass" style="background: rgba(255, 255, 255, 0.95);">
    <thead>
      <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Event</th><th>Message</th><th>Actions</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($reservations as $r): ?>
      <tr>
        <td><?= $r["id"] ?></td>
        <td><?= htmlspecialchars($r["full_name"]) ?></td>
        <td><?= htmlspecialchars($r["email"]) ?></td>

        <td>
          <?= isset($eventsById[$r["event_id"]]) ? htmlspecialchars($eventsById[$r["event_id"]]) : "Event #" . $r["event_id"] ?>
        </td>

        <td><?= htmlspecialchars($r["message"]) ?></td>

        <td>
          <a href="/res_event/public/index.php?action=edit&id=<?= $r['id'] ?>"
             class="btn btn-warning btn-sm">Edit</a>

          <a href="/res_event/public/index.php?action=delete&id=<?= $r['id'] ?>"
             class="btn btn-danger btn-sm"
             onclick="return confirm('Delete this reservation?')">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php endif; ?>
</div>

</body>
</html>
