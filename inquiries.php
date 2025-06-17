<?php
$pdo = require_once 'data/db_connect.php';
session_start();

try {
    $stmt = $pdo->prepare("
        SELECT i.inquiry_id, i.subject, i.message, i.status, i.created_at,
               i.answer, i.answered_at,
               u.username AS user_name,
               a.username AS answered_by_name
        FROM inquiries i
        JOIN users u ON i.user_id = u.user_id
        LEFT JOIN users a ON i.answered_by = a.user_id
        ORDER BY i.created_at DESC
    ");
    $stmt->execute();
    $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $inquiries = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inquiries</title>
    <link rel="stylesheet" href="css/train_management.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
            .table-section {
                overflow-x: auto;
                margin-top: 1rem;
            }

            table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0 12px;
                font-size: 15px;
                
            }

            thead th {
                background-color: #ff8a00;
                color: white;
                padding: 12px;
                text-align: left;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
                padding: 12px 20px;
            }

            tbody tr {
                background-color: #fff;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
                transition: 0.2s;
            }

            tbody tr:hover {
                transform: scale(1.01);
            }

            tbody td {
                padding: 12px 20px;
                border-bottom: 1px solid #eee;
            }

            td:last-child {
                display: flex;
                gap: 10px;
            }

            .status-open {
                
                color: #e74c3c;
                font-weight: bold;
            }
            .status-answered {
                color: #27ae60;
                font-weight: bold;
            }
            .status-closed {
                color: #7f8c8d;
                font-weight: bold;
            }

            .action-btn {
                padding: 6px 12px;
                background: #ff8a00; /* Orange background */
                color: white; /* White text */
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-size: 13px;
                transition: 0.2s;
            }
            .action-btn:hover {
                background: #e67e22;
            }
            .action-btn:disabled {
                background-color: #bdc3c7; /* Light gray when disabled */
                color: #7f8c8d; /* Gray text when disabled */
                cursor: not-allowed;
            }
        .status-open { 
                color: #e74c3c; /* Red for open status */
                font-weight: bold; 
            }
        .status-answered { 
                color: #27ae60; /* Green for answered status */
                font-weight: bold; 
            }
        .status-closed { 
                color: #7f8c8d; /* Gray for closed status */
                font-weight: bold; 
            }

        table {
            border-collapse: separate;
            border-spacing: 0 12px; /* row gap */
        }
        table td {
        color: #333; /* Dark gray for regular text */
        }
        table tr {
            background: #f9f9f9;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.6);
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            width: 50%;
            border-radius: 8px;
            position: relative;
        }
        .close-modal {
            position: absolute;
            right: 10px;
            top: 10px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            color: #333;
        }
    </style>
</head>
<body>

<section class="hero-section">
    <div class="overlay"></div>

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

    <div class="dashboard-content">
        <h2 style="color: #ff8a00; margin-bottom: 1rem;">User Inquiries</h2>

        <div class="table-section">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($inquiries) > 0): ?>
                <?php foreach ($inquiries as $i => $inq): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($inq['user_name']) ?></td>
                        <td><?= htmlspecialchars($inq['subject']) ?></td>
                        <td><?= date("d M Y, h:i A", strtotime($inq['created_at'])) ?></td>
                        <td class="status-<?= $inq['status'] ?>"><?= ucfirst($inq['status']) ?></td>
                        <td>
                            <button class="action-btn" onclick="openModal(<?= $inq['inquiry_id'] ?>)">View</button>
                            <a href="answer_inquiry.php?id=<?= $inq['inquiry_id'] ?>">
                                <button class="action-btn" <?= $inq['status'] !== 'open' ? 'disabled' : '' ?>>Reply</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">No inquiries found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
    </div>
</section>

<!-- MODALS OUTSIDE THE TABLE -->
<?php foreach ($inquiries as $inq): ?>
    <div class="modal" id="modal-<?= $inq['inquiry_id'] ?>">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal(<?= $inq['inquiry_id'] ?>)">&times;</span>
            <h3>Inquiry Details</h3>
            <p><strong>User:</strong> <?= htmlspecialchars($inq['user_name']) ?></p>
            <p><strong>Subject:</strong> <?= htmlspecialchars($inq['subject']) ?></p>
            <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($inq['message'])) ?></p>
            <p><strong>Status:</strong> <?= ucfirst($inq['status']) ?></p>
            <p><strong>Answer:</strong> <?= $inq['answer'] ? nl2br(htmlspecialchars($inq['answer'])) : '-' ?></p>
            <p><strong>Answered By:</strong> <?= $inq['answered_by_name'] ?? '-' ?></p>
            <p><strong>Date:</strong> <?= date("d M Y, h:i A", strtotime($inq['created_at'])) ?></p>
        </div>
    </div>
<?php endforeach; ?>

<script>
    function openModal(id) {
        document.getElementById("modal-" + id).style.display = "block";
    }

    function closeModal(id) {
        document.getElementById("modal-" + id).style.display = "none";
    }

    window.onclick = function (event) {
        document.querySelectorAll(".modal").forEach(modal => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    }
</script>

</body>
</html>
