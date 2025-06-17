<?php
session_start();
$pdo = require 'data/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        die("You must be logged in to book a ticket.");
    }

    $user_id = $_SESSION['user_id'];
    $schedule_id = $_POST['schedule_id'] ?? null;
    $seat_number = trim($_POST['seat_number'] ?? '');
    $fare = floatval($_POST['fare'] ?? 0);
    $travel_date = $_POST['travel_date'] ?? '';

    if ($schedule_id && $seat_number !== '' && $fare > 0 && $travel_date) {
        $today = date('Y-m-d');
        $now = date('H:i:s');

        // Fetch schedule departure time
        $stmt = $pdo->prepare("SELECT departure_time FROM schedules WHERE schedule_id = ?");
        $stmt->execute([$schedule_id]);
        $schedule = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$schedule) {
            header("Location: user_dashboard.php?error=" . urlencode("Invalid schedule selected."));
            exit();
        }

        $departureDateTime = new DateTime($schedule['departure_time']);
        $selectedDate = new DateTime($travel_date);

        // Check if selected travel date is in the past
        if ($selectedDate < new DateTime($today)) {
            header("Location: user_dashboard.php?error=" . urlencode("You cannot book a ticket for a past date."));
            exit();
        }

        // If travel date is today, ensure departure time has not passed
        if ($selectedDate->format('Y-m-d') === $today && $departureDateTime->format('H:i:s') < $now) {
            header("Location: user_dashboard.php?error=" . urlencode("You cannot book this train. Its departure time has already passed for today."));
            exit();
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO tickets (user_id, schedule_id, seat_number, travel_date, price) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $schedule_id, $seat_number, $travel_date, $fare]);

            header("Location: user_dashboard.php?success=1");
            exit();
        } catch (PDOException $e) {
            header("Location: user_dashboard.php?error=" . urlencode("Booking failed. Please try again."));
            exit();
        }
    } else {
        header("Location: user_dashboard.php?error=" . urlencode("All fields are required. Please fill the form completely."));
        exit();
    }
} else {
    header("Location: user_dashboard.php");
    exit();
}
?>
