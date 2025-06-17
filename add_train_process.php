<?php
$pdo = require_once 'data/db_connect.php';
session_start();

// Replace this with the actual logged-in admin's user_id
$created_by = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $train_name = $_POST['train_name'];
    $capacity = (int) $_POST['capacity'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO trains (train_name, capacity, status, created_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([$train_name, $capacity, $status, $created_by]);

    $train_id = $pdo->lastInsertId();

    $from_stations = $_POST['from_station'];
    $to_stations = $_POST['to_station'];
    $departures = $_POST['departure_time'];
    $arrivals = $_POST['arrival_time'];
    $frequencies = $_POST['frequency'];

    for ($i = 0; $i < count($from_stations); $i++) {
        $stmt2 = $pdo->prepare("INSERT INTO schedules (train_id, from_station, to_station, departure_time, arrival_time, frequency, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt2->execute([
            $train_id,
            $from_stations[$i],
            $to_stations[$i],
            $departures[$i],
            $arrivals[$i],
            $frequencies[$i],
            $created_by
        ]);
    }

    header("Location: train_management.php?success=1");
    exit();
}
?>
