<?php
// Include database connection
include("../includes/connect.php");

// Handle form submission for updating admin note
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_note'])) {
    $quoteid = mysqli_real_escape_string($conn, $_POST['quoteid']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $admin_note = mysqli_real_escape_string($conn, $_POST['admin_note']);

    // Update query with sanitized inputs
    $updateQuery = "UPDATE Quote SET status = '$status', admin_note = '$admin_note' WHERE quoteid = '$quoteid'";
    $result = $conn->query($updateQuery);

    if ($result) {
        echo "<div class='alert alert-success'>Admin Note updated successfully for Quote ID $quoteid.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to update Admin Note for Quote ID $quoteid.</div>";
    }
}

$driveway = [];
$showDriveway = isset($_POST['largest_driveway']) && $_POST['largest_driveway'] === '1';
$quotesThisMonth = [];
$showQuotesThisMonth = isset($_POST['quotes_this_month']) && $_POST['quotes_this_month'] === '1';

// Handle toggle for the largest driveway
if (isset($_POST['toggle_driveway'])) {
    $showDriveway = !$showDriveway;
}

// Handle toggle for quotes from this month
if (isset($_POST['toggle_quotes_this_month'])) {
    $showQuotesThisMonth = !$showQuotesThisMonth;
}

// Fetch the largest driveways if toggle is active
if ($showDriveway) {
    $sqlDriveway = "
        SELECT 
            q.quoteid,
            q.userid,
            q.address,
            q.dimension_length,
            q.dimension_width,
            (q.dimension_length * q.dimension_width) AS area
        FROM 
            Quote q
        WHERE 
            (q.dimension_length * q.dimension_width) = (
                SELECT MAX(q1.dimension_length * q1.dimension_width) 
                FROM Quote q1
            ); 
    ";

    $resultDriveway = $conn->query($sqlDriveway);
    if ($resultDriveway && $resultDriveway->num_rows > 0) {
        while ($row = $resultDriveway->fetch_assoc()) {
            $driveway[] = $row;
        }
    }
}

// Fetch quotes from the current month if toggle is active
if ($showQuotesThisMonth) {
    $sqlQuotesThisMonth = "
        SELECT 
            quoteid,
            userid,
            address,
            dimension_length,
            dimension_width,
            price,
            client_note,
            status,
            admin_note,
            created_at
        FROM 
            Quote
        WHERE 
            MONTH(created_at) = MONTH(CURRENT_DATE) AND YEAR(created_at) = YEAR(CURRENT_DATE);
    ";

    $resultQuotesThisMonth = $conn->query($sqlQuotesThisMonth);
    if ($resultQuotesThisMonth && $resultQuotesThisMonth->num_rows > 0) {
        while ($row = $resultQuotesThisMonth->fetch_assoc()) {
            $quotesThisMonth[] = $row;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        #largestDrivewayTable {
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
        <!-- Toggle for Driveway -->
        <form method="post">
            <input type="hidden" name="largest_driveway" value="<?= $showDriveway ? '1' : '0' ?>">
            <button type="submit" name="toggle_driveway" class="btn btn-primary toggle-button">
                <?= $showDriveway ? 'Hide Largest Driveway' : 'Show Largest Driveway' ?>
            </button>
        </form>

        <!-- Largest Driveway Table -->
        <div id="largestDrivewayTable" style="display: <?= $showDriveway ? 'block' : 'none' ?>;">
            <h2>Largest Driveways</h2>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Quote ID</th>
                        <th>User ID</th>
                        <th>Address</th>
                        <th>Length</th>
                        <th>Width</th>
                        <th>Area</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($driveway)) {
                        foreach ($driveway as $entry) {
                            echo "
                            <tr>
                                <td>{$entry['quoteid']}</td>
                                <td>{$entry['userid']}</td>
                                <td>{$entry['address']}</td>
                                <td>{$entry['dimension_length']}</td>
                                <td>{$entry['dimension_width']}</td>
                                <td>{$entry['area']}</td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No data available.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Toggle for Quotes from This Month -->
        <form method="post">
            <input type="hidden" name="quotes_this_month" value="<?= $showQuotesThisMonth ? '1' : '0' ?>">
            <button type="submit" name="toggle_quotes_this_month" class="btn btn-secondary toggle-button">
                <?= $showQuotesThisMonth ? 'Hide Quotes This Month' : 'Show Quotes This Month' ?>
            </button>
        </form>

        <!-- Quotes This Month Table -->
        <div id="quotesThisMonthTable" style="display: <?= $showQuotesThisMonth ? 'block' : 'none' ?>;">
            <h2>Quotes From This Month</h2>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Quote ID</th>
                        <th>User ID</th>
                        <th>Address</th>
                        <th>Length</th>
                        <th>Width</th>
                        <th>Price</th>
                        <th>Client Note</th>
                        <th>Status</th>
                        <th>Admin Note</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($quotesThisMonth)) {
                        foreach ($quotesThisMonth as $quote) {
                            echo "
                            <tr>
                                <td>{$quote['quoteid']}</td>
                                <td>{$quote['userid']}</td>
                                <td>{$quote['address']}</td>
                                <td>{$quote['dimension_length']}</td>
                                <td>{$quote['dimension_width']}</td>
                                <td>{$quote['price']}</td>
                                <td>{$quote['client_note']}</td>
                                <td>{$quote['status']}</td>
                                <td>{$quote['admin_note']}</td>
                                <td>{$quote['created_at']}</td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No quotes available for this month.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Quotes Table -->
        <div class="container">
            <div class="row">
                <h2>Quotes Table</h2>
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Quote ID</th>
                            <th>User ID</th>
                            <th>Address</th>
                            <th>Size</th>
                            <th>Proposed Price</th>
                            <th>Photos</th>
                            <th>Client's Note</th>
                            <th>Status</th>
                            <th>My Note</th>
                            <th>My Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        global $conn;
                        $selectQuotes = "SELECT * FROM Quote";
                        $resultQuotes = $conn->query($selectQuotes);

                        if ($resultQuotes) {
                            while ($row_quote = $resultQuotes->fetch_assoc()) {
                                $quote_quoteID = $row_quote['quoteid'];
                                $quote_userID = $row_quote['userid'];
                                $quote_address = $row_quote['address'];
                                $quote_length = $row_quote['dimension_length'];
                                $quote_width = $row_quote['dimension_width'];
                                $quote_price = $row_quote['price'];
                                $quote_photo1 = $row_quote['photo1'];
                                $quote_photo2 = $row_quote['photo2'];
                                $quote_photo3 = $row_quote['photo3'];
                                $quote_photo4 = $row_quote['photo4'];
                                $quote_photo5 = $row_quote['photo5'];
                                $quote_client_note = $row_quote['client_note'];
                                $quote_status = $row_quote['status'];
                                $quote_admin_note = $row_quote['admin_note'];

                                echo "
                                <tr>
                                    <td>$quote_quoteID</td>
                                    <td>$quote_userID</td>
                                    <td>$quote_address</td>
                                    <td>$quote_length x $quote_width</td>
                                    <td>$quote_price</td>
                                    <td>
                                        <img src='./images/$quote_photo1' alt='Photo1' width='50'>
                                        <img src='./images/$quote_photo2' alt='Photo2' width='50'>
                                        <img src='./images/$quote_photo3' alt='Photo3' width='50'>
                                        <img src='./images/$quote_photo4' alt='Photo4' width='50'>
                                        <img src='./images/$quote_photo5' alt='Photo5' width='50'>
                                    </td>
                                    <td>$quote_client_note</td>
                                    <td>
                                        <form method='POST' action=''>
                                            <select name='status' class='form-control'>
                                                <option value='pending' " . ($quote_status == 'pending' ? 'selected' : '') . ">Pending</option>
                                                <option value='accepted' " . ($quote_status == 'accepted' ? 'selected' : '') . ">Accepted</option>
                                                <option value='refused' " . ($quote_status == 'refused' ? 'selected' : '') . ">Refused</option>
                                            </select>
                                    </td>
                                    <td>
                                        <input type='text' name='admin_note' value='$quote_admin_note' class='form-control'>
                                        <input type='hidden' name='quoteid' value='$quote_quoteID'>
                                    </td>
                                    <td>
                                        <button type='submit' name='update_note' class='btn btn-primary'>Update</button>
                                        </form>
                                    </td>
                                </tr>
                                ";
                            }
                        } else {
                            echo "<tr><td colspan='9'>Error fetching data.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>