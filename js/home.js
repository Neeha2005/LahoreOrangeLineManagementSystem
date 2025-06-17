// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", () => {
  // Handle Login Button
  const loginBtn = document.querySelector(".login-btn");
  if (loginBtn) {
    loginBtn.addEventListener("click", () => {
      // Redirect to login page
      window.location.href = 'login.php';
    });
  }

  // Handle Register Button
  const registerBtn = document.querySelector(".register-btn");
  if (registerBtn) {
    registerBtn.addEventListener("click", () => {
      // Redirect to register page
      window.location.href =  '/register1.php';
    });
  }
});
