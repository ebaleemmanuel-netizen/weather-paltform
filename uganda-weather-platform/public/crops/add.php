<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_role(['admin','ministry']);
require_once __DIR__ . '/../../app/models/Crop.php';

$user = current_user();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!trim($_POST['name'] ?? '')) $errors[] = 'Crop name is required.';
    if (!$errors) {
        Crop::create($pdo, $_POST, $user['id']);
        header('Location: ' . BASE_URL . '/crops/list.php?tab=pending');
        exit;
    }
}
$pageTitle = 'Add Crop';
require __DIR__ . '/../../app/helpers/header.php';
?>
<h3 class="mb-3">Add New Crop</h3>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<form method="post" class="row g-3">
  <div class="col-md-6"><label class="form-label">Crop name</label><input name="name" class="form-control" required></div>
  <div class="col-md-6"><label class="form-label">Scientific name</label><input name="scientific_name" class="form-control"></div>
  <div class="col-md-4"><label class="form-label">Category</label>
    <select name="category" class="form-select"><option value="food">Food</option><option value="cash">Cash</option><option value="both">Both</option></select></div>
  <div class="col-md-4"><label class="form-label">Min rainfall (mm)</label><input type="number" name="min_rain_mm" class="form-control"></div>
  <div class="col-md-4"><label class="form-label">Max rainfall (mm)</label><input type="number" name="max_rain_mm" class="form-control"></div>
  <div class="col-md-4"><label class="form-label">Min temp (°C)</label><input type="number" step="0.1" name="min_temp_c" class="form-control"></div>
  <div class="col-md-4"><label class="form-label">Max temp (°C)</label><input type="number" step="0.1" name="max_temp_c" class="form-control"></div>
  <div class="col-md-4"><label class="form-label">Days to maturity</label><input type="number" name="days_to_maturity" class="form-control"></div>
  <div class="col-md-6"><label class="form-label">Preferred soil</label><input name="preferred_soil" class="form-control" placeholder="e.g. Loam, Sandy loam"></div>
  <div class="col-md-6"><label class="form-label">Planting season</label><input name="planting_season" class="form-control" placeholder="e.g. Mar-May, Aug-Sep"></div>
  <div class="col-md-6"><label class="form-label">Planting window</label><input name="planting_window" class="form-control"></div>
  <div class="col-md-6"><label class="form-label">Best transplant time</label><input name="best_transplant_time" class="form-control"></div>
  <div class="col-md-6"><label class="form-label">Best watering time</label><input name="best_watering_time" class="form-control"></div>
  <div class="col-md-6"><label class="form-label">Common pests</label><input name="common_pests" class="form-control"></div>
  <div class="col-md-6"><label class="form-label">Common diseases</label><input name="common_diseases" class="form-control"></div>
  <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
  <div class="col-12"><button class="btn btn-success">Save Crop (as pending)</button></div>
</form>
<?php require __DIR__ . '/../../app/helpers/footer.php'; ?>
