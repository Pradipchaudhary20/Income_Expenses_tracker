<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "income_expenses_manager");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin credentials
$username = 'Pradip';
$password = password_hash('S@niarchu0854', PASSWORD_BCRYPT); // Replace 'S@niarchu0854' with your desired password

// SQL statement to update the admin user
$sql = "UPDATE admin SET password = ? WHERE username = ?";

// Prepare and bind
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $password, $username);

// Execute the statement
if ($stmt->execute() === TRUE) {
    echo "Password updated successfully";
} else {
    echo "Error updating password: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
