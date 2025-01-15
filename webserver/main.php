<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DeskPlanner</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: 'Roboto', 'sans-serif';
    }

    .background-image {
      height: 100vh;
      width: 100%;
      object-fit: cover;
      position: fixed;
      top: 0;
      left: 0;
      z-index: -1;
      filter: brightness(0.9);
    }

    .container {
      display: flex;
      align-items: center; /* Elemente vertikal zentrieren */
      justify-content: space-between; /* Abstand zwischen den Gruppen */
      padding: 0 32px; /* Abstand rechts und links */
      margin-top: 30px; /* Abstand von oben */
    }

    .nav-left {
      display: flex;
      align-items: center;
    }

    .menu {
      margin-left: 16px; /* Platz zwischen Logo und Links */
    }

    .menu-link {
      color: white;
      text-decoration: none;
      margin-left: 16px;
    }

    .menu-link:hover {
      text-decoration: underline;
    }

    .active {
      text-decoration: underline;
      font-weight: bold;
    }

    .logo {
      height: 40px;
      width: 40px;
    }

    .page-content {
      position: absolute;
      top: 100px;
      left: 50px;
      right: 50px;
      padding: 20px;
      background-color: rgba(255, 255, 255, 0.8);
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    h1 {
      font-size: 36px;
      color: navy;
      margin-bottom: 20px;
    }

    p {
      font-size: 18px;
      line-height: 1.5;
      color: #333;
    }

    .form-container input {
      width: 100%;
      margin-bottom: 10px;
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .form-container button {
      padding: 10px 20px;
      font-size: 16px;
      background-color: navy;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .form-container button:hover {
      background-color: darkblue;
    }

    .task {
      background-color: #f4f4f4;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 10px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      position: relative;
    }

    .task strong {
      color: navy;
    }

    .delete-button {
      position: absolute;
      top: 10px;
      right: 10px;
      color: red;
      font-size: 18px;
      cursor: pointer;
    }

    .delete-button:hover {
      color: darkred;
    }

    /* Grid für Wochentage */
    .weekdays-container {
      display: flex;
      justify-content: space-around;
      margin-bottom: 20px;
    }

    .weekday-container {
      width: 18%;
      padding: 10px;
      text-align: center;
    }

    .weekday {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 5px; 
    }

    .date {
      font-size: 16px;
      color: #666; /* Optional, um das Datum etwas abzudämpfen */
    }

    /* logout */   
    .logout-container {
      display: flex;
      align-items: center;
    }

    .logout-button {
      padding: 10px 20px;
      font-size: 16px;
      background-color: #ff4d4d; /* Auffällige Farbe */
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-family: 'Roboto', sans-serif;
    }

    .logout-button:hover {
      background-color: #e60000; /* Etwas dunkler bei Hover */
    }

    /* timer, stopwatch */
    .timercontainer {
      display: flex;
      gap: 20px;
      margin-top: 20px;
      justify-content: space-between;
    }

    .timer, .stopwatch {
      flex: 1;
      padding: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      background-color: #f9f9f9;
      text-align: center;
    }

    .timer h2, .stopwatch h2 {
      margin-bottom: 10px;
    }

    
    /* Popup style */
    .popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      padding: 20px;
      background-color: white;
      border: 2px solid #ccc;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      z-index: 10;
    }

    .popup button {
      margin: 10px;
      padding: 10px 20px;
      cursor: pointer;
      font-size: 16px;
    }

    .popup .cancel-btn {
      background-color: lightgray;
    }

    .popup .confirm-btn {
      background-color: red;
      color: white;
    }
  </style>
</head>

<body>
  <img class="background-image" src="/img/background.jpg" alt="Hintergrundbild">

  <div class="container">
    <div class="nav-left">
      <img src="img/logo.png" class="logo">
      <div class="menu">
        <?php
        $current_page = isset($_GET['page']) ? $_GET['page'] : 'start';
        ?>
        <a href="?page=start" class="menu-link <?= $current_page == 'start' ? 'active' : '' ?>">Start</a> |
        <a href="?page=week" class="menu-link <?= $current_page == 'week' ? 'active' : '' ?>">Aktuelle Woche</a> |
        <a href="?page=deadlines" class="menu-link <?= $current_page == 'deadlines' ? 'active' : '' ?>">Deadlines</a> |
        <a href="?page=exercises" class="menu-link <?= $current_page == 'exercises' ? 'active' : '' ?>">Aufgaben</a> |
        <a href="?page=exercise" class="menu-link <?= $current_page == 'exercise' ? 'active' : '' ?>">Aufgabe hinzuf&uuml;gen</a>
      </div>
    </div>
    <form method="POST" action="logout.php" class="logout-container">
      <button type="submit" class="logout-button">Logout</button>
    </form>
  </div>

  <div class="page-content">
    <?php
    header('Content-Type: text/html; charset=UTF-8');

    $headline = 'Mein Wochenplaner';

    // Funktion zum Laden der Temperatur aus sensorData.txt
    function getTemperature() {
      $filePath = __DIR__ . '/sensorData.txt';
      if (file_exists($filePath)) {
          $data = file_get_contents($filePath);
          $values = explode(',', $data); // Annahme: Werte sind durch ein Komma getrennt
          if (isset($values[0])) {
              return htmlspecialchars(trim($values[0]), ENT_QUOTES, 'UTF-8'); // Temperatur
          }
      }
      return 'Keine Daten verf&uuml;gbar'; // Fallback, falls Datei fehlt
    }

    // Funktion zum Laden der Luftfeuchtigkeit aus sensorData.txt
    function getHumidity() {
      $filePath = __DIR__ . '/sensorData.txt';
      if (file_exists($filePath)) {
          $data = file_get_contents($filePath);
          $values = explode(',', $data); // Annahme: Werte sind durch ein Komma getrennt
          if (isset($values[1])) {
              return htmlspecialchars(trim($values[1]), ENT_QUOTES, 'UTF-8'); // Luftfeuchtigkeit
          }
      }
      return 'Keine Daten verf&uuml;gbar'; // Fallback, falls Datei fehlt
    }

    $current_page = isset($_GET['page']) ? $_GET['page'] : 'start';

    if ($current_page == 'start') {
        echo "<h1>Willkommen!</h1>";
        
        // Temperaturanzeige
        $temperature = getTemperature();
        echo "<div class='temperature'>
                <h2>Aktuelle Temperatur:</h2>
                <p><strong>{$temperature}&deg;C</strong></p>
              </div>";

        // Luftfeuchtigkeitsanzeige
        $humidity = getHumidity();
        echo "<div class='humidity'>
                <h2>Aktuelle Luftfeuchtigkeit:</h2>
                <p><strong>{$humidity}%</strong></p>
              </div>";

        // Timer und Stoppuhr
        echo "
        <div class='timercontainer'>
            <div class='timer'>
                <h2>Timer</h2>
                <input id='timer-input' type='number' min='1' max='180' placeholder='Minuten einstellen'>
                <p id='timer-display'>00:00</p>
                <button onclick='startTimer()'>Start</button>
            </div>

            <div class='stopwatch'>
                <h2>Stoppuhr</h2>
                <p id='stopwatch-display'>00:00:00</p>
                <button onclick='startStopwatch()'>Start</button>
                <button onclick='stopStopwatch()'>Stop</button>
                <button onclick='resetStopwatch()'>L&ouml;schen</button>
            </div>
        </div>";
    }

    // Berechne das Datum des Montags der aktuellen Woche
    function getMondayOfCurrentWeek() {
        $currentDate = new DateTime(); // Aktuelles Datum
        $dayOfWeek = $currentDate->format('N'); // Tag der Woche (1 = Montag, 7 = Sonntag)
        $currentDate->modify('-' . ($dayOfWeek - 1) . ' days'); // Gehe zurück auf den Montag
        return $currentDate;
    }

    // Berechne das Datum des Sonntags der aktuellen Woche
    function getSundayOfCurrentWeek() {
        $currentDate = getMondayOfCurrentWeek();
        $currentDate->modify('+6 days'); // Gehe zum Sonntag
        return $currentDate;
    }

    // Funktion zum Prüfen, ob das Datum in der aktuellen Woche liegt
    function isInCurrentWeek($date) {
        $monday = getMondayOfCurrentWeek();
        $sunday = getSundayOfCurrentWeek();
        return $date >= $monday && $date <= $sunday;
    }

    // Erstelle eine Liste der Wochentage von Montag bis Sonntag für die aktuelle Woche
    $monday = getMondayOfCurrentWeek();
    $weekDays = [];
    
    for ($i = 0; $i < 7; $i++) {
        // Jeden Tag einzeln erstellen, um das $monday-Objekt nicht zu verändern
        $day = clone $monday;
        $day->modify('+' . $i . ' days');
        $weekDays[] = $day;
    }
    

    // Aufgaben nach Wochentagen gruppieren
    $tasksByWeekday = [
        'Monday' => [],
        'Tuesday' => [],
        'Wednesday' => [],
        'Thursday' => [],
        'Friday' => [],
        'Saturday' => [],
        'Sunday' => []
    ];

    // Aufgaben aus Datei laden
    $filePath = __DIR__ . '/exercises.txt';
    $exercises = [];

    // Aufgaben aus der Datei lesen, falls sie existiert
    if (file_exists($filePath)) {
        $text = file_get_contents($filePath);
        $decoded = json_decode($text, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            echo '<p style="color: red;">Fehler: Ung&uuml;ltige JSON-Daten in exercises.txt</p>';
        } else {
            $exercises = $decoded;
        }
    }

    // Die Aufgaben den Wochentagen zuordnen
    foreach ($exercises as $exercise) {
        $taskDate = new DateTime($exercise['deadline']);
        
        // Prüfen, ob die Aufgabe in der aktuellen Woche liegt
        if (isInCurrentWeek($taskDate)) {
            // Aufgabe dem richtigen Wochentag zuordnen
            foreach ($weekDays as $weekday) {
                if ($taskDate->format('Y-m-d') === $weekday->format('Y-m-d')) {
                    $tasksByWeekday[date('l', strtotime($weekday->format('Y-m-d')))][] = $exercise;
                    break;
                }
            }
        }
    }

    // Überprüfen, ob eine Aufgabe gelöscht werden soll
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $deleteIndex = (int)$_GET['delete'];
        // Vergewissern, dass der Index gültig ist und innerhalb des Arrays existiert
        if (isset($exercises[$deleteIndex])) {
            // Aufgabe löschen
            array_splice($exercises, $deleteIndex, 1);
            // Aufgaben nach dem Löschen in die Datei speichern
            file_put_contents($filePath, json_encode($exercises, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    // Neue Aufgabe hinzufügen
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['description']) && isset($_POST['deadline'])) {
        // Neue Aufgabe
        $newExercise = [
            'name' => htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars(substr($_POST['description'], 0, 150), ENT_QUOTES, 'UTF-8'), // Begrenzung auf 150 Zeichen
            'deadline' => htmlspecialchars($_POST['deadline'], ENT_QUOTES, 'UTF-8'),
        ];
        // Aufgabe zum Array hinzufügen
        array_push($exercises, $newExercise);

        // Aufgaben in die Datei speichern
        $result = file_put_contents($filePath, json_encode($exercises, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        if ($result === false) {
            echo '<p style="color: red;">Fehler: Die Datei konnte nicht gespeichert werden.</p>';
        } else {
            echo '<p>Aufgabe <b>' . htmlspecialchars($newExercise['name'], ENT_QUOTES, 'UTF-8') . '</b> wurde hinzugef&uuml;gt!</p>';
        }
    }

    // Ausgabe der aktuellen Woche mit den Aufgaben unter "Aktuelle Woche"
    if ($_GET['page'] == 'week') {
        echo "<h1>Aktuelle Woche</h1>";
        echo "<div class='weekdays-container'>";

        foreach ($weekDays as $weekday) {
            $dayName = $weekday->format('l'); // Name des Wochentags
            $dayFormatted = $weekday->format('d.m.Y');
            echo "<div class='weekday-container'>
                    <div class='weekday'>$dayName</div>
                    <div class='date'>$dayFormatted</div>"; // Datum unter dem Wochentag anzeigen

        // Aufgaben des jeweiligen Wochentags anzeigen
            $tasksForToday = $tasksByWeekday[$dayName];
            if (empty($tasksForToday)) {
                echo "<p>-</p>";
            } else {
                foreach ($tasksForToday as $task) {
                    echo "<div class='task'>
                            <strong>" . htmlspecialchars($task['name'], ENT_QUOTES, 'UTF-8') . ":</strong> " . htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8') . "
                          </div>";
                }
            }
    
            echo "</div>"; // Ende der weekday-container
        }
    
        echo "</div>"; // Ende der weekdays-container
    }

    // Ausgabe der Aufgaben unter "Aufgaben"
    if ($current_page == 'exercises') {
        echo "<p>Hier sind deine aktuellen Aufgaben:</p>";
        foreach ($exercises as $index => $exercise) {
            echo "<div class='task'>
                    <strong>" . htmlspecialchars($exercise['name'], ENT_QUOTES, 'UTF-8') . ":</strong> " . htmlspecialchars($exercise['description'], ENT_QUOTES, 'UTF-8') . "
                    <a href='?page=" . $current_page . "&delete=" . $index . "' class='delete-button'>[X]</a>
                  </div>";
        }
    }

    // Ausgabe der Deadlines unter "Deadlines"
    if ($current_page == 'deadlines') {
        echo "<p>Hier sind deine aktuellen Deadlines:</p>";
        // Deadlines nach Datum sortieren
        usort($exercises, function ($a, $b) {
            return strtotime($a['deadline']) - strtotime($b['deadline']);
        });
        foreach ($exercises as $index => $exercise) {
            $deadline = htmlspecialchars($exercise['deadline']);
            echo "<div class='task'>
                    <strong>" . htmlspecialchars($exercise['name'], ENT_QUOTES, 'UTF-8') . ":</strong> " . htmlspecialchars($exercise['description'], ENT_QUOTES, 'UTF-8') . "
                    <br><strong>Deadline:</strong> <span style='font-weight: bold;'>" . $deadline . "</span>
                    <a href='?page=" . $current_page . "&delete=" . $index . "' class='delete-button'>[X]</a>
                  </div>";
        }
    }

    // Formular zum Hinzufügen einer neuen Aufgabe
    if ($current_page == 'exercise') {
        echo "
        <div class='form-container'>
            <p>F&uuml;ge eine Aufgabe hinzu:</p>
            <form action='?page=exercises' method='POST'>
                <input placeholder='Aufgabentitel eingeben' name='name'required>
                <input placeholder='Beschreibung eingeben' name='description' required>
                <input type='date' name='deadline' required> 
                <button type='submit'>Absenden</button>
            </form>
        </div>";
    }
    ?>
  </div>

  <!-- Einbinden des externen JavaScripts -->
  <script src="script.js"></script>

  <!-- Popup-Dialog -->
  <div class="popup" id="popup">
    <p>M&ouml;chtest du diese Aufgabe wirklich l&ouml;schen?</p>
    <button class="cancel-btn" onclick="cancelDelete()">Nein</button>
    <button class="confirm-btn" onclick="confirmDeleteAction()">Ja</button>
  </div>

  <script>
    let taskIndexToDelete = null;

    function confirmDelete(index) {
      taskIndexToDelete = index;
      document.getElementById('popup').style.display = 'block';
    }

    function cancelDelete() {
      document.getElementById('popup').style.display = 'none';
      taskIndexToDelete = null;
    }

    function confirmDeleteAction() {
      if (taskIndexToDelete !== null) {
        window.location.href = `?page=deadlines&delete=${taskIndexToDelete}`;
      }
    }

    // Stoppuhr-Logik
    let stopwatchInterval;
    let stopwatchTime = 0;

    function startStopwatch() {
        if (!stopwatchInterval) {
            stopwatchInterval = setInterval(() => {
                stopwatchTime++;
                const hours = Math.floor(stopwatchTime / 3600).toString().padStart(2, '0');
                const minutes = Math.floor((stopwatchTime % 3600) / 60).toString().padStart(2, '0');
                const seconds = (stopwatchTime % 60).toString().padStart(2, '0');
                document.getElementById('stopwatch-display').innerText = `${hours}:${minutes}:${seconds}`;
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
        document.getElementById('stopwatch-display').innerText = "00:00:00";
    }

    // Timer-Logik
    let timerInterval;

    function startTimer() {
        const input = document.getElementById('timer-input');
        let time = parseInt(input.value) * 60;

        if (isNaN(time) || time <= 0 || time > 180 * 60) {
            alert('Bitte eine g&uuml;ltige Zeit (1-180 Minuten) eingeben.');
            return;
        }

        clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            if (time > 0) {
                time--;
                const minutes = Math.floor(time / 60).toString().padStart(2, '0');
                const seconds = (time % 60).toString().padStart(2, '0');
                document.getElementById('timer-display').innerText = `${minutes}:${seconds}`;
            } else {
                clearInterval(timerInterval);
                alert('Timer abgelaufen!');
            }
        }, 1000);
    }
  </script>
</body>

</html>
