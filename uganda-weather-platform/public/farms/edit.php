<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_login();
require_once __DIR__ . '/../../app/models/Farm.php';

$user = current_user();
$farm = Farm::find($pdo, (int)($_GET['id'] ?? $_POST['id'] ?? 0));
if (!$farm || $farm['user_id'] != $user['id']) { http_response_code(404); die('Farm not found.'); }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = [
        'name' => trim($_POST['name'] ?? ''), 'district' => $_POST['district'] ?? '',
        'sub_county' => trim($_POST['sub_county'] ?? ''), 'latitude' => $_POST['latitude'] ?? null,
        'longitude' => $_POST['longitude'] ?? null, 'size_acres' => $_POST['size_acres'] ?? null,
        'soil_type' => $_POST['soil_type'] ?? '', 'irrigation_type' => $_POST['irrigation_type'] ?? '',
    ];
    if (!$d['name']) $errors[] = 'Farm name is required.';
    if (!$errors) {
        Farm::update($pdo, $farm['id'], $d);
        header('Location: ' . BASE_URL . '/farms/list.php');
        exit;
    }
}
$districts = $pdo->query("SELECT name FROM districts ORDER BY name")->fetchAll();
$pageTitle = 'Edit Farm';
require __DIR__ . '/../../app/helpers/header.php';
?>
<h3 class="mb-3">Edit Farm</h3>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<form method="post" class="row g-3">
  <input type="hidden" name="id" value="<?= $farm['id'] ?>">
  <div class="col-md-6"><label class="form-label">Farm name</label><input name="name" class="form-control" value="<?= htmlspecialchars($farm['name']) ?>" required></div>
  <div class="col-md-6"><label class="form-label">District</label>
    <select name="district" class="form-select"><?php foreach ($districts as $d): ?><option <?= $d['name']==$farm['district']?'selected':'' ?>><?= htmlspecialchars($d['name']) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-6"><label class="form-label">Sub-county</label><input name="sub_county" class="form-control" value="<?= htmlspecialchars($farm['sub_county']) ?>"></div>
  <div class="col-md-6"><label class="form-label">Size (acres)</label><input type="number" step="0.1" name="size_acres" class="form-control" value="<?= htmlspecialchars($farm['size_acres']) ?>"></div>
  <div class="col-md-6"><label class="form-label">GPS latitude</label><input name="latitude" class="form-control" value="<?= htmlspecialchars($farm['latitude']) ?>"></div>
  <div class="col-md-6"><label class="form-label">GPS longitude</label><input name="longitude" class="form-control" value="<?= htmlspecialchars($farm['longitude']) ?>"></div>
  <div class="col-md-6"><label class="form-label">Soil type</label>
    <select name="soil_type" class="form-select">
      <?php foreach (['Loam','Sandy loam','Clay loam','Clay','Volcanic soil','Sandy'] as $s): ?><option <?= $s==$farm['soil_type']?'selected':'' ?>><?= $s ?></option><?php endforeach; ?>
    </select></div>
  <div class="col-md-6"><label class="form-label">Irrigation type</label>
    <select name="irrigation_type" class="form-select">
      <?php foreach (['Rain-fed','Drip irrigation','Sprinkler','Furrow','None'] as $s): ?><option <?= $s==$farm['irrigation_type']?'selected':'' ?>><?= $s ?></option><?php endforeach; ?>
    </select></div>
  <div class="col-12"><button class="btn btn-success">Save Changes</button></div>
</form>
<?php require __DIR__ . '/../../app/helpers/footer.php'; ?>
