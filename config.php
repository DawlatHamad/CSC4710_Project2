<?php

$conn = mysqli_connect('localhost', 'root', '', 'final');

if(!$conn) {
    die("Connection Failed:".mysqli_connect_error());
}

?>