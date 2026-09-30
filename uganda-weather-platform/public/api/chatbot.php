<?php
// Lightweight rule-based assistant. Not a large language model — it matches
// intents by keyword and answers using live data from the crops/districts
// tables, so its "knowledge" always matches what's actually in the database.
// This keeps the chatbot dependency-free and safe to ship without an API key.
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');
if ($message === '') { echo json_encode(['reply' => "Ask me about a crop, the weather, or how to log a planting."]); exit; }

$msg = strtolower($message);
$user = current_user();

function crop_lookup(PDO $pdo, string $msg) {
    $crops = $pdo->query("SELECT * FROM crops WHERE status='active'")->fetchAll();
    foreach ($crops as $c) {
        if (strpos($msg, strtolower($c['name'])) !== false || strpos($msg, strtolower(explode('(', $c['name'])[0])) !== false) {
            return $c;
        }
    }
    // also match common short names like "matooke", "coffee"
    $aliases = ['matooke' => 'Bananas/Matooke', 'robusta' => 'Coffee (Robusta)', 'rice' => 'Rice (Upland)'];
    foreach ($aliases as $alias => $full) {
        if (strpos($msg, $alias) !== false) {
            foreach ($crops as $c) if ($c['name'] === $full) return $c;
        }
    }
    return null;
}

$reply = null;

// 1. Greetings
if (preg_match('/\b(hi|hello|hey|good morning|good afternoon)\b/', $msg)) {
    $name = $user ? $user['full_name'] : 'there';
    $reply = "Hello, " . explode(' ', $name)[0] . "! I can tell you about crops (planting time, watering, pests), current weather in a district, how to log a planting, or how to submit a yield report. What do you need?";
}

// 2. Crop-specific question
if (!$reply) {
    $crop = crop_lookup($pdo, $msg);
    if ($crop) {
        if (strpos($msg, 'pest') !== false) {
            $reply = "Common pests for {$crop['name']}: " . ($crop['common_pests'] ?: 'none recorded') . ".";
        } elseif (strpos($msg, 'disease') !== false) {
            $reply = "Common diseases for {$crop['name']}: " . ($crop['common_diseases'] ?: 'none recorded') . ".";
        } elseif (strpos($msg, 'water') !== false) {
            $reply = "Best watering time for {$crop['name']}: " . ($crop['best_watering_time'] ?: 'not specified') . ".";
        } elseif (strpos($msg, 'plant') !== false || strpos($msg, 'when') !== false || strpos($msg, 'season') !== false) {
            $reply = "{$crop['name']} is best planted during: {$crop['planting_season']} ({$crop['planting_window']}). It takes about {$crop['days_to_maturity']} days to mature.";
        } elseif (strpos($msg, 'soil') !== false) {
            $reply = "{$crop['name']} does best in: " . ($crop['preferred_soil'] ?: 'a range of soils') . ".";
        } else {
            $reply = "{$crop['name']} ({$crop['scientific_name']}): plant during {$crop['planting_season']}, needs {$crop['min_rain_mm']}-{$crop['max_rain_mm']}mm rainfall and {$crop['min_temp_c']}-{$crop['max_temp_c']}°C, matures in about {$crop['days_to_maturity']} days. Ask me about its pests, diseases, watering or soil for more detail.";
        }
    }
}

// 3. Weather question
if (!$reply && (strpos($msg, 'weather') !== false || strpos($msg, 'rain') !== false || strpos($msg, 'temperature') !== false)) {
    $district = null;
    $districts = $pdo->query("SELECT name FROM districts")->fetchAll();
    foreach ($districts as $d) {
        if (strpos($msg, strtolower($d['name'])) !== false) { $district = $d['name']; break; }
    }
    if (!$district && $user) $district = $user['district'];

    if ($district) {
        $stmt = $pdo->prepare("SELECT * FROM districts WHERE name = ?");
        $stmt->execute([$district]);
        $d = $stmt->fetch();
        if ($d) {
            $ch = curl_init("https://api.open-meteo.com/v1/forecast?latitude={$d['latitude']}&longitude={$d['longitude']}&current=temperature_2m,rain");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $resp = curl_exec($ch);
            curl_close($ch);
            $json = $resp ? json_decode($resp, true) : null;
            if ($json && isset($json['current'])) {
                $reply = "Right now in {$district}: {$json['current']['temperature_2m']}°C, {$json['current']['rain']}mm of rain. Check the weather card on your dashboard or the full map for more districts.";
            }
        }
    }
    if (!$reply) {
        $reply = "I can check live weather for any of our 12 districts — just mention the district name, e.g. \"weather in Mbale\". You can also see the live map at " . BASE_URL . "/map.php.";
    }
}

// 4. Recommendation / "what should I plant"
if (!$reply && (strpos($msg, 'recommend') !== false || strpos($msg, 'what should i plant') !== false || strpos($msg, 'advice') !== false)) {
    $reply = $user
        ? "Head to the Crop Advisor (" . BASE_URL . "/recommend.php) and pick one of your farms — I'll score every active crop against that farm's soil, current weather and the season."
        : "Register and add a farm first, then the Crop Advisor will score every crop against your farm's soil, weather and the season.";
}

// 5. How to log planting / yield report
if (!$reply && (strpos($msg, 'log') !== false || strpos($msg, 'planting') !== false) && strpos($msg, 'yield') === false) {
    $reply = "To log a planting: go to My Reports → Log Planting, choose the farm and crop, and enter the planting date. You can mark it \"harvested\" later.";
}
if (!$reply && strpos($msg, 'yield') !== false) {
    $reply = "Once a planting is marked \"harvested\", go to My Reports → Submit Yield Report to record whether it went well or badly and why. That feeds the analytics dashboards.";
}

// 6. Farm help
if (!$reply && strpos($msg, 'farm') !== false) {
    $reply = "You can add a farm under My Farms → Add Farm. Fill in its district, soil type and size — this is what the Crop Advisor uses to score recommendations.";
}

// 7. Fallback
if (!$reply) {
    $reply = "I'm not sure about that yet. Try asking about a specific crop (e.g. \"when to plant maize\"), the weather in a district, how to log a planting, or how to get crop recommendations.";
}

echo json_encode(['reply' => $reply]);
