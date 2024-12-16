<?php
include("../includes/connect.php");

// Fetch available quotes for the dropdown
$worksQuery = "SELECT workid, quoteid, userid FROM Work WHERE client_status = 'accepted' AND admin_status = 'pending'";
$worksResult = $conn->query($worksQuery);

// Handle form submission for inserting a new transaction
if (isset($_POST['submit'])) {
    $workid = $_POST['workid'];
    $price = $_POST['price'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $pay_status = $_POST['pay_status'];
    $admin_note = $_POST['admin_note'];

    // Retrieve quoteid and userid from the Work table
    $workDetailsQuery = "SELECT quoteid, userid FROM Work WHERE workid = '$workid'";
    $workDetailsResult = $conn->query($workDetailsQuery);

    if ($workDetailsResult && $workDetailsResult->num_rows > 0) {
        $workDetails = $workDetailsResult->fetch_assoc();
        $quoteid = $workDetails['quoteid'];
        $userid = $workDetails['userid'];

        // Check if the work already has an order
        $selectQuery = "SELECT * FROM Transactions WHERE workid = '$workid'";
        $result = $conn->query($selectQuery);

        if ($result && $result->num_rows > 0) {
            echo "<div class='alert alert-success'>Work Form already has an Order</div>";
        } else {
            // Prepare the SQL statement
            $insertQuery = "INSERT INTO Transactions (workid, quoteid, userid, start_date, end_date, price, pay_status, admin_note) 
                            VALUES ('$workid', '$quoteid', '$userid', '$start_date', '$end_date', '$price', '$pay_status', '$admin_note')";

            if ($conn->query($insertQuery) === TRUE) {
                echo "<div class='alert alert-success'>Order Form added successfully!</div>";
            } else {
                echo "<script>alert('Error: " . $conn->error . "');</script>";
            }
        }
    } else {
        echo "<script>alert('Invalid Work ID. Could not retrieve details.');</script>";
    }
}

// Handle form submission for updating an existing transaction
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_transaction'])) {
    $transactionid = mysqli_real_escape_string($conn, $_POST['transactionid']);
    $pay_status = mysqli_real_escape_string($conn, $_POST['pay_status']);
    $admin_note = mysqli_real_escape_string($conn, $_POST['admin_note']);

    // Update the Transactions table
    $updateTransactionQuery = "UPDATE Transactions SET pay_status = '$pay_status', admin_note = '$admin_note' WHERE transactionid = '$transactionid'";
    if ($conn->query($updateTransactionQuery) === TRUE) {
        // Check if the transaction is now marked as paid
        if ($pay_status === 'paid') {
            // Retrieve transaction details
            $selectTransactionQuery = "SELECT * FROM Transactions WHERE transactionid = '$transactionid'";
            $transactionResult = $conn->query($selectTransactionQuery);

            if ($transactionResult && $transactionResult->num_rows > 0) {
                $transaction = $transactionResult->fetch_assoc();

                // Log the transaction into TransactionPaid table
                $insertPaidQuery = "INSERT INTO TransactionPaid (transactionid, quoteid, workid, userid, cardid, created_at, paid_date) 
                                    VALUES ('{$transaction['transactionid']}', '{$transaction['quoteid']}', '{$transaction['workid']}', '{$transaction['userid']}', '{$transaction['cardid']}', '{$transaction['created_at']}', NOW())";
                if ($conn->query($insertPaidQuery) === TRUE) {
                    echo "<div class='alert alert-success'>Transaction updated to paid and logged in TransactionPaid table!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Failed to log paid transaction: " . $conn->error . "</div>";
                }
            }
        } else {
            echo "<div class='alert alert-success'>Transaction updated successfully for Transaction ID $transactionid.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Failed to update transaction for Transaction ID $transactionid.</div>";
    }
}

// Fetch transactions to display in the table
$selectCard = "SELECT t.*, c.nickname, c.number FROM Transactions t 
               LEFT JOIN Card c ON t.cardid = c.cardid";
$resultCard = $conn->query($selectCard);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link rel="stylesheet" href="design.css">
    <style>
        #orderForm {
            display: none;
            margin-top: 20px;
        }
        .toggle-button {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Toggle Button -->
        <button class="toggle-button btn btn-info" onclick="toggleForm()">Add New Order</button>

        <!-- Order Form -->
        <form action="" method="post" class="mb-2" id="orderForm">
            <div class="form-container">
                <h3>Order Form</h3>

                <!-- Work ID Dropdown -->
                <div class="mb-3">
                    <label for="workid-input" class="form-label">Work ID</label>
                    <select name="workid" id="workid-input" class="form-select" required>
                        <option value="" disabled selected>Select Work Form</option>
                        <?php
                        if ($worksResult && $worksResult->num_rows > 0) {
                            while ($row = $worksResult->fetch_assoc()) {
                                echo "<option value='" . $row['workid'] . "'>Work ID: " . $row['workid'] . " | Quote ID: " . $row['quoteid'] . " | User ID: " . $row['userid'] . "</option>";
                            }
                        } else {
                            echo "<option value='' disabled>No available quotes</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Price Field -->
                <div class="mb-3">
                    <label for="price-input" class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" id="price-input" class="form-control" required placeholder="00.00">
                </div>

                <!-- Time Frame Fields -->
                <div class="mb-3">
                    <label for="start-date-input" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start-date-input" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="end-date-input" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end-date-input" class="form-control" required>
                </div>

                <!-- Pay Status Field -->
                <div class="mb-3">
                    <label for="pay-status-input" class="form-label">Pay Status</label>
                    <select name="pay_status" id="pay-status-input" class="form-select" required>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="declined">Declined</option>
                    </select>
                </div>

                <!-- Note Field -->
                <div class="mb-3">
                    <label for="note-input" class="form-label">Note</label>
                    <textarea name="admin_note" id="note-input" class="form-control" placeholder="Add note"></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="submit" class="btn btn-info">Submit</button>
            </div>
        </form>

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
                            <th>User ID</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Price</th>
                            <th>Charge Status</th>
                            <th>Client Note</th>
                            <th>Paid Status</th>
                            <th>My Note</th>
                            <th>Card Information</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultCard && $resultCard->num_rows > 0) {
                            while ($row_transaction = $resultCard->fetch_assoc()) {
                                $cardInfo = !empty($row_transaction['cardid']) 
                                    ? "Card ID: {$row_transaction['cardid']} ({$row_transaction['nickname']} - Ending with " . substr($row_transaction['number'], -4) . ")" 
                                    : "No Card Assigned";

                                echo "
                                <tr>
                                    <td>{$row_transaction['transactionid']}</td>
                                    <td>{$row_transaction['quoteid']}</td>
                                    <td>{$row_transaction['workid']}</td>
                                    <td>{$row_transaction['userid']}</td>
                                    <td>{$row_transaction['start_date']}</td>
                                    <td>{$row_transaction['end_date']}</td>
                                    <td>{$row_transaction['price']}</td>
                                    <td>{$row_transaction['charge_status']}</td>
                                    <td>{$row_transaction['client_note']}</td>
                                    <td>
                                        <form method='POST' action=''>
                                            <select name='pay_status' class='form-control'>
                                                <option value='pending' " . ($row_transaction['pay_status'] == 'pending' ? 'selected' : '') . ">Pending</option>
                                                <option value='paid' " . ($row_transaction['pay_status'] == 'paid' ? 'selected' : '') . ">Paid</option>
                                                <option value='declined' " . ($row_transaction['pay_status'] == 'declined' ? 'selected' : '') . ">Declined</option>
                                            </select>
                                    </td>
                                    <td>
                                        <input type='text' name='admin_note' value='{$row_transaction['admin_note']}' class='form-control'>
                                        <input type='hidden' name='transactionid' value='{$row_transaction['transactionid']}'>
                                    </td>
                                    <td>{$cardInfo}</td>
                                    <td>
                                        <button type='submit' name='update_transaction' class='btn btn-primary'>Update</button>
                                        </form>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='13'>No Transaction records found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            const form = document.getElementById('orderForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
