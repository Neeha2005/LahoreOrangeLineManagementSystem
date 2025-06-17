<?php
session_start();
$pdo = require_once 'data/db_connect.php';

$username = $_SESSION['username'] ?? 'Guest';

$stations = $pdo->query("SELECT station_id, station_name, address, latitude, longitude FROM stations");
$stationRows = $stations->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Stations - Orange Line</title>
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
    <h2 style="color: #ff8a00;">📍 Stations</h2>

    <div class="ticket-cards-container">
      <?php foreach ($stationRows as $station): ?>
        <div class="ticket-card">
          <div class="ticket-info">
            <div><strong><?= htmlspecialchars($station['station_name']) ?></strong></div>
            <div><strong>Address:</strong> <?= htmlspecialchars($station['address']) ?></div>
          </div>
          <button class="download-btn" onclick="openMapModal(<?= $station['latitude'] ?>, <?= $station['longitude'] ?>, '<?= addslashes($station['station_name']) ?>')">View on Map</button>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Modal -->
  <div id="mapModal" class="modal hidden">
    <div class="modal-content">
      <span class="close" onclick="closeMapModal()">&times;</span>
      <h3 id="mapTitle" style="text-align:center;"></h3>
      <iframe id="mapFrame" width="100%" height="400" frameborder="0" style="border:0;" allowfullscreen></iframe>
    </div>
  </div>
</section>

<!-- Styling for Modal -->
<style>
.modal {
  display: none;
  position: fixed;
  z-index: 100;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background-color: rgba(0, 0, 0, 0.7);
  justify-content: center;
  align-items: center;
}
.modal-content {
  background: #fff;
  padding: 20px;
  width: 80%;
  max-width: 600px;
  border-radius: 10px;
  position: relative;
}
.modal .close {
  position: absolute;
  top: 10px; right: 20px;
  font-size: 24px;
  cursor: pointer;
}
</style>

<!-- Script for Modal -->
<script>
function openMapModal(lat, lng, name) {
    console.log("Opening map for:", name);
    const modal = document.getElementById("mapModal");
    const title = document.getElementById("mapTitle");
    const iframe = document.getElementById("mapFrame");

    if (modal && title && iframe) {
        const mapURL = `https://www.google.com/maps?q=${lat},${lng}&hl=es;z=14&output=embed`;
        iframe.src = mapURL;
        title.textContent = name;
        modal.style.display = "flex"; // show the modal
    }
}

function closeMapModal() {
    const modal = document.getElementById("mapModal");
    const iframe = document.getElementById("mapFrame");

    if (modal && iframe) {
        iframe.src = ""; // reset
        modal.style.display = "none";
    }
}
</script>
</body>
</html>
