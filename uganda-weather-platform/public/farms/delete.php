<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_login();
require_once __DIR__ . '/../../app/models/Farm.php';

$user = current_user();
$farm = Farm::find($pdo, (int)($_GET['id'] ?? 0));
if ($farm && $farm['user_id'] == $user['id']) {
    Farm::delete($pdo, $farm['id']);
}
header('Location: ' . BASE_URL . '/farms/list.php');
exit;
