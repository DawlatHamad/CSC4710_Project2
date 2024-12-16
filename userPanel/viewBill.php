<?php
// Include database connection
include("../includes/connect.php");

// Ensure the user is logged in
session_start();
if (!isset($_SESSION['userid'])) {
    header('location:../login.php');
    exit();
}

$userid = $_SESSION['userid']; // Get the logged-in user's ID
?>

<!-- HTML Form -->
<div class="container">
    <div class="row">
        <h2>Bill Table</h2>
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

                // Query to fetch data
                $selectBill = "SELECT * FROM Transactions WHERE pay_status = 'paid' AND userid = '$userid'";
                $resultBill = mysqli_query($conn, $selectBill);

                // Check if the query executed successfully
                if ($resultBill) {
                    if (mysqli_num_rows($resultBill) > 0) {
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
                        // No rows returned
                        echo "<tr><td colspan='7'>No bills found for this user.</td></tr>";
                    }
                } else {
                    // Query execution error
                    echo "<tr><td colspan='7'>Error fetching data: " . mysqli_error($conn) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
