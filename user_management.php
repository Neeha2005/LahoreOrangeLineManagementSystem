<?php
$pdo = require 'data/db_connect.php';
session_start();

// Fetch all non-admin users
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'user'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $deleteId = $_POST['delete_user_id'];
    $deleteStmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $deleteStmt->execute([$deleteId]);
    header("Location: user_management.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management - Orange Line</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
            body {
                margin: 0;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: #f2f2f2;
            }

            .user-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px;
                background-color: rgba(0, 0, 0, 0.65);
                border-radius: 12px;
                color: white;
                margin-bottom: 24px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            }

            .user-name {
                font-size: 1.2rem;
                font-weight: bold;
            }

            .action-btn {
                background-color: #ff8a00;
                color: white;
                padding: 8px 14px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                margin-left: 8px;
                transition: background-color 0.3s ease;
            }

            .action-btn:hover {
                background-color: #e67600;
            }

            .export-btn {
                margin-bottom: 20px;
                display: inline-block;
            }

            /* Modal Styles */
            .modal {
                display: none;
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background: rgba(0, 0, 0, 0.6);
                justify-content: center;
                align-items: center;
                z-index: 9999;
                backdrop-filter: blur(4px);
                animation: fadeIn 0.3s ease-in-out;
            }

            .modal-content {
                background: linear-gradient(to right, #ffffff, #f9f9f9);
                padding: 30px 25px;
                border-radius: 15px;
                width: 90%;
                max-width: 500px;
                position: relative;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
                animation: slideUp 0.4s ease;
                transition: all 0.3s ease-in-out;
            }

            .modal-content h3 {
                margin-bottom: 15px;
                color: #ff8a00;
                font-size: 1.5rem;
                text-align: center;
            }

            .modal-content p {
                margin: 10px 0;
                color: #333;
                font-size: 1rem;
                line-height: 1.4;
            }

            .modal-content p strong {
                color: #555;
                display: inline-block;
                width: 130px;
            }

            .close-modal {
                position: absolute;
                top: 12px; right: 16px;
                font-size: 22px;
                color: #666;
                cursor: pointer;
                transition: color 0.2s ease;
            }

            .close-modal:hover {
                color: #ff8a00;
            }

            @keyframes fadeIn {
                from { opacity: 0; } 
                to { opacity: 1; }
            }

            @keyframes slideUp {
                from {
                    transform: translateY(40px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
        .dashboard-content {
            margin-left: 220px;
            padding: 2rem;
            padding-top: 100px;
            height: calc(100vh - 2px); /* Already correct */
            overflow-y: auto;
            overflow-x: hidden; /* Optional: prevent horizontal scroll */
            background: white;
            color: #333;
            position: relative;
            z-index: 2;
            }

        </style>

</head>
<body>

<section class="hero-section">
    <div class="overlay"></div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo-wrapper">
            <div class="logo-box">
                <img src="css/images/logo (2).png" alt="Logo" class="logo-image">
            </div>
            <div class="text-box">
                <div class="main-title">Lahore Orange Line</div>
                <div class="sub-title">Metro Train Management System</div>
            </div>
        </div>
        <div class="welcome-message">
            <span>Welcome,</span> <strong>Admin</strong>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar">
        <h3><i class="fas fa-bars"></i> Admin Panel</h3>
        <ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-chart-bar"></i> Overview</a></li>
            <li><a href="train_management.php"><i class="fas fa-train"></i> Train Management</a></li>
            <li><a href="inquiries.php"><i class="fas fa-envelope"></i> Inquiries</a></li>
            <li><a href="user_management.php"><i class="fas fa-users-cog"></i> User Management</a></li>
            <li><a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
  </aside>

    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <h2 style="color: #ff8a00; margin-bottom: 20px;">Registered Users</h2>

        <div class="export-btn">
            <a href="export_users.php" class="action-btn" style="background: #007bff;">Export to CSV</a>
        </div>

        <table style="width: 100%; border-collapse: collapse; background: rgba(0, 0, 0, 0.65); color: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);">
    <thead style="background-color: #222;">
        <tr>
            <th style="padding: 12px; text-align: left;">Username</th>
            <th style="padding: 12px; text-align: left;">Email</th>
            <th style="padding: 12px; text-align: left;">Registered At</th>
            <th style="padding: 12px; text-align: left;">Last Login</th>
            <th style="padding: 12px; text-align: left;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr style="border-top: 1px solid #444;">
                <td style="padding: 12px;"><?php echo htmlspecialchars($user['username']); ?></td>
                <td style="padding: 12px;"><?php echo htmlspecialchars($user['email']); ?></td>
                <td style="padding: 12px;"><?php echo htmlspecialchars($user['created_at']); ?></td>
                <td style="padding: 12px;"><?php echo $user['last_login'] ?? 'Never'; ?></td>
                <td style="padding: 12px;">
                    <button class="action-btn show-detail"
                            data-username="<?php echo htmlspecialchars($user['username']); ?>"
                            data-email="<?php echo htmlspecialchars($user['email']); ?>"
                            data-created="<?php echo htmlspecialchars($user['created_at']); ?>"
                            data-login="<?php echo $user['last_login'] ?? 'Never'; ?>">
                        Show Details
                    </button>

                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="delete_user_id" value="<?php echo $user['user_id']; ?>">
                        <button type="submit" class="action-btn" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

    </div>
</section>

<!-- Modal -->
<div class="modal" id="userModal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h3>User Details</h3>
        <p><strong>Username:</strong> <span id="modalUsername"></span></p>
        <p><strong>Email:</strong> <span id="modalEmail"></span></p>
        <p><strong>Registered At:</strong> <span id="modalCreated"></span></p>
        <p><strong>Last Login:</strong> <span id="modalLogin"></span></p>
    </div>
</div>

<script>
    const modal = document.getElementById("userModal");
    const closeModal = document.querySelector(".close-modal");
    const showButtons = document.querySelectorAll(".show-detail");

    showButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("modalUsername").innerText = btn.dataset.username;
            document.getElementById("modalEmail").innerText = btn.dataset.email;
            document.getElementById("modalCreated").innerText = btn.dataset.created;
            document.getElementById("modalLogin").innerText = btn.dataset.login;
            modal.style.display = "flex";
        });
    });

    closeModal.addEventListener("click", () => {
        modal.style.display = "none";
    });

    window.onclick = function(e) {
        if (e.target == modal) {
            modal.style.display = "none";
        }
    };
</script>

</body>
</html>
