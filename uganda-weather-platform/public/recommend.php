<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
require_login();
require_once __DIR__ . '/../app/models/Farm.php';
require_once __DIR__ . '/../app/models/Recommendation.php';

$user = current_user();
$farms = Farm::forUser($pdo, $user['id']);
$results = null;
$selectedFarm = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['farm_id'])) {
    $selectedFarm = Farm::find($pdo, (int)$_POST['farm_id']);
    if ($selectedFarm) {
        $district = $pdo->prepare("SELECT * FROM districts WHERE name = ?");
        $district->execute([$selectedFarm['district']]);
        $d = $district->fetch();
        $weather = ['temperature_c' => 24, 'rain_mm' => 80]; // fallback
        if ($d) {
            $ch = curl_init("https://api.open-meteo.com/v1/forecast?latitude={$d['latitude']}&longitude={$d['longitude']}&current=temperature_2m,rain");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $resp = curl_exec($ch);
            curl_close($ch);
            if ($resp) {
                $json = json_decode($resp, true);
                if (isset($json['current'])) {
                    $weather = ['temperature_c' => $json['current']['temperature_2m'], 'rain_mm' => $json['current']['rain']];
                }
            }
        }
        $results = Recommendation::forFarm($pdo, $selectedFarm, $weather);
    }
}

$pageTitle = 'Crop Advisor';
require __DIR__ . '/../app/helpers/header.php';
?>
<h3 class="mb-3">🌱 Crop Recommendation Advisor</h3>

<?php if (empty($farms)): ?>
  <div class="alert alert-warning">You need to <a href="<?= BASE_URL ?>/farms/add.php">add a farm</a> first before getting recommendations.</div>
<?php else: ?>
<form method="post" class="row g-2 mb-4">
  <div class="col-md-8">
    <select name="farm_id" class="form-select" required>
      <option value="">Select a farm...</option>
      <?php foreach ($farms as $f): ?>
        <option value="<?= $f['id'] ?>" <?= (isset($_POST['farm_id']) && $_POST['farm_id']==$f['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($f['name']) ?> (<?= htmlspecialchars($f['district']) ?>)
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-4"><button class="btn btn-success w-100">Get Recommendations</button></div>
</form>
<?php endif; ?>

<?php if ($results): ?>
<h5>Top crops for <?= htmlspecialchars($selectedFarm['name']) ?>:</h5>
<div class="row g-3">
<?php foreach ($results as $i => $r): ?>
  <div class="col-md-6">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h5 class="card-title">#<?= $i+1 ?> <?= htmlspecialchars($r['crop']['name']) ?> <span class="badge bg-success"><?= $r['percent'] ?>% match</span></h5>
        <p class="text-muted small mb-1"><?= htmlspecialchars($r['crop']['scientific_name']) ?></p>
        <ul class="small mb-2">
          <?php foreach ($r['reasons'] as $reason): ?><li><?= htmlspecialchars($reason) ?></li><?php endforeach; ?>
        </ul>
        <p class="small mb-0"><strong>Planting window:</strong> <?= htmlspecialchars($r['crop']['planting_window']) ?><br>
        <strong>Transplant:</strong> <?= htmlspecialchars($r['crop']['best_transplant_time']) ?><br>
        <strong>Watering:</strong> <?= htmlspecialchars($r['crop']['best_watering_time']) ?></p>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../app/helpers/footer.php'; ?>
