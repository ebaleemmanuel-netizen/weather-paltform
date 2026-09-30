<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_login();
require_once __DIR__ . '/../../app/models/Farm.php';
$user = current_user();
$farms = Farm::forUser($pdo, $user['id']);
$pageTitle = 'My Farms';
require __DIR__ . '/../../app/helpers/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>My Farms</h3><a href="<?= BASE_URL ?>/farms/add.php" class="btn btn-success">+ Add Farm</a>
</div>
<?php if (empty($farms)): ?>
  <div class="alert alert-info">You haven't added any farms yet.</div>
<?php else: ?>
<div class="table-responsive"><table class="table table-hover bg-white shadow-sm">
<thead><tr><th>Name</th><th>District</th><th>Size</th><th>Soil</th><th>Irrigation</th><th></th></tr></thead>
<tbody>
<?php foreach ($farms as $f): ?>
<tr>
  <td><?= htmlspecialchars($f['name']) ?></td>
  <td><?= htmlspecialchars($f['district']) ?></td>
  <td><?= htmlspecialchars($f['size_acres']) ?> acres</td>
  <td><?= htmlspecialchars($f['soil_type']) ?></td>
  <td><?= htmlspecialchars($f['irrigation_type']) ?></td>
  <td>
    <a href="<?= BASE_URL ?>/farms/edit.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
    <a href="<?= BASE_URL ?>/farms/delete.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this farm?')">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<?php require __DIR__ . '/../../app/helpers/footer.php'; ?>
