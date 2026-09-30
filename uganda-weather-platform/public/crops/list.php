<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_role(['admin','ministry']);
require_once __DIR__ . '/../../app/models/Crop.php';

$tab = $_GET['tab'] ?? 'active';
$crops = Crop::all($pdo, in_array($tab, ['active','pending','inactive']) ? $tab : null);
$pageTitle = 'Manage Crops';
require __DIR__ . '/../../app/helpers/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Crop Management</h3><a href="<?= BASE_URL ?>/crops/add.php" class="btn btn-success">+ Add Crop</a>
</div>
<ul class="nav nav-tabs mb-3">
  <?php foreach (['active'=>'Active','pending'=>'Pending','inactive'=>'Inactive'] as $key => $label): ?>
  <li class="nav-item"><a class="nav-link <?= $tab==$key?'active':'' ?>" href="?tab=<?= $key ?>"><?= $label ?></a></li>
  <?php endforeach; ?>
</ul>
<div class="table-responsive"><table class="table table-hover bg-white shadow-sm">
<thead><tr><th>Name</th><th>Category</th><th>Rain (mm)</th><th>Temp (°C)</th><th>Days to maturity</th><th></th></tr></thead>
<tbody>
<?php foreach ($crops as $c): ?>
<tr>
  <td><?= htmlspecialchars($c['name']) ?><br><small class="text-muted"><?= htmlspecialchars($c['scientific_name']) ?></small></td>
  <td><?= htmlspecialchars($c['category']) ?></td>
  <td><?= $c['min_rain_mm'] ?>-<?= $c['max_rain_mm'] ?></td>
  <td><?= $c['min_temp_c'] ?>-<?= $c['max_temp_c'] ?></td>
  <td><?= $c['days_to_maturity'] ?></td>
  <td>
    <a href="<?= BASE_URL ?>/crops/edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
    <a href="<?= BASE_URL ?>/crops/toggle.php?id=<?= $c['id'] ?>&status=<?= $c['status']=='active'?'inactive':'active' ?>" class="btn btn-sm btn-outline-<?= $c['status']=='active'?'danger':'success' ?>" onclick="return confirm('Change status?')"><?= $c['status']=='active'?'Deactivate':'Activate' ?></a>
  </td>
</tr>
<?php endforeach; ?>
<?php if (empty($crops)): ?><tr><td colspan="6" class="text-center text-muted">No crops in this tab.</td></tr><?php endif; ?>
</tbody></table></div>
<?php require __DIR__ . '/../../app/helpers/footer.php'; ?>
