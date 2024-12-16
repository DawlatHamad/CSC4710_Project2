<?php

@include 'config.php';

if(isset($_POST['submit'])){

   // For User Table
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = md5($_POST['password']);
   $cpass = md5($_POST['cpassword']);
   $first = mysqli_real_escape_string($conn, $_POST['firstname']);
   $last = mysqli_real_escape_string($conn, $_POST['lastname']);
   $address = mysqli_real_escape_string($conn, $_POST['address']);
   $phone = mysqli_real_escape_string($conn, $_POST['phone']);
   $role = mysqli_real_escape_string($conn, $_POST['role']);

   // For Card Table
   $nickname = mysqli_real_escape_string($conn, $_POST['nickname']);
   $number = mysqli_real_escape_string($conn, $_POST['number']);
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $month = mysqli_real_escape_string($conn, $_POST['month']);
   $year = mysqli_real_escape_string($conn, $_POST['year']);
   $cvv = mysqli_real_escape_string($conn, $_POST['cvv']);

   if ($pass !== $cpass) {
      echo "<div class='alert error-msg'>Password does not match!</div>";
   } else {
      $selectUser = "SELECT * FROM Users WHERE email = '$email'";
      $resultUser = mysqli_query($conn, $selectUser);

      if (mysqli_num_rows($resultUser) > 0) {
         echo "<div class='alert success-msg'>User already has an account</div>";
         header('location:login.php');
      } else {
         // Insert the user
         $insertUser = "INSERT INTO Users (email, password, firstname, lastname, address, phone, role) 
                        VALUES ('$email', '$pass', '$first', '$last', '$address', '$phone', '$role')";
         $executeUser = mysqli_query($conn, $insertUser);

         if ($executeUser) {
            // Get the userid of the newly inserted user
            $userid = mysqli_insert_id($conn);

            // Insert the card associated with the new userid
            $insertCard = "INSERT INTO Card (userid, nickname, number, name, month, year, cvv) 
                           VALUES ('$userid', '$nickname', '$number', '$name', '$month', '$year', '$cvv')";
            $executeCard = mysqli_query($conn, $insertCard);

            if ($executeCard) {
               echo "<div class='alert success-msg'>User and Card created successfully!</div>";
            } else {
               echo "<div class='alert error-msg'>Error adding card: " . mysqli_error($conn) . "</div>";
            }
         } else {
            echo "<div class='alert error-msg'>Error creating user: " . mysqli_error($conn) . "</div>";
         }
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   <link rel="stylesheet" href="design.css">
</head>
<body>

   <div class="form-container">
      <form action="" method="post">
         <h3>Register</h3>
         <div class="field input">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required placeholder="Enter your email">
         </div>
         <div class="field input">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required placeholder="Enter your password">
            <input type="password" name="cpassword" id="cpassword" required placeholder="Confirm your password">
         </div>
         <div class="field input">
            <label for="firstname">First Name</label>
            <input type="text" name="firstname" id="firstname" required placeholder="Enter your first name">
         </div>
         <div class="field input">
            <label for="lastname">Last Name</label>
            <input type="text" name="lastname" id="lastname" required placeholder="Enter your last name">
         </div>
         <div class="field input">
            <label for="address">Address</label>
            <input type="text" name="address" id="address" required placeholder="Enter your address">
         </div>
         <div class="field input">
            <label for="phone">Phone Number</label>
            <input type="tel" name="phone" id="phone" required placeholder="Enter your phone number">
         </div>
         <div>
            <label for="role">Role</label>
            <select name="role" id="role" required>
               <option value="client">Client</option>
            </select>
         </div>
        <div class="field input mb-2">
            <label for="nickname-input">Card Nickname</label>
            <input type="text" name="nickname" id="nickname-input" required placeholder="Nickname">
        </div>
        <div class="field input mb-2">
            <label for="number-input">Card Number</label>
            <input type="text" name="number" id="number-input" required maxlength="20" placeholder="1234 1234 1234 1234">
        </div>
        <div class="field input mb-2">
            <label for="name-input">Cardholder Name</label>
            <input type="text" name="name" id="name-input" required placeholder="First Last">
        </div>
        <div class="field input mb-2">
            <label for="month-input">Exp. Date</label>
            <input type="number" name="month" id="month-input" required placeholder="MM">
            <span>/</span>
            <input type="number" name="year" id="year-input" required placeholder="YY">
        </div>
        <div class="field input mb-2">
            <label for="cvv-input">CVV</label>
            <input type="text" name="cvv" id="cvv-input" required maxlength="3" placeholder="000">
        </div>
         <input type="submit" name="submit" value="Register" class="form-btn">
         <p>Already have an account? <a href="login.php">Login</a></p>
      </form>
   </div>

</body>
</html>
