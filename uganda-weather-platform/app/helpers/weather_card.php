<?php
// Include this on any page (after $user is available) to render the interactive weather card.
// Usage: require __DIR__ . '/../../app/helpers/weather_card.php';
$districts = $pdo->query("SELECT name FROM districts ORDER BY name")->fetchAll();
?>
<div class="card shadow-sm mb-4 weather-card" id="weatherCard">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
      <div>
        <h6 class="mb-1 text-uppercase text-muted small">Current Weather</h6>
        <div class="d-flex align-items-center gap-2">
          <select id="wcDistrict" class="form-select form-select-sm" style="width:auto;">
            <?php foreach ($districts as $d): ?>
              <option <?= $d['name'] === ($user['district'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <button id="wcUnitToggle" class="btn btn-sm btn-outline-secondary" type="button" title="Toggle °C/°F">°C</button>
          <button id="wcRefresh" class="btn btn-sm btn-outline-success" type="button" title="Refresh">⟳</button>
        </div>
      </div>
      <div class="text-end">
        <div id="wcIcon" style="font-size:2.5rem;line-height:1;">…</div>
      </div>
    </div>
    <div class="row mt-3 text-center" id="wcBody">
      <div class="col-3"><div class="fw-bold fs-4" id="wcTemp">--</div><small class="text-muted">Temp</small></div>
      <div class="col-3"><div class="fw-bold fs-4" id="wcRain">--</div><small class="text-muted">Rain (mm)</small></div>
      <div class="col-3"><div class="fw-bold fs-4" id="wcHumidity">--</div><small class="text-muted">Humidity</small></div>
      <div class="col-3"><div class="fw-bold fs-4" id="wcWind">--</div><small class="text-muted">Wind (km/h)</small></div>
    </div>
    <div id="wcRange" class="text-center text-muted small mt-2"></div>
    <div id="wcUpdated" class="text-center text-muted small mt-1"></div>
  </div>
</div>
<script>const BASE_URL_WC = "<?= BASE_URL ?>";</script>
<script src="<?= BASE_URL ?>/assets/js/weather_card.js"></script>
