<?php
$pdo = require_once 'data/db_connect.php';
session_start();

// Static admin name
$username = "Admin";

// Fetch live stats
try {
    // Total trains
    $stmt = $pdo->query("SELECT COUNT(*) FROM trains");
    $totalTrains = $stmt->fetchColumn();

    // Daily passengers
    $stmt = $pdo->query("SELECT COUNT(*) FROM tickets WHERE DATE(travel_date) = DATE('now', 'localtime')");
    $dailyPassengers = $stmt->fetchColumn();


    // Pending inquiries
    $stmt = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'open'");
    $pendingInquiries = $stmt->fetchColumn();

    // Maintenance count
    $stmt = $pdo->query("SELECT COUNT(*) FROM trains WHERE status = 'maintenance'");
    $maintenanceCount = $stmt->fetchColumn();

    $systemStatus = ($maintenanceCount > 0) ? "Some issues" : "Operational";
    $systemColor = ($maintenanceCount > 0) ? "orange" : "limegreen";

    // Fetch recent activities
$query = "
    SELECT * FROM (
        SELECT 'Train Updated' AS type, 
               t.train_name || ' was updated by ' || u.username AS title, 
               t.updated_at AS time 
        FROM trains t
        JOIN users u ON t.created_by = u.user_id
        ORDER BY t.updated_at DESC
        LIMIT 1
    )
    
    UNION ALL

    SELECT * FROM (
        SELECT 'Schedule Modified' AS type, 
               'Schedule for train ' || t.train_name || ' updated by ' || u.username AS title, 
               s.updated_at AS time 
        FROM schedules s
        JOIN trains t ON s.train_id = t.train_id
        JOIN users u ON s.updated_by = u.user_id
        ORDER BY s.updated_at DESC
        LIMIT 1
    )
    
    UNION ALL

    SELECT * FROM (
        SELECT 'Ticket Booked' AS type, 
               'User ID ' || t.user_id || ' booked ticket for ' || t.travel_date AS title, 
               t.booking_time AS time 
        FROM tickets t 
        WHERE t.travel_date = DATE('now', 'localtime')
        ORDER BY t.booking_time DESC
        LIMIT 1
    )

    UNION ALL

    SELECT * FROM (
        SELECT 'New Inquiry' AS type, 
               u.username || ' asked: ' || i.subject AS title, 
               i.created_at AS time 
        FROM inquiries i 
        JOIN users u ON i.user_id = u.user_id
        WHERE i.status = 'open'
        ORDER BY i.created_at DESC
        LIMIT 1
    )

    ORDER BY time DESC;
";


    $stmt = $pdo->query($query);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $totalTrains = $dailyPassengers = $pendingInquiries = 0;
    $systemStatus = "Error!";
    $systemColor = "red";
    $activities = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Orange Line</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>

<section class="hero-section">
    <div class="overlay"></div>

    <!-- ===== Navbar ===== -->
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
            <span>Welcome,</span> <strong><?php echo htmlspecialchars($username); ?></strong>
        </div>
    </nav>

    <!-- ===== Sidebar ===== -->
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

    <!-- ===== Dashboard Content ===== -->
    <div class="dashboard-content">
        <div class="card-section">
            <div class="dash-card">
                <h3>Total Trains</h3>
                <p><?php echo $totalTrains; ?></p>
                <small>Registered in system</small>
            </div>
            <div class="dash-card">
                <h3>Daily Passengers</h3>
                <p><?php echo $dailyPassengers; ?></p>
                <small>Tickets booked today</small>
            </div>
            <div class="dash-card">
                <h3>Pending Inquiries</h3>
                <p><?php echo $pendingInquiries; ?></p>
                <small>Requires attention</small>
            </div>
            <div class="dash-card">
                <h3>System Status</h3>
                <p style="color: <?php echo $systemColor; ?>;"><?php echo htmlspecialchars($systemStatus); ?></p>
                <small><?php echo ($systemStatus === "Operational") ? "All systems normal" : "Maintenance ongoing"; ?></small>
            </div>
        </div>

        <div class="table-section">
            <h2>Recent Activities</h2>
            <ul style="color:white; font-size: 1rem; list-style-type: none;">
                <?php foreach ($activities as $activity): ?>
                    <li>
                        <span style="color:
                            <?php
                                switch ($activity['type']) {
                                    case 'Train Updated': echo '#4285f4'; break;
                                    case 'Schedule Modified': echo '#ea4335'; break;
                                    case 'Ticket Booked': echo '#fbbc05'; break;
                                    case 'New Inquiry': echo '#34a853'; break;
                                    default: echo '#ccc';
                                }
                            ?>">
                        ●</span> 
                        <strong><?php echo htmlspecialchars($activity['type']); ?></strong> - 
                        <?php echo htmlspecialchars($activity['title']); ?> - 
                        <i><?php echo date('M d, h:i A', strtotime($activity['time'])); ?></i>
                    </li>
                <?php endforeach; ?>

                <?php if (empty($activities)): ?>
                    <li>No recent activities found.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</section>

</body>
</html>
