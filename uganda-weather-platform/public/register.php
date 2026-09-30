<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/models/User.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'farmer';
    $district = $_POST['district'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if (!$full_name) $errors[] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if (!in_array($role, ['farmer','association_admin','extension_officer','researcher'])) $errors[] = 'Invalid role.';
    if (!$errors && User::emailExists($pdo, $email)) $errors[] = 'That email is already registered.';

    if (!$errors) {
        $id = User::create($pdo, [
            'full_name' => $full_name, 'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role, 'district' => $district, 'phone' => $phone,
        ]);
        $u = User::findByEmail($pdo, $email);
        $_SESSION['user'] = ['id' => $u['id'], 'full_name' => $u['full_name'], 'role' => $u['role'], 'district' => $u['district']];
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    }
}

$districts = $pdo->query("SELECT name FROM districts ORDER BY name")->fetchAll();
$pageTitle = 'Register';
require __DIR__ . '/../app/helpers/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm"><div class="card-body p-4">
      <h3 class="mb-3">Create an account</h3>
      <?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
      <form method="post">
        <div class="mb-3"><label class="form-label">Full name</label>
          <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required></div>
        <div class="mb-3"><label class="form-label">Email</label>
          <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
          <small id="emailCheck" class="form-text"></small></div>
        <div class="mb-3"><label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" minlength="6" required></div>
        <div class="mb-3"><label class="form-label">Phone (optional)</label>
          <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"></div>
        <div class="mb-3"><label class="form-label">I am a...</label>
          <select name="role" class="form-select">
            <option value="farmer">Farmer</option>
            <option value="association_admin">Farmer Association Admin</option>
            <option value="extension_officer">Extension Officer</option>
            <option value="researcher">Researcher</option>
          </select></div>
        <div class="mb-3"><label class="form-label">District</label>
          <select name="district" class="form-select">
            <?php foreach ($districts as $d): ?><option><?= htmlspecialchars($d['name']) ?></option><?php endforeach; ?>
          </select></div>
        <button class="btn btn-success w-100">Register</button>
      </form>
      <p class="mt-3 text-center mb-0"><small>Already have an account? <a href="<?= BASE_URL ?>/login.php">Login</a></small></p>
    </div></div>
  </div>
</div>
<script>
document.getElementById('email').addEventListener('blur', async function() {
  const email = this.value;
  if (!email) return;
  const res = await fetch('<?= BASE_URL ?>/api/check_email.php?email=' + encodeURIComponent(email));
  const data = await res.json();
  document.getElementById('emailCheck').textContent = data.exists ? 'This email is already registered.' : 'Email available';
  document.getElementById('emailCheck').className = data.exists ? 'form-text text-danger' : 'form-text text-success';
});
</script>
<?php require __DIR__ . '/../app/helpers/footer.php'; ?>
