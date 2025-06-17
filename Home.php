<?php
// Optional: session_start(); or include other PHP files
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lahore Orange Line</title>
    <link rel="stylesheet" href="css/home.css" />
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap"
          rel="stylesheet">
</head>
<body>
  <div class="hero-section">
    <div class="overlay"></div>

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
        <button class="login-btn" id="loginButton">Login</button>
        <button class="register-btn" id="registerButton">Register</button>
      </div>
    </nav>
      <div class="content">
        <h1>LAHORE ORANGE LINE</h1>
        <p>Safe, Comfortable & Efficient</p>
      </div>
          <!-- Stats Section -->
    <div class="stats-container">
      <div class="stat-box">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-number">2.5M+</div>
        <div class="stat-label">People Moved Daily</div>
      </div>
      
      <div class="stat-box">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-number">99.8%</div>
        <div class="stat-label">On-Time Performance</div>
      </div>
      
      <div class="stat-box">
        <div class="stat-icon"><i class="fas fa-shield-alt"></i></div>
        <div class="stat-number">100%</div>
        <div class="stat-label">Safety Record</div>
      </div>
    </div>
  </div>
  <script src="js/home.js"></script>
</body>
</html>
