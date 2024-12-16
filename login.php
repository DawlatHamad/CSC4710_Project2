<?php
include 'config.php';
session_start();

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = md5($_POST['password']);

    $sql = "SELECT * FROM Users WHERE email='$email' AND password='$pass'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $_SESSION['userid'] = $row['userid'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['firstname'] = $row['firstname'];
        $_SESSION['role'] = $row['role'];

        if ($row['role'] == 'contractor') {
            header('location:adminPanel/admin.php');
            exit();
        } elseif ($row['role'] == 'client') {
            header('location:userPanel/user.php');
            exit();
        } else {
            echo "<div class='alert error-msg'>Invalid user role.</div>";
        }
    } else {
        echo "<div class='alert error-msg'>Incorrect Email or Password</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>

    <div class="form-container">
        <form action="" method="post">
            <h3>Login</h3>
            <div class="field input">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required placeholder="Enter your email">
            </div>
            <div class="field input">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required placeholder="Enter your password">
            </div>
            <input type="submit" name="submit" value="Login" class="form-btn">
            <p>Need an Account? <a href="register.php">Register</a></p>
        </form>
    </div>

</body>
</html>
