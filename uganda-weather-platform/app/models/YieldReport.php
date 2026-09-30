<?php
class YieldReport {
    public static function create(PDO $pdo, int $plantingId, string $outcome, string $comment, array $reasonIds): int {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO yield_reports (planting_id, outcome, comment) VALUES (?,?,?)");
            $stmt->execute([$plantingId, $outcome, $comment]);
            $reportId = (int)$pdo->lastInsertId();
            $stmt2 = $pdo->prepare("INSERT INTO yield_report_reasons (yield_report_id, yield_reason_id) VALUES (?,?)");
            foreach ($reasonIds as $rid) {
                $stmt2->execute([$reportId, $rid]);
            }
            $pdo->commit();
            return $reportId;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function find(PDO $pdo, int $id) {
        $stmt = $pdo->prepare("SELECT yr.*, p.crop_id, c.name as crop_name, f.name as farm_name, f.user_id
            FROM yield_reports yr JOIN plantings p ON yr.planting_id = p.id
            JOIN crops c ON p.crop_id = c.id JOIN farms f ON p.farm_id = f.id WHERE yr.id = ?");
        $stmt->execute([$id]);
        $report = $stmt->fetch();
        if ($report) {
            $stmt2 = $pdo->prepare("SELECT yr2.label FROM yield_report_reasons yrr JOIN yield_reasons yr2 ON yrr.yield_reason_id = yr2.id WHERE yrr.yield_report_id = ?");
            $stmt2->execute([$id]);
            $report['reasons'] = array_column($stmt2->fetchAll(), 'label');
        }
        return $report;
    }

    public static function countAll(PDO $pdo): int {
        return (int)$pdo->query("SELECT COUNT(*) c FROM yield_reports")->fetch()['c'];
    }
}
