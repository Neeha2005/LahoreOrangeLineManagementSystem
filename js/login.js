document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("loginPage")) {
        initLoginPage();
        setupEventListeners();
    }
});

function initLoginPage() {
    hideAllScreens();
    showScreen("welcome-screen");
}

function setupEventListeners() {
    document.getElementById('mainLoginBtn')?.addEventListener('click', () => showScreen("role-screen"));
    document.getElementById('adminRoleBtn')?.addEventListener('click', () => showScreen("admin-login"));
    document.getElementById('userRoleBtn')?.addEventListener('click', () => showScreen("user-login"));
    document.getElementById('backToWelcomeBtn')?.addEventListener('click', () => showScreen("welcome-screen"));
    document.getElementById('backToRolesBtn1')?.addEventListener('click', () => showScreen("role-screen"));
    document.getElementById('backToRolesBtn2')?.addEventListener('click', () => showScreen("role-screen"));
    document.getElementById('closeLoginBtn')?.addEventListener('click', handleCloseButton);
}

function showScreen(screenId) {
    hideAllScreens();
    const screen = document.getElementById(screenId);
    if (screen) screen.classList.remove("hidden");
}

function hideAllScreens() {
    document.querySelectorAll(".login-screen").forEach(screen => {
        screen.classList.add("hidden");
    });
}

function handleCloseButton() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = 'Home.php';
    }
}
