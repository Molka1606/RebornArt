<?php
require_once __DIR__ . '/../model/Event.php';
$model = new Event();
$events = $model->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RebornArt - Events & Reservations</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="/res_event/assets/css/fontawesome.css">
  <link rel="stylesheet" href="/res_event/assets/css/templatemo-space-dynamic.css">
  <link rel="stylesheet" href="/res_event/assets/css/animated.css">
  <link rel="stylesheet" href="/res_event/assets/css/owl.css">
  <link rel="stylesheet" href="/res_event/assets/css/rebornart-events.css" />
  <link rel="stylesheet" href="/res_event/assets/css/modern-styles.css" />
</head>
<body>

<header class="header-area header-sticky wow slideInDown glass" data-wow-duration="0.75s" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
  <div class="container">
    <div class="row">
      <div class="col-12 text-center">
        <a class="logo" href="#" style="text-decoration: none;">
          <h4 style="font-size: 2.5rem; font-weight: bold; background: linear-gradient(45deg, #008037, #23d5ab); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Reborn<span style="color: #008037;">Art</span></h4>
        </a>
        <p class="text-muted" style="font-size: 1.1rem; margin-top: 10px;">Eco-Art Events & Workshops</p>
      </div>
    </div>
  </div>
</header>

<div class="main-banner text-center wow fadeIn fade-in" id="top" data-wow-duration="1s" style="padding: 60px 0; background: linear-gradient(135deg, rgba(0, 128, 55, 0.1), rgba(35, 213, 171, 0.1)); margin: 30px 0; border-radius: 15px;">
  <div class="container">
    <h2 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 15px;">Join Our <em style="color: #008037;">Recycling & Art</em> Events</h2>
    <p style="font-size: 1.2rem; color: #555;">Experience creative workshops that turn waste into artistic creations.</p>
  </div>
</div>

<div class="text-center mt-4 mb-3 fade-in">
  <a href="index.php?action=event_add_public" class="btn btn-success btn-lg pulse">
    + Add Your Event
  </a>
</div>

<section id="events" class="our-blog section">
  <div class="container">

    <div class="section-heading text-center">
      <h2>Upcoming <em>Workshops</em> & <span>Events</span></h2>
    </div>

    <div class="row">

      <?php if (empty($events)): ?>
        <div class="col-12 text-center">
          <p>No events available yet.</p>
        </div>
      <?php endif; ?>

      <?php foreach ($events as $e): ?>
      <div class="col-lg-4 col-md-6 wow fadeInUp fade-in" data-wow-delay="0.2s">
        <div class="item text-center glass" style="background: rgba(255, 255, 255, 0.95); padding: 15px; margin-bottom: 20px;">

          <?php if (!empty($e["image"])): ?>
            <img src="/res_event/uploads/events/<?= htmlspecialchars($e["image"]) ?>"
                 class="img-fluid rounded"
                 style="height: 250px; width: 100%; object-fit: cover;">
          <?php else: ?>
            <img src="/res_event/assets/images/event-3dprint.jpg"
                 class="img-fluid rounded"
                 style="height: 250px; width: 100%; object-fit: cover;">
          <?php endif; ?>

          <div class="inner-content p-3">
            <h4><?= htmlspecialchars($e["title"]) ?></h4>
            <p><?= nl2br(htmlspecialchars($e["description"])) ?></p>

            <p><i class="fa fa-map-marker"></i> <?= htmlspecialchars($e["location"]) ?></p>
            <p><i class="fa fa-calendar"></i> <?= htmlspecialchars($e["event_date"]) ?></p>

            <a href="index.php?action=event&id=<?= $e['id'] ?>"
               class="btn btn-outline-primary btn-sm mt-2">
              View Details
            </a>
>
            <button class="btn btn-primary btn-sm mt-2"
                    onclick="reserveEvent(<?= $e['id'] ?>)">
              Reserve Spot
            </button>
          </div>

        </div>
      </div>
      <?php endforeach; ?>

    </div>

  </div>
</section>

<!-- RESERVATION SECTION -->
<section id="reservation" class="contact-us section fade-in">
  <div class="container">

    <div class="section-heading text-center">
      <h2 style="font-size: 2.2rem; margin-bottom: 10px;">Reserve Your <em style="color: #008037;">Spot</em></h2>
      <p style="font-size: 1.1rem; color: #666;">Book your participation. Limited seats available!</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card glass" style="background: rgba(255, 255, 255, 0.95); padding: 30px; border-radius: 15px; margin-top: 20px;">
          <form id="reservation-form" action="index.php?action=submit" method="post">

          <div class="row">
            <div class="col-md-6">
              <fieldset>
                <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Full Name" required minlength="2" maxlength="100" />
              </fieldset>
            </div>

            <div class="col-md-6">
              <fieldset>
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required maxlength="255" />
              </fieldset>
            </div>

            <div class="col-md-12">
              <fieldset>
                <select name="event_id" id="event_select" class="form-control" required>
                  <option value="">Select Event</option>

                  <?php foreach ($events as $e): ?>
                    <option value="<?= $e["id"] ?>">
                      <?= htmlspecialchars($e["title"]) ?>
                    </option>
                  <?php endforeach; ?>

                </select>
              </fieldset>
            </div>

            <div class="col-md-12">
              <fieldset>
                <textarea name="message" id="message" class="form-control" placeholder="Message or special request..." maxlength="500" rows="4"></textarea>
              </fieldset>
            </div>

            <div class="col-md-12 text-center">
              <button type="submit" class="main-button mt-3">Submit Reservation</button>
            </div>

          </div>

          </form>
        </div>
      </div>
    </div>

  </div>
</section>

<footer class="fade-in" style="padding: 30px 0; margin-top: 50px; background: rgba(255, 255, 255, 0.8);">
  <div class="container text-center">
    <p style="margin: 0; color: #666;">© Copyright RebornArt 2025. All Rights Reserved.</p>
  </div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/res_event/assets/js/owl-carousel.js"></script>
<script src="/res_event/assets/js/animation.js"></script>
<script src="/res_event/assets/js/imagesloaded.js"></script>
<script src="/res_event/assets/js/templatemo-custom.js"></script>
<script src="/res_event/assets/js/form-validation.js"></script>

<script>
function reserveEvent(id) {
    localStorage.setItem("reserve_event", id);
    window.location.href = "#reservation";
}

window.onload = () => {
    const id = localStorage.getItem("reserve_event");
    if (id) {
        document.getElementById("event_select").value = id;
        localStorage.removeItem("reserve_event");
    }
};
</script>

</body>
</html>
