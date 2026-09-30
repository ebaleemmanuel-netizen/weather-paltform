<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_login();
require_once __DIR__ . '/../../app/models/Farm.php';
require_once __DIR__ . '/../../app/models/Planting.php';

$user = current_user();
$farms = Farm::forUser($pdo, $user['id']);
$crops = $pdo->query("SELECT * FROM crops WHERE status='active' ORDER BY name")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farm = Farm::find($pdo, (int)$_POST['farm_id']);
    if (!$farm || $farm['user_id'] != $user['id']) $errors[] = 'Invalid farm selected.';
    if (empty($_POST['crop_id'])) $errors[] = 'Please select a crop.';
    if (empty($_POST['planting_date'])) $errors[] = 'Planting date is required.';
    if (!$errors) {
        Planting::create($pdo, [
            'farm_id' => $_POST['farm_id'], 'crop_id' => $_POST['crop_id'],
            'planting_date' => $_POST['planting_date'], 'transplant_date' => $_POST['transplant_date'] ?? null,
            'expected_harvest_date' => $_POST['expected_harvest_date'] ?? null, 'status' => $_POST['status'] ?? 'growing',
        ]);
        header('Location: ' . BASE_URL . '/reports/list.php');
        exit;
    }
}
$pageTitle = 'Log Planting';
require __DIR__ . '/../../app/helpers/header.php';
?>
<h3 class="mb-3">Log a Planting</h3>
<?php if (empty($farms)): ?>
  <div class="alert alert-warning">Add a <a href="<?= BASE_URL ?>/farms/add.php">farm</a> first.</div>
<?php else: ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<form method="post" class="row g-3">
  <div class="col-md-6"><label class="form-label">Farm</label>
    <select name="farm_id" class="form-select" required><?php foreach ($farms as $f): ?><option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-6"><label class="form-label">Crop</label>
    <select name="crop_id" class="form-select" required><?php foreach ($crops as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-4"><label class="form-label">Planting date</label><input type="date" name="planting_date" class="form-control" required></div>
  <div class="col-md-4"><label class="form-label">Transplant date (optional)</label><input type="date" name="transplant_date" class="form-control"></div>
  <div class="col-md-4"><label class="form-label">Expected harvest date</label><input type="date" name="expected_harvest_date" class="form-control"></div>
  <div class="col-md-4"><label class="form-label">Status</label>
    <select name="status" class="form-select"><option value="growing">Growing</option><option value="harvested">Harvested</option><option value="failed">Failed</option></select></div>
  <div class="col-12"><button class="btn btn-success">Save Planting</button></div>
</form>
<?php endif; ?>
<?php require __DIR__ . '/../../app/helpers/footer.php'; ?>
