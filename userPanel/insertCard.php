<?php
include("../includes/connect.php");

// Ensure the user is logged in
session_start();
if (!isset($_SESSION['userid'])) {
    header('location:../login.php');
    exit();
}

$userid = $_SESSION['userid']; // Get the logged-in user's ID

// Handle form submission for inserting a new card
if (isset($_POST['submit'])) {
    $nickname = mysqli_real_escape_string($conn, $_POST['nickname']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $month = mysqli_real_escape_string($conn, $_POST['month']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $cvv = mysqli_real_escape_string($conn, $_POST['cvv']);

    // Check if the card already exists for the logged-in user
    $selectQuery = "SELECT * FROM Card WHERE userid = '$userid' AND number = '$number'";
    $result = $conn->query($selectQuery);

    if ($result && $result->num_rows > 0) {
        echo "<div class='alert alert-success'>Card already exists.</div>";
    } else {
        // Insert the new card for the logged-in user
        $insertQuery = "INSERT INTO Card (userid, nickname, number, name, month, year, cvv) 
                        VALUES ('$userid', '$nickname', '$number', '$name', '$month', '$year', '$cvv')";

        if ($conn->query($insertQuery) === TRUE) {
            echo "<div class='alert alert-success'>Card added successfully!</div>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}

// Handle form submission for updating an existing card
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_card'])) {
    $cardid = mysqli_real_escape_string($conn, $_POST['cardid']); 
    $nickname = mysqli_real_escape_string($conn, $_POST['nickname']);
    $month = mysqli_real_escape_string($conn, $_POST['month']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $cvv = mysqli_real_escape_string($conn, $_POST['cvv']);

    // Update the card details for the logged-in user
    $updateCardQuery = "UPDATE Card SET nickname = '$nickname', month = '$month', year = '$year', cvv = '$cvv' WHERE cardid = '$cardid' AND userid = '$userid'";
    if ($conn->query($updateCardQuery) === TRUE) {
        echo "<div class='alert alert-success'>Card updated successfully for Card ID $cardid.</div>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Fetch user's cards
$selectCard = "SELECT * FROM Card WHERE userid = '$userid'";
$resultCard = $conn->query($selectCard);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Cards</title>
    <link rel="stylesheet" href="design.css">
    <style>
        #cardForm {
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
        <button class="toggle-button btn btn-info" onclick="toggleForm()">Add New Card</button>

        <!-- Card Form -->
        <form method="post" action="" id="cardForm">
            <div class="form-container">
                <h3>Add Card</h3>
                <!-- Nickname Field -->
                <div class="field input mb-2">
                    <label for="nickname-input">Card Nickname</label>
                    <input type="text" name="nickname" id="nickname-input" required placeholder="Nickname">
                </div>

                <!-- Number Field -->
                <div class="field input mb-2">
                    <label for="number-input">Card Number</label>
                    <input type="text" name="number" id="number-input" required maxlength="20" placeholder="1234 1234 1234 1234">
                </div>

                <!-- Name Field -->
                <div class="field input mb-2">
                    <label for="name-input">Cardholder Name</label>
                    <input type="text" name="name" id="name-input" required placeholder="First Last">
                </div>

                <!-- Exp. Date Field -->
                <div class="field input mb-2">
                    <label for="month-input">Exp. Date</label>
                    <input type="number" name="month" id="month-input" required placeholder="MM">
                    <span>/</span>
                    <input type="number" name="year" id="year-input" required placeholder="YY">
                </div>

                <!-- CVV Field -->
                <div class="field input mb-2">
                    <label for="cvv-input">CVV</label>
                    <input type="text" name="cvv" id="cvv-input" required maxlength="3" placeholder="000">
                </div>

                <!-- Submit Button -->
                <input type="submit" name="submit" value="Submit" class="bg-info border-0 p-2 my-3">
            </div>
        </form>

        <!-- Order Table -->
        <h2>Card Table</h2>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Card Nickname</th>
                    <th>Card Number</th>
                    <th>Card Name</th>
                    <th>Exp. Date</th>
                    <th>CVV</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultCard && $resultCard->num_rows > 0) {
                    while ($row_card = $resultCard->fetch_assoc()) {
                        echo "
                        <form method='post' action=''>
                            <tr>
                                <td>
                                    <input type='text' name='nickname' value='{$row_card['nickname']}' class='form-control'>
                                    <input type='hidden' name='cardid' value='{$row_card['cardid']}'>
                                </td>
                                <td>{$row_card['number']}</td>
                                <td>{$row_card['name']}</td>
                                <td>
                                    <input type='number' name='month' value='{$row_card['month']}' class='form-control'> /
                                    <input type='number' name='year' value='{$row_card['year']}' class='form-control'>
                                </td>
                                <td>
                                    <input type='text' name='cvv' value='{$row_card['cvv']}' class='form-control'>
                                </td>
                                <td>
                                    <button type='submit' name='update_card' class='btn btn-primary'>Update</button>
                                </td>
                            </tr>
                        </form>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No cards saved for this user.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleForm() {
            const form = document.getElementById('cardForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
