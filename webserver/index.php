<?php
// index.php

// Temporäre Fehlerberichterstattung für Debugging (nicht in der Produktion verwenden)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/************************************************************
 * 1) PHP FUNCTIONS FOR SOCKET, TASKS, ETC.
 ************************************************************/

/**
 * Calls the socket.py script with '0' or '1' to turn the socket off or on.
 * Returns the output from the script.
 *
 * @param string $onOff '1' to turn on, '0' to turn off
 * @return string Output from socket.py
 */
function switchSocket($onOff) {
    // Absolute Pfad zur socket.py, falls nötig anpassen
    $pythonScriptPath = __DIR__ . "/sensors/socket.py";
    
    // Prüfen, ob die Datei existiert
    if (!file_exists($pythonScriptPath)) {
        return "socket.py not found.";
    }

    // Command zusammenstellen
    $command = "/usr/bin/python3 " . escapeshellarg($pythonScriptPath) . " " . escapeshellarg($onOff) . " 2>&1";
    
    // Skript ausführen
    $output = shell_exec($command);
    
    // Rückgabe des Outputs
    return $output;
}

/**
 * Placeholder for LED status. Can be expanded if LED control is implemented.
 *
 * @return string Placeholder text
 */
function getLEDStatus() {
    return 'No data available';
}

/**
 * Reads tasks from the JSON file.
 *
 * @return array Array of tasks
 */
function readTasks() {
    $exercisesFile = __DIR__ . '/exercises.txt';
    
    if (!file_exists($exercisesFile)) {
        // Wenn die Datei nicht existiert, ein leeres Array zurückgeben
        return [];
    }

    $jsonData = file_get_contents($exercisesFile);
    $tasks = json_decode($jsonData, true);

    if (!is_array($tasks)) {
        // Wenn JSON ungültig ist, ein leeres Array zurückgeben
        return [];
    }

    return $tasks;
}

/**
 * Writes tasks to the JSON file.
 *
 * @param array $tasks Array of tasks
 * @return bool True on success, false on failure
 */
function writeTasks($tasks) {
    $exercisesFile = __DIR__ . '/exercises.txt';
    $jsonData = json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if ($jsonData === false) {
        // JSON-Encoding fehlgeschlagen
        return false;
    }

    // Schreiben mit exklusivem Lock zur Vermeidung von Schreibkonflikten
    $result = file_put_contents($exercisesFile, $jsonData, LOCK_EX);
    return ($result !== false);
}

/************************************************************
 * 2) HANDLE POST REQUESTS
 ************************************************************/
$socketMessage = null;  // Speichert die letzte Nachricht von socket.py
$addTaskError = null;   // Speichert Fehlermeldungen beim Hinzufügen von Tasks

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Socket an-/ausschalten
    if (isset($_POST['socket_on'])) {
        $socketMessage = switchSocket('1');
    } elseif (isset($_POST['socket_off'])) {
        $socketMessage = switchSocket('0');
    }

    // LED an-/ausschalten (Platzhalter)
    if (isset($_POST['led_on'])) {
        // LED an Logik hier implementieren
    } elseif (isset($_POST['led_off'])) {
        // LED aus Logik hier implementieren
    }

    // Task hinzufügen
    if (isset($_POST['name'], $_POST['description'], $_POST['deadline'])) {
        // Eingaben trimmen
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $deadline = trim($_POST['deadline']);

        // Basisvalidierung
        if ($name === '' || $description === '' || $deadline === '') {
            $addTaskError = 'All fields are required.';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
            $addTaskError = 'Deadline must be in YYYY-MM-DD format.';
        } else {
            // Bestehende Tasks lesen
            $tasks = readTasks();

            // Neue Aufgabe hinzufügen
            $newTask = [
                'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                'description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
                'deadline' => $deadline // Annahme: gültiges Datum
            ];
            $tasks[] = $newTask;

            // Zurückschreiben in die Datei
            if (writeTasks($tasks)) {
                // Erfolgreich hinzugefügt, Weiterleitung zur tasks Seite
                header('Location: ?page=exercises');
                exit;
            } else {
                $addTaskError = 'Failed to write to exercises.txt. Check file permissions.';
            }
        }
    }
}

/************************************************************
 * 3) HANDLE DELETE REQUEST FOR TASKS
 ************************************************************/
$deleteMessage = null; // Speichert Nachrichten zum Löschen

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteIndex = (int) $_GET['delete'];
    $tasks = readTasks();

    if (isset($tasks[$deleteIndex])) {
        // Aufgabe entfernen
        array_splice($tasks, $deleteIndex, 1);

        // Zurückschreiben in die Datei
        if (writeTasks($tasks)) {
            $deleteMessage = "Task at index $deleteIndex has been deleted.";
            // Weiterleitung zur aktuellen Seite, um erneutes Löschen beim Refresh zu vermeiden
            $currentPage = isset($_GET['page']) ? $_GET['page'] : 'start';
            header("Location: ?page=$currentPage");
            exit;
        } else {
            $deleteMessage = 'Failed to delete task. Check file permissions.';
        }
    } else {
        $deleteMessage = 'Task not found.';
    }
}

/************************************************************
 * 4) DETERMINE WHICH PAGE TO SHOW
 ************************************************************/
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'start';

/************************************************************
 * 5) CREATE A VARIABLE FOR DISPLAYING SOCKET STATUS
 ************************************************************/
$socketStatus = 'Status unknown';
if ($socketMessage !== null) {
    // Wenn die Ausgabe von socket.py "eingeschaltet" enthält
    if (strpos($socketMessage, 'eingeschaltet') !== false) {
        $socketStatus = 'Socket turned on';
    } elseif (strpos($socketMessage, 'ausgeschaltet') !== false) {
        $socketStatus = 'Socket turned off';
    } else {
        $socketStatus = 'Socket state unknown';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DeskPlanner</title>
    <link rel="stylesheet" href="deskplanner.css">
</head>
<body>

    <!-- Background image -->
    <img class="background-image" src="img/background.jpg" alt="Background">

    <!-- Top container / navigation -->
    <div class="container">
        <div class="nav-left">
            <img src="img/logo.png" class="logo" alt="Logo">
            <div class="menu">
                <a href="?page=start" class="menu-link <?= ($currentPage=='start') ? 'active' : '' ?>">Start</a> |
                <a href="?page=week" class="menu-link <?= ($currentPage=='week') ? 'active' : '' ?>">Current Week</a> |
                <a href="?page=deadlines" class="menu-link <?= ($currentPage=='deadlines') ? 'active' : '' ?>">Deadlines</a> |
                <a href="?page=exercises" class="menu-link <?= ($currentPage=='exercises') ? 'active' : '' ?>">Tasks</a> |
                <a href="?page=exercise" class="menu-link <?= ($currentPage=='exercise') ? 'active' : '' ?>">Add Task</a>
            </div>
        </div>
    </div>

    <!-- Main content area -->
    <div class="page-content">
        <?php
        // Zeige die Socket-Status Nachricht, wenn vorhanden
        if ($socketMessage !== null) {
            echo "<p style='color:blue;'><strong>$socketStatus</strong></p>";
        }

        // Zeige Fehlermeldung beim Hinzufügen eines Tasks, wenn vorhanden
        if ($addTaskError !== null) {
            echo "<p style='color:red;'><strong>$addTaskError</strong></p>";
        }

        // Zeige Nachricht beim Löschen eines Tasks, wenn vorhanden
        if ($deleteMessage !== null) {
            echo "<p style='color:green;'><strong>$deleteMessage</strong></p>";
        }

        // Lade die angeforderte Unterseite
        $subPageFile = __DIR__ . '/pages/' . $currentPage . '.php';
        if (file_exists($subPageFile)) {
            include $subPageFile;
        } else {
            include __DIR__ . '/pages/start.php';
        }
        ?>
    </div>

    <!-- JavaScript file -->
    <script src="script.js"></script>
</body>
</html>
