<?php
$conn = new mysqli("localhost", "root", "", "income_expenses_manager");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
