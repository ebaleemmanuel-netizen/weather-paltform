<?php
require_once __DIR__ . '/../helpers/crop_rules.php';

class Recommendation {
    // $weather = ['temperature_c'=>..,'rain_mm'=>..]
    public static function forFarm(PDO $pdo, array $farm, array $weather): array {
        $crops = $pdo->query("SELECT * FROM crops WHERE status = 'active'")->fetchAll();
        $results = [];
        foreach ($crops as $crop) {
            $m = match_score($farm, $weather, $crop);
            $results[] = [
                'crop' => $crop,
                'score' => $m['score'],
                'max' => $m['max'],
                'percent' => round(($m['score'] / $m['max']) * 100),
                'reasons' => $m['reasons'],
            ];
        }
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($results, 0, 5);
    }
}
