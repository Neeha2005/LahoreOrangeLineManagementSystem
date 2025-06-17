<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Orange Line - Login</title>
  <link rel="stylesheet" href="css/login.css">
</head>
<body id="loginPage">
  <div class="background-image"></div>

  <div class="login-container">
    <button class="close-login" id="closeLoginBtn" aria-label="Close login">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>

    <?php if (!empty($_SESSION['login_error'])): ?>
      <div class="error-message" style="color: red; text-align: center; margin-bottom: 15px;">
        <?= htmlspecialchars($_SESSION['login_error']) ?>
        <?php unset($_SESSION['login_error']); ?>
      </div>
    <?php endif; ?>

    <div class="login-screen" id="welcome-screen">
      <img src="css/images/logo (2).png" alt="Orange Line Logo" class="logo">
      <h2>Welcome Back</h2>
      <p>Sign in to access Orange Line services</p>
      <button id="mainLoginBtn">Login</button>
    </div>

    <div class="login-screen hidden" id="role-screen">
      <h2>Select Login Role</h2>
      <button id="adminRoleBtn">Login as Admin</button>
      <button id="userRoleBtn">Login as User</button>
      <button class="back-btn" id="backToWelcomeBtn">Back</button>
    </div>

    <div class="login-screen hidden" id="admin-login">
      <h2>Admin Login</h2>
      <form id="adminForm" action="login_handler.php" method="POST">
        <input type="hidden" name="role" value="admin">
        <input type="email" name="email" placeholder="Admin Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
      </form>
      <button class="back-btn" id="backToRolesBtn1">Back</button>
    </div>

    <div class="login-screen hidden" id="user-login">
      <h2>User Login</h2>
      <form id="userForm" action="login_handler.php" method="POST">
        <input type="hidden" name="role" value="user">
        <input type="email" name="email" placeholder="User Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
      </form>
      <button class="back-btn" id="backToRolesBtn2">Back</button>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      function showScreen(id) {
        document.querySelectorAll(".login-screen").forEach(div => div.classList.add("hidden"));
        document.getElementById(id).classList.remove("hidden");
      }

      showScreen("welcome-screen");

      document.getElementById("mainLoginBtn").onclick = () => showScreen("role-screen");
      document.getElementById("adminRoleBtn").onclick = () => showScreen("admin-login");
      document.getElementById("userRoleBtn").onclick = () => showScreen("user-login");
      document.getElementById("backToWelcomeBtn").onclick = () => showScreen("welcome-screen");
      document.getElementById("backToRolesBtn1").onclick = () => showScreen("role-screen");
      document.getElementById("backToRolesBtn2").onclick = () => showScreen("role-screen");

      document.getElementById("closeLoginBtn").onclick = () => {
        window.location.href = "Home.php";
      };
    });
  </script>
</body>
</html>

