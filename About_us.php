<?php
// session_start(); // optional if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us - Lahore Orange Line</title>
  <link rel="stylesheet" href="css/home.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', sans-serif;
      background-color: #fff;
      color: #333;
    }

    .about-container {
      padding: 60px 50px;
      max-width: 1200px;
      margin: auto;
    }

    .about-container h2 {
      text-align: center;
      font-size: 2.4rem;
      color: #ff8a00;
      margin-bottom: 30px;
    }

    .about-container p {
      font-size: 1.1rem;
      line-height: 1.8;
      text-align: justify;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 25px;
      margin-top: 40px;
    }

    .feature-card {
      background: rgba(0, 0, 0, 0.6);
      color: #fff;
      border-radius: 12px;
      padding: 25px;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      transition: transform 0.3s ease;
    }

    .feature-card:hover {
      transform: translateY(-6px);
    }

    .feature-card i {
      font-size: 2.3rem;
      color: #ff8a00;
      margin-bottom: 10px;
    }

    .feature-card h4 {
      margin: 10px 0;
      font-size: 1.2rem;
    }

    .vision-mission {
      margin-top: 60px;
      padding: 40px;
      background: rgba(0, 0, 0, 0.6);
      color: #fff;
      border-radius: 10px;
    }

    .vision-mission h3 {
      color: #ff8a00;
      margin-bottom: 10px;
    }

    .vision-mission p {
      margin-bottom: 20px;
    }

    .footer-note {
      text-align: center;
      margin-top: 50px;
      font-size: 0.95rem;
      color: #777;
    }

    .nav-buttons button {
      margin-left: 10px;
      padding: 8px 14px;
      border: none;
      border-radius: 5px;
      background-color: #ff8a00;
      color: #fff;
      cursor: pointer;
    }

    .nav-buttons button:hover {
      background-color: #e57900;
    }
  </style>
</head>
<body>

  <!-- Navbar (same as home) -->
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

  <!-- About Content -->
  <div class="about-container">
    <h2>About Lahore Orange Line</h2>
    <p>
      The <strong>Lahore Orange Line Metro Train</strong> is Pakistan’s first mass transit rail project,
      offering safe, clean, and efficient public transportation. With a 27-km route and 26 modern stations,
      it connects the historic and economic centers of Lahore.
    </p>

    <div class="features-grid">
      <div class="feature-card">
        <i class="fas fa-subway"></i>
        <h4>26 Stations</h4>
        <p>Strategically located to connect all major areas of the city.</p>
      </div>
      <div class="feature-card">
        <i class="fas fa-bolt"></i>
        <h4>Fully Electric</h4>
        <p>Eco-friendly operation with minimal carbon footprint.</p>
      </div>
      <div class="feature-card">
        <i class="fas fa-shield-alt"></i>
        <h4>Safe & Secure</h4>
        <p>Advanced surveillance, platform screen doors, and trained staff.</p>
      </div>
      <div class="feature-card">
        <i class="fas fa-clock"></i>
        <h4>High Frequency</h4>
        <p>Trains every 5–8 minutes to keep the city moving.</p>
      </div>
    </div>

    <div class="vision-mission">
      <h3>Our Vision</h3>
      <p>To redefine Lahore’s urban mobility through sustainable and innovative transportation.</p>

      <h3>Our Mission</h3>
      <p>To deliver reliable, affordable, and inclusive mass transit that enhances daily life and reduces congestion.</p>
    </div>

    <div class="footer-note">
      &copy; 2025 Lahore Orange Line Metro | Empowering Your Commute 🚆
    </div>
  </div>

<script src="js/home.js"></script>
</body>
</html>
