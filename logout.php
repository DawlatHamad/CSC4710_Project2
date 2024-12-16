<?php

@include 'config.php';

session_start();

// Check if the session exists before unsetting/destroying it
if (isset($_SESSION)) {
    session_unset();
    session_destroy();
}

// Redirect to the login page
header('location:login.php');
exit();
?>
