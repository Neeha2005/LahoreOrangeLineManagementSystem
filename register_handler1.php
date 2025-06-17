<?php
session_start();

// Get form input
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Input validation
if (!$username || !$email || !$password) {
    $_SESSION['register_error'] = "All fields are required.";
    header("Location: register.php");
    exit;
}

try {
    // Connect to SQLite database directly
    $pdo = new PDO("sqlite:data/orange_line_project.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert user into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->execute([$username, $email, $password]);
    header("Location: login.php");
    exit;

} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'UNIQUE')) {
        $_SESSION['register_error'] = "Username or email already exists.";
    } else {
        $_SESSION['register_error'] = "Registration failed. Please try again.";
    }
    header("Location: register.php");
    exit;
}
