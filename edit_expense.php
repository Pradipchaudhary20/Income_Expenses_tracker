<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch expense details
    $sql = "SELECT * FROM expenses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $expense = $result->fetch_assoc();

    // Fetch categories and bank accounts for the dropdowns
    $categoriesResult = $conn->query("SELECT id, name FROM categories");
    $bankAccountsResult = $conn->query("SELECT id, bank_name FROM bank_accounts");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $amount = $_POST['amount'];
        $date = $_POST['date'];
        $category_id = $_POST['category'];
        $notes = $_POST['notes'];
        $bank_account_id = $_POST['bank_account'];

        $sql = "UPDATE expenses SET amount = ?, date = ?, category_id = ?, notes = ?, bank_account_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dsisii", $amount, $date, $category_id, $notes, $bank_account_id, $id);

        if ($stmt->execute()) {
            header('Location: view_expenses.php');
            exit;
        } else {
            echo "Error updating expense: " . $conn->error;
        }
    }
} else {
    echo "No expense ID provided.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expense</title>
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
                <h1>Edit Expense</h1>
            </div>
            <div class="user-info">
                <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <img src="profile.jpg" alt="Profile Picture">
            </div>
        </header>
        <div class="form-container">
            <form method="POST" action="">
                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount"
                    value="<?php echo htmlspecialchars($expense['amount']); ?>" required>

                <label for="date">Date:</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($expense['date']); ?>"
                    required>

                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <?php while ($category = $categoriesResult->fetch_assoc()): ?>
                    <option value="<?php echo $category['id']; ?>"
                        <?php if ($category['id'] == $expense['category_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>

                <label for="notes">Notes:</label>
                <textarea id="notes" name="notes"><?php echo htmlspecialchars($expense['notes']); ?></textarea>

                <label for="bank_account">Bank Account:</label>
                <select id="bank_account" name="bank_account" required>
                    <?php while ($bankAccount = $bankAccountsResult->fetch_assoc()): ?>
                    <option value="<?php echo $bankAccount['id']; ?>"
                        <?php if ($bankAccount['id'] == $expense['bank_account_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($bankAccount['bank_name']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit">Update Expense</button>
            </form>
        </div>
    </div>
</body>

</html>