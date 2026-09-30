<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/User.php';
header('Content-Type: application/json');
$email = trim($_GET['email'] ?? '');
echo json_encode(['exists' => $email ? User::emailExists($pdo, $email) : false]);
