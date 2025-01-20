<?php
// pages/start.php

// Example LED placeholder if needed
$ledStatus = getLEDStatus();

// The $socketStatus is prepared in index.php after a POST
global $socketStatus;
?>
<head>

    <link rel="stylesheet" href="deskplanner.css">
</head>
<h1>Welcome!</h1>

<div class="status-container">
    <div class="status-left">
        <div class="temperature">
            <h2>Temperature:</h2>
            <!-- This will be updated by script.js via AJAX from sensorData.php -->
            <p><strong id="tempValue">---</strong> °C</p>
        </div>

        <div class="humidity">
            <h2>Humidity:</h2>
            <!-- This will be updated by script.js via AJAX from sensorData.php -->
            <p><strong id="humValue">---</strong> %</p>
        </div>
    </div>

    <div class="status-right">
        <!-- Optional LED status (if you have a script, otherwise remove) -->
        <div class="led">
            <h2>LED Status:</h2>
            <p><strong><?= htmlspecialchars($ledStatus); ?></strong></p>
            <form method="POST" action="?page=start">
                <button type="submit" name="led_on">Turn LED On</button>
                <button type="submit" name="led_off">Turn LED Off</button>
            </form>
        </div>

        <!-- Socket control -->
        <div class="socket">
            <h2>Socket Control:</h2>
            <p>Current status: <strong><?= htmlspecialchars($socketStatus); ?></strong></p>
            <form method="POST" action="?page=start">
                <button type="submit" name="socket_on">Turn Socket On</button>
                <button type="submit" name="socket_off">Turn Socket Off</button>
            </form>
        </div>
    </div>
</div>

<!-- Timer and stopwatch sections -->
<div class="timercontainer">
    <div class="timer">
        <h2>Timer</h2>
        <input id="timer-input" type="number" min="1" max="180" placeholder="Minutes">
        <p id="timer-display">00:00</p>
        <button onclick="startTimer()">Start</button>
    </div>

    <div class="stopwatch">
        <h2>Stopwatch</h2>
        <p id="stopwatch-display">00:00:00</p>
        <button onclick="startStopwatch()">Start</button>
        <button onclick="stopStopwatch()">Stop</button>
        <button onclick="resetStopwatch()">Reset</button>
    </div>
</div>
