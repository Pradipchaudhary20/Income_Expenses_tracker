<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

if (!isset($_GET['id'])) {
    header('Location: view_bank.php');
    exit;
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bank_name = $_POST['bank_name'];
    $balance = $_POST['balance'];

    $sql = "UPDATE bank_accounts SET bank_name = ?, balance = ? WHERE id = ? AND admin_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("sdii", $bank_name, $balance, $id, $_SESSION['id']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header('Location: view_bank.php');
        exit;
    } else {
        echo "Error updating bank account.";
    }

    $stmt->close();
}

// Fetch the bank details to populate the form
$sql = "SELECT * FROM bank_accounts WHERE id = ? AND admin_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("ii", $id, $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
$bank = $result->fetch_assoc();

if (!$bank) {
    echo "Error: Bank account not found.";
    exit;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bank Account</title>
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
                <h1>Edit Bank Account</h1>
            </div>
            <div class="user-info">
                <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <img src="profile.jpg" alt="Profile Picture">
            </div>
        </header>
        <main>
            <div class="form-container">
                <form action="edit_bank.php?id=<?php echo $id; ?>" method="post">
                    <label for="bank_name">Bank Name:</label>
                    <input type="text" id="bank_name" name="bank_name" value="<?php echo htmlspecialchars($bank['bank_name']); ?>" required>

                    <label for="balance">Balance:</label>
                    <input type="number" step="0.01" id="balance" name="balance" value="<?php echo htmlspecialchars($bank['balance']); ?>" required>

                    <button type="submit">Update Bank</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
