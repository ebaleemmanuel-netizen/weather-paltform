<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_login();
require_once __DIR__ . '/../../app/models/Planting.php';

$user = current_user();
$planting = Planting::find($pdo, (int)($_GET['id'] ?? 0));
if (!$planting || $planting['user_id'] != $user['id']) { http_response_code(404); die('Not found.'); }

$stmt = $pdo->prepare("SELECT * FROM yield_reports WHERE planting_id = ?");
$stmt->execute([$planting['id']]);
$report = $stmt->fetch();
if ($report) {
    $stmt2 = $pdo->prepare("SELECT yr2.label FROM yield_report_reasons yrr JOIN yield_reasons yr2 ON yrr.yield_reason_id = yr2.id WHERE yrr.yield_report_id = ?");
    $stmt2->execute([$report['id']]);
    $reasons = array_column($stmt2->fetchAll(), 'label');
}
$pageTitle = 'Planting Detail';
require __DIR__ . '/../../app/helpers/header.php';
?>
<h3 class="mb-3"><?= htmlspecialchars($planting['crop_name']) ?> — <?= htmlspecialchars($planting['farm_name']) ?></h3>
<div class="card shadow-sm mb-3"><div class="card-body">
  <p><strong>Planted:</strong> <?= htmlspecialchars($planting['planting_date']) ?><br>
  <strong>Transplanted:</strong> <?= htmlspecialchars($planting['transplant_date'] ?? 'N/A') ?><br>
  <strong>Expected harvest:</strong> <?= htmlspecialchars($planting['expected_harvest_date'] ?? 'N/A') ?><br>
  <strong>Status:</strong> <span class="badge bg-secondary"><?= htmlspecialchars($planting['status']) ?></span></p>
</div></div>
<?php if ($report): ?>
<div class="card shadow-sm"><div class="card-body">
  <h5>Yield Report: <span class="badge bg-<?= $report['outcome']=='good'?'success':'danger' ?>"><?= ucfirst($report['outcome']) ?></span></h5>
  <p><strong>Reasons:</strong> <?= htmlspecialchars(implode(', ', $reasons ?? [])) ?></p>
  <p><strong>Comment:</strong> <?= nl2br(htmlspecialchars($report['comment'])) ?></p>
</div></div>
<?php else: ?>
<div class="alert alert-info">No yield report submitted for this planting yet.
<?php if ($planting['status'] === 'harvested'): ?><a href="<?= BASE_URL ?>/reports/yield.php">Submit one now</a>.<?php endif; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../../app/helpers/footer.php'; ?>
