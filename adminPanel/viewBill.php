<?php
// Include database connection
include("../includes/connect.php");

$overdue = [];
$showOverdue = isset($_POST['overdue_bills']) && $_POST['overdue_bills'] === '1';

// Handle toggle for the overdue bills
if (isset($_POST['toggle_overdue'])) {
    $showOverdue = !$showOverdue;
}

// Fetch overdue bills if toggle is active
if ($showOverdue) {
    $sqlOverdue = "
        SELECT 
            t.transactionid,
            t.quoteid,
            t.workid,
            t.userid,
            t.start_date,
            t.end_date,
            t.price,
            t.created_at
        FROM 
            Transactions t
        WHERE 
            t.charge_status = 'pending'
            AND t.created_at <= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) -- Pending for over a week

    ";

    $resultOverdue = $conn->query($sqlOverdue);
    if ($resultOverdue && $resultOverdue->num_rows > 0) {
        while ($row = $resultOverdue->fetch_assoc()) {
            $overdue[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bills Management</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .toggle-button {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <!-- Toggle for Overdue Bills -->
            <form method="post">
                <input type="hidden" name="overdue_bills" value="<?= $showOverdue ? '1' : '0' ?>">
                <button type="submit" name="toggle_overdue" class="btn btn-primary toggle-button">
                    <?= $showOverdue ? 'Hide Overdue Bills' : 'Show Overdue Bills' ?>
                </button>
            </form>

            <!-- Overdue Bills Table -->
            <div id="overdueBillsTable" style="display: <?= $showOverdue ? 'block' : 'none' ?>;">
                <h2>Overdue Bills</h2>
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Quote ID</th>
                            <th>Work ID</th>
                            <th>User ID</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Price</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($overdue)) {
                            foreach ($overdue as $bill) {
                                echo "
                                <tr>
                                    <td>{$bill['transactionid']}</td>
                                    <td>{$bill['quoteid']}</td>
                                    <td>{$bill['workid']}</td>
                                    <td>{$bill['userid']}</td>
                                    <td>{$bill['start_date']}</td>
                                    <td>{$bill['end_date']}</td>
                                    <td>{$bill['price']}</td>
                                    <td>{$bill['created_at']}</td>
                                </tr>
                                ";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No overdue bills found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Paid Bills Table -->
            <h2>Paid Bills</h2>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Quote ID</th>
                        <th>Work ID</th>
                        <th>User ID</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Use global connection variable
                    global $conn;

                    // Query to fetch paid bills
                    $selectBill = "SELECT * FROM Transactions WHERE pay_status = 'paid'";
                    $resultBill = mysqli_query($conn, $selectBill);

                    // Check if the query executed successfully
                    if ($resultBill) {
                        while ($row_bill = mysqli_fetch_assoc($resultBill)) {
                            // Fetch data
                            $bill_transactionID = $row_bill['transactionid'];
                            $bill_quoteID = $row_bill['quoteid'];
                            $bill_workID = $row_bill['workid'];
                            $bill_userID = $row_bill['userid'];
                            $bill_start_date = $row_bill['start_date'];
                            $bill_end_date = $row_bill['end_date'];
                            $bill_price = $row_bill['price'];

                            // Output table row
                            echo "
                            <tr>
                                <td>$bill_transactionID</td>
                                <td>$bill_quoteID</td>
                                <td>$bill_workID</td>
                                <td>$bill_userID</td>
                                <td>$bill_start_date</td>
                                <td>$bill_end_date</td>
                                <td>$bill_price</td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='7'>Error fetching data.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
