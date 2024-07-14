<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

// Fetch summary data
$totalIncome = getTotalIncome($conn);
$totalExpenses = getTotalExpenses($conn);
$expensesToday = getExpensesToday($conn);
$expensesYesterday = getExpensesYesterday($conn);
$expensesThisWeek = getExpensesThisWeek($conn);
$expensesThisMonth = getExpensesThisMonth($conn);
$expensesThisYear = getExpensesThisYear($conn);

$incomeThisWeek = getIncomeThisWeek($conn);
$incomeThisMonth = getIncomeThisMonth($conn);
$incomeThisYear = getIncomeThisYear($conn);

function getTotalIncome($conn) {
    $result = mysqli_query($conn, "SELECT SUM(amount) as total FROM income");
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getTotalExpenses($conn) {
    $result = mysqli_query($conn, "SELECT SUM(amount) as total FROM expenses");
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getExpensesToday($conn) {
    $result = mysqli_query($conn, "SELECT SUM(amount) as total FROM expenses WHERE DATE(date) = CURDATE()");
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getExpensesYesterday($conn) {
    $result = mysqli_query($conn, "SELECT SUM(amount) as total FROM expenses WHERE DATE(date) = CURDATE() - INTERVAL 1 DAY");
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getExpensesThisWeek($conn) {
    $result = mysqli_query($conn, "SELECT SUM(amount) as total FROM expenses WHERE YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1)");
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getExpensesThisMonth($conn) {
    $result = mysqli_query($conn, "SELECT SUM(amount) as total FROM expenses WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())");
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getExpensesThisYear($conn) {
    $result = mysqli_query($conn, "SELECT SUM(amount) as total FROM expenses WHERE YEAR(date) = YEAR(CURDATE())");
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getIncomeThisWeek($conn) {
    $result = mysqli_query($conn, "SELECT SUM(amount) as total FROM income WHERE YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1)");
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getIncomeThisMonth($conn) {
    $result = mysqli_query($conn, "SELECT SUM(amount) as total FROM income WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())");
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getIncomeThisYear($conn) {
    $result = mysqli_query($conn, "SELECT SUM(amount) as total FROM income WHERE YEAR(date) = YEAR(CURDATE())");
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

$incomeData = getMonthlyData($conn, 'income');
$expensesData = getMonthlyData($conn, 'expenses');

function getMonthlyData($conn, $table) {
    $query = "
        SELECT 
            MONTH(date) as month, 
            SUM(amount) as total 
        FROM $table 
        WHERE YEAR(date) = YEAR(CURDATE()) 
        GROUP BY MONTH(date)";
    $result = mysqli_query($conn, $query);
    $data = array_fill(0, 12, 0);
    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row['month'] - 1] = $row['total'];
    }
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income Expense Tracker</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.sub-menu').hide();
        $('.sidebar-menu li a.main-menu').click(function() {
            $(this).next('.sub-menu').slideToggle();
        });

        const incomeData = <?php echo json_encode($incomeData); ?>;
        const expensesData = <?php echo json_encode($expensesData); ?>;

        const ctx = document.getElementById('incomeVsExpensesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                    'Dec'
                ],
                datasets: [{
                        label: 'Income',
                        data: incomeData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Expenses',
                        data: expensesData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
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
                <h1>Dashboard</h1>
            </div>
            <div class="user-info">
                <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <img src="profile.jpg" alt="Profile Picture">
            </div>
        </header>
        <main>
            <div class="dashboard-card">
                <div class="card">
                    <h3>Total Income <i class="fas fa-chart-line"></i></h3>
                    <p>Rs <?php echo number_format($totalIncome, 2); ?></p>
                </div>
                <div class="card">
                    <h3>Total Expenses <i class="fas fa-money-bill-wave"></i></h3>
                    <p>Rs <?php echo number_format($totalExpenses, 2); ?></p>
                </div>
                <div class="card">
                    <h3>Expenses Today <i class="fas fa-calendar-day"></i></h3>
                    <p>Rs <?php echo number_format($expensesToday, 2); ?></p>
                </div>
                <div class="card">
                    <h3>Expenses Yesterday <i class="fas fa-calendar"></i></h3>
                    <p>Rs <?php echo number_format($expensesYesterday, 2); ?></p>
                </div>
                <div class="card">
                    <h3>Expenses This Week <i class="fas fa-calendar-week"></i></h3>
                    <p>Rs <?php echo number_format($expensesThisWeek, 2); ?></p>
                </div>
                <div class="card">
                    <h3>Expenses This Month <i class="fas fa-calendar-alt"></i></h3>
                    <p>Rs <?php echo number_format($expensesThisMonth, 2); ?></p>
                </div>
                <div class="card">
                    <h3>Expenses This Year <i class="fas fa-calendar-check"></i></h3>
                    <p>Rs <?php echo number_format($expensesThisYear, 2); ?></p>
                </div>
                <div class="card">
                    <h3>Income This Week <i class="fas fa-calendar-week"></i></h3>
                    <p>Rs <?php echo number_format($incomeThisWeek, 2); ?></p>
                </div>
                <div class="card">
                    <h3>Income This Month <i class="fas fa-calendar-alt"></i></h3>
                    <p>Rs <?php echo number_format($incomeThisMonth, 2); ?></p>
                </div>
                <div class="card">
                    <h3>Income This Year <i class="fas fa-calendar-check"></i></h3>
                    <p>Rs <?php echo number_format($incomeThisYear, 2); ?></p>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="incomeVsExpensesChart"></canvas>
            </div>
        </main>
    </div>
</body>

</html>