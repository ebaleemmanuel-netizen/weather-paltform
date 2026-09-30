<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?><?= SITE_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
<script>
// Apply saved theme/contrast before the page paints, so there's no flash of the wrong style.
(function () {
  var theme = localStorage.getItem('agri_theme') || 'light';
  var contrast = localStorage.getItem('agri_contrast') || 'normal';
  document.documentElement.setAttribute('data-bs-theme', theme);
  if (contrast === 'high') document.documentElement.classList.add('contrast-high');
})();
</script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/index.php">🌾 Agri-Weather UG</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/map.php">Weather Map</a></li>
        <?php if ($user): ?>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/recommend.php">Crop Advisor</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/farms/list.php">My Farms</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/reports/list.php">My Reports</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/analytics/index.php">Analytics</a></li>
        <?php if (in_array($user['role'], ['admin','ministry'])): ?>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/crops/list.php">Manage Crops</a></li>
        <?php endif; ?>
        <?php if (in_array($user['role'], ['admin','ministry'])): ?>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/ministry/dashboard.php">Ministry Dashboard</a></li>
        <?php endif; ?>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav align-items-lg-center">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="displaySettingsToggle" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Display settings">
            ⚙️ Display
          </a>
          <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 220px;" aria-labelledby="displaySettingsToggle">
            <p class="small fw-bold mb-1 text-muted text-uppercase">Theme</p>
            <div class="btn-group w-100 mb-3" role="group">
              <button type="button" class="btn btn-sm btn-outline-success" id="themeLightBtn">☀️ Light</button>
              <button type="button" class="btn btn-sm btn-outline-success" id="themeDarkBtn">🌙 Dark</button>
            </div>
            <p class="small fw-bold mb-1 text-muted text-uppercase">Contrast</p>
            <div class="btn-group w-100" role="group">
              <button type="button" class="btn btn-sm btn-outline-success" id="contrastNormalBtn">Normal</button>
              <button type="button" class="btn btn-sm btn-outline-success" id="contrastHighBtn">High contrast</button>
            </div>
          </div>
        </li>
        <?php if ($user): ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/dashboard.php"><?= htmlspecialchars($user['full_name']) ?> (<?= htmlspecialchars($user['role']) ?>)</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="container py-4">
