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

// Check if form is submitted
if (isset($_POST['submit'])) {
    // Escape and validate user inputs
    $address = $_POST['address'];
    $length = $_POST['length'];
    $width = $_POST['width'];
    $price = $_POST['price'];
    $client_note = $_POST['client_note'];

    $photo1 = $_FILES['photo1']['name'];
    $photo2 = $_FILES['photo2']['name'];
    $photo3 = $_FILES['photo3']['name'];
    $photo4 = $_FILES['photo4']['name'];
    $photo5 = $_FILES['photo5']['name'];

    $temp1 = $_FILES['photo1']['tmp_name'];
    $temp2 = $_FILES['photo2']['tmp_name'];
    $temp3 = $_FILES['photo3']['tmp_name'];
    $temp4 = $_FILES['photo4']['tmp_name'];
    $temp5 = $_FILES['photo5']['tmp_name'];

    // Check if a similar quote already exists
    $selectQuery = "SELECT * FROM Quote WHERE userid = '$userid' AND address = '$address' AND price = '$price'";
    $result = $conn->query($selectQuery);

    if ($result->num_rows > 0) {
        echo "<div class='alert alert-warning'>Quote with similar details already exists!</div>";
    } else {
        // Move uploaded files to the target directory
        move_uploaded_file($temp1, "./user_images/$photo1");
        move_uploaded_file($temp2, "./user_images/$photo2");
        move_uploaded_file($temp3, "./user_images/$photo3");
        move_uploaded_file($temp4, "./user_images/$photo4");
        move_uploaded_file($temp5, "./user_images/$photo5");

        // Insert the new quote
        $insertQuery = "INSERT INTO Quote (userid, address, dimension_length, dimension_width, price, photo1, photo2, photo3, photo4, photo5, client_note) 
                        VALUES ('$userid', '$address', '$length', '$width', '$price', '$photo1', '$photo2', '$photo3', '$photo4', '$photo5', '$client_note')";
        if ($conn->query($insertQuery) === TRUE) {
            echo "<div class='alert alert-success'>Quote submitted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    }
}

// Fetch user's quotes
$selectQuote = "SELECT * FROM Quote WHERE userid = '$userid'";
$resultQuote = $conn->query($selectQuote);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotes</title>
    <link rel="stylesheet" href="design.css">
    <style>
        #quoteForm {
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
        <button class="toggle-button btn btn-info" onclick="toggleForm()">Add New Quote</button>

        <!-- HTML Form -->
        <form action="" method="post" enctype="multipart/form-data" id="quoteForm">
            <div class="form-container">
                <h3>Submit Quote Form</h3>

                <!-- Address Field -->
                <div class="field input mb-2">
                    <label for="address-input">Address</label>
                    <input type="text" name="address" id="address-input" required placeholder="123 Address">
                </div>

                <!-- Size Field -->
                <div class="field input mb-2">
                    <label for="length-input">Size</label>
                    <input type="number" name="length" id="length-input" required placeholder="Length">
                    <span>x</span>
                    <input type="number" name="width" id="width-input" required placeholder="Width">
                </div>

                <!-- Proposed Price Field -->
                <div class="field input mb-2">
                    <label for="price-input">Proposed Price</label>
                    <input type="number" step="0.01" name="price" id="price-input" required placeholder="00.00">
                </div>

                <!-- Photos -->
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                <div class="form-outline mb-4 w-50 m-auto">
                    <label for="photo<?= $i ?>-input" class="form-label">Photo <?= $i ?></label>
                    <input type="file" name="photo<?= $i ?>" id="photo<?= $i ?>-input" class="form-control" <?= $i === 1 ? 'required' : '' ?>>
                </div>
                <?php endfor; ?>

                <!-- Note Field -->
                <div class="field input mb-2">
                    <label for="note-input">Note</label>
                    <textarea name="client_note" id="note-input" placeholder="Add note"></textarea>
                </div>

                <!-- Submit Button -->
                <input type="submit" name="submit" value="Submit" class="bg-info border-0 p-2 my-3">
            </div>
        </form>

        <!-- HTML Table -->
        <h2>Quote Table</h2>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Quote ID</th>
                    <th>Address</th>
                    <th>Size</th>
                    <th>Proposed Price</th>
                    <th>Photos</th>
                    <th>My Note</th>
                    <th>Status</th>
                    <th>Contractor's Note</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultQuote && $resultQuote->num_rows > 0) {
                    while ($row_quote = $resultQuote->fetch_assoc()) {
                        echo "
                        <tr>
                            <td>{$row_quote['quoteid']}</td>
                            <td>{$row_quote['address']}</td>
                            <td>{$row_quote['dimension_length']} x {$row_quote['dimension_width']}</td>
                            <td>{$row_quote['price']}</td>
                            <td>
                                <img src='./user_images/{$row_quote['photo1']}' alt='Photo1' width='50'>
                                <img src='./user_images/{$row_quote['photo2']}' alt='Photo2' width='50'>
                                <img src='./user_images/{$row_quote['photo3']}' alt='Photo3' width='50'>
                                <img src='./user_images/{$row_quote['photo4']}' alt='Photo4' width='50'>
                                <img src='./user_images/{$row_quote['photo5']}' alt='Photo5' width='50'>
                            </td>
                            <td>{$row_quote['client_note']}</td>
                            <td>{$row_quote['status']}</td>
                            <td>{$row_quote['admin_note']}</td>
                        </tr>
                        ";
                    }
                } else {
                    echo "<tr><td colspan='8'>No quotes found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleForm() {
            const form = document.getElementById('quoteForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
