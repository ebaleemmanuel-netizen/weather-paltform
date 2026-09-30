<?php
class Farm {
    public static function forUser(PDO $pdo, int $userId): array {
        $stmt = $pdo->prepare("SELECT * FROM farms WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function find(PDO $pdo, int $id) {
        $stmt = $pdo->prepare("SELECT * FROM farms WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create(PDO $pdo, array $d): int {
        $stmt = $pdo->prepare("INSERT INTO farms (user_id, name, district, sub_county, latitude, longitude, size_acres, soil_type, irrigation_type)
            VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$d['user_id'],$d['name'],$d['district'],$d['sub_county'],$d['latitude'] ?: null,$d['longitude'] ?: null,$d['size_acres'],$d['soil_type'],$d['irrigation_type']]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(PDO $pdo, int $id, array $d): void {
        $stmt = $pdo->prepare("UPDATE farms SET name=?, district=?, sub_county=?, latitude=?, longitude=?, size_acres=?, soil_type=?, irrigation_type=? WHERE id=?");
        $stmt->execute([$d['name'],$d['district'],$d['sub_county'],$d['latitude'] ?: null,$d['longitude'] ?: null,$d['size_acres'],$d['soil_type'],$d['irrigation_type'],$id]);
    }

    public static function delete(PDO $pdo, int $id): void {
        $stmt = $pdo->prepare("DELETE FROM farms WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function countAll(PDO $pdo): int {
        return (int)$pdo->query("SELECT COUNT(*) c FROM farms")->fetch()['c'];
    }
}
