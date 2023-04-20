<?php
// MySQL database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'profileimages';

// Connect to the database
$conn = new mysqli($host, $user, $pass, $dbname);

// Check for database connection errors
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Database connection error: ' . $conn->connect_error
    ));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['profile_image'])) {
        // Profile image file not provided
        http_response_code(400);
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Profile image file not provided.'
        ));
        exit();
    }
    
    // Retrieve the file name and temporary file path
    $fileName = $_FILES['profile_image']['name'];
    $tempFilePath = $_FILES['profile_image']['tmp_name'];
    
    // Create a unique file name to prevent overwriting existing files
    $uniqueFileName = uniqid() . '_' . $fileName;
    
    // Specify the upload directory and file path
    $uploadDir = './uploads/';
    $uploadFilePath = $uploadDir . $uniqueFileName;
    
    // Move the file from the temporary directory to the upload directory
    if (!move_uploaded_file($tempFilePath, $uploadFilePath)) {
        // Error moving file
        http_response_code(500);
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Error moving profile image file to upload directory.'
        ));
        exit();
    }
    
    // Insert the file path into the database
    $sql = "INSERT INTO profile_image (image) VALUES ('$uploadFilePath')";
    if ($conn->query($sql) !== TRUE) {
        // Error inserting into database
        http_response_code(500);
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Error inserting profile image file path into database: ' . $conn->error
        ));
        exit();
    }
    
    // Return the file path and ID as a response
    $filePath = 'http://' . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', $uploadFilePath);
    $id = $conn->insert_id;
    echo json_encode(array(
        'status' => 'success',
        'file_path' => $filePath,
        'id' => $id
    ));
} else {
    // HTTP method not allowed
    http_response_code(405);
    echo json_encode(array(
        'status' => 'error',
        'message' => 'HTTP method not allowed.'
    ));
}

// Close the database connection
$conn->close();

?>