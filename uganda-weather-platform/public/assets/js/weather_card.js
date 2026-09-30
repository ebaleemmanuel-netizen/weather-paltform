// Interactive weather card: district switcher, refresh, °C/°F toggle
(function () {
  let unit = 'C';
  let lastCelsius = null;

  function icon(code) {
    if (code === null || code === undefined) return '❓';
    if (code === 0) return '☀️';
    if (code <= 3) return '⛅';
    if (code <= 48) return '🌫️';
    if (code <= 67) return '🌧️';
    if (code <= 77) return '❄️';
    if (code <= 99) return '⛈️';
    return '🌤️';
  }

  function toF(c) { return c === null || c === undefined ? null : Math.round((c * 9 / 5 + 32) * 10) / 10; }

  function render(data) {
    const w = data.weather || {};
    lastCelsius = w.temperature_c;
    document.getElementById('wcIcon').textContent = icon(w.weather_code);
    const temp = unit === 'C' ? w.temperature_c : toF(w.temperature_c);
    document.getElementById('wcTemp').textContent = (temp ?? '--') + (temp !== null ? '°' + unit : '');
    document.getElementById('wcRain').textContent = w.rain_mm ?? '--';
    document.getElementById('wcHumidity').textContent = (w.humidity ?? '--') + (w.humidity ? '%' : '');
    document.getElementById('wcWind').textContent = w.wind_kph ?? '--';
    document.getElementById('wcRange').textContent = (w.temp_min !== undefined && w.temp_min !== null)
      ? `Today's range: ${w.temp_min}° – ${w.temp_max}°C` : '';
    document.getElementById('wcUpdated').textContent = 'Updated ' + new Date().toLocaleTimeString();
  }

  function load() {
    const district = document.getElementById('wcDistrict').value;
    document.getElementById('wcIcon').textContent = '⏳';
    fetch(BASE_URL_WC + '/api/weather_district.php?district=' + encodeURIComponent(district))
      .then(res => res.json())
      .then(data => {
        if (data.error) {
          document.getElementById('wcIcon').textContent = '⚠️';
          return;
        }
        render(data);
      })
      .catch(() => { document.getElementById('wcIcon').textContent = '⚠️'; });
  }

  document.addEventListener('DOMContentLoaded', function () {
    if (!document.getElementById('weatherCard')) return;
    load();
    document.getElementById('wcDistrict').addEventListener('change', load);
    document.getElementById('wcRefresh').addEventListener('click', load);
    document.getElementById('wcUnitToggle').addEventListener('click', function () {
      unit = unit === 'C' ? 'F' : 'C';
      this.textContent = '°' + unit;
      if (lastCelsius !== null) {
        const temp = unit === 'C' ? lastCelsius : toF(lastCelsius);
        document.getElementById('wcTemp').textContent = temp + '°' + unit;
      }
    });
  });
})();
