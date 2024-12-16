<?php
include("../includes/connect.php");
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: ../login.php");
    exit();
}

// Get the logged-in user's ID
$loggedInUserId = $_SESSION['userid'];
$loggedInUserName = $_SESSION['firstname'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container-fluid p-0">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-info">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">DrivewayDASH</a>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="">Welcome <?php echo htmlspecialchars($loggedInUserName); ?></a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Dashboard Heading -->
        <div class="bg-light">
            <h3 class="text-center p-2">User Dashboard</h3>
        </div>

        <!-- Navigation Buttons --> 
        <div class="row">
            <div class="col-md-12 bg-secondary p-1 text-center">
                <div class="d-inline-block">
                    <a href="user.php" class="btn btn-info my-1">Home</a>
                    <a href="user.php?insertQuote" class="btn btn-info my-1">See Quotes</a>
                    <a href="user.php?viewWork" class="btn btn-info my-1">Work Forms</a>
                    <a href="user.php?viewOrders" class="btn btn-info my-1">Order Forms</a>
                    <a href="user.php?viewBill" class="btn btn-info my-1">Bills</a>
                    <a href="user.php?insertCard" class="btn btn-info my-1">Card</a>
                    <a href="../logout.php" class="btn btn-info my-1">Logout</a>
                </div>
            </div>
        </div>

        <!-- Boxes Section -->
        <div class="container my-5">
            <?php 
            if(isset($_GET['viewWork'])) {
                include('viewWork.php');
            } 
            else if(isset($_GET['insertQuote'])) {
                include('insertQuote.php');
            } 
            else if(isset($_GET['viewBill'])) {
                include('viewBill.php');
            } 
            else if(isset($_GET['insertCard'])) {
                include('insertCard.php');
            }
            else if(isset($_GET['viewOrders'])) {
                include('viewOrders.php');
            } 
            else {
                // Default content when no specific page is loaded
                echo "<h3 class='text-center'>Welcome to DASH</h3>";
                ?>
                <div class="d-flex justify-content-center">
                    <div class="row row-cols-1 row-cols-md-5 g-4 text-center">
                        <div class="col">
                            <div class="card border border-dark" style="height: 250px;">
                                <i class="fa-solid fa-file mb-3" style="font-size: 70px;"></i>
                                <div class="card-body">
                                    <h5 class="card-title">Total Quotes</h5>
                                    <p class="card-text">
                                        <?php
                                        $sql="SELECT * from Quote where userid = '$loggedInUserId'";
                                        $result=$conn-> query($sql);
                                        $count=0;
                                        if ($result-> num_rows > 0){
                                            while ($row=$result-> fetch_assoc()) {
                                                $count++;
                                            }
                                        }
                                        echo $count;
                                        ?>
                                    </p>
                                    <a href="user.php?insertQuote" class="btn btn-info">See Quotes</a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card border border-dark" style="height: 250px;">
                                <i class="fa-solid fa-briefcase mb-3" style="font-size: 70px;"></i>
                                <div class="card-body">
                                    <h5 class="card-title">Total Work</h5>
                                    <p class="card-text">
                                        <?php
                                        $sql="SELECT * from Work where userid = '$loggedInUserId'";
                                        $result=$conn-> query($sql);
                                        $count=0;
                                        if ($result-> num_rows > 0){
                                            while ($row=$result-> fetch_assoc()) {
                                                $count++;
                                            }
                                        }
                                        echo $count;
                                        ?>
                                    </p>
                                    <a href="user.php?viewWork" class="btn btn-info">See Work Forms</a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card border border-dark" style="height: 250px;">
                                <i class="fa-solid fa-pen-to-square mb-3" style="font-size: 70px;"></i>
                                <div class="card-body">
                                    <h5 class="card-title">Total Orders</h5>
                                    <p class="card-text">
                                        <?php
                                        $sql="SELECT * from Transactions where userid = '$loggedInUserId'";
                                        $result=$conn-> query($sql);
                                        $count=0;
                                        if ($result-> num_rows > 0){
                                            while ($row=$result-> fetch_assoc()) {
                                                $count++;
                                            }
                                        }
                                        echo $count;
                                        ?>
                                    </p>
                                    <a href="user.php?viewOrders" class="btn btn-info">See Orders</a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card border border-dark" style="height: 250px;">
                                <i class="fa-solid fa-file-invoice-dollar mb-3" style="font-size: 70px;"></i>
                                <div class="card-body">
                                    <h5 class="card-title">Total Bills</h5>
                                    <p class="card-text">
                                        <?php
                                        $sql="SELECT * from Transactions WHERE pay_status = 'paid' AND userid = '$loggedInUserId'";
                                        $result=$conn-> query($sql);
                                        $count=0;
                                        if ($result-> num_rows > 0){
                                            while ($row=$result-> fetch_assoc()) {
                                                $count++;
                                            }
                                        }
                                        echo $count;
                                        ?>
                                    </p>
                                    <a href="user.php?viewBill" class="btn btn-info">See Bills</a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card border border-dark" style="height: 250px;">
                                <i class="fa-solid fa-credit-card mb-3" style="font-size: 70px;"></i>
                                <div class="card-body">
                                    <h5 class="card-title">See Cards</h5>
                                    <p class="card-text">
                                        <?php
                                        $sql="SELECT * from Card where userid = '$loggedInUserId'";
                                        $result=$conn-> query($sql);
                                        $count=0;
                                        if ($result-> num_rows > 0){
                                            while ($row=$result-> fetch_assoc()) {
                                                $count++;
                                            }
                                        }
                                        echo $count;
                                        ?>
                                    </p>
                                    <a href="user.php?insertCard" class="btn btn-info">See Cards</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>

        <!-- Footer -->
        <div class="bg-info p-3 text-center footer">
            <p>All rights reserved to Sumaiya Ahmed and Dawlat Hamd</p>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
