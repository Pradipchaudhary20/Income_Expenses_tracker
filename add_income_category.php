<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

// Check if the session contains the user ID
if (!isset($_SESSION['id'])) {
    die("User ID not found in session. Please log in again.");
}

// Fetch the username from the session
$userId = $_SESSION['id'];
$username = $_SESSION['username'];

// Handle form submission for adding a category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_income_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    $query = "INSERT INTO income_categories (name) VALUES ('$name')";
    if (mysqli_query($conn, $query)) {
        header('Location: view_income_categories.php');
        exit;
    } else {
        // Handle the error if the query fails
        die("Error inserting income category: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Income Category</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.sidebar-menu li a').click(function() {
                $(this).next('.sub-menu').slideToggle();
            });
        });
    </script>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Tracker.</h2>
            <p>Income.Expenses.Tracker</p>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="add_category.php"><i class="fas fa-plus-circle"></i> Add Category</a></li>
            <li><a href="view_categories.php"><i class="fas fa-list"></i> View Category</a></li>
            <li>
                <a href="javascript:void(0)"><i class="fas fa-money-bill-wave"></i> Income</a>
                <ul class="sub-menu">
                    <li><a href="add_income.php"><i class="fas fa-plus-circle"></i> Add Income</a></li>
                    <li><a href="view_income.php"><i class="fas fa-eye"></i> View Income</a></li>
                    <li><a href="add_income_category.php"><i class="fas fa-plus-circle"></i> Add Income Category</a></li>
                    <li><a href="view_income_categories.php"><i class="fas fa-eye"></i> View Income Categories</a></li>
                </ul>
            </li>
            <li><a href="expenses.php"><i class="fas fa-money-check-alt"></i> Expenses</a></li>
            <li>
                <a href="javascript:void(0)"><i class="fas fa-university"></i> Bank Account</a>
                <ul class="sub-menu">
                    <li><a href="add_bank.php"><i class="fas fa-plus-circle"></i> Add Bank</a></li>
                    <li><a href="view_bank.php"><i class="fas fa-eye"></i> View Bank</a></li>
                </ul>
            </li>
            <li><a href="transactions.php"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <header>
            <div class="title">
                <h1>Add Income Category</h1>
            </div>
            <div class="user-info">
                <p><?php echo htmlspecialchars($username); ?></p>
                <img src="profile.jpg" alt="Profile Picture">
            </div>
        </header>
        <div class="form-container">
            <form method="POST" action="">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <button type="submit" name="add_income_category">Add Income Category</button>
            </form>
        </div>
    </div>
</body>
</html>
