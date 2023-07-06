<?php
// create a Api for user Login parms like phone , password with all checks like post or get method, prams empty error etc. and also update auth token in php

// check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // get the input parameters from the request body
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // validate the input parameters
    if (empty($phone) || empty($password)) {
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

    // prepare and bind a SQL statement to select the user data by phone and password
    $stmt = $db->prepare("SELECT * FROM users WHERE phone = ? AND password = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $phone, $password);
        // execute the statement and check if it succeeds
        if ($stmt->execute()) {
            // get the result set from the statement
            $result = $stmt->get_result();
            // check if the result set is not empty
            if ($result->num_rows > 0) {
                // fetch the user data as an associative array
                $user = $result->fetch_assoc();
                // extract the user id, name, email and user type from the array
                $userId = $user['id'];
                $name = $user['name'];
                $email = $user['email'];
                $userType = $user['userType'];
                // generate a random auth token for the user
                $authToken = bin2hex(random_bytes(16));
                // update the auth token in a table with the user id as a foreign key
                $stmt2 = $db->prepare("UPDATE auth_tokens SET token = ? WHERE user_id = ?");
                if ($stmt2) {
                    $stmt2->bind_param("si", $authToken, $userId);
                    // execute the statement and check if it succeeds
                    if ($stmt2->execute()) {
                        // return a success response with the user data and auth token
                        http_response_code(200); // ok
                        echo json_encode(array(
                            "message" => "User logged in successfully",
                            "status" => "success",
                            "data" => array(
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
                        echo json_encode(array("message" => "Failed to update auth token"));
                    }
                    // close the second statement
                    $stmt2->close();
                } else {
                    // return an error response if the second statement cannot be prepared
                    http_response_code(500); // internal server error
                    echo json_encode(array("message" => "Failed to prepare statement"));
                }
            } else {
                // return an error response if the result set is empty (no matching user found)
                http_response_code(404); // not found
                echo json_encode(array("message" => "Invalid phone or password"));
            }
            // free the result set
            $result->free();
        } else {
            // return an error response if the first statement fails
            http_response_code(500); // internal server error
            echo json_encode(array("message" => "Failed to select user data"));
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
