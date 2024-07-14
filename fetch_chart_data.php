<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include('db.php');

// Fetch data for charts
$chartData = [];

// Income by month
$incomeMonthQuery = "
    SELECT MONTH(date) as month, SUM(amount) as total_income
    FROM income
    GROUP BY MONTH(date)
    ORDER BY MONTH(date)
";
$incomeMonthResult = mysqli_query($conn, $incomeMonthQuery);
$incomeMonthData = [];
$incomeMonthLabels = [];
while ($row = mysqli_fetch_assoc($incomeMonthResult)) {
    $incomeMonthLabels[] = date('F', mktime(0, 0, 0, $row['month'], 10));
    $incomeMonthData[] = $row['total_income'];
}
$chartData['incomeMonth'] = [
    'labels' => $incomeMonthLabels,
    'income' => $incomeMonthData
];

// Income by week
$incomeWeekQuery = "
    SELECT WEEK(date) as week, SUM(amount) as total_income
    FROM income
    GROUP BY WEEK(date)
    ORDER BY WEEK(date)
";
$incomeWeekResult = mysqli_query($conn, $incomeWeekQuery);
$incomeWeekData = [];
$incomeWeekLabels = [];
while ($row = mysqli_fetch_assoc($incomeWeekResult)) {
    $incomeWeekLabels[] = 'Week ' . $row['week'];
    $incomeWeekData[] = $row['total_income'];
}
$chartData['incomeWeek'] = [
    'labels' => $incomeWeekLabels,
    'income' => $incomeWeekData
];

// Income by year
$incomeYearQuery = "
    SELECT YEAR(date) as year, SUM(amount) as total_income
    FROM income
    GROUP BY YEAR(date)
    ORDER BY YEAR(date)
";
$incomeYearResult = mysqli_query($conn, $incomeYearQuery);
$incomeYearData = [];
$incomeYearLabels = [];
while ($row = mysqli_fetch_assoc($incomeYearResult)) {
    $incomeYearLabels[] = $row['year'];
    $incomeYearData[] = $row['total_income'];
}
$chartData['incomeYear'] = [
    'labels' => $incomeYearLabels,
    'income' => $incomeYearData
];

// Income vs Expenses by month
$incomeExpenseMonthQuery = "
    SELECT MONTH(date) as month, 
           SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
           SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expenses
    FROM (
        SELECT amount, date, 'income' as type FROM income
        UNION ALL
        SELECT amount, date, 'expense' as type FROM expenses
    ) as t
    GROUP BY MONTH(date)
    ORDER BY MONTH(date)
";
$incomeExpenseMonthResult = mysqli_query($conn, $incomeExpenseMonthQuery);
$incomeExpenseMonthData = [
    'income' => [],
    'expenses' => []
];
$incomeExpenseMonthLabels = [];
while ($row = mysqli_fetch_assoc($incomeExpenseMonthResult)) {
    $incomeExpenseMonthLabels[] = date('F', mktime(0, 0, 0, $row['month'], 10));
    $incomeExpenseMonthData['income'][] = $row['total_income'];
    $incomeExpenseMonthData['expenses'][] = $row['total_expenses'];
}
$chartData['incomeExpenseMonth'] = [
    'labels' => $incomeExpenseMonthLabels,
    'income' => $incomeExpenseMonthData['income'],
    'expenses' => $incomeExpenseMonthData['expenses']
];

// Expenses today
$expensesTodayQuery = "
    SELECT HOUR(date) as hour, SUM(amount) as total_expenses
    FROM expenses
    WHERE DATE(date) = CURDATE()
    GROUP BY HOUR(date)
    ORDER BY HOUR(date)
";
$expensesTodayResult = mysqli_query($conn, $expensesTodayQuery);
$expensesTodayData = [];
$expensesTodayLabels = [];
while ($row = mysqli_fetch_assoc($expensesTodayResult)) {
    $expensesTodayLabels[] = $row['hour'] . ':00';
    $expensesTodayData[] = $row['total_expenses'];
}
$chartData['expensesToday'] = [
    'labels' => $expensesTodayLabels,
    'expenses' => $expensesTodayData
];

// Expenses yesterday
$expensesYesterdayQuery = "
    SELECT HOUR(date) as hour, SUM(amount) as total_expenses
    FROM expenses
    WHERE DATE(date) = CURDATE() - INTERVAL 1 DAY
    GROUP BY HOUR(date)
    ORDER BY HOUR(date)
";
$expensesYesterdayResult = mysqli_query($conn, $expensesYesterdayQuery);
$expensesYesterdayData = [];
$expensesYesterdayLabels = [];
while ($row = mysqli_fetch_assoc($expensesYesterdayResult)) {
    $expensesYesterdayLabels[] = $row['hour'] . ':00';
    $expensesYesterdayData[] = $row['total_expenses'];
}
$chartData['expensesYesterday'] = [
    'labels' => $expensesYesterdayLabels,
    'expenses' => $expensesYesterdayData
];

// Expenses by week
$expensesWeekQuery = "
    SELECT WEEK(date) as week, SUM(amount) as total_expenses
    FROM expenses
    GROUP BY WEEK(date)
    ORDER BY WEEK(date)
";
$expensesWeekResult = mysqli_query($conn, $expensesWeekQuery);
$expensesWeekData = [];
$expensesWeekLabels = [];
while ($row = mysqli_fetch_assoc($expensesWeekResult)) {
    $expensesWeekLabels[] = 'Week ' . $row['week'];
    $expensesWeekData[] = $row['total_expenses'];
}
$chartData['expensesWeek'] = [
    'labels' => $expensesWeekLabels,
    'expenses' => $expensesWeekData
];

// Expenses by month
$expensesMonthQuery = "
    SELECT MONTH(date) as month, SUM(amount) as total_expenses
    FROM expenses
    GROUP BY MONTH(date)
    ORDER BY MONTH(date)
";
$expensesMonthResult = mysqli_query($conn, $expensesMonthQuery);
$expensesMonthData = [];
$expensesMonthLabels = [];
while ($row = mysqli_fetch_assoc($expensesMonthResult)) {
    $expensesMonthLabels[] = date('F', mktime(0, 0, 0, $row['month'], 10));
    $expensesMonthData[] = $row['total_expenses'];
}
$chartData['expensesMonth'] = [
    'labels' => $expensesMonthLabels,
    'expenses' => $expensesMonthData
];

// Expenses by year
$expensesYearQuery = "
    SELECT YEAR(date) as year, SUM(amount) as total_expenses
    FROM expenses
    GROUP BY YEAR(date)
    ORDER BY YEAR(date)
";
$expensesYearResult = mysqli_query($conn, $expensesYearQuery);
$expensesYearData = [];
$expensesYearLabels = [];
while ($row = mysqli_fetch_assoc($expensesYearResult)) {
    $expensesYearLabels[] = $row['year'];
    $expensesYearData[] = $row['total_expenses'];
}
$chartData['expensesYear'] = [
    'labels' => $expensesYearLabels,
    'expenses' => $expensesYearData
];

// Expenses by category
$expensesCategoryQuery = "
    SELECT categories.name as category, SUM(expenses.amount) as total_expenses
    FROM expenses
    JOIN categories ON expenses.category_id = categories.id
    GROUP BY categories.name
";
$expensesCategoryResult = mysqli_query($conn, $expensesCategoryQuery);
$expensesCategoryData = [];
$expensesCategoryLabels = [];
while ($row = mysqli_fetch_assoc($expensesCategoryResult)) {
    $expensesCategoryLabels[] = $row['category'];
    $expensesCategoryData[] = $row['total_expenses'];
}
$chartData['expensesCategory'] = [
    'labels' => $expensesCategoryLabels,
    'data' => $expensesCategoryData
];

echo json_encode($chartData);
?>
