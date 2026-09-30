<?php
// Farmer's own analytics — same charts, scoped to the logged-in user (handled server-side in api/analytics.php)
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_login();
header('Location: ' . BASE_URL . '/analytics/index.php');
exit;
