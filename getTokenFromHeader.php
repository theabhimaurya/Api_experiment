<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "test";
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetching the key from the header
$key = $_SERVER['HTTP_TOKEN'];

echo "New record created successfully with API key: " . $key;

mysqli_close($conn);
?>
