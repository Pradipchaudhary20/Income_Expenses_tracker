<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: view_expenses.php');
    exit;
}

include('db.php');

$id = $_GET['id'];
$admin_id = $_SESSION['id'];

$sql = "DELETE FROM expenses WHERE id = ? AND admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $admin_id);
$stmt->execute();

header('Location: view_expenses.php');
exit;
