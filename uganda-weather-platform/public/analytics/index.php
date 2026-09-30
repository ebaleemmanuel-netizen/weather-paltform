<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_login();
$pageTitle = 'Analytics';
require __DIR__ . '/../../app/helpers/header.php';
?>
<h3 class="mb-3">📊 Analytics</h3>
<p class="text-muted">Yield outcomes, top failure causes, and crop performance.</p>
<div class="row g-4">
  <div class="col-md-6"><div class="card shadow-sm"><div class="card-body"><h6>Yield Success vs Failure</h6><canvas id="outcomeChart"></canvas></div></div></div>
  <div class="col-md-6"><div class="card shadow-sm"><div class="card-body"><h6>Top Failure Causes</h6><canvas id="reasonsChart"></canvas></div></div></div>
  <div class="col-12"><div class="card shadow-sm"><div class="card-body"><h6>Crop Performance</h6><canvas id="cropChart"></canvas></div></div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>const BASE_URL = "<?= BASE_URL ?>"; const ANALYTICS_SCOPE = "mine";</script>
<script src="<?= BASE_URL ?>/assets/js/charts.js"></script>
<?php require __DIR__ . '/../../app/helpers/footer.php'; ?>
