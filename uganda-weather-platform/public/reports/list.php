<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_login();
require_once __DIR__ . '/../../app/models/Planting.php';

$user = current_user();
$plantings = Planting::forFarmer($pdo, $user['id']);
$pageTitle = 'My Plantings';
require __DIR__ . '/../../app/helpers/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>My Plantings</h3>
  <div><a href="<?= BASE_URL ?>/reports/plant.php" class="btn btn-success me-2">+ Log Planting</a><a href="<?= BASE_URL ?>/reports/yield.php" class="btn btn-outline-success">Submit Yield Report</a></div>
</div>
<?php if (empty($plantings)): ?>
  <div class="alert alert-info">No plantings logged yet.</div>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover bg-white shadow-sm">
<thead><tr><th>Farm</th><th>Crop</th><th>Planted</th><th>Expected harvest</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php foreach ($plantings as $p): ?>
<tr>
  <td><?= htmlspecialchars($p['farm_name']) ?></td>
  <td><?= htmlspecialchars($p['crop_name']) ?></td>
  <td><?= htmlspecialchars($p['planting_date']) ?></td>
  <td><?= htmlspecialchars($p['expected_harvest_date']) ?></td>
  <td><span class="badge bg-<?= $p['status']=='harvested'?'success':($p['status']=='failed'?'danger':'secondary') ?>"><?= htmlspecialchars($p['status']) ?></span></td>
  <td><a href="<?= BASE_URL ?>/reports/view.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary">View</a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<?php require __DIR__ . '/../../app/helpers/footer.php'; ?>
