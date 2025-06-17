<?php
session_start();
$pdo = require_once 'data/db_connect.php';

$username = $_SESSION['username'] ?? 'Guest';
$user_id = $_SESSION['user_id'] ?? 0;

// Ticket stats
$total = $pdo->query("SELECT COUNT(*) FROM tickets WHERE user_id = $user_id")->fetchColumn();
$used = $pdo->query("SELECT COUNT(*) FROM tickets WHERE user_id = $user_id AND status = 'used'")->fetchColumn();
$cancelled = $pdo->query("SELECT COUNT(*) FROM tickets WHERE user_id = $user_id AND status = 'cancelled'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Dashboard - Orange Line</title>
  <link rel="stylesheet" href="css/user_dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
    <?php if (isset($_GET['error'])): ?>
  <div class="error-popup" id="error-popup">
    <span class="error-text"><?= htmlspecialchars($_GET['error']) ?></span>
    <button class="close-btn" onclick="closeError()">×</button>
  </div>
<?php endif; ?>

<section class="hero-section">
  <div class="overlay"></div>

  <nav class="navbar">
    <div class="logo-wrapper">
      <div class="logo-box"><img src="css/images/logo (2).png" alt="Logo" class="logo-image" /></div>
      <div class="text-box">
        <div class="main-title">Lahore Orange Line</div>
        <div class="sub-title">Metro Train Management System</div>
      </div>
    </div>
    <div class="welcome-message">Welcome, <strong><?= htmlspecialchars($username) ?></strong></div>
  </nav>

  <aside class="sidebar">
    <h3><i class="fas fa-bars"></i> MENU</h3>
    <ul>
      <li><i class="fas fa-tachometer-alt"></i> <a href="user_dashboard.php" style="color: inherit;">Dashboard</a></li>
      <li><i class="fas fa-ticket-alt"></i> <a href="tickets.php" style="color: inherit;">Tickets</a></li>
      <li><i class="fas fa-map-marker-alt"></i> <a href="stations.php" style="color: inherit;">Stations</a></li>
      <li><i class="fas fa-envelope"></i> <a href="contact_us.php" style="color: inherit;">Contact Us</a></li>
      <li><i class="fas fa-comments"></i> <a href="messages.php" style="color: inherit;">Messages</a></li>
      <li><i class="fas fa-sign-out-alt"></i> <a href="logout_user.php" style="color: inherit;">Logout</a></li>
    </ul>
  </aside>

  <div class="dashboard-content">
<div class="top-dashboard-row">
  <div class="dash-card">Booked Tickets: <?= $total ?></div>
</div>

<div class="card-box">
  <!-- Filter Section -->
<div class="filter-section">
  <form method="GET" action="user_dashboard.php">
    <select name="from_station">
        <option value="">From Station</option>
        <?php
            $stations = $pdo->query("SELECT station_id, station_name FROM stations")->fetchAll(PDO::FETCH_ASSOC);
            $selectedFrom = $_GET['from_station'] ?? '';
            foreach ($stations as $station) {
            $isSelected = ($selectedFrom == $station['station_id']) ? 'selected' : '';
            echo "<option value=\"{$station['station_id']}\" $isSelected>" . htmlspecialchars($station['station_name']) . "</option>";
            }
        ?>
        </select>

        <select name="to_station">
        <option value="">To Station</option>
        <?php
            $selectedTo = $_GET['to_station'] ?? '';
            foreach ($stations as $station) {
            $isSelected = ($selectedTo == $station['station_id']) ? 'selected' : '';
            echo "<option value=\"{$station['station_id']}\" $isSelected>" . htmlspecialchars($station['station_name']) . "</option>";
            }
        ?>
        </select>

    <input type="date" name="travel_date">
    <button type="submit">Filter</button>
  </form>
</div>

</div>



    <!-- Trains Table -->
        <div class="table-section">
        <h2>Available Trains</h2>
        <div class="table-wrapper">
        <table>
            <thead>
            <tr>
                <th>Train Name</th>
                <th>From</th>
                <th>To</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Fare</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
          <?php
          $query = "
            SELECT t.train_name, s.schedule_id, s.departure_time, s.arrival_time,
                   fs.station_name AS from_station, ts.station_name AS to_station,
                   fs.latitude AS from_lat, fs.longitude AS from_lng,
                   ts.latitude AS to_lat, ts.longitude AS to_lng
            FROM schedules s
            JOIN trains t ON s.train_id = t.train_id
            JOIN stations fs ON s.from_station = fs.station_id
            JOIN stations ts ON s.to_station = ts.station_id
            WHERE s.status = 'active'
          ";

          $from = $_GET['from_station'] ?? '';
$to = $_GET['to_station'] ?? '';
$travelDate = $_GET['travel_date'] ?? '';

            if (!empty($from)) {
            $query .= " AND s.from_station = " . (int)$from;
            }

            if (!empty($to)) {
            $query .= " AND s.to_station = " . (int)$to;
            }

            if (!empty($travelDate)) {
            $query .= " AND DATE(s.departure_time) = " . $pdo->quote($travelDate);
            }

          $schedules = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

          foreach ($schedules as $s) {
                $lat1 = $s['from_lat']; $lng1 = $s['from_lng'];
                $lat2 = $s['to_lat'];   $lng2 = $s['to_lng'];

                // Accurate Haversine-based distance
                $theta = $lng1 - $lng2;
                $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
                        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                $dist = max(-1, min(1, $dist)); // Clamp value to avoid domain error
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $km = $dist * 60 * 1.1515 * 1.609344; // Convert to kilometers

                // Query fare from MetroFares table
                $stmt = $pdo->prepare("SELECT fare FROM MetroFares WHERE min_distance <= :distance AND max_distance >= :distance LIMIT 1");
                $stmt->execute(['distance' => $km]);
                $fare = $stmt->fetchColumn();
                if (!$fare) $fare = 'N/A'; // fallback if no fare found

                echo "<tr>
                <td>{$s['train_name']}</td>
                <td>{$s['from_station']}</td>
                <td>{$s['to_station']}</td>
                <td>{$s['departure_time']}</td>
                <td>{$s['arrival_time']}</td>
                <td>Rs. $fare</td>
                <td><button class='book-btn' data-schedule='{$s['schedule_id']}' data-fare='$fare'>Book</button></td>
                </tr>";
            }
          ?>
        </tbody>
      </table>
    </div> 
    </div>
  </div>
</section>

<!-- Booking Form -->
<div class="overlay-bg"></div>
<div class="booking-form-popup">
  <h3>Book Ticket</h3>
  <form action="book_ticket.php" method="POST">
    <input type="hidden" name="schedule_id" id="schedule_id" />
    <input type="hidden" name="fare" id="fare" />
    <label>Travel Date:</label>
    <input type="date" name="travel_date" required />
    <label>Seat Number:</label>
    <input type="text" name="seat_number" required />
    <button type="submit">Confirm Booking</button>
    <button type="button" onclick="hideForm()">Cancel</button>
  </form>
</div>

<script>
  const bookBtns = document.querySelectorAll('.book-btn');
  const overlay = document.querySelector('.overlay-bg');
  const formPopup = document.querySelector('.booking-form-popup');

  bookBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('schedule_id').value = btn.dataset.schedule;
      document.getElementById('fare').value = btn.dataset.fare;
      overlay.style.display = 'block';
      formPopup.style.display = 'block';
    });
  });

  function hideForm() {
    overlay.style.display = 'none';
    formPopup.style.display = 'none';
  }

  overlay.addEventListener('click', hideForm);
  function closeError() {
    document.getElementById("error-popup").style.display = "none";
  }

  // Auto-hide after 5 seconds
  setTimeout(() => {
    const popup = document.getElementById("error-popup");
    if (popup) popup.style.display = "none";
  }, 5000);

</script>
</body>
</html>
