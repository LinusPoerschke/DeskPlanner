<?php
// sensorData.php
header('Content-Type: application/json; charset=utf-8');

// Path to your temperature.py
$pythonScriptPath = __DIR__ . "/sensors/temperature.py";

$output = shell_exec("/usr/bin/python3 " . escapeshellarg($pythonScriptPath));

// If there's no output, return an error
if (!$output) {
    echo json_encode([
        'error' => 'No data received or error running temperature.py'
    ]);
    exit;
}

// Trim and check if there's an error reading the sensor
$output = trim($output);
if (strpos($output, 'Fehler beim Lesen') !== false) {
    echo json_encode(['error' => $output]);
    exit;
}

// Expect "23.1,45" or similar
list($temperature, $humidity) = explode(',', $output);

// Convert them to float/int
$temperature = (float)$temperature;
$humidity    = (int)$humidity;

// Return as JSON
echo json_encode([
    'temperature' => $temperature,
    'humidity'    => $humidity
]);
