<?php
// JSON data for Chart.js dashboards
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_login();
header('Content-Type: application/json');

$user = current_user();
$scope = $_GET['scope'] ?? 'mine';
$where = '';
$params = [];
if ($scope === 'mine' && !in_array($user['role'], ['admin','ministry','researcher'])) {
    $where = 'WHERE f.user_id = ?';
    $params[] = $user['id'];
}

$outcomeStmt = $pdo->prepare("SELECT yr.outcome, COUNT(*) as cnt FROM yield_reports yr
    JOIN plantings p ON yr.planting_id = p.id JOIN farms f ON p.farm_id = f.id $where GROUP BY yr.outcome");
$outcomeStmt->execute($params);
$outcome = $outcomeStmt->fetchAll();

$reasonsStmt = $pdo->prepare("SELECT yrs.label, COUNT(*) as cnt FROM yield_report_reasons yrr
    JOIN yield_reasons yrs ON yrr.yield_reason_id = yrs.id
    JOIN yield_reports yr ON yrr.yield_report_id = yr.id
    JOIN plantings p ON yr.planting_id = p.id JOIN farms f ON p.farm_id = f.id
    " . ($where ? $where . " AND yrs.outcome='bad'" : "WHERE yrs.outcome='bad'") . "
    GROUP BY yrs.label ORDER BY cnt DESC LIMIT 6");
$reasonsStmt->execute($params);
$reasons = $reasonsStmt->fetchAll();

$cropStmt = $pdo->prepare("SELECT c.name, yr.outcome, COUNT(*) as cnt FROM yield_reports yr
    JOIN plantings p ON yr.planting_id = p.id JOIN crops c ON p.crop_id = c.id JOIN farms f ON p.farm_id = f.id
    $where GROUP BY c.name, yr.outcome");
$cropStmt->execute($params);
$byCrop = $cropStmt->fetchAll();

echo json_encode(['outcome' => $outcome, 'top_failure_reasons' => $reasons, 'by_crop' => $byCrop]);
