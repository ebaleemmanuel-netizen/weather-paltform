<?php
require_once __DIR__ . '/../app/config/db.php';
$pageTitle = 'Weather Map';
require __DIR__ . '/../app/helpers/header.php';
?>
<h3 class="mb-3">Live Weather Map</h3>
<p class="text-muted">Current temperature and rainfall for all 12 districts, sourced from Open-Meteo.</p>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<div id="weatherMap" style="height:520px;border-radius:8px;" class="shadow-sm"></div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>const BASE_URL = "<?= BASE_URL ?>";</script>
<script src="<?= BASE_URL ?>/assets/js/map.js"></script>
<?php require __DIR__ . '/../app/helpers/footer.php'; ?>
