// Leaflet map logic + Open-Meteo API calls (via PHP proxy at /api/weather.php)
document.addEventListener('DOMContentLoaded', function () {
  const map = L.map('weatherMap').setView([1.3733, 32.2903], 7); // Uganda center

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  function weatherIcon(code) {
    if (code === null || code === undefined) return '❓';
    if (code === 0) return '☀️';
    if (code <= 3) return '⛅';
    if (code <= 48) return '🌫️';
    if (code <= 67) return '🌧️';
    if (code <= 77) return '❄️';
    if (code <= 99) return '⛈️';
    return '🌤️';
  }

  fetch(BASE_URL + '/api/weather.php')
    .then(res => res.json())
    .then(districts => {
      districts.forEach(d => {
        const w = d.weather || {};
        const marker = L.marker([d.latitude, d.longitude]).addTo(map);
        marker.bindPopup(
          `<strong>${d.name}</strong><br>` +
          `${weatherIcon(w.weather_code)} Temp: ${w.temperature_c ?? 'N/A'}°C<br>` +
          `Rainfall: ${w.rain_mm ?? 'N/A'} mm`
        );
      });
    })
    .catch(err => console.error('Weather fetch failed:', err));
});
