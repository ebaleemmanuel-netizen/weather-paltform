<?php
require_once __DIR__ . '/../app/config/db.php';
$pageTitle = 'Home';
require __DIR__ . '/../app/helpers/header.php';

$districts = (int)$pdo->query("SELECT COUNT(*) c FROM districts")->fetch()['c'];
$crops = (int)$pdo->query("SELECT COUNT(*) c FROM crops WHERE status='active'")->fetch()['c'];
require_once __DIR__ . '/../app/models/User.php';
$farmers = User::countAll($pdo);
?>
<div class="p-5 mb-4 bg-success bg-gradient text-white rounded-3 shadow-sm">
  <h1 class="display-6 fw-bold">Uganda Agricultural Weather Platform</h1>
  <p class="col-md-8 fs-5">Live district weather, personalized crop recommendations, and yield tracking for Uganda's farmers &mdash; built to help the Ministry of Agriculture see what's really working in the field.</p>
  <?php if (!is_logged_in()): ?>
  <a class="btn btn-light btn-lg mt-2" href="<?= BASE_URL ?>/register.php">Get Started</a>
  <?php endif; ?>
</div>

<div class="row g-4 text-center mb-4">
  <div class="col-md-4">
    <div class="card shadow-sm h-100"><div class="card-body">
      <h2 class="display-5 text-success"><?= $districts ?></h2>
      <p class="text-muted mb-0">Districts covered</p>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm h-100"><div class="card-body">
      <h2 class="display-5 text-success"><?= $crops ?></h2>
      <p class="text-muted mb-0">Crops in advisory engine</p>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm h-100"><div class="card-body">
      <h2 class="display-5 text-success"><?= $farmers ?></h2>
      <p class="text-muted mb-0">Registered users</p>
    </div></div>
  </div>
</div>

<div class="row g-4">
  <div class="col-md-4"><div class="card h-100 shadow-sm"><div class="card-body">
    <h5>🗺️ Live Weather Map</h5><p class="text-muted">See real-time temperature and rainfall across all 12 districts.</p>
    <a href="<?= BASE_URL ?>/map.php" class="btn btn-outline-success btn-sm">Open map</a>
  </div></div></div>
  <div class="col-md-4"><div class="card h-100 shadow-sm"><div class="card-body">
    <h5>🌱 Crop Advisor</h5><p class="text-muted">Get ranked crop recommendations for your farm based on weather and soil.</p>
    <a href="<?= BASE_URL ?>/recommend.php" class="btn btn-outline-success btn-sm">Get advice</a>
  </div></div></div>
  <div class="col-md-4"><div class="card h-100 shadow-sm"><div class="card-body">
    <h5>📊 Yield Analytics</h5><p class="text-muted">Track planting outcomes and see what's driving success or failure.</p>
    <a href="<?= BASE_URL ?>/analytics/index.php" class="btn btn-outline-success btn-sm">View analytics</a>
  </div></div></div>
</div>

<?php require __DIR__ . '/../app/helpers/footer.php'; ?>
