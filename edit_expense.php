<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

if (!isset($_GET['id'])) {
    header('Location: view_expenses.php');
    exit;
}

$id = $_GET['id'];
$admin_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $category_id = $_POST['category_id'];
    $notes = $_POST['notes'];
    $bank_account_id = $_POST['bank_account_id'];

    $sql = "UPDATE expenses SET amount = ?, date = ?, category_id = ?, notes = ?, bank_account_id = ? WHERE id = ? AND admin_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("dsssiii", $amount, $date, $category_id, $notes, $bank_account_id, $id, $admin_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header('Location: view_expenses.php');
        exit;
    } else {
        echo "Error updating expense.";
    }

    $stmt->close();
}

$sql = "SELECT * FROM expenses WHERE id = ? AND admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$expense = $result->fetch_assoc();

// Fetch categories for the dropdown
$sql = "SELECT id, name FROM categories WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$categories = $stmt->get_result();

// Fetch bank accounts for the dropdown
$sql = "SELECT id, bank_name FROM bank_accounts WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$bank_accounts = $stmt->get_result();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expense</title>
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
            <li><a href="income.php"><i class="fas fa-money-bill-wave"></i> Income</a></li>
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
                <h1>Edit Expense</h1>
            </div>
            <div class="user-info">
                <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <img src="profile.jpg" alt="Profile Picture">
            </div>
        </header>
        <main>
            <div class="form-container">
                <form action="edit_expense.php?id=<?php echo $id; ?>" method="post">
                    <label for="amount">Amount:</label>
                    <input type="number" step="0.01" id="amount" name="amount" value="<?php echo htmlspecialchars($expense['amount']); ?>" required>

                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($expense['date']); ?>" required>

                    <label for="category_id">Category:</label>
                    <select id="category_id" name="category_id" required>
                        <?php while ($row = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>" <?php if ($row['id'] == $expense['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($row['name']); ?></option>
                        <?php endwhile; ?>
                    </select>

                    <label for="notes">Notes:</label>
                    <textarea id="notes" name="notes"><?php echo htmlspecialchars($expense['notes']); ?></textarea>

                    <label for="bank_account_id">Bank Account:</label>
                    <select id="bank_account_id" name="bank_account_id" required>
                        <?php while ($row = $bank_accounts->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>" <?php if ($row['id'] == $expense['bank_account_id']) echo 'selected'; ?>><?php echo htmlspecialchars($row['bank_name']); ?></option>
                        <?php endwhile; ?>
                    </select>

                    <button type="submit">Update Expense</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
