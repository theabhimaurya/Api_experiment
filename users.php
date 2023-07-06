<?php
// check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // get the input parameters from the request body
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $userType = $_POST['userType'];

    // validate the input parameters
    if (empty($name) || empty($phone) || empty($email) || empty($password) || empty($userType)) {
        // return an error response if any parameter is missing
        http_response_code(400); // bad request
        echo json_encode(array("message" => "All parameters are required"));
        exit();
    }

    // connect to the database
    $db = new mysqli("localhost", "root", "", "test");
    if ($db->connect_error) {
        // return an error response if the database connection fails
        http_response_code(500); // internal server error
        echo json_encode(array("message" => "Database connection failed"));
        exit();
    }

    // prepare and bind a SQL statement to insert the user data
    $stmt = $db->prepare("INSERT INTO users (name, phone, email, password, userType) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssss", $name, $phone, $email, $password, $userType);
        // execute the statement and check if it succeeds
        if ($stmt->execute()) {
            // generate a random auth token for the user
            $authToken = bin2hex(random_bytes(16));
            // store the auth token in a table with the user id as a foreign key
            $userId = $stmt->insert_id; // get the last inserted id
            $stmt2 = $db->prepare("INSERT INTO auth_tokens (user_id, token) VALUES (?, ?)");
            if ($stmt2) {
                $stmt2->bind_param("is", $userId, $authToken);
                // execute the statement and check if it succeeds
                if ($stmt2->execute()) {
                    // return a success response with the auth token
                    http_response_code(201); // created
                    // echo json_encode(array("message" => "User created successfully", "authToken" => $authToken));
                    echo json_encode(array(
                        "message" => "User created successfully",
                        "status" => "success",
                        "data" => array(
                        "id" => $userId,
                        "name" => $name,
                        "email" => $email,
                        "user_type" => $userType,
                        "mobile" => $phone,
                        "authToken" => $authToken
                        )
                        ));

                } else {
                    // return an error response if the second statement fails
                    http_response_code(500); // internal server error
                    echo json_encode(array("message" => "Failed to generate auth token"));
                }
                // close the second statement
                $stmt2->close();
            } else {
                // return an error response if the second statement cannot be prepared
                http_response_code(500); // internal server error
                echo json_encode(array("message" => "Failed to prepare statement"));
            }
        } else {
            // return an error response if the first statement fails
            http_response_code(500); // internal server error
            echo json_encode(array("message" => "Failed to insert user data"));
        }
        // close the first statement
        $stmt->close();
    } else {
        // return an error response if the first statement cannot be prepared
        http_response_code(500); // internal server error
        echo json_encode(array("message" => "Failed to prepare statement"));
    }

    // close the database connection
    $db->close();
} else {
    // return an error response if the request method is not POST
    http_response_code(405); // method not allowed
    echo json_encode(array("message" => "Only POST method is allowed"));
}
