<?php
$pdo = new PDO("sqlite:data/orange_line_project.db");
$stmt = $pdo->query("SELECT station_name, address FROM stations WHERE status = 'active'");
$stations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Orange Line - Stations</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/home.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', sans-serif;
      background-color: #f4f4f4;
    }

    .stations-section {
      padding: 40px;
    }

    .search-bar {
      text-align: center;
      margin-bottom: 30px;
    }

    .search-bar input {
      padding: 10px 20px;
      width: 300px;
      border: 1px solid #ccc;
      border-radius: 25px;
      font-size: 16px;
    }

    .station-list {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }

    .station-card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
      transition: transform 0.2s ease;
    }

    .station-card:hover {
      transform: scale(1.02);
    }

    .station-name {
      font-size: 18px;
      font-weight: 700;
      color: #d9534f;
      margin-bottom: 10px;
    }

    .station-address {
      color: #555;
      font-size: 15px;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo-wrapper">
      <div class="logo-box">
        <img src="css/images/logo (2).png" alt="Logo" class="logo-image" />
      </div>
      <div class="menu-center">
        <a href="Home.php" class="home-link"><i class="fas fa-home"></i></a>
        <a href="stations2.php" class="nav-link-button active">Stations</a>
        <a href="routes.php" class="nav-link-button">Routes</a>
        <a href="About_us.php" class="nav-link-button">About</a>
      </div>
      <div class="text-box">
        <div class="main-title">Lahore Orange Line</div>
        <div class="sub-title">Metro Train Management System</div>
      </div>
    </div>
    <div class="nav-buttons">
      <button class="login-btn" onclick="location.href='login.php'">Login</button>
      <button class="register-btn" onclick="location.href='register2.php'">Register</button>
    </div>
  </nav>

  <!-- Stations Section -->
  <div class="stations-section">
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search station by name..." onkeyup="filterStations()" />
    </div>

    <div class="station-list" id="stationList">
      <?php foreach ($stations as $station): ?>
        <div class="station-card">
          <div class="station-name"><?= htmlspecialchars($station['station_name']) ?></div>
          <div class="station-address"><?= htmlspecialchars($station['address']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script>
    function filterStations() {
      const input = document.getElementById('searchInput').value.toLowerCase();
      const cards = document.querySelectorAll('.station-card');

      cards.forEach(card => {
        const name = card.querySelector('.station-name').innerText.toLowerCase();
        card.style.display = name.includes(input) ? 'block' : 'none';
      });
    }
  </script>
</body>
</html>
