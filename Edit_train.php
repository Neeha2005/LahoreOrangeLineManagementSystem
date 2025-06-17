<?php
$pdo = require_once 'data/db_connect.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: train_management.php");
    exit();
}

$train_id = (int) $_GET['id'];
$error = '';
$success = '';

// Fetch train data
$stmt = $pdo->prepare("SELECT * FROM trains WHERE train_id = ?");
$stmt->execute([$train_id]);
$train = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$train) {
    $error = "Train not found.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $train_name = $_POST['train_name'] ?? '';
    $capacity = $_POST['capacity'] ?? '';
    $status = $_POST['status'] ?? '';
    $schedules = $_POST['schedules'] ?? [];

    if (empty($train_name) || empty($capacity) || empty($status)) {
        $error = "Please fill all required fields.";
    } else {
        $pdo->beginTransaction();
        try {
            $updateTrain = $pdo->prepare("UPDATE trains SET train_name = ?, capacity = ?, status = ? WHERE train_id = ?");
            $updateTrain->execute([$train_name, $capacity, $status, $train_id]);

            // Delete old schedules
            $pdo->prepare("DELETE FROM train_schedules WHERE train_id = ?")->execute([$train_id]);

            // Insert new schedules
            $insertSchedule = $pdo->prepare("INSERT INTO train_schedules (train_id, schedule_id) VALUES (?, ?)");
            foreach ($schedules as $schedule_id) {
                $insertSchedule->execute([$train_id, $schedule_id]);
            }

            $pdo->commit();
            $success = "Train updated successfully!";
            $train['train_name'] = $train_name;
            $train['capacity'] = $capacity;
            $train['status'] = $status;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Update failed: " . $e->getMessage();
        }
    }
}

// Fetch all schedules
$scheduleStmt = $pdo->query("
    SELECT s.schedule_id, s.departure_time, s.arrival_time, 
           f.station_name as from_station, t.station_name as to_station 
    FROM schedules s 
    JOIN stations f ON s.from_station = f.station_id 
    JOIN stations t ON s.to_station = t.station_id
");
$allSchedules = $scheduleStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch train's assigned schedules
$assignedStmt = $pdo->prepare("SELECT schedule_id FROM train_schedules WHERE train_id = ?");
$assignedStmt->execute([$train_id]);
$assignedSchedules = $assignedStmt->fetchAll(PDO::FETCH_COLUMN);

// Helper functions
function renderTrainStatusDropdown($currentStatus) {
    $statuses = [
        'active' => 'Active',
        'maintenance' => 'Maintenance',
        'retired' => 'Retired'
    ];
    echo '<select name="status" id="status" required>';
    foreach ($statuses as $value => $label) {
        $selected = ($currentStatus === $value) ? 'selected' : '';
        echo "<option value=\"$value\" $selected>$label</option>";
    }
    echo '</select>';
}

function renderScheduleMultiSelect($allSchedules, $selectedScheduleIds) {
    echo '<select name="schedules[]" multiple size="5" style="height:auto;" required>';
    foreach ($allSchedules as $schedule) {
        $id = $schedule['schedule_id'];
        $label = htmlspecialchars($schedule['from_station']) . ' → ' . htmlspecialchars($schedule['to_station']);
        $label .= " ({$schedule['departure_time']} - {$schedule['arrival_time']})";
        $selected = in_array($id, $selectedScheduleIds) ? 'selected' : '';
        echo "<option value=\"$id\" $selected>$label</option>";
    }
    echo '</select>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Train - Admin Panel</title>
    <link rel="stylesheet" href="css/train_management.css">
    <style>
        .edit-form {
            background: white;
            padding: 2rem;
            max-width: 700px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .edit-form h2 {
            margin-bottom: 1.5rem;
            color: #ff8a00;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.3rem;
            font-weight: bold;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .form-actions {
            margin-top: 1.5rem;
        }
        .form-actions button {
            padding: 0.6rem 1.2rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .alert {
            padding: 0.7rem 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
        }
        .alert-success {
            background-color: #e0ffe0;
            color: #007b00;
        }
        .alert-error {
            background-color: #ffe0e0;
            color: #a00;
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
        <div class="edit-form">
            <h2>Edit Train Info</h2>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="train_name">Train Name</label>
                    <input type="text" name="train_name" id="train_name" value="<?= htmlspecialchars($train['train_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="capacity">Capacity</label>
                    <input type="number" name="capacity" id="capacity" value="<?= htmlspecialchars($train['capacity']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <?php renderTrainStatusDropdown($train['status']); ?>
                </div>
                <div class="form-group">
                    <label for="schedules">Assign Schedules</label>
                    <?php renderScheduleMultiSelect($allSchedules, $assignedSchedules); ?>
                </div>
                <div class="form-actions">
                    <button type="submit">Update Train</button>
                    <a href="train_management.php" style="margin-left: 1rem;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
</body>
</html>
