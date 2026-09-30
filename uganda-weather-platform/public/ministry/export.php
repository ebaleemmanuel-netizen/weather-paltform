<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_role(['admin','ministry']);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="yield_reports_export.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Report ID', 'Farmer', 'District', 'Farm', 'Crop', 'Planting Date', 'Outcome', 'Comment', 'Reported At']);

$rows = $pdo->query("
    SELECT yr.id, u.full_name, f.district, f.name as farm_name, c.name as crop_name, p.planting_date, yr.outcome, yr.comment, yr.created_at
    FROM yield_reports yr
    JOIN plantings p ON yr.planting_id = p.id
    JOIN farms f ON p.farm_id = f.id
    JOIN users u ON f.user_id = u.id
    JOIN crops c ON p.crop_id = c.id
    ORDER BY yr.created_at DESC
")->fetchAll();

foreach ($rows as $r) {
    fputcsv($out, [$r['id'], $r['full_name'], $r['district'], $r['farm_name'], $r['crop_name'], $r['planting_date'], $r['outcome'], $r['comment'], $r['created_at']]);
}
fclose($out);
exit;
