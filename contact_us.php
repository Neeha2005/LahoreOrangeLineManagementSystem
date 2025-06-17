<?php
session_start();
$pdo = require_once 'data/db_connect.php';

$username = $_SESSION['username'] ?? 'Guest';
$user_id = $_SESSION['user_id'] ?? 0;

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    if (!empty($subject) && !empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO inquiries (user_id, subject, message) VALUES (?, ?, ?)");
        if ($stmt->execute([$user_id, $subject, $message])) {
            $success = "Inquiry submitted successfully!";
        } else {
            $error = "Failed to submit inquiry.";
        }
    } else {
        $error = "Please fill out all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us - Orange Line</title>
  <link rel="stylesheet" href="css/user_dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .contact-form {
      background-color: rgba(255, 255, 255, 0.05);
      padding: 2rem;
      border-radius: 10px;
      max-width: 600px;
      margin: 0 auto;
    }
    .contact-form input, .contact-form textarea {
      width: 100%;
      padding: 0.8rem;
      margin-bottom: 1rem;
      border: 1px solid #ccc; /* Added visible border */
      border-radius: 5px;
      background-color: rgba(255, 255, 255, 0.08);
      color: white;
    }

    .contact-form button {
      padding: 0.7rem 1.5rem;
      border: none;
      background-color: #ff5722;
      color: white;
      border-radius: 5px;
      cursor: pointer;
    }
    .contact-form h2 {
      margin-bottom: 1.5rem;
    }
    .success, .error {
      text-align: center;
      margin-bottom: 1rem;
    }
    .success { color: limegreen; }
    .error { color: tomato; }
  </style>
</head>
<body>
<section class="hero-section">
  <div class="overlay"></div>

  <nav class="navbar">
    <div class="logo-wrapper">
      <div class="logo-box"><img src="css/images/logo (2).png" alt="Logo" class="logo-image" /></div>
      <div class="text-box">
        <div class="main-title">Lahore Orange Line</div>
        <div class="sub-title">Metro Train Management System</div>
      </div>
    </div>
    <div class="welcome-message">Welcome, <strong><?= htmlspecialchars($username) ?></strong></div>
  </nav>

  <aside class="sidebar">
    <h3><i class="fas fa-bars"></i> MENU</h3>
    <ul>
      <li><i class="fas fa-tachometer-alt"></i> <a href="user_dashboard.php" style="color: inherit;">Dashboard</a></li>
      <li><i class="fas fa-ticket-alt"></i> <a href="tickets.php" style="color: inherit;">Tickets</a></li>
      <li><i class="fas fa-map-marker-alt"></i> <a href="stations.php" style="color: inherit;">Stations</a></li>
      <li><i class="fas fa-envelope"></i> <a href="contact_us.php" style="color: inherit;">Contact Us</a></li>
      <li><i class="fas fa-comments"></i> <a href="messages.php" style="color: inherit;">Messages</a></li>
      <li><i class="fas fa-sign-out-alt"></i> <a href="logout_user.php" style="color: inherit;">Logout</a></li>
    </ul>
  </aside>

  <div class="dashboard-content">
    <div class="contact-form">
      <h2>Contact Admin</h2>

      <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST" action="contact_us.php">
        <input type="text" name="subject" placeholder="Subject" required>
        <textarea name="message" rows="6" placeholder="Your message or complaint..." required></textarea>
        <button type="submit">Submit Inquiry</button>
      </form>
    </div>
  </div>
</section>
</body>
</html>
