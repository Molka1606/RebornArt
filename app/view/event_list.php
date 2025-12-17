<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Admin Panel - Events</title>
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

  <!-- SIDEBAR -->
  <div class="sidebar">
    <h3>RebornArt</h3>
    <a href="/res_event/public/index.php?action=dashboard"><i class="fa fa-chart-line me-2"></i> Dashboard</a>
    <a href="/res_event/public/index.php?action=list"><i class="fa fa-table me-2"></i> Reservations</a>
    <a href="/res_event/public/index.php?action=event_list"><i class="fa fa-calendar me-2"></i> Events</a>
    <a href="/res_event/public/index.php?action=home"><i class="fa fa-home me-2"></i> Back to Home</a>
  </div>

<div class="main fade-in">
<h2 class="mb-4 slide-in-left">Events</h2>
<a href="/res_event/public/index.php?action=event_add" class="btn btn-primary mb-3 fade-in">+ Add Event</a>

<table class="table table-bordered glass" style="background: rgba(255, 255, 255, 0.95);">
  <tr>
    <th>ID</th><th>Title</th><th>Location</th><th>Date</th><th>Image</th><th>Actions</th>
  </tr>

  <?php foreach ($events as $e): ?>
    <tr>
      <td><?= $e["id"] ?></td>
      <td><?= htmlspecialchars($e["title"]) ?></td>
      <td><?= htmlspecialchars($e["location"]) ?></td>
      <td><?= $e["event_date"] ?></td>
      <td>
        <?php if ($e["image"]): ?>
          <img src="/res_event/uploads/events/<?= $e["image"] ?>" width="80">
        <?php endif; ?>
      </td>
      <td>
        <a class="btn btn-warning btn-sm"
        href="/res_event/public/index.php?action=event_edit&id=<?= $e["id"] ?>">Edit</a>

        <a class="btn btn-danger btn-sm"
        href="/res_event/public/index.php?action=event_delete&id=<?= $e["id"] ?>"
        onclick="return confirm('Delete event?')">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>

</table>

</div>

</body>
</html>
