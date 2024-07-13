<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

// Handle delete request
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query = "DELETE FROM categories WHERE id = $id";
    mysqli_query($conn, $query);
    header('Location: view_categories.php');
    exit;
}
?>
