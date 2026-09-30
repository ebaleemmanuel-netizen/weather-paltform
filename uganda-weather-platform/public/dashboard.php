<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
require_login();
require_once __DIR__ . '/../app/models/Farm.php';
require_once __DIR__ . '/../app/models/Planting.php';

$user = current_user();
$pageTitle = 'Dashboard';
require __DIR__ . '/../app/helpers/header.php';

if (in_array($user['role'], ['farmer','association_admin','extension_officer'])) {
    $farms = Farm::forUser($pdo, $user['id']);
    $plantings = Planting::forFarmer($pdo, $user['id']);
}
?>
<h3 class="mb-4">Welcome, <?= htmlspecialchars($user['full_name']) ?> 👋</h3>

<?php require __DIR__ . '/../app/helpers/weather_card.php'; ?>

<?php if (in_array($user['role'], ['farmer','association_admin','extension_officer','researcher'])): ?>
<div class="row g-4">
  <div class="col-md-4"><div class="card shadow-sm text-center h-100"><div class="card-body">
    <h2 class="text-success"><?= count($farms ?? []) ?></h2><p class="text-muted">My farms</p>
    <a href="<?= BASE_URL ?>/farms/list.php" class="btn btn-sm btn-outline-success">Manage farms</a>
  </div></div></div>
  <div class="col-md-4"><div class="card shadow-sm text-center h-100"><div class="card-body">
    <h2 class="text-success"><?= count($plantings ?? []) ?></h2><p class="text-muted">Plantings logged</p>
    <a href="<?= BASE_URL ?>/reports/list.php" class="btn btn-sm btn-outline-success">View reports</a>
  </div></div></div>
  <div class="col-md-4"><div class="card shadow-sm text-center h-100"><div class="card-body">
    <h2 class="text-success">🌱</h2><p class="text-muted">Need crop advice?</p>
    <a href="<?= BASE_URL ?>/recommend.php" class="btn btn-sm btn-outline-success">Ask the advisor</a>
  </div></div></div>
</div>
<?php endif; ?>

<?php if (in_array($user['role'], ['admin','ministry'])): ?>
<div class="alert alert-success mt-4">You have administrative access. <a href="<?= BASE_URL ?>/ministry/dashboard.php">Open the Ministry Dashboard &rarr;</a></div>
<?php endif; ?>

<?php require __DIR__ . '/../app/helpers/footer.php'; ?>
