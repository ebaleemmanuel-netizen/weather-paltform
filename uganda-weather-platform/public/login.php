<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/models/User.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $u = User::findByEmail($pdo, $email);
    if ($u && password_verify($password, $u['password_hash'])) {
        $_SESSION['user'] = ['id' => $u['id'], 'full_name' => $u['full_name'], 'role' => $u['role'], 'district' => $u['district']];
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
$pageTitle = 'Login';
require __DIR__ . '/../app/helpers/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm"><div class="card-body p-4">
      <h3 class="mb-3">Login</h3>
      <?php if ($error): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <form method="post">
        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
        <button class="btn btn-success w-100">Login</button>
      </form>
      <p class="mt-3 text-center mb-0"><small>No account? <a href="<?= BASE_URL ?>/register.php">Register</a></small></p>
      <hr>
      <p class="text-muted small mb-0">Demo accounts (after running seed_demo.sql): <br>
      farmer@demo.ug / password123<br>
      admin@agriweather.ug / admin123<br>
      ministry@demo.ug / password123</p>
    </div></div>
  </div>
</div>
<?php require __DIR__ . '/../app/helpers/footer.php'; ?>
