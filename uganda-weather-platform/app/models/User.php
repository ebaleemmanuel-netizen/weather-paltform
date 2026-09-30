<?php
class User {
    public static function findByEmail(PDO $pdo, string $email) {
        $stmt = $pdo->prepare("SELECT u.*, r.name as role FROM users u JOIN roles r ON u.role_id = r.id WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function create(PDO $pdo, array $data): int {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role_id, district, phone) 
            VALUES (?, ?, ?, (SELECT id FROM roles WHERE name = ?), ?, ?)");
        $stmt->execute([$data['full_name'], $data['email'], $data['password_hash'], $data['role'], $data['district'], $data['phone'] ?? null]);
        return (int)$pdo->lastInsertId();
    }

    public static function emailExists(PDO $pdo, string $email): bool {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return (bool)$stmt->fetch();
    }

    public static function countAll(PDO $pdo): int {
        return (int)$pdo->query("SELECT COUNT(*) c FROM users")->fetch()['c'];
    }
}
