<?php
// Returns live weather for a single district (used by the dashboard weather card).
// Accepts ?district=Name. Falls back to the logged-in user's own district if omitted.
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
header('Content-Type: application/json');

$name = trim($_GET['district'] ?? (current_user()['district'] ?? ''));
if (!$name) { echo json_encode(['error' => 'No district specified']); exit; }

$stmt = $pdo->prepare("SELECT * FROM districts WHERE name = ?");
$stmt->execute([$name]);
$d = $stmt->fetch();
if (!$d) { echo json_encode(['error' => 'Unknown district']); exit; }

// Check cache (last 30 minutes)
$stmt = $pdo->prepare("SELECT * FROM weather_cache WHERE district_id = ? AND fetched_at > (NOW() - INTERVAL 30 MINUTE) ORDER BY fetched_at DESC LIMIT 1");
$stmt->execute([$d['id']]);
$cached = $stmt->fetch();

$weather = null;
if ($cached) {
    $weather = [
        'temperature_c' => $cached['temperature_c'], 'rain_mm' => $cached['rain_mm'],
        'weather_code' => $cached['weather_code'], 'humidity' => null, 'wind_kph' => null,
    ];
} else {
    $url = "https://api.open-meteo.com/v1/forecast?latitude={$d['latitude']}&longitude={$d['longitude']}"
         . "&current=temperature_2m,rain,weather_code,relative_humidity_2m,wind_speed_10m"
         . "&daily=temperature_2m_max,temperature_2m_min&forecast_days=1&timezone=Africa%2FKampala";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    $resp = curl_exec($ch);
    curl_close($ch);
    $json = $resp ? json_decode($resp, true) : null;

    if ($json && isset($json['current'])) {
        $c = $json['current'];
        $weather = [
            'temperature_c' => $c['temperature_2m'] ?? null,
            'rain_mm' => $c['rain'] ?? null,
            'weather_code' => $c['weather_code'] ?? null,
            'humidity' => $c['relative_humidity_2m'] ?? null,
            'wind_kph' => isset($c['wind_speed_10m']) ? round($c['wind_speed_10m'], 1) : null,
            'temp_max' => $json['daily']['temperature_2m_max'][0] ?? null,
            'temp_min' => $json['daily']['temperature_2m_min'][0] ?? null,
        ];
        $ins = $pdo->prepare("INSERT INTO weather_cache (district_id, temperature_c, rain_mm, weather_code) VALUES (?,?,?,?)");
        $ins->execute([$d['id'], $weather['temperature_c'], $weather['rain_mm'], $weather['weather_code']]);
    } else {
        $stmt2 = $pdo->prepare("SELECT * FROM weather_cache WHERE district_id = ? ORDER BY fetched_at DESC LIMIT 1");
        $stmt2->execute([$d['id']]);
        $old = $stmt2->fetch();
        $weather = $old
            ? ['temperature_c' => $old['temperature_c'], 'rain_mm' => $old['rain_mm'], 'weather_code' => $old['weather_code'], 'humidity' => null, 'wind_kph' => null]
            : ['temperature_c' => null, 'rain_mm' => null, 'weather_code' => null, 'humidity' => null, 'wind_kph' => null];
    }
}

echo json_encode(['district' => $d['name'], 'weather' => $weather]);
