<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM expenses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header('Location: view_expenses.php');
        exit;
    } else {
        echo "Error deleting expense: " . $conn->error;
    }
} else {
    echo "No expense ID provided.";
}
?>
