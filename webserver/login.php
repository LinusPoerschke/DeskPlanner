<?php
session_start();

// Constants for the user file
define('USER_FILE', __DIR__ . '/users.txt');

// Save a new user to the file
function save_user($username, $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    file_put_contents(USER_FILE, "$username:$hashedPassword\n", FILE_APPEND);
}

// Load all users from the file
function load_users() {
    if (!file_exists(USER_FILE)) {
        return [];
    }
    return file(USER_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

// Check if a username already exists
function does_user_exist($username, $users) {
    foreach ($users as $user) {
        list($storedUsername, ) = explode(':', $user);
        if ($storedUsername === $username) {
            return true;
        }
    }
    return false;
}

// Authenticate a user by checking username and password
function authenticate_user($username, $password, $users) {
    foreach ($users as $user) {
        list($storedUsername, $storedPassword) = explode(':', $user);
        if ($storedUsername === $username && password_verify($password, $storedPassword)) {
            return true;
        }
    }
    return false;
}

// Process POST requests
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
            $_SESSION['user'] = ['username' => $username, 'logged_in' => true];
            header('Location: main.php');
        } else {
            header('Location: DeskPlanner.html?error=3');
        }
        exit;
    }
}
