<?php
include("../includes/connect.php");
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['userid'])) {
    header('location:../login.php');
    exit();
}

$userid = $_SESSION['userid']; // Get the logged-in user's ID

// Handle Work update submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_work'])) {
    $workid = mysqli_real_escape_string($conn, $_POST['workid']);
    $client_status = mysqli_real_escape_string($conn, $_POST['client_status']);
    $client_note = mysqli_real_escape_string($conn, $_POST['client_note']);

    $updateWorkQuery = "UPDATE Work SET client_status = '$client_status', client_note = '$client_note' WHERE workid = '$workid' AND userid = '$userid'";
    if ($conn->query($updateWorkQuery) === TRUE) {
        echo "<div class='alert alert-success'>Work updated successfully for Work ID $workid.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to update Work for Work ID $workid.</div>";
    }
}

// Query Work data for the logged-in user
$selectWork = "SELECT * FROM Work WHERE userid = '$userid'";
$resultWork = $conn->query($selectWork);
?>

<!-- Table displaying Work records -->
<div class="container">
    <div class="row">
        <h2>Work Table</h2>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Work ID</th>
                    <th>Quote ID</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Price</th>
                    <th>My Status</th>
                    <th>My Note</th>
                    <th>Contractor's Status</th>
                    <th>Contractor's Note</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultWork && $resultWork->num_rows > 0) {
                    while ($row_work = $resultWork->fetch_assoc()) {
                        echo "
                        <tr>
                            <td>{$row_work['workid']}</td>
                            <td>{$row_work['quoteid']}</td>
                            <td>{$row_work['start_date']}</td>
                            <td>{$row_work['end_date']}</td>
                            <td>{$row_work['price']}</td>
                            <td>
                                <form method='POST' action=''>
                                    <select name='client_status' class='form-control'>
                                        <option value='pending' " . ($row_work['client_status'] == 'pending' ? 'selected' : '') . ">Pending</option>
                                        <option value='accepted' " . ($row_work['client_status'] == 'accepted' ? 'selected' : '') . ">Accepted</option>
                                        <option value='refused' " . ($row_work['client_status'] == 'refused' ? 'selected' : '') . ">Refused</option>
                                    </select>
                            </td>
                            <td>
                                <input type='text' name='client_note' value='{$row_work['client_note']}' class='form-control'>
                                <input type='hidden' name='workid' value='{$row_work['workid']}'>
                            </td>                            
                            <td>{$row_work['admin_status']}</td>
                            <td>{$row_work['admin_note']}</td>
                            <td>
                                <button type='submit' name='update_work' class='btn btn-primary'>Update</button>
                                </form>
                            </td>
                        </tr>";
                    }
                } 
                else {
                    echo "<tr><td colspan='10'>No Work records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
