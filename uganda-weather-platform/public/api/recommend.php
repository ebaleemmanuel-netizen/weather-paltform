<?php
// Optional AJAX endpoint mirroring recommend.php logic, for JS-driven UIs
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/models/Farm.php';
require_once __DIR__ . '/../../app/models/Recommendation.php';
header('Content-Type: application/json');

if (!is_logged_in()) { http_response_code(401); echo json_encode(['error' => 'Not logged in']); exit; }

$farmId = (int)($_GET['farm_id'] ?? 0);
$farm = Farm::find($pdo, $farmId);
if (!$farm || $farm['user_id'] != current_user()['id']) { http_response_code(404); echo json_encode(['error' => 'Farm not found']); exit; }

$weather = ['temperature_c' => 24, 'rain_mm' => 80];
$results = Recommendation::forFarm($pdo, $farm, $weather);
echo json_encode($results);
