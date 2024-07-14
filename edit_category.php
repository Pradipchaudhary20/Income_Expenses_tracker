<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

$id = $_GET['id'];
$query = "SELECT * FROM categories WHERE id = $id";
$result = mysqli_query($conn, $query);
$category = mysqli_fetch_assoc($result);

// Handle form submission for updating a category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $query = "UPDATE categories SET name='$name' WHERE id=$id";
    mysqli_query($conn, $query);
    header('Location: view_categories.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link rel="stylesheet" href="add_category.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
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
                </ul>
            </li>
            <li><a href="transactions.php"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <header>
            <div class="title">
                <h1>Edit Category</h1>
            </div>
        </header>
        <main>
            <form method="POST" action="">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>"
                    required>
                <button type="submit" name="update_category">Update Category</button>
            </form>
        </main>
    </div>
</body>

</html>