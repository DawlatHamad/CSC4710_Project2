<?php
include("../includes/connect.php");

// Ensure the user is logged in
session_start();
if (!isset($_SESSION['userid'])) {
    header('location:../login.php');
    exit();
}

$userid = $_SESSION['userid']; // Get the logged-in user's ID

// Handle form submission for updating an existing transaction
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_transaction'])) {
    $transactionid = mysqli_real_escape_string($conn, $_POST['transactionid']); 
    $charge_status = mysqli_real_escape_string($conn, $_POST['charge_status']);
    $client_note = mysqli_real_escape_string($conn, $_POST['client_note']);
    $cardid = mysqli_real_escape_string($conn, $_POST['cardid']); // Get the selected cardid

    // Update query with sanitized inputs
    $updateTransactionQuery = "UPDATE Transactions 
        SET charge_status = '$charge_status', client_note = '$client_note', cardid = '$cardid' 
        WHERE transactionid = '$transactionid' AND userid = '$userid'";

    if ($conn->query($updateTransactionQuery) === TRUE) {
        echo "<div class='alert alert-success'>Order updated successfully for Order ID $transactionid.</div>";
    } else {
        echo "<script>alert('Error: " . addslashes($conn->error) . "');</script>";
    }
}

// Fetch transactions to display in the table
$selectTransaction = "SELECT * FROM Transactions WHERE userid = '$userid'";
$resultTransaction = $conn->query($selectTransaction);

// Fetch available cards for the user
$selectCards = "SELECT * FROM Card WHERE userid = '$userid'";
$resultCards = $conn->query($selectCards);
?>

<!-- Order Table -->
<div class="container">
    <div class="row">
        <h2>Order Table</h2>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Quote ID</th>
                    <th>Work ID</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Price</th>
                    <th>Charge Status</th>
                    <th>My Note</th>
                    <th>Paid Status</th>
                    <th>Contractor's Note</th>
                    <th>Card Selection</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultTransaction && $resultTransaction->num_rows > 0) {
                    while ($row_transaction = $resultTransaction->fetch_assoc()) {
                        echo "
                        <form method='POST' action=''>
                            <tr>
                                <td>{$row_transaction['transactionid']}</td>
                                <td>{$row_transaction['quoteid']}</td>
                                <td>{$row_transaction['workid']}</td>
                                <td>{$row_transaction['start_date']}</td>
                                <td>{$row_transaction['end_date']}</td>
                                <td>{$row_transaction['price']}</td>
                                <td>
                                    <select name='charge_status' class='form-control'>
                                        <option value='pending' " . ($row_transaction['charge_status'] == 'pending' ? 'selected' : '') . ">Pending</option>
                                        <option value='charge' " . ($row_transaction['charge_status'] == 'charge' ? 'selected' : '') . ">Charge</option>
                                        <option value='deny' " . ($row_transaction['charge_status'] == 'deny' ? 'selected' : '') . ">Deny</option>
                                    </select>
                                </td>
                                <td>
                                    <input type='text' name='client_note' value='{$row_transaction['client_note']}' class='form-control'>
                                    <input type='hidden' name='transactionid' value='{$row_transaction['transactionid']}'>
                                </td>
                                <td>{$row_transaction['pay_status']}</td>
                                <td>{$row_transaction['admin_note']}</td>
                                <td>
                                    <select name='cardid' class='form-control'>
                                        <option value='' disabled selected>Select a Card</option>";

                        // Reset result pointer and populate card options
                        mysqli_data_seek($resultCards, 0);
                        if ($resultCards && $resultCards->num_rows > 0) {
                            while ($row_card = $resultCards->fetch_assoc()) {
                                echo "<option value='{$row_card['cardid']}'" . ($row_card['cardid'] == $row_transaction['cardid'] ? ' selected' : '') . ">{$row_card['nickname']}</option>";
                            }
                        } else {
                            echo "<option value='' disabled>No available cards</option>";
                        }

                        echo "</select>
                                </td>
                                <td>
                                    <button type='submit' name='update_transaction' class='btn btn-primary'>Update</button>
                                </td>
                            </tr>
                        </form>";
                    }
                } else {
                    echo "<tr><td colspan='12'>No Transaction records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
