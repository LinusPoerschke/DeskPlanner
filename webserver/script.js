// script.js

// Stopwatch logic
let stopwatchInterval;
let stopwatchTime = 0;

function startStopwatch() {
  if (!stopwatchInterval) {
    stopwatchInterval = setInterval(() => {
      stopwatchTime++;
      const hours   = Math.floor(stopwatchTime / 3600).toString().padStart(2, '0');
      const minutes = Math.floor((stopwatchTime % 3600) / 60).toString().padStart(2, '0');
      const seconds = (stopwatchTime % 60).toString().padStart(2, '0');
      document.getElementById('stopwatch-display').textContent = `${hours}:${minutes}:${seconds}`;
    }, 1000);
  }
}

function stopStopwatch() {
  clearInterval(stopwatchInterval);
  stopwatchInterval = null;
}

function resetStopwatch() {
  stopStopwatch();
  stopwatchTime = 0;
  document.getElementById('stopwatch-display').textContent = "00:00:00";
}

// Timer logic
let timerInterval;
let remainingTime;

function startTimer() {
  const timerInput = document.getElementById('timer-input').value;
  if (timerInput && timerInput > 0) {
    remainingTime = parseInt(timerInput, 10) * 60;
    document.getElementById('timer-display').textContent = formatTime(remainingTime);
    timerInterval = setInterval(updateTimer, 1000);
  }
}

function updateTimer() {
  if (remainingTime > 0) {
    remainingTime--;
    document.getElementById('timer-display').textContent = formatTime(remainingTime);
  } else {
    clearInterval(timerInterval);
    alert("Time's up!");
  }
}

function formatTime(seconds) {
  const minutes = Math.floor(seconds / 60);
  const sec     = seconds % 60;
  return `${String(minutes).padStart(2, '0')}:${String(sec).padStart(2, '0')}`;
}

// Fetch temperature and humidity from sensorData.php
async function fetchSensorData() {
  try {
    const response = await fetch('sensorData.php');
    const data     = await response.json();
    if (data.error) {
      console.error("Error fetching sensor data:", data.error);
      return;
    }
    // Place temperature/humidity in DOM
    const tempElem = document.getElementById('tempValue');
    const humElem  = document.getElementById('humValue');
    if (tempElem) {
      tempElem.textContent = data.temperature.toFixed(1);
    }
    if (humElem) {
      humElem.textContent  = data.humidity;
    }
  } catch (err) {
    console.error("AJAX request failed:", err);
  }
}

// Call fetchSensorData once page is loaded, then every 10 seconds
document.addEventListener('DOMContentLoaded', function() {
  fetchSensorData();
  setInterval(fetchSensorData, 10000);
});
