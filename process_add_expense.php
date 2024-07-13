<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_expense'])) {
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $bank_account_id = mysqli_real_escape_string($conn, $_POST['bank_account']);

    $query = "INSERT INTO expenses (amount, date, category_id, notes, bank_account_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("dsssi", $amount, $date, $category_id, $notes, $bank_account_id);
    if ($stmt->execute()) {
        header('Location: view_expenses.php');
        exit;
    } else {
        die("Error inserting expense: " . $stmt->error);
    }
}
?>
