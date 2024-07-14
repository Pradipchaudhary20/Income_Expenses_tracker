<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

if (!isset($_GET['id'])) {
    header('Location: view_money_to_get.php');
    exit;
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $name = $_POST['name'];
    $date = $_POST['date'];

    $sql = "UPDATE money_to_get SET amount = ?, name = ?, date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dssi", $amount, $name, $date, $id);

    if ($stmt->execute()) {
        header('Location: view_money_to_get.php');
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    $sql = "SELECT * FROM money_to_get WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();

    if (!$record) {
        header('Location: view_money_to_get.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Money To Get</title>
    <link rel="stylesheet" href="styles.css">
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
        <li>
            <a href="javascript:void(0)"><i class="fas fa-money-check-alt"></i> Expenses</a>
            <ul class="sub-menu">
                <li><a href="add_expense.php"><i class="fas fa-plus-circle"></i> Add Expense</a></li>
                <li><a href="view_expenses.php"><i class="fas fa-eye"></i> View Expenses</a></li>
            </ul>
        </li>
        <li>
            <a href="javascript:void(0)"><i class="fas fa-university"></i> Bank Account</a>
            <ul class="sub-menu">
                <li><a href="add_bank.php"><i class="fas fa-plus-circle"></i> Add Bank</a></li>
                <li><a href="view_bank.php"><i class="fas fa-eye"></i> View Bank</a></li>
            </ul>
        </li>
        <li>
            <a href="javascript:void(0)"><i class="fas fa-hand-holding-usd"></i> Loans</a>
            <ul class="sub-menu">
                <li><a href="add_loan_to_pay.php"><i class="fas fa-plus-circle"></i> Loan To Pay</a></li>
                <li><a href="view_loans_to_pay.php"><i class="fas fa-eye"></i> View Loans To Pay</a></li>
                <li><a href="add_money_to_get.php"><i class="fas fa-plus-circle"></i> Money To Get</a></li>
                <li><a href="view_money_to_get.php"><i class="fas fa-eye"></i> View Money To Get</a></li>
            </ul>
        </li>
        <li><a href="transactions.php"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <header>
        <div class="title">
            <h1>Edit Money To Get</h1>
        </div>
        <div class="user-info">
            <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <img src="profile.jpg" alt="Profile Picture">
        </div>
    </header>
    <div class="form-container">
        <form method="POST" action="">
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" value="<?php echo htmlspecialchars($record['amount']); ?>" required>
            
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($record['name']); ?>" required>
            
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($record['date']); ?>" required>
            
            <button type="submit">Update Money To Get</button>
        </form>
    </div>
</div>
</body>
</html>
