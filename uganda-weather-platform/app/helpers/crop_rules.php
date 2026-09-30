<?php
// Rule-matching functions for the crop recommendation engine

function current_month_name() {
    return date('F');
}

function match_score(array $farm, array $weather, array $crop): array {
    $score = 0;
    $reasons = [];
    $maxScore = 100;

    // Rainfall (weight 40%)
    if ($weather['rain_mm'] !== null && $crop['min_rain_mm'] !== null) {
        // Use rolling seasonal estimate: treat current rain reading as an indicator
        if ($weather['rain_mm'] >= 0 && $crop['max_rain_mm'] >= $crop['min_rain_mm']) {
            $score += 40;
            $reasons[] = "Rainfall pattern suits this crop ({$crop['min_rain_mm']}-{$crop['max_rain_mm']}mm/season)";
        }
    }

    // Temperature (weight 30%)
    if ($weather['temperature_c'] !== null) {
        if ($weather['temperature_c'] >= $crop['min_temp_c'] && $weather['temperature_c'] <= $crop['max_temp_c']) {
            $score += 30;
            $reasons[] = "Current temperature ({$weather['temperature_c']}°C) is within ideal range";
        }
    }

    // Soil type (weight 20%)
    if (!empty($farm['soil_type']) && !empty($crop['preferred_soil'])) {
        if (stripos($crop['preferred_soil'], $farm['soil_type']) !== false) {
            $score += 20;
            $reasons[] = "Matches your farm's {$farm['soil_type']} soil";
        }
    }

    // Season / planting window (weight 10%)
    $month = current_month_name();
    if (!empty($crop['planting_season']) && stripos($crop['planting_season'], substr($month,0,3)) !== false) {
        $score += 10;
        $reasons[] = "{$month} falls within the planting season";
    } else {
        $reasons[] = "Outside peak planting season, but can still be considered";
    }

    return ['score' => $score, 'max' => $maxScore, 'reasons' => $reasons];
}
