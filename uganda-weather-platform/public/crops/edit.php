<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_role(['admin','ministry']);
require_once __DIR__ . '/../../app/models/Crop.php';

$user = current_user();
$crop = Crop::find($pdo, (int)($_GET['id'] ?? $_POST['id'] ?? 0));
if (!$crop) { http_response_code(404); die('Crop not found.'); }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!trim($_POST['name'] ?? '')) $errors[] = 'Crop name is required.';
    if (!$errors) {
        Crop::update($pdo, $crop['id'], $_POST, $user['id']);
        header('Location: ' . BASE_URL . '/crops/list.php');
        exit;
    }
}
$pageTitle = 'Edit Crop';
require __DIR__ . '/../../app/helpers/header.php';
?>
<h3 class="mb-3">Edit Crop: <?= htmlspecialchars($crop['name']) ?></h3>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<form method="post" class="row g-3">
  <input type="hidden" name="id" value="<?= $crop['id'] ?>">
  <div class="col-md-6"><label class="form-label">Crop name</label><input name="name" class="form-control" value="<?= htmlspecialchars($crop['name']) ?>" required></div>
  <div class="col-md-6"><label class="form-label">Scientific name</label><input name="scientific_name" class="form-control" value="<?= htmlspecialchars($crop['scientific_name']) ?>"></div>
  <div class="col-md-4"><label class="form-label">Category</label>
    <select name="category" class="form-select">
      <?php foreach (['food','cash','both'] as $c): ?><option value="<?= $c ?>" <?= $crop['category']==$c?'selected':'' ?>><?= ucfirst($c) ?></option><?php endforeach; ?>
    </select></div>
  <div class="col-md-4"><label class="form-label">Min rainfall (mm)</label><input type="number" name="min_rain_mm" class="form-control" value="<?= $crop['min_rain_mm'] ?>"></div>
  <div class="col-md-4"><label class="form-label">Max rainfall (mm)</label><input type="number" name="max_rain_mm" class="form-control" value="<?= $crop['max_rain_mm'] ?>"></div>
  <div class="col-md-4"><label class="form-label">Min temp (°C)</label><input type="number" step="0.1" name="min_temp_c" class="form-control" value="<?= $crop['min_temp_c'] ?>"></div>
  <div class="col-md-4"><label class="form-label">Max temp (°C)</label><input type="number" step="0.1" name="max_temp_c" class="form-control" value="<?= $crop['max_temp_c'] ?>"></div>
  <div class="col-md-4"><label class="form-label">Days to maturity</label><input type="number" name="days_to_maturity" class="form-control" value="<?= $crop['days_to_maturity'] ?>"></div>
  <div class="col-md-6"><label class="form-label">Preferred soil</label><input name="preferred_soil" class="form-control" value="<?= htmlspecialchars($crop['preferred_soil']) ?>"></div>
  <div class="col-md-6"><label class="form-label">Planting season</label><input name="planting_season" class="form-control" value="<?= htmlspecialchars($crop['planting_season']) ?>"></div>
  <div class="col-md-6"><label class="form-label">Planting window</label><input name="planting_window" class="form-control" value="<?= htmlspecialchars($crop['planting_window']) ?>"></div>
  <div class="col-md-6"><label class="form-label">Best transplant time</label><input name="best_transplant_time" class="form-control" value="<?= htmlspecialchars($crop['best_transplant_time']) ?>"></div>
  <div class="col-md-6"><label class="form-label">Best watering time</label><input name="best_watering_time" class="form-control" value="<?= htmlspecialchars($crop['best_watering_time']) ?>"></div>
  <div class="col-md-6"><label class="form-label">Common pests</label><input name="common_pests" class="form-control" value="<?= htmlspecialchars($crop['common_pests']) ?>"></div>
  <div class="col-md-6"><label class="form-label">Common diseases</label><input name="common_diseases" class="form-control" value="<?= htmlspecialchars($crop['common_diseases']) ?>"></div>
  <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($crop['notes']) ?></textarea></div>
  <div class="col-12"><button class="btn btn-success">Save Changes</button></div>
</form>
<?php require __DIR__ . '/../../app/helpers/footer.php'; ?>
