<?php
$pdo = require_once 'data/db_connect.php';
session_start();

// Static admin name
$username = "Admin";

// Fetch all trains
$stmt = $pdo->query("SELECT * FROM trains ORDER BY train_id ASC");
$trains = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Train Management - Admin Panel</title>
  <link rel="stylesheet" href="css/train_management.css">
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
        <div class="dashboard-header">
        <h1 class="dashboard-title">Train Management</h1>
        <div class="train-header-bar">
            <button class="add-train-btn">
            <i class="fas fa-plus-circle"></i> Add New Train
            </button>
        </div>
        </div>

      <div class="train-list">
        <?php foreach ($trains as $train): ?>
            <div class="train-item">
                <div class="train-icon"><i class="fas fa-train"></i></div>
                <div class="train-title"><?= htmlspecialchars($train['train_name']) ?></div>
                <div class="train-details">
                    <span>Capacity: <?= (int)$train['capacity']; ?> passengers</span>
                    <div class="status-and-action">
                        <span class="badge <?= strtolower($train['status']); ?>">
                            <?= ucfirst($train['status']); ?>
                        </span>
                        <a href="Edit_train.php?id=<?= $train['train_id'] ?>" class="btn btn-edit">Edit</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

    </div>
  </div>
</section>
<!-- Add New Train Modal -->
<div class="modal" id="addTrainModal" style="display:none;" role="dialog" aria-modal="true">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h2>Add New Train</h2>
    <form action="add_train_process.php" method="POST">
      <label>Train Name:</label>
      <input type="text" name="train_name" required>

      <label>Capacity:</label>
      <input type="number" name="capacity" required min="10">

      <label>Status:</label>
      <select name="status" required>
        <option value="active">Active</option>
        <option value="maintenance">Maintenance</option>
        <option value="retired">Retired</option>
      </select>

      <h3>Schedules:</h3>
      <div id="schedules-wrapper">
        <div class="schedule-group">
          <label>From Station:</label>
          <select name="from_station[]" required>
            <?php
              $stations = $pdo->query("SELECT * FROM stations WHERE status = 'active'")->fetchAll(PDO::FETCH_ASSOC);
              foreach ($stations as $station) {
                echo "<option value='{$station['station_id']}'>" . htmlspecialchars($station['station_name']) . "</option>";
              }
            ?>
          </select>

          <label>To Station:</label>
          <select name="to_station[]" required>
            <?php
              foreach ($stations as $station) {
                echo "<option value='{$station['station_id']}'>" . htmlspecialchars($station['station_name']) . "</option>";
              }
            ?>
          </select>

          <label>Departure Time:</label>
          <input type="time" name="departure_time[]" required>

          <label>Arrival Time:</label>
          <input type="time" name="arrival_time[]" required>

          <label>Frequency:</label>
          <select name="frequency[]">
            <option value="daily">Daily</option>
            <option value="weekdays">Weekdays</option>
            <option value="weekends">Weekends</option>
          </select>
        </div>
      </div>

      <button type="button" onclick="addSchedule()">+ Add Another Schedule</button>
      <br><br>
      <input type="submit" value="Add Train">
    </form>
  </div>
</div>

<!-- JavaScript -->
<script>
  const modal = document.getElementById("addTrainModal");
  const openBtn = document.querySelector(".add-train-btn");

  openBtn.onclick = function () {
    modal.style.display = "flex"; // use flex to center
    document.body.style.overflow = "hidden"; // prevent background scroll
  };

  function closeModal() {
    modal.style.display = "none";
    document.body.style.overflow = "auto"; // restore scroll
  }

  function addSchedule() {
    const wrapper = document.getElementById("schedules-wrapper");
    const firstGroup = wrapper.querySelector(".schedule-group");
    const clone = firstGroup.cloneNode(true);

    // Clear cloned inputs and selects
    const inputs = clone.querySelectorAll("input, select");
    inputs.forEach(input => {
      if (input.tagName === 'SELECT') {
        input.selectedIndex = 0;
      } else {
        input.value = '';
      }
    });

    wrapper.appendChild(clone);
  }

  // Optional: Close modal with ESC key
  window.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeModal();
    }
  });
</script>


</body>
</html>
