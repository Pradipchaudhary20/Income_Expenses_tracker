<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

if (!isset($_GET['id'])) {
    header('Location: view_bank.php');
    exit;
}

$id = $_GET['id'];

$sql = "DELETE FROM bank_accounts WHERE id = ? AND admin_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("ii", $id, $_SESSION['id']);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header('Location: view_bank.php');
    exit;
} else {
    echo "Error deleting bank account.";
}

$stmt->close();
?>
