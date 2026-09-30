<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_login();
require_once __DIR__ . '/../../app/models/Farm.php';

$user = current_user();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = [
        'user_id' => $user['id'], 'name' => trim($_POST['name'] ?? ''),
        'district' => $_POST['district'] ?? '', 'sub_county' => trim($_POST['sub_county'] ?? ''),
        'latitude' => $_POST['latitude'] ?? null, 'longitude' => $_POST['longitude'] ?? null,
        'size_acres' => $_POST['size_acres'] ?? null, 'soil_type' => $_POST['soil_type'] ?? '',
        'irrigation_type' => $_POST['irrigation_type'] ?? '',
    ];
    if (!$d['name']) $errors[] = 'Farm name is required.';
    if (!$errors) {
        Farm::create($pdo, $d);
        header('Location: ' . BASE_URL . '/farms/list.php');
        exit;
    }
}
$districts = $pdo->query("SELECT name FROM districts ORDER BY name")->fetchAll();
$pageTitle = 'Add Farm';
require __DIR__ . '/../../app/helpers/header.php';
?>
<h3 class="mb-3">Add a Farm</h3>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<form method="post" class="row g-3">
  <div class="col-md-6"><label class="form-label">Farm name</label><input name="name" class="form-control" required></div>
  <div class="col-md-6"><label class="form-label">District</label>
    <select name="district" class="form-select"><?php foreach ($districts as $d): ?><option><?= htmlspecialchars($d['name']) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-6"><label class="form-label">Sub-county</label><input name="sub_county" class="form-control"></div>
  <div class="col-md-6"><label class="form-label">Size (acres)</label><input type="number" step="0.1" name="size_acres" class="form-control"></div>
  <div class="col-md-4"><label class="form-label">GPS latitude</label><input type="text" id="lat" name="latitude" class="form-control"></div>
  <div class="col-md-4"><label class="form-label">GPS longitude</label><input type="text" id="lng" name="longitude" class="form-control"></div>
  <div class="col-md-4 d-flex align-items-end"><button type="button" onclick="useLocation()" class="btn btn-outline-secondary w-100">📍 Use my location</button></div>
  <div class="col-md-6"><label class="form-label">Soil type</label>
    <select name="soil_type" class="form-select">
      <option>Loam</option><option>Sandy loam</option><option>Clay loam</option><option>Clay</option><option>Volcanic soil</option><option>Sandy</option>
    </select></div>
  <div class="col-md-6"><label class="form-label">Irrigation type</label>
    <select name="irrigation_type" class="form-select">
      <option>Rain-fed</option><option>Drip irrigation</option><option>Sprinkler</option><option>Furrow</option><option>None</option>
    </select></div>
  <div class="col-12"><button class="btn btn-success">Save Farm</button></div>
</form>
<script>
function useLocation() {
  if (!navigator.geolocation) return alert('Geolocation not supported');
  navigator.geolocation.getCurrentPosition(pos => {
    document.getElementById('lat').value = pos.coords.latitude.toFixed(6);
    document.getElementById('lng').value = pos.coords.longitude.toFixed(6);
  }, () => alert('Could not get your location'));
}
</script>
<?php require __DIR__ . '/../../app/helpers/footer.php'; ?>
