<?php
include("../includes/connect.php");

// Fetch available quotes for dropdown
$quotesQuery = "SELECT quoteid, userid FROM Quote WHERE status = 'pending'";
$quotesResult = $conn->query($quotesQuery);

// Handle new Work form submission
if (isset($_POST['submit'])) {
    $quoteid = $_POST['quoteid'];
    $price = $_POST['price'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $admin_status = $_POST['admin_status'];
    $admin_note = $_POST['admin_note'];

    $getUserQuery = "SELECT userid FROM Quote WHERE quoteid = '$quoteid'";
    $userResult = $conn->query($getUserQuery);

    if ($userResult && $userResult->num_rows > 0) {
        $userRow = $userResult->fetch_assoc();
        $userid = $userRow['userid'];

        $selectQuery = "SELECT * FROM Work WHERE quoteid = '$quoteid' AND price = '$price' AND start_date = '$start_date' AND end_date = '$end_date'";
        $result = $conn->query($selectQuery);

        if ($result->num_rows > 0) {
            echo "<div class='alert alert-warning'>Work Form with similar entry exists!</div>";
        } else {
            $insertQuery = "INSERT INTO Work (quoteid, userid, price, start_date, end_date, admin_status, admin_note) 
                            VALUES ('$quoteid', '$userid', '$price', '$start_date', '$end_date', '$admin_status', '$admin_note')";
            if ($conn->query($insertQuery) === TRUE) {
                $lastWorkId = $conn->insert_id;

                $insertHistoryQuery = "INSERT INTO WorkHistory (workid, userid, quoteid, price, start_date, end_date, client_status, client_note, admin_status, admin_note) 
                                       VALUES ('$lastWorkId', '$userid', '$quoteid', '$price', '$start_date', '$end_date', 'pending', NULL, '$admin_status', '$admin_note')";
                if ($conn->query($insertHistoryQuery) === TRUE) {
                    echo "<div class='alert alert-success'>Work entry and history record added successfully!</div>";
                } else {
                    echo "Error inserting into WorkHistory: " . $conn->error;
                }
            } else {
                echo "Error: " . $conn->error;
            }
        }
    } else {
        echo "<script>alert('Invalid Quote ID. Could not retrieve User ID.');</script>";
    }
}

// Handle Work update submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_work'])) {
    $workid = mysqli_real_escape_string($conn, $_POST['workid']);
    $admin_status = mysqli_real_escape_string($conn, $_POST['admin_status']);
    $admin_note = mysqli_real_escape_string($conn, $_POST['admin_note']);

    $updateWorkQuery = "UPDATE Work SET admin_status = '$admin_status', admin_note = '$admin_note' WHERE workid = '$workid'";
    if ($conn->query($updateWorkQuery) === TRUE) {
        $selectUpdatedWorkQuery = "SELECT * FROM Work WHERE workid = '$workid'";
        $updatedWorkResult = $conn->query($selectUpdatedWorkQuery);

        if ($updatedWorkResult && $updatedWorkResult->num_rows > 0) {
            $updatedWork = $updatedWorkResult->fetch_assoc();

            $insertUpdatedHistoryQuery = "INSERT INTO WorkHistory (workid, userid, quoteid, price, start_date, end_date, client_status, client_note, admin_status, admin_note) 
                                          VALUES ('{$updatedWork['workid']}', '{$updatedWork['userid']}', '{$updatedWork['quoteid']}', '{$updatedWork['price']}', '{$updatedWork['start_date']}', '{$updatedWork['end_date']}', '{$updatedWork['client_status']}', '{$updatedWork['client_note']}', '{$updatedWork['admin_status']}', '{$updatedWork['admin_note']}')";
            if ($conn->query($insertUpdatedHistoryQuery) === TRUE) {
                echo "<div class='alert alert-success'>Work updated and logged in history successfully for Work ID $workid.</div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to log Work update in history for Work ID $workid.</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Failed to update Work for Work ID $workid.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Management</title>
    <link rel="stylesheet" href="design.css">
    <style>
        #workForm {
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
        <button class="toggle-button btn btn-info" onclick="toggleForm()">Add New Work</button>

        <!-- Form for submitting new Work -->
        <form action="" method="post" id="workForm">
            <div class="form-container">
                <h3>Submit Work Form</h3>

                <!-- Quote ID Dropdown -->
                <div class="field input mb-2">
                    <label for="quoteid-input">Quote ID</label>
                    <select name="quoteid" id="quoteid-input" required>
                        <option value="" disabled selected>Select a Quote</option>
                        <?php
                        if ($quotesResult->num_rows > 0) {
                            while ($row = $quotesResult->fetch_assoc()) {
                                echo "<option value='" . $row['quoteid'] . "'>Quote ID: " . $row['quoteid'] . " | User ID: " . $row['userid'] . "</option>";
                            }
                        } else {
                            echo "<option value='' disabled>No available quotes</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Price Field -->
                <div class="field input mb-2">
                    <label for="price-input">Price</label>
                    <input type="number" step="0.01" name="price" id="price-input" required placeholder="00.00">
                </div>

                <!-- Time Frame Fields -->
                <div class="field input mb-2">
                    <label for="start-date-input">Time Frame</label>
                    <input type="date" name="start_date" id="start-date-input" required>
                    <span>to</span>
                    <input type="date" name="end_date" id="end-date-input" required>
                </div>

                <!-- Admin Status Field -->
                <div class="field input mb-2">
                    <label for="admin-status-input">My Status</label>
                    <select name="admin_status" id="admin-status-input" required>
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="refused">Refused</option>
                    </select>
                </div>

                <!-- Note Field -->
                <div class="field input mb-2">
                    <label for="note-input">Note</label>
                    <textarea name="admin_note" id="note-input" placeholder="Add note"></textarea>
                </div>

                <!-- Submit Button -->
                <input type="submit" name="submit" value="Submit" class="bg-info border-0 p-2 my-3">
            </div>
        </form>

        <!-- Table displaying Work records -->
        <h2>Work Table</h2>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Work ID</th>
                    <th>Quote ID</th>
                    <th>User ID</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Price</th>
                    <th>Client's Status</th>
                    <th>Client's Note</th>
                    <th>My Status</th>
                    <th>My Note</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $selectWork = "SELECT * FROM Work";
                $resultWork = $conn->query($selectWork);

                if ($resultWork && $resultWork->num_rows > 0) {
                    while ($row_work = $resultWork->fetch_assoc()) {
                        echo "
                        <tr>
                            <td>{$row_work['workid']}</td>
                            <td>{$row_work['quoteid']}</td>
                            <td>{$row_work['userid']}</td>
                            <td>{$row_work['start_date']}</td>
                            <td>{$row_work['end_date']}</td>
                            <td>{$row_work['price']}</td>
                            <td>{$row_work['client_status']}</td>
                            <td>{$row_work['client_note']}</td>
                            <td>
                                <form method='POST' action=''>
                                    <select name='admin_status' class='form-control'>
                                        <option value='pending' " . ($row_work['admin_status'] == 'pending' ? 'selected' : '') . ">Pending</option>
                                        <option value='accepted' " . ($row_work['admin_status'] == 'accepted' ? 'selected' : '') . ">Accepted</option>
                                        <option value='refused' " . ($row_work['admin_status'] == 'refused' ? 'selected' : '') . ">Refused</option>
                                    </select>
                            </td>
                            <td>
                                <input type='text' name='admin_note' value='{$row_work['admin_note']}' class='form-control'>
                                <input type='hidden' name='workid' value='{$row_work['workid']}'>
                            </td>
                            <td>
                                <button type='submit' name='update_work' class='btn btn-primary'>Update</button>
                                </form>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>No Work records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Table displaying Work records -->
    <div class="container">
        <div class="row">
            <h2>Work History Table</h2>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>History ID</th>
                        <th>Work ID</th>
                        <th>Quote ID</th>
                        <th>User ID</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Price</th>
                        <th>Client's Status</th>
                        <th>Client's Note</th>
                        <th>My Status</th>
                        <th>My Note</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $selectWork = "SELECT * FROM WorkHistory";
                    $resultWork = $conn->query($selectWork);

                    if ($resultWork && $resultWork->num_rows > 0) {
                        while ($row_work = $resultWork->fetch_assoc()) {
                            echo "
                            <tr>
                                <td>{$row_work['historyid']}</td>
                                <td>{$row_work['workid']}</td>
                                <td>{$row_work['quoteid']}</td>
                                <td>{$row_work['userid']}</td>
                                <td>{$row_work['start_date']}</td>
                                <td>{$row_work['end_date']}</td>
                                <td>{$row_work['price']}</td>
                                <td>{$row_work['client_status']}</td>
                                <td>{$row_work['client_note']}</td>
                                <td>{$row_work['admin_status']}</td>
                                <td>{$row_work['admin_note']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='11'>No Work records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleForm() {
            const form = document.getElementById('workForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
