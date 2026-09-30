<?php
class Crop {
    public static function all(PDO $pdo, ?string $status = null): array {
        if ($status) {
            $stmt = $pdo->prepare("SELECT * FROM crops WHERE status = ? ORDER BY name");
            $stmt->execute([$status]);
            return $stmt->fetchAll();
        }
        return $pdo->query("SELECT * FROM crops ORDER BY name")->fetchAll();
    }

    public static function find(PDO $pdo, int $id) {
        $stmt = $pdo->prepare("SELECT * FROM crops WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create(PDO $pdo, array $d, int $userId): int {
        $stmt = $pdo->prepare("INSERT INTO crops (name, scientific_name, category, min_rain_mm, max_rain_mm, min_temp_c, max_temp_c, preferred_soil, planting_season, planting_window, days_to_maturity, best_transplant_time, best_watering_time, common_pests, common_diseases, notes, status)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'pending')");
        $stmt->execute([$d['name'],$d['scientific_name'],$d['category'],$d['min_rain_mm'],$d['max_rain_mm'],$d['min_temp_c'],$d['max_temp_c'],$d['preferred_soil'],$d['planting_season'],$d['planting_window'],$d['days_to_maturity'],$d['best_transplant_time'],$d['best_watering_time'],$d['common_pests'],$d['common_diseases'],$d['notes']]);
        $id = (int)$pdo->lastInsertId();
        self::log($pdo, $id, $userId, 'create', 'Crop created');
        return $id;
    }

    public static function update(PDO $pdo, int $id, array $d, int $userId): void {
        $stmt = $pdo->prepare("UPDATE crops SET name=?, scientific_name=?, category=?, min_rain_mm=?, max_rain_mm=?, min_temp_c=?, max_temp_c=?, preferred_soil=?, planting_season=?, planting_window=?, days_to_maturity=?, best_transplant_time=?, best_watering_time=?, common_pests=?, common_diseases=?, notes=? WHERE id=?");
        $stmt->execute([$d['name'],$d['scientific_name'],$d['category'],$d['min_rain_mm'],$d['max_rain_mm'],$d['min_temp_c'],$d['max_temp_c'],$d['preferred_soil'],$d['planting_season'],$d['planting_window'],$d['days_to_maturity'],$d['best_transplant_time'],$d['best_watering_time'],$d['common_pests'],$d['common_diseases'],$d['notes'],$id]);
        self::log($pdo, $id, $userId, 'update', 'Crop details updated');
    }

    public static function toggleStatus(PDO $pdo, int $id, string $newStatus, int $userId): void {
        $stmt = $pdo->prepare("UPDATE crops SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
        self::log($pdo, $id, $userId, 'status_change', "Status changed to $newStatus");
    }

    public static function log(PDO $pdo, int $cropId, int $userId, string $action, string $details): void {
        $stmt = $pdo->prepare("INSERT INTO crop_audit_log (crop_id, user_id, action, details) VALUES (?,?,?,?)");
        $stmt->execute([$cropId, $userId, $action, $details]);
    }

    public static function countActive(PDO $pdo): int {
        return (int)$pdo->query("SELECT COUNT(*) c FROM crops WHERE status='active'")->fetch()['c'];
    }

    public static function countAll(PDO $pdo): int {
        return (int)$pdo->query("SELECT COUNT(*) c FROM crops")->fetch()['c'];
    }
}
