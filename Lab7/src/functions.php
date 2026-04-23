<?php
declare(strict_types=1);

function getHabits(string $dataFile): array {
    if (!file_exists($dataFile)) {
        file_put_contents($dataFile, json_encode([]));
    }
    $json = file_get_contents($dataFile);
    return json_decode($json, true) ?: [];
}

function saveHabit(string $dataFile, array $habit): void {
    $habits = getHabits($dataFile);
    $habits[] = $habit;
    file_put_contents($dataFile, json_encode($habits, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
