<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$whereClause = "";

switch ($filter) {
    case 'day':
        $whereClause = "WHERE DATE(date) = CURDATE()";
        break;
    case 'week':
        $whereClause = "WHERE WEEK(date) = WEEK(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
        break;
    case 'month':
        $whereClause = "WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
        break;
    case 'year':
        $whereClause = "WHERE YEAR(date) = YEAR(CURDATE())";
        break;
    case 'custom':
        if (!empty($startDate) && !empty($endDate)) {
            $whereClause = "WHERE DATE(date) BETWEEN '$startDate' AND '$endDate'";
        }
        break;
    default:
        $whereClause = "";
        break;
}

$query = "
    SELECT 
        expenses.id,
        expenses.amount, 
        expenses.date, 
        expenses.notes, 
        bank_accounts.bank_name as bank_account, 
        categories.name as category
    FROM expenses
    JOIN bank_accounts ON expenses.bank_account_id = bank_accounts.id
    JOIN categories ON expenses.category_id = categories.id
    $whereClause
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Expenses</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.sub-menu').hide();
            $('.sidebar-menu li a.main-menu').click(function() {
                $(this).next('.sub-menu').slideToggle();
            });
        });
        function toggleDateInputs(value) {
            if (value === 'custom') {
                document.getElementById('custom-date-inputs').style.display = 'inline-block';
            } else {
                document.getElementById('custom-date-inputs').style.display = 'none';
            }
        }
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
            <h1>View Expenses</h1>
        </div>
        <div class="user-info">
            <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <img src="profile.jpg" alt="Profile Picture">
        </div>
    </header>
    <main>
        <div class="filter-container">
            <form method="GET" action="">
                <label for="filter">Filter by:</label>
                <select name="filter" id="filter" onchange="toggleDateInputs(this.value)">
                    <option value="all" <?php if ($filter == 'all') echo 'selected'; ?>>All</option>
                    <option value="day" <?php if ($filter == 'day') echo 'selected'; ?>>Day</option>
                    <option value="week" <?php if ($filter == 'week') echo 'selected'; ?>>Week</option>
                    <option value="month" <?php if ($filter == 'month') echo 'selected'; ?>>Month</option>
                    <option value="year" <?php if ($filter == 'year') echo 'selected'; ?>>Year</option>
                    <option value="custom" <?php if ($filter == 'custom') echo 'selected'; ?>>Custom</option>
                </select>
                <div id="custom-date-inputs" style="display: <?php echo $filter == 'custom' ? 'inline-block' : 'none'; ?>">
                    <label for="start_date">From:</label>
                    <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($startDate); ?>">
                    <label for="end_date">To:</label>
                    <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($endDate); ?>">
                </div>
                <button type="submit">Apply</button>
            </form>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Notes</th>
                        <th>Bank Account</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>Rs <?php echo number_format($row['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['notes']); ?></td>
                        <td><?php echo htmlspecialchars($row['bank_account']); ?></td>
                        <td>
                            <a href="edit_expense.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a>
                            <a href="delete_expense.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this expense?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
