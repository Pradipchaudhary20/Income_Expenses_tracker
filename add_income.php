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

// Handle form submission for adding an income
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_income'])) {
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $category_id = isset($_POST['category_id']) ? mysqli_real_escape_string($conn, $_POST['category_id']) : null;
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $bank_account_id = isset($_POST['bank_account_id']) ? mysqli_real_escape_string($conn, $_POST['bank_account_id']) : null;

    if ($category_id && $bank_account_id) {
        $query = "INSERT INTO income (admin_id, category_id, bank_account_id, amount, date, notes) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("iiidss", $adminId, $category_id, $bank_account_id, $amount, $date, $notes);
        if ($stmt->execute()) {
            header('Location: view_income.php');
            exit;
        } else {
            die("Error inserting income: " . $stmt->error);
        }
    } else {
        die("Category and Bank Account must be selected.");
    }
}

// Fetch categories for the dropdown
$categoryQuery = "SELECT id, name FROM income_categories";
$categoryResult = $conn->query($categoryQuery);

// Fetch bank accounts for the dropdown
$bankQuery = "SELECT id, bank_name FROM bank_accounts";
$bankResult = $conn->query($bankQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Income</title>
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
                <li><a href="add_income_category.php"><i class="fas fa-plus-circle"></i> Add Income Category</a></li>
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
                <h1>Add Income</h1>
            </div>
            <div class="user-info">
                <p><?php echo htmlspecialchars($username); ?></p>
                <img src="profile.jpg" alt="Profile Picture">
            </div>
        </header>
        <div class="form-container">
            <form method="POST" action="">
                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" required>
                
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
                
                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php while ($row = $categoryResult->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                    <?php endwhile; ?>
                </select>
                
                <label for="notes">Notes:</label>
                <textarea id="notes" name="notes"></textarea>
                
                <label for="bank_account_id">Bank Account:</label>
                <select id="bank_account_id" name="bank_account_id" required>
                    <option value="">Select a bank account</option>
                    <?php while ($row = $bankResult->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['bank_name']); ?></option>
                    <?php endwhile; ?>
                </select>
                
                <button type="submit" name="add_income">Add Income</button>
            </form>
        </div>
    </div>
</body>
</html>
