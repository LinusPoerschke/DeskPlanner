<?php

session_start();

define('USER_FILE', __DIR__ . '/users.txt');

// Save user
function save_user($username, $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    file_put_contents(USER_FILE, "$username:$hashedPassword\n", FILE_APPEND);
}

// Load users
function load_users() {
    if (!file_exists(USER_FILE)) {
        return [];
    }
    return file(USER_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

// Does user exist?
function does_user_exist($username, $users) {
    foreach ($users as $user) {
        list($storedUsername, ) = explode(':', $user);
        if ($storedUsername === $username) {
            return true;
        }
    }
    return false;
}

// Authenticate user
function authenticate_user($username, $password, $users) {
    foreach ($users as $user) {
        list($storedUsername, $storedPassword) = explode(':', $user);
        if ($storedUsername === $username && password_verify($password, $storedPassword)) {
            return true;
        }
    }
    return false;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $password = trim($_POST['password']);
    $action = $_POST['action'];

    $users = load_users();

    if (empty($username) || empty($password)) {
        header('Location: DeskPlanner.html?error=2');
        exit;
    }

    if ($action === 'register') {
        if (does_user_exist($username, $users)) {
            header('Location: DeskPlanner.html?error=1');
        } else {
            save_user($username, $password);
            header('Location: DeskPlanner.html?success=1');
        }
        exit;
    }

    if ($action === 'login') {
        if (authenticate_user($username, $password, $users)) {
            session_regenerate_id(true);

            // Initialize user-specific files
            $baseDir = __DIR__ . "/user_data/{$username}";
            if (!is_dir($baseDir)) {
                mkdir($baseDir, 0755, true);
            }

            $exercisesFile = $baseDir . "/exercises.json";

            if (!file_exists($exercisesFile)) {
                file_put_contents($exercisesFile, json_encode([]));
            }

            $_SESSION['user'] = $username;
            $_SESSION['exercises_file'] = $exercisesFile;

            header('Location: index.php');
        } else {
            header('Location: DeskPlanner.html?error=3');
        }
        exit;
    }
}
?>