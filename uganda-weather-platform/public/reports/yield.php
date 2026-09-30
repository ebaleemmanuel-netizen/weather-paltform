<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_login();
require_once __DIR__ . '/../../app/models/Planting.php';
require_once __DIR__ . '/../../app/models/YieldReport.php';

$user = current_user();
$plantings = Planting::harvestedWithoutReport($pdo, $user['id']);
$goodReasons = $pdo->query("SELECT * FROM yield_reasons WHERE outcome='good'")->fetchAll();
$badReasons = $pdo->query("SELECT * FROM yield_reasons WHERE outcome='bad'")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $planting = Planting::find($pdo, (int)$_POST['planting_id']);
    $outcome = $_POST['outcome'] ?? '';
    $reasonIds = $_POST['reasons'] ?? [];
    if (!$planting || $planting['user_id'] != $user['id']) $errors[] = 'Invalid planting.';
    if (!in_array($outcome, ['good','bad'])) $errors[] = 'Select an outcome.';
    if (!$errors) {
        YieldReport::create($pdo, $planting['id'], $outcome, trim($_POST['comment'] ?? ''), $reasonIds);
        header('Location: ' . BASE_URL . '/reports/list.php');
        exit;
    }
}
$pageTitle = 'Submit Yield Report';
require __DIR__ . '/../../app/helpers/header.php';
?>
<h3 class="mb-3">Submit Yield Report</h3>
<?php if (empty($plantings)): ?>
  <div class="alert alert-info">No harvested plantings awaiting a report. Mark a planting as "harvested" first.</div>
<?php else: ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<form method="post" class="row g-3">
  <div class="col-12"><label class="form-label">Which planting?</label>
    <select name="planting_id" class="form-select" required>
      <?php foreach ($plantings as $p): ?><option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['crop_name']) ?> — <?= htmlspecialchars($p['farm_name']) ?> (planted <?= htmlspecialchars($p['planting_date']) ?>)</option><?php endforeach; ?>
    </select></div>
  <div class="col-12">
    <label class="form-label d-block">Outcome</label>
    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="outcome" value="good" id="og" onclick="toggleReasons('good')" required><label class="form-check-label" for="og">Good</label></div>
    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="outcome" value="bad" id="ob" onclick="toggleReasons('bad')"><label class="form-check-label" for="ob">Bad</label></div>
  </div>
  <div class="col-12" id="goodReasons" style="display:none">
    <label class="form-label">What went well?</label><br>
    <?php foreach ($goodReasons as $r): ?>
    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="reasons[]" value="<?= $r['id'] ?>" id="gr<?= $r['id'] ?>"><label class="form-check-label" for="gr<?= $r['id'] ?>"><?= htmlspecialchars($r['label']) ?></label></div>
    <?php endforeach; ?>
  </div>
  <div class="col-12" id="badReasons" style="display:none">
    <label class="form-label">What went wrong?</label><br>
    <?php foreach ($badReasons as $r): ?>
    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="reasons[]" value="<?= $r['id'] ?>" id="br<?= $r['id'] ?>"><label class="form-check-label" for="br<?= $r['id'] ?>"><?= htmlspecialchars($r['label']) ?></label></div>
    <?php endforeach; ?>
  </div>
  <div class="col-12"><label class="form-label">Comments</label><textarea name="comment" class="form-control" rows="3"></textarea></div>
  <div class="col-12"><button class="btn btn-success">Submit Report</button></div>
</form>
<script>
function toggleReasons(outcome) {
  document.getElementById('goodReasons').style.display = outcome === 'good' ? 'block' : 'none';
  document.getElementById('badReasons').style.display = outcome === 'bad' ? 'block' : 'none';
}
</script>
<?php endif; ?>
<?php require __DIR__ . '/../../app/helpers/footer.php'; ?>
