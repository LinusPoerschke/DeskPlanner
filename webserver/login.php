<?php
session_start();

// File to store user data
$file = 'users.txt';

// Save a new user to the file
function saveUser($username, $password, $file) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    file_put_contents($file, "$username:$hashedPassword\n", FILE_APPEND);
}

// Check if a username already exists
function userExists($username, $file) {
    if (!file_exists($file)) return false;
    $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($users as $user) {
        list($storedUsername, ) = explode(':', $user);
        if ($storedUsername === $username) {
            return true;
        }
    }
    return false;
}

// Authenticate a user by checking username and password
function authenticate($username, $password, $file) {
    if (!file_exists($file)) return false;
    $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $action = $_POST['action'];

    // Error and success messages (using codes)
    $errorMessages = [
        1 => "Username already exists.",
        2 => "Please fill out all fields.",
        3 => "Invalid login credentials."
    ];

    $successMessages = [
        1 => "Registration successful."
    ];

    // Check for empty fields
    if (empty($username) || empty($password)) {
        header('Location: DeskPlanner.html?error=2');
        exit;
    }

    if ($action === 'register') {
        // Check if the username exists
        if (userExists($username, $file)) {
            header('Location: DeskPlanner.html?error=1'); // Username exists
        } else {
            saveUser($username, $password, $file);
            header('Location: DeskPlanner.html?success=1'); // Registration successful
        }
        exit;
    }

    if ($action === 'login') {
        // Authenticate the user
        if (authenticate($username, $password, $file)) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            header('Location: main.php'); // Redirect to main.php after successful login
        } else {
            header('Location: DeskPlanner.html?error=3'); // Invalid credentials
        }
        exit;
    }
}
