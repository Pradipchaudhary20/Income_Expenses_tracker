<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

// Fetch the username from the session
$username = $_SESSION['username'];
$adminId = $_SESSION['id'];

// Delete the income entry
if (isset($_GET['id'])) {
    $incomeId = $_GET['id'];
    $query = "DELETE FROM income WHERE id = ? AND admin_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("ii", $incomeId, $adminId);
    if ($stmt->execute()) {
        header('Location: view_income.php');
        exit;
    } else {
        die("Error deleting income: " . $stmt->error);
    }
} else {
    die("No income ID provided.");
}
?>
