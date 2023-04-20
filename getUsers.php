<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "test";
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "SELECT * FROM personal_data";
    $result = mysqli_query($conn, $query);

    $users = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    mysqli_close($conn);

    echo json_encode($users);
} else {
    // HTTP method not allowed
    http_response_code(405);
    echo json_encode(array(
        'status' => 'error',
        'message' => 'HTTP method not allowed.'
    ));
}

?>