<?php
session_start();
$pdo = require_once 'data/db_connect.php';

$username = $_SESSION['username'] ?? 'Guest';
$user_id = $_SESSION['user_id'] ?? 0;

$tickets = $pdo->prepare("
  SELECT t.ticket_id, t.seat_number, t.travel_date, t.status, t.price, 
         s.departure_time, s.arrival_time,
         tr.train_name,
         fs.station_name AS from_station,
         ts.station_name AS to_station
  FROM tickets t
  JOIN schedules s ON t.schedule_id = s.schedule_id
  JOIN trains tr ON s.train_id = tr.train_id
  JOIN stations fs ON s.from_station = fs.station_id
  JOIN stations ts ON s.to_station = ts.station_id
  WHERE t.user_id = ?
  ORDER BY t.travel_date DESC
");
$tickets->execute([$user_id]);
$ticketRows = $tickets->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Your Tickets - Orange Line</title>
  <link rel="stylesheet" href="css/user_dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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
  <h2 style="color: #ff8a00; font-size: 1.8rem; margin-bottom: 20px;">🎟️ Your Booked Tickets</h2>

  <?php if (count($ticketRows) === 0): ?>
    <p style="text-align: center; font-size: 1.1rem;">No tickets booked yet.</p>
  <?php else: ?>
    <div class="ticket-cards-container">
      <?php foreach ($ticketRows as $index => $ticket): ?>
        <div class="ticket-card">
          <div class="ticket-info">
            <div><strong>Train:</strong> <?= htmlspecialchars($ticket['train_name']) ?></div>
            <div><strong>From:</strong> <?= htmlspecialchars($ticket['from_station']) ?></div>
            <div><strong>To:</strong> <?= htmlspecialchars($ticket['to_station']) ?></div>
            <div><strong>Departure:</strong> <?= htmlspecialchars($ticket['departure_time']) ?></div>
            <div><strong>Arrival:</strong> <?= htmlspecialchars($ticket['arrival_time']) ?></div>
            <div><strong>Travel Date:</strong> <?= htmlspecialchars($ticket['travel_date']) ?></div>
            <div><strong>Seat:</strong> <?= htmlspecialchars($ticket['seat_number']) ?></div>
            <div><strong>Status:</strong> <?= ucfirst($ticket['status']) ?></div>
            <div><strong>Fare:</strong> Rs. <?= number_format($ticket['price']) ?></div>
          </div>
          <button class="download-btn" onclick="downloadTicket(<?= $index ?>)">Download Ticket</button>

          <!-- Hidden Ticket Template for Download -->
          <div id="ticket-content-<?= $index ?>" style="display: none;">
            <h2>Train Ticket</h2>
            <p><strong>Train:</strong> <?= htmlspecialchars($ticket['train_name']) ?></p>
            <p><strong>From:</strong> <?= htmlspecialchars($ticket['from_station']) ?></p>
            <p><strong>To:</strong> <?= htmlspecialchars($ticket['to_station']) ?></p>
            <p><strong>Departure:</strong> <?= htmlspecialchars($ticket['departure_time']) ?></p>
            <p><strong>Arrival:</strong> <?= htmlspecialchars($ticket['arrival_time']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($ticket['travel_date']) ?></p>
            <p><strong>Seat:</strong> <?= htmlspecialchars($ticket['seat_number']) ?></p>
            <p><strong>Status:</strong> <?= ucfirst($ticket['status']) ?></p>
            <p><strong>Fare:</strong> Rs. <?= number_format($ticket['price']) ?></p>
            <p>Thank you for booking with Orange Line Metro!</p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

</section>
</body>
<script>
function downloadTicket(index) {
  const content = document.getElementById(`ticket-content-${index}`).innerHTML;
  const blob = new Blob([content], { type: 'text/html' });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = `ticket-${index + 1}.html`;
  a.click();
}
</script>
</html>
