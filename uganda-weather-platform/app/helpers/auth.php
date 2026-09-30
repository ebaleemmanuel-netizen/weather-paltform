<?php
// Session check + role guard functions
function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!current_user()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function require_role($roles) {
    require_login();
    $roles = is_array($roles) ? $roles : [$roles];
    if (!in_array(current_user()['role'], $roles)) {
        http_response_code(403);
        die('<div style="font-family:sans-serif;padding:40px;text-align:center;"><h2>403 - Access denied</h2><p>You do not have permission to view this page.</p><a href="' . BASE_URL . '/dashboard.php">Back to dashboard</a></div>');
    }
}

function is_logged_in() {
    return current_user() !== null;
}
