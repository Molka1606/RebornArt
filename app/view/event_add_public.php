<!DOCTYPE html>
<html>
<head>
  <title>Submit Your Event</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/res_event/assets/css/modern-styles.css" />

  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  <style>
      #map {
          height: 350px;
          width: 100%;
          background: #eee;
          border-radius: 10px;
          margin-bottom: 20px;
      }
  </style>
</head>

<body class="bg-light">

<div class="container mt-5">

  <div class="card shadow p-4 mx-auto glass fade-in" style="max-width: 700px; background: rgba(255, 255, 255, 0.95);">
    <h2 class="text-center mb-4 slide-in-left">Submit Your Event</h2>

    <form action="/res_event/public/index.php?action=event_add_public_submit"
          method="POST"
          enctype="multipart/form-data">

      <label class="form-label">Event Title <span class="text-danger">*</span></label>
      <input type="text" name="title" id="title" class="form-control mb-3" required minlength="3" maxlength="200" placeholder="Enter event title">

      <label class="form-label">Location (text) <span class="text-danger">*</span></label>
      <input type="text" name="location" id="location" class="form-control mb-3" placeholder="Tunis, Sousse... (or click map to auto-fill)" required minlength="2" maxlength="255">

      <h5 class="mt-3">Select Location on Map</h5>
      <p class="text-muted">Click on the map to set the event's exact position. The location field will be auto-filled.</p>

      <div id="map"></div>

      <input type="hidden" id="lat" name="latitude">
      <input type="hidden" id="lng" name="longitude">


      <label class="form-label">Event Date <span class="text-danger">*</span></label>
      <input type="date" name="event_date" id="event_date" class="form-control mb-3" required min="<?= date('Y-m-d') ?>">

      <label class="form-label">Event Description <span class="text-danger">*</span></label>
      <textarea name="description" id="description" class="form-control mb-3" rows="4" required maxlength="1000" placeholder="Enter event description"></textarea>

      <label class="form-label">Event Image</label>
      <input type="file" name="image" id="image" class="form-control mb-4" accept="image/*" data-max-size="5242880">
      <small class="text-muted">Accepted formats: JPG, PNG. Max size: 5MB</small>

      <button class="btn btn-success w-100">Submit Event</button>

    </form>

  </div>

  <div class="text-center mt-3">
    <a href="index.php" class="btn btn-secondary">Back to Home</a>
  </div>

</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/res_event/assets/js/form-validation.js"></script>

<script>
    console.log("Leaflet loaded:", L ? "YES" : "NO");

    let map = L.map('map').setView([36.8065, 10.1815], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    let marker = null;

    async function getLocationName(lat, lng) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10&addressdetails=1`);
            const data = await response.json();
            
            if (data && data.address) {
                const addr = data.address;
            
                let locationName = '';
                
                if (addr.city) locationName = addr.city;
                else if (addr.town) locationName = addr.town;
                else if (addr.village) locationName = addr.village;
                else if (addr.municipality) locationName = addr.municipality;
                
                if (addr.state && locationName) {
                    locationName += ', ' + addr.state;
                } else if (addr.state) {
                    locationName = addr.state;
                }
                
                if (addr.country && locationName) {
                    locationName += ', ' + addr.country;
                } else if (addr.country) {
                    locationName = addr.country;
                }
                
                if (!locationName && data.display_name) {
                    locationName = data.display_name.split(',')[0]; 
                }
                
                return locationName || 'Selected Location';
            }
            return 'Selected Location';
        } catch (error) {
            console.error('Geocoding error:', error);
            return 'Selected Location';
        }
    }

    map.on('click', async function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (marker) marker.setLatLng([lat, lng]);
        else marker = L.marker([lat, lng]).addTo(map);

        document.getElementById("lat").value = lat;
        document.getElementById("lng").value = lng;
        
    
        const locationInput = document.getElementById("location");
        locationInput.value = "Loading...";
        const locationName = await getLocationName(lat, lng);
        locationInput.value = locationName;
    });
</script>

