<!DOCTYPE html>
<html>
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="/res_event/assets/css/modern-styles.css" />
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
<body class="p-4">

<div class="container" style="max-width: 800px; margin: 0 auto;">
  <div class="card glass fade-in" style="background: rgba(255, 255, 255, 0.95); padding: 30px; margin-top: 20px;">
    <h2 class="mb-4 slide-in-left">Add Event</h2>

    <form action="/res_event/public/index.php?action=event_add_submit"
          method="post" enctype="multipart/form-data">

  <label>Title <span class="text-danger">*</span></label>
  <input name="title" id="title" class="form-control mb-2" required minlength="3" maxlength="200" placeholder="Enter event title">

  <label>Location (text) <span class="text-danger">*</span></label>
  <input name="location" id="location" class="form-control mb-2" placeholder="e.g., Tunis, Sousse... (or click map to auto-fill)" required minlength="2" maxlength="255">

  <label class="mt-3">Select Location on Map (optional)</label>
  <p class="text-muted small">Click on the map to set the event's exact coordinates. The location field will be auto-filled.</p>
  <div id="map"></div>
  <input type="hidden" id="lat" name="latitude">
  <input type="hidden" id="lng" name="longitude">

  <label>Date <span class="text-danger">*</span></label>
  <input type="date" name="event_date" id="event_date" class="form-control mb-2" required min="<?= date('Y-m-d') ?>">

  <label>Description</label>
  <textarea name="description" id="description" class="form-control mb-2" rows="5" maxlength="1000" placeholder="Enter event description"></textarea>

  <label>Image</label>
  <input type="file" name="image" id="image" class="form-control mb-3" accept="image/*" data-max-size="5242880">
  <small class="text-muted">Accepted formats: JPG, PNG. Max size: 5MB</small>

      <button class="btn btn-success">Save</button>
    </form>

    <a class="btn btn-secondary mt-3"
       href="/res_event/public/index.php?action=event_list">Back</a>
  </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/res_event/assets/js/form-validation.js"></script>
<script>
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

</body>
</html>
