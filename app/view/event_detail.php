<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($event["title"]) ?> - Event Details</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/res_event/assets/css/templatemo-space-dynamic.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="/res_event/assets/css/modern-styles.css" />
<style>
    #event-map {
        height: 400px;
        width: 100%;
        border-radius: 10px;
        margin-top: 20px;
    }
</style>
</head>

<body class="bg-light">

<header class="header-area header-sticky">
    <div class="container text-center p-3">
        <h2>RebornArt</h2>
        <p class="text-muted">Eco-Art Events & Workshops</p>
    </div>
</header>

<section class="container mt-4 fade-in">
    <div class="card shadow glass" style="background: rgba(255, 255, 255, 0.95);">

        <?php if (!empty($event["image"])): ?>
            <img src="/res_event/uploads/events/<?= $event["image"] ?>"
                 class="card-img-top"
                 style="height: 400px; object-fit: cover;">
        <?php endif; ?>

        <div class="card-body">
            <h2 class="card-title"><?= htmlspecialchars($event["title"]) ?></h2>

            <p><i class="fa fa-map-marker"></i>
                <strong>Location:</strong> <?= htmlspecialchars($event["location"]) ?>
            </p>

            <p><i class="fa fa-calendar"></i>
                <strong>Date:</strong> <?= htmlspecialchars($event["event_date"]) ?>
            </p>

            <?php if (!empty($event["latitude"]) && !empty($event["longitude"])): ?>
                <hr>
                <h5 class="mb-3"><i class="fa fa-map"></i> Event Location on Map</h5>
                <div id="event-map"></div>
            <?php endif; ?>

            <hr>

            <p class="card-text fs-5">
                <?= nl2br(htmlspecialchars($event["description"])) ?>
            </p>

            <hr>

            <div class="text-center">
                <a href="/res_event/public/index.php#reservation"
                   class="btn btn-primary btn-lg"
                   onclick="selectEvent(<?= $event['id'] ?>)">
                    Reserve Your Spot
                </a>
            </div>
        </div>
    </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>

function selectEvent(id) {
    localStorage.setItem("reserve_event", id);
}

<?php if (!empty($event["latitude"]) && !empty($event["longitude"])): ?>
document.addEventListener('DOMContentLoaded', function() {
    const lat = <?= floatval($event["latitude"]) ?>;
    const lng = <?= floatval($event["longitude"]) ?>;
    
    const map = L.map('event-map', {
        zoomControl: true,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        boxZoom: false,
        keyboard: false,
        dragging: false,
        touchZoom: false
    }).setView([lat, lng], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    L.marker([lat, lng]).addTo(map)
        .bindPopup('<strong><?= htmlspecialchars($event["title"], ENT_QUOTES) ?></strong><br><?= htmlspecialchars($event["location"], ENT_QUOTES) ?>')
        .openPopup();
});
<?php endif; ?>
</script>

</body>
</html>
