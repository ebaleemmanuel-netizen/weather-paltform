<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_role(['admin','ministry']);
require_once __DIR__ . '/../../app/models/User.php';
require_once __DIR__ . '/../../app/models/Farm.php';
require_once __DIR__ . '/../../app/models/Crop.php';
require_once __DIR__ . '/../../app/models/Planting.php';
require_once __DIR__ . '/../../app/models/YieldReport.php';

$farmers = User::countAll($pdo);
$farms = Farm::countAll($pdo);
$crops = Crop::countActive($pdo);
$plantings = Planting::countAll($pdo);
$reports = YieldReport::countAll($pdo);
$associations = (int)$pdo->query("SELECT COUNT(*) c FROM associations")->fetch()['c'];
$announcements = $pdo->query("SELECT a.*, u.full_name FROM announcements a LEFT JOIN users u ON a.created_by = u.id ORDER BY a.created_at DESC LIMIT 5")->fetchAll();

$pageTitle = 'Ministry Dashboard';
require __DIR__ . '/../../app/helpers/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>🏛️ Ministry Dashboard</h3>
  <a href="<?= BASE_URL ?>/ministry/export.php" class="btn btn-outline-success">Export CSV</a>
</div>
<div class="row g-3 mb-4 text-center">
  <div class="col"><div class="card shadow-sm"><div class="card-body"><h4><?= $farmers ?></h4><small class="text-muted">Users</small></div></div></div>
  <div class="col"><div class="card shadow-sm"><div class="card-body"><h4><?= $associations ?></h4><small class="text-muted">Associations</small></div></div></div>
  <div class="col"><div class="card shadow-sm"><div class="card-body"><h4><?= $farms ?></h4><small class="text-muted">Farms</small></div></div></div>
  <div class="col"><div class="card shadow-sm"><div class="card-body"><h4><?= $crops ?></h4><small class="text-muted">Active Crops</small></div></div></div>
  <div class="col"><div class="card shadow-sm"><div class="card-body"><h4><?= $plantings ?></h4><small class="text-muted">Plantings</small></div></div></div>
  <div class="col"><div class="card shadow-sm"><div class="card-body"><h4><?= $reports ?></h4><small class="text-muted">Yield Reports</small></div></div></div>
</div>

<div class="row g-4">
  <div class="col-md-6"><div class="card shadow-sm"><div class="card-body"><h6>National Yield Outcomes</h6><canvas id="outcomeChart"></canvas></div></div></div>
  <div class="col-md-6"><div class="card shadow-sm"><div class="card-body"><h6>Top Failure Causes (Nationwide)</h6><canvas id="reasonsChart"></canvas></div></div></div>
  <div class="col-12"><div class="card shadow-sm"><div class="card-body"><h6>Crop Performance (Nationwide)</h6><canvas id="cropChart"></canvas></div></div></div>
</div>

<div class="card shadow-sm mt-4"><div class="card-body">
  <h6>Recent Announcements</h6>
  <?php if (empty($announcements)): ?><p class="text-muted mb-0">No announcements yet.</p><?php endif; ?>
  <?php foreach ($announcements as $a): ?>
    <div class="border-bottom py-2"><strong><?= htmlspecialchars($a['title']) ?></strong> <small class="text-muted">by <?= htmlspecialchars($a['full_name'] ?? 'System') ?></small>
    <p class="mb-0 small"><?= htmlspecialchars($a['body']) ?></p></div>
  <?php endforeach; ?>
</div></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>const BASE_URL = "<?= BASE_URL ?>"; const ANALYTICS_SCOPE = "all";</script>
<script src="<?= BASE_URL ?>/assets/js/charts.js"></script>
<?php require __DIR__ . '/../../app/helpers/footer.php'; ?>
