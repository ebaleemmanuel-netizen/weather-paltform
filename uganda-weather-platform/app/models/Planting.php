<?php
class Planting {
    public static function forFarmer(PDO $pdo, int $userId): array {
        $stmt = $pdo->prepare("SELECT p.*, f.name as farm_name, c.name as crop_name
            FROM plantings p JOIN farms f ON p.farm_id = f.id JOIN crops c ON p.crop_id = c.id
            WHERE f.user_id = ? ORDER BY p.created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function find(PDO $pdo, int $id) {
        $stmt = $pdo->prepare("SELECT p.*, f.name as farm_name, f.user_id, c.name as crop_name
            FROM plantings p JOIN farms f ON p.farm_id = f.id JOIN crops c ON p.crop_id = c.id WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function harvestedWithoutReport(PDO $pdo, int $userId): array {
        $stmt = $pdo->prepare("SELECT p.*, f.name as farm_name, c.name as crop_name
            FROM plantings p JOIN farms f ON p.farm_id = f.id JOIN crops c ON p.crop_id = c.id
            LEFT JOIN yield_reports yr ON yr.planting_id = p.id
            WHERE f.user_id = ? AND p.status = 'harvested' AND yr.id IS NULL");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function create(PDO $pdo, array $d): int {
        $stmt = $pdo->prepare("INSERT INTO plantings (farm_id, crop_id, planting_date, transplant_date, expected_harvest_date, status)
            VALUES (?,?,?,?,?,?)");
        $stmt->execute([$d['farm_id'],$d['crop_id'],$d['planting_date'],$d['transplant_date'] ?: null,$d['expected_harvest_date'] ?: null,$d['status']]);
        return (int)$pdo->lastInsertId();
    }

    public static function countAll(PDO $pdo): int {
        return (int)$pdo->query("SELECT COUNT(*) c FROM plantings")->fetch()['c'];
    }
}
