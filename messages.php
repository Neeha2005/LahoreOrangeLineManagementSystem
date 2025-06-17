<?php
session_start();
$pdo = require_once 'data/db_connect.php';

$username = $_SESSION['username'] ?? 'Guest';
$user_id = $_SESSION['user_id'] ?? 0;

// Fetch inquiries that have an answer
$stmt = $pdo->prepare("
    SELECT subject, message, answer, created_at, answered_at 
    FROM inquiries 
    WHERE user_id = ? AND answer IS NOT NULL 
    ORDER BY answered_at DESC
");
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Messages - Orange Line</title>
  <link rel="stylesheet" href="css/user_dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .messages-container {
      background-color: rgba(255, 255, 255, 0.05);
      padding: 2rem;
      border-radius: 10px;
      max-width: 800px;
      margin: 0 auto;
      color: white;
    }
    .message-card {
      background-color: rgba(255, 255, 255, 0.08);
      padding: 1rem;
      margin-bottom: 1.5rem;
      border-radius: 8px;
    }
    .message-card h3 {
      margin-bottom: 0.5rem;
      color: #ff9800;
    }
    .message-card .user-msg {
      font-style: italic;
      margin-bottom: 0.5rem;
    }
    .message-card .admin-reply {
      background-color: rgba(0, 128, 0, 0.2);
      padding: 0.8rem;
      border-left: 4px solid limegreen;
      border-radius: 5px;
    }
    .message-card small {
      display: block;
      margin-top: 0.5rem;
      color: #ccc;
    }
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
    <div class="messages-container">
      <h2>Admin Replies</h2>

      <?php if (count($messages) === 0): ?>
        <p>No replies yet. Check back later.</p>
      <?php else: ?>
        <?php foreach ($messages as $m): ?>
          <div class="message-card">
            <h3><?= htmlspecialchars($m['subject']) ?></h3>
            <div class="user-msg">Your Message: <?= nl2br(htmlspecialchars($m['message'])) ?></div>
            <div class="admin-reply">
              <strong>Admin Reply:</strong><br>
              <?= nl2br(htmlspecialchars($m['answer'])) ?>
            </div>
            <small>Submitted: <?= $m['created_at'] ?> | Replied: <?= $m['answered_at'] ?></small>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>
</body>
</html>
