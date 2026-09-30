<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_role(['admin','ministry']);
require_once __DIR__ . '/../../app/models/Crop.php';

$user = current_user();
$id = (int)($_GET['id'] ?? 0);
$status = in_array($_GET['status'] ?? '', ['active','inactive','pending']) ? $_GET['status'] : 'inactive';
if ($id) Crop::toggleStatus($pdo, $id, $status, $user['id']);
header('Location: ' . BASE_URL . '/crops/list.php');
exit;
