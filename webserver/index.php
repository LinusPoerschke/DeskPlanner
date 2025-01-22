<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Temporary error reporting for debugging 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/************************************************************
 * 1) PHP FUNCTIONS FOR SOCKET, LED, TASKS, ETC.
 ************************************************************/

/**
 * Calls the socket.py script with '0' or '1' to turn the socket off or on.
 * Returns the output from the script.
 *
 * @param string $onOff '1' to turn on, '0' to turn off
 * @return string Output from socket.py
 */
function switchSocket($onOff) {
    // Absolute path to socket.py, adjust if necessary
    $pythonScriptPath = __DIR__ . "/sensors/socket.py";
    
    // Check if the file exists
    if (!file_exists($pythonScriptPath)) {
        return "socket.py not found.";
    }

    // Assemble the command
    $command = "/usr/bin/python3 " . escapeshellarg($pythonScriptPath) . " " . escapeshellarg($onOff) . " 2>&1";
    
    // Execute the script
    $output = shell_exec($command);
    
    // Return the output
    return $output;
}

/**
 * Calls the LED.py script with '0' or '1' to turn the LED off or on.
 * Returns the output from the script.
 *
 * @param string $onOff '1' to turn on, '0' to turn off
 * @return string Output from LED.py
 */
function switchLED($onOff) {
    // Absolute path to LED.py, adjust if necessary
    $pythonScriptPath = __DIR__ . "/sensors/LED.py";
    
    // Check if the file exists
    if (!file_exists($pythonScriptPath)) {
        return "LED.py not found.";
    }

    // Assemble the command
    $command = "/usr/bin/python3 " . escapeshellarg($pythonScriptPath) . " " . escapeshellarg($onOff) . " 2>&1";
    
    // Execute the script
    $output = shell_exec($command);
    
    // Return the output
    return $output;
}

/**
 * Retrieves the current LED status by calling LED.py without arguments.
 *
 * @return string 'ON', 'OFF' oder 'Unknown'
 */
function getLEDStatus() {
    // Absolute path to LED.py, adjust if necessary
    $pythonScriptPath = __DIR__ . "/sensors/LED.py";
    
    // Check if the file exists
    if (!file_exists($pythonScriptPath)) {
        return "LED.py not found.";
    }

    // Assemble the command to get the LED status
    $command = "/usr/bin/python3 " . escapeshellarg($pythonScriptPath) . " 2>&1";
    
    // Execute the script and capture the output
    $output = trim(shell_exec($command));
    
    // Determine the status based on the output
    if (strtoupper($output) === 'ON') {
        return 'ON';
    } elseif (strtoupper($output) === 'OFF') {
        return 'OFF';
    } else {
        return 'Unknown';
    }
}

/**
 * Reads tasks from the user-specific JSON file.
 *
 * @return array Array of tasks
 */
function readTasks() {
    if (!isset($_SESSION['exercises_file'])) {
        // No task file found in the session
        return [];
    }

    $exercisesFile = $_SESSION['exercises_file'];
    
    if (!file_exists($exercisesFile)) {
        // If the file does not exist, return an empty array
        return [];
    }

    $jsonData = file_get_contents($exercisesFile);
    $tasks = json_decode($jsonData, true);

    if (!is_array($tasks)) {
        // If JSON is invalid, return an empty array
        return [];
    }

    return $tasks;
}

/**
 * Writes tasks to the user-specific JSON file.
 *
 * @param array $tasks Array of tasks
 * @return bool True on success, false on failure
 */
function writeTasks($tasks) {
    if (!isset($_SESSION['exercises_file'])) {
        // No task file found in the session
        return false;
    }

    $exercisesFile = $_SESSION['exercises_file'];
    $jsonData = json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if ($jsonData === false) {
        // JSON encoding failed
        return false;
    }

    // Write with exclusive lock to avoid write conflicts
    $result = file_put_contents($exercisesFile, $jsonData, LOCK_EX);
    return ($result !== false);
}

/************************************************************
 * 2) HANDLE POST REQUESTS
 ************************************************************/
$socketMessage = null;  // Stores the last message from socket.py
$ledMessage = null;     // Stores the last message from LED.py
$addTaskError = null;   // Stores error messages when adding tasks

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Toggle socket
    if (isset($_POST['socket_on'])) {
        $socketMessage = switchSocket('1');
    } elseif (isset($_POST['socket_off'])) {
        $socketMessage = switchSocket('0');
    }

    // Toggle LED
    if (isset($_POST['led_on'])) {
        $ledMessage = switchLED('1');
    } elseif (isset($_POST['led_off'])) {
        $ledMessage = switchLED('0');
    }

    // Add task
    if (isset($_POST['name'], $_POST['description'], $_POST['deadline'])) {
        // Trim inputs
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $deadline = trim($_POST['deadline']);

        // Basic validation
        if ($name === '' || $description === '' || $deadline === '') {
            $addTaskError = 'All fields are required.';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
            $addTaskError = 'Deadline must be in YYYY-MM-DD format.';
        } else {
            // Read existing tasks
            $tasks = readTasks();

            // Add new task
            $newTask = [
                'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                'description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
                'deadline' => $deadline // Assuming valid date
            ];
            $tasks[] = $newTask;

            // Write back to the file
            if (writeTasks($tasks)) {
                // Successfully added, redirect to tasks page
                header('Location: ?page=exercises');
                exit;
            } else {
                $addTaskError = 'Failed to write to exercises.json. Check file permissions.';
            }
        }
    }
}

/************************************************************
 * 3) HANDLE DELETE REQUEST FOR TASKS
 ************************************************************/
$deleteMessage = null; // Stores messages related to deletion

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteIndex = (int) $_GET['delete'];
    $tasks = readTasks();

    if (isset($tasks[$deleteIndex])) {
        // Remove task
        array_splice($tasks, $deleteIndex, 1);

        // Write back to the file
        if (writeTasks($tasks)) {
            $deleteMessage = "Task at index $deleteIndex has been deleted.";
            // Redirect to the current page to avoid re-deleting on refresh
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
 * 5) CREATE VARIABLES FOR DISPLAYING STATUS
 ************************************************************/
$socketStatus = 'Status unknown';
if ($socketMessage !== null) {
    // If the output from socket.py contains "eingeschaltet"
    if (strpos(strtolower($socketMessage), 'eingeschaltet') !== false || strpos(strtolower($socketMessage), 'on') !== false) {
        $socketStatus = 'Socket turned on';
    } elseif (strpos(strtolower($socketMessage), 'ausgeschaltet') !== false || strpos(strtolower($socketMessage), 'off') !== false) {
        $socketStatus = 'Socket turned off';
    } else {
        $socketStatus = 'Socket state unknown';
    }
}

$ledStatus = getLEDStatus(); // Current LED status
?>
<!DOCTYPE html>
<html lang="de">
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
                <?php if (isset($_SESSION['user'])): ?>
                    | <a href="logout.php" class="menu-link">Logout (<?= htmlspecialchars($_SESSION['user']) ?>)</a>
                <?php else: ?>
                    | <a href="login.php" class="menu-link">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main content area -->
    <div class="page-content">
        <?php
        // Display error message when adding a task, if available
        if ($addTaskError !== null) {
            echo "<p style='color:red;'><strong>$addTaskError</strong></p>";
        }

        // Display deletion message, if available
        if ($deleteMessage !== null) {
            echo "<p style='color:green;'><strong>$deleteMessage</strong></p>";
        }

        // Load the requested subpage
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
