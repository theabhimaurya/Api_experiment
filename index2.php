<?php
header('Content-Type: application/json');
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "test";
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
header('Content-Type: application/json');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$email= $_REQUEST['email'];
$name= $_REQUEST['name'];
$mobile= $_REQUEST['mobile'];


$empQuery = "SELECT `id` FROM `personal_data` WHERE `email`='$email'";
$resultData = mysqli_query($conn, $empQuery);
$data = mysqli_fetch_array($resultData);
  

if ($empQuery != $email) {
    $response = array('status' => 'success', 'message' => 'your email allready inserted in data','profile_status'=>'1');
}else{
   $sql = "INSERT INTO `personal_data`(`name`,`mobile`,`email`) VALUES ('$name','$mobile','$email')";  
}

$sqli= (mysqli_query($conn, $sql)) ;

if($sqli){
  $response = array('status' => 'success', 'message' => 'Data updated successfully','profile_status'=>'1');
}   
else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

echo json_encode($response);
mysqli_close($conn);
?>