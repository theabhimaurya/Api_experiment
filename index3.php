<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
header('Content-Type: application/json');
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "test";
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// $name = $_POST['name'];
// $email = $_POST['email'];
// $mobile = $_POST['mobile'];

$email= $_POST['email'];
$name= $_POST['name'];
$mobile= $_POST['mobile'];

// if (isset($_POST['email']) && isset($_POST['mobile'])) {
//     $email = $_POST['email'];
//     $mobile = $_POST['mobile'];

//     // Rest of your code
// } else {
//     // Handle missing parameters error
//     $response = array(
//         'status' => 'error',
//         'message' => 'Missing parameters: email or mobile.'
//     );
//     echo json_encode($response);
//     exit;
// }


$email_check_query = "SELECT * FROM personal_data WHERE email = '$email'";
$email_check_result = mysqli_query($conn, $email_check_query);

$token = bin2hex(random_bytes(50)); // generate unique token

if (mysqli_num_rows($email_check_result) > 0) {
    // Email already exists in the database
    $response = array(
        'status' => 'error',
        'message' => 'This email is already registered.'
    );
    echo json_encode($response);
    exit();
}


$insert_query = "INSERT INTO personal_data (name, email, mobile) VALUES ('$name', '$email', '$mobile')";


if (mysqli_query($conn, $insert_query)) {
    // Data inserted successfully
    $response = array(
        'status' => 'success',
        'message' => 'Data inserted successfully.',
        'token' => '$token'
    );
    echo json_encode($response);
} else {
    // Error inserting data
    $response = array(
        'status' => 'error',
        'message' => 'Error inserting data into the database.',
         'token' => '$token'
    );
    echo json_encode($response);
}
mysqli_close($conn);
}else {
    // HTTP method not allowed
    http_response_code(405);
    echo json_encode(array(
        'status' => 'error',
        'message' => 'HTTP method not allowed.'
    ));
}

?>