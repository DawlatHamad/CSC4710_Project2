<?php
include("../includes/connect.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- bootstrap css link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- font awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- css file -->
    <link rel="stylesheet" href="../style.css">

</head>
<body>
    <div class="container-fluid p-0"> <!-- padding = 0 -->
        <!-- Navbar-->
        <nav class="navbar navbar-expand-lg navbar-light bg-info"> <!-- bg-info makes it blue -->
            <div class="container-fluid">
                <a class="navbar-brand" href="#">DrivewayDASH</a>
                <nav class="navbar navbar-expand-lg">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="">Welcome</a> 
                        </li>
                    </ul>
                </nav>
            </div>
        </nav>

        <!-- Dashboard Heading -->
        <div class="bg-light">
            <h3 class="text-center p-2">Admin Dashboard</h3>
        </div>

        <!-- Navigation Buttons -->  
        <div class="row">
            <div class="col-md-12 bg-secondary p-1 text-center">
                <div class="d-inline-block">
                    <a href="admin.php" class="btn btn-info my-1">Home</a>
                    <a href="admin.php?viewClients" class="btn btn-info my-1">Client List</a>
                    <a href="admin.php?viewQuotes" class="btn btn-info my-1">Quotes List</a>
                    <a href="admin.php?insertWork" class="btn btn-info my-1">Work Forms</a>
                    <a href="admin.php?insertOrder" class="btn btn-info my-1">Order Forms</a>
                    <a href="admin.php?viewBill" class="btn btn-info my-1">Bills</a>
                    <a href="../logout.php" class="btn btn-info my-1">Logout</a>
                </div>
            </div>
        </div>

        <!-- Boxes Section -->
        <div class="container my-5">
            <?php 
            if(isset($_GET['insertWork'])) {
                include('insertWork.php');
            }
            else if(isset($_GET['insertOrder'])) {
                include('insertOrder.php');
            }
            else if(isset($_GET['viewBill'])) {
                include('viewBill.php');
            }
            else if(isset($_GET['viewClients'])) {
                include('viewClients.php');
            }
            else if(isset($_GET['viewQuotes'])) {
                include('viewQuotes.php');
            } 
            else {
                echo "<h3 class='text-center'>Welcome to DASH</h3>";
                ?>
                <div class="d-flex justify-content-center">
                    <div class="row row-cols-1 row-cols-md-5 g-4 text-center">
                        <div class="col">
                            <div class="card border border-dark" style="height: 250px;">
                                <i class="fa-solid fa-users mb-3" style="font-size: 70px;"></i>
                                <div class="card-body">
                                    <h5 class="card-title">Total Clients</h5>
                                    <p class="card-text">
                                        <?php
                                        $sql="SELECT * from Users where role= 'client'";
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
                                    <a href="admin.php?viewClients" class="btn btn-info">See Clients</a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card border border-dark" style="height: 250px;">
                                <i class="fa-solid fa-file mb-3" style="font-size: 70px;"></i>
                                <div class="card-body">
                                    <h5 class="card-title">Total Quotes</h5>
                                    <p class="card-text">
                                        <?php
                                        $sql="SELECT * from Quote";
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
                                    <a href="admin.php?viewQuotes" class="btn btn-info">See Quotes</a>
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
                                        $sql="SELECT * from Work";
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
                                    <a href="admin.php?insertWork" class="btn btn-info">See Work Forms</a>
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
                                        $sql="SELECT * from Transactions";
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
                                    <a href="admin.php?insertOrder" class="btn btn-info">See Orders</a>
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
                                        $sql="SELECT * from Transactions WHERE pay_status = 'paid'";
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
                                    <a href="admin.php?insertBill" class="btn btn-info">See Bills</a>
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

    <!-- bootstrap js link -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>