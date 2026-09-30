<?php
// PHP proxy for Open-Meteo API, with caching to weather_cache table
require_once __DIR__ . '/../../app/config/db.php';
header('Content-Type: application/json');

$districts = $pdo->query("SELECT * FROM districts")->fetchAll();
$out = [];

foreach ($districts as $d) {
    $weather = null;
    // Check cache (last 30 minutes)
    $stmt = $pdo->prepare("SELECT * FROM weather_cache WHERE district_id = ? AND fetched_at > (NOW() - INTERVAL 30 MINUTE) ORDER BY fetched_at DESC LIMIT 1");
    $stmt->execute([$d['id']]);
    $cached = $stmt->fetch();

    if ($cached) {
        $weather = ['temperature_c' => $cached['temperature_c'], 'rain_mm' => $cached['rain_mm'], 'weather_code' => $cached['weather_code']];
    } else {
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$d['latitude']}&longitude={$d['longitude']}&current=temperature_2m,rain,weather_code";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $resp = curl_exec($ch);
        curl_close($ch);
        $json = $resp ? json_decode($resp, true) : null;

        if ($json && isset($json['current'])) {
            $weather = [
                'temperature_c' => $json['current']['temperature_2m'],
                'rain_mm' => $json['current']['rain'],
                'weather_code' => $json['current']['weather_code'],
            ];
            $ins = $pdo->prepare("INSERT INTO weather_cache (district_id, temperature_c, rain_mm, weather_code) VALUES (?,?,?,?)");
            $ins->execute([$d['id'], $weather['temperature_c'], $weather['rain_mm'], $weather['weather_code']]);
        } else {
            // fallback to last known cache regardless of age
            $stmt2 = $pdo->prepare("SELECT * FROM weather_cache WHERE district_id = ? ORDER BY fetched_at DESC LIMIT 1");
            $stmt2->execute([$d['id']]);
            $old = $stmt2->fetch();
            $weather = $old ? ['temperature_c' => $old['temperature_c'], 'rain_mm' => $old['rain_mm'], 'weather_code' => $old['weather_code']] : ['temperature_c' => null, 'rain_mm' => null, 'weather_code' => null];
        }
    }

    $out[] = [
        'id' => $d['id'], 'name' => $d['name'],
        'latitude' => (float)$d['latitude'], 'longitude' => (float)$d['longitude'],
        'weather' => $weather,
    ];
}

echo json_encode($out);
