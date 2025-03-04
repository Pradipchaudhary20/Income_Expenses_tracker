<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

// Fetch transactions related to "Money to Get"
$sql = "SELECT * FROM transactions WHERE transaction_type = 'money_to_get' ORDER BY action_date DESC";
$result = $conn->query($sql);

if ($result === false) {
    die('Query failed: ' . htmlspecialchars($conn->error));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Money to Get Transactions</title>

    <!-- Ensure your paths to the styles and scripts are correct -->
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Ensure jQuery is loaded -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        console.log("JS loaded successfully");
        $('.sub-menu').hide();
        $('.sidebar-menu li a.main-menu').click(function() {
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
                <a href="javascript:void(0)" class="main-menu"><i class="fas fa-money-bill-wave"></i> Income</a>
                <ul class="sub-menu">
                    <li><a href="add_income.php"><i class="fas fa-plus-circle"></i> Add Income</a></li>
                    <li><a href="view_income.php"><i class="fas fa-eye"></i> View Income</a></li>
                    <li><a href="add_income_category.php"><i class="fas fa-plus-circle"></i> Add Income Category</a>
                    </li>
                    <li><a href="view_income_categories.php"><i class="fas fa-eye"></i> View Income Categories</a></li>
                </ul>
            </li>
            <li>
                <a href="javascript:void(0)" class="main-menu"><i class="fas fa-money-check-alt"></i> Expenses</a>
                <ul class="sub-menu">
                    <li><a href="add_expense.php"><i class="fas fa-plus-circle"></i> Add Expense</a></li>
                    <li><a href="view_expenses.php"><i class="fas fa-eye"></i> View Expenses</a></li>
                </ul>
            </li>
            <li>
                <a href="javascript:void(0)" class="main-menu"><i class="fas fa-university"></i> Bank Account</a>
                <ul class="sub-menu">
                    <li><a href="add_bank.php"><i class="fas fa-plus-circle"></i> Add Bank</a></li>
                    <li><a href="view_bank.php"><i class="fas fa-eye"></i> View Bank</a></li>
                </ul>
            </li>
            <li>
                <a href="javascript:void(0)" class="main-menu"><i class="fas fa-hand-holding-usd"></i> Loans</a>
                <ul class="sub-menu">
                    <li><a href="add_loan_to_pay.php"><i class="fas fa-plus-circle"></i> Loan To Pay</a></li>
                    <li><a href="view_loans_to_pay.php"><i class="fas fa-eye"></i> View Loans To Pay</a></li>
                    <li><a href="add_money_to_get.php"><i class="fas fa-plus-circle"></i> Money To Get</a></li>
                    <li><a href="view_money_to_get.php"><i class="fas fa-eye"></i> View Money To Get</a></li>
                    <li><a href="view_money_transactions.php"><i class="fas fa-exchange-alt"></i> View Money to Get Transactions</a></li>
                </ul>
            </li>
            <li><a href="transactions.php"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <div class="title">
                <h1>Money Transactions</h1>
            </div>
        </header>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Amount</th>
                        <th>Performed By</th>
                        <th>Date</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['action']); ?></td>
                        <td><?php echo number_format($row['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['performed_by']); ?></td>
                        <td><?php echo htmlspecialchars($row['action_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
