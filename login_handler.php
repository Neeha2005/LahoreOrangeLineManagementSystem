<?php
session_start();
$pdo = require 'data/db_connect.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

if (!$email || !$password || !$role) {
    $_SESSION['login_error'] = "All fields are required.";
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
$stmt->execute([$email, $role]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check user exists and password matches
if (!$user || $user['password'] !== $password) {
    $_SESSION['login_error'] = "Invalid credentials.";
    header("Location: login.php");
    exit;
}

// ✅ Corrected: use 'user_id' from database
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

// Redirect to correct dashboard
if ($role === 'admin') {
    header("Location: admin_dashboard.php");
} else {
    header("Location: user_dashboard.php");
}
exit;
?>
