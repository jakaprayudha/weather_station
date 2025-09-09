<?php
$hostname = "localhost"; //127.0.0.1
$username = "root";
$dbname = "db_weather";
$password = "";
$connect = mysqli_connect("$hostname", "$username", "$password", "$dbname");
// if (!$connect) {
//    echo "Connection Failed: " . mysqli_connect_error();
// } else {
//    echo "Connected Successfully to the database";
// }
