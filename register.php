<?php

// Define your API endpoint
$apiEndpoint = '/register';

// Check if the request is for the registration endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === $apiEndpoint) {
    // Get the request body
    $requestData = json_decode(file_get_contents('php://input'), true);

    // Check if required parameters are present
    if (empty($requestData['name']) || empty($requestData['phone']) || empty($requestData['email']) || empty($requestData['password']) || empty($requestData['userType'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing parameters']);
        exit();
    }

    // Perform additional parameter checks if needed

    // All checks passed, insert the user registration data into your database (replace this with your actual database insertion logic)
    $userId = insertUserData($requestData);

    // Generate an auth token (replace this with your actual token generation logic)
    $authToken = generateAuthToken($userId);

    // Return the auth token in the response
    echo json_encode(['authToken' => $authToken]);
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === $apiEndpoint) {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found']);
    exit();
}

// Function to insert user registration data into the database (replace this with your actual database insertion logic)
function insertUserData($data)
{
    // Implement your database insertion logic here and return the newly created user ID
    // Example code using PDO:
    $pdo = new PDO('mysql:host=localhost;dbname=test', 'root', '');

    $stmt = $pdo->prepare('INSERT INTO users (name, phone, email, password, userType) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$data['name'], $data['phone'], $data['email'], $data['password'], $data['userType']]);

    return $pdo->lastInsertId();
}

// Function to generate an auth token (replace this with your actual token generation logic)
function generateAuthToken($userId)
{
    $token = bin2hex(random_bytes(16));

    // Implement your token storage logic here (e.g., storing the token in the database alongside the user ID)
    // Example code using PDO:
    $pdo = new PDO('mysql:host=localhost;dbname=test', 'root', '');

    $stmt = $pdo->prepare('UPDATE users SET auth_token = ? WHERE id = ?');
    $stmt->execute([$token, $userId]);

    return $token;
}


?>
