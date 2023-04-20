<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "test";
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$value1 = "John";
$value2 = "Doe";
$value3 = "john@example.com";

$sql = "INSERT INTO personal_data (name, mobile, email) VALUES ('$value1', '$value2', '$value3')";

if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

//http://localhost/project/cites.php?

mysqli_close($conn);
?>