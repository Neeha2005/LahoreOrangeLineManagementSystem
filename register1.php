<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Orange Line - Register</title>
  <link rel="stylesheet" href="css/login.css"> <!-- Reusing login style -->
  <style>
    .fade-message {
      transition: opacity 1s ease-in-out;
    }
    .fade-out {
      opacity: 0;
    }
  </style>
</head>
<body id="loginPage">
  <div class="background-image"></div>

  <div class="login-container">
    <button class="close-login" id="closeRegisterBtn" aria-label="Close register">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>

    <?php if (!empty($_SESSION['register_error'])): ?>
      <div id="fadeMessage" class="error-message fade-message" style="color: red; text-align: center; margin-bottom: 15px;">
        <?= htmlspecialchars($_SESSION['register_error']) ?>
        <?php unset($_SESSION['register_error']); ?>
      </div>
    <?php elseif (!empty($_SESSION['register_success'])): ?>
      <div id="fadeMessage" class="success-message fade-message" style="color: green; text-align: center; margin-bottom: 15px;">
        <?= htmlspecialchars($_SESSION['register_success']) ?>
        <?php unset($_SESSION['register_success']); ?>
      </div>
    <?php endif; ?>

    <div class="login-screen" id="welcome-screen">
      <img src="css/images/logo (2).png" alt="Orange Line Logo" class="logo">
      <h2>Join Orange Line</h2>
      <p>Create your account to access our services</p>
      <button id="goToRegisterForm">Register</button>
    </div>

    <div class="login-screen hidden" id="register-screen">
      <h2>Create Account</h2>
      <form action="register_handler1.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
      </form>
      <button class="back-btn" id="backToWelcomeBtn">Back</button>
    </div>
  </div>

  <script>
    // Fade out the message after 2 seconds
    setTimeout(() => {
      const msg = document.getElementById('fadeMessage');
      if (msg) {
        msg.classList.add('fade-out');
      }
    }, 2000);

    document.addEventListener("DOMContentLoaded", function () {
      function showScreen(id) {
        document.querySelectorAll(".login-screen").forEach(div => div.classList.add("hidden"));
        document.getElementById(id).classList.remove("hidden");
      }

      showScreen("welcome-screen");

      document.getElementById("goToRegisterForm").onclick = () => showScreen("register-screen");
      document.getElementById("backToWelcomeBtn").onclick = () => showScreen("welcome-screen");

      document.getElementById("closeRegisterBtn").onclick = () => {
        window.location.href = "Home.php";
      };
    });
  </script>
</body>
</html>
