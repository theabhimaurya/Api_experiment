<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $userId = $_GET['user_id'];

    if (empty($userId)) {
        http_response_code(400);
        echo json_encode(array("message" => "User id is required"));
        exit();
    }

    $authToken = $_SERVER['HTTP_TOKEN'];

    if (empty($authToken)) {
        http_response_code(401);
        echo json_encode(array("message" => "Auth token is required"));
        exit();
    }

    $db = new mysqli("localhost", "root", "", "test");

    if ($db->connect_error) {
        http_response_code(500);
        echo json_encode(array("message" => "Database connection failed"));
        exit();
    }

    $stmt = $db->prepare("SELECT u.*, t.token FROM users u JOIN auth_tokens t ON u.id = t.user_id WHERE u.id = ? AND t.token = ?");

    if ($stmt) {
        $stmt->bind_param("is", $userId, $authToken);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                http_response_code(200);
                echo json_encode(array(
                    "message" => "User fetched successfully",
                    "status" => "success",
                    "data" => array($user)
                ));
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Invalid user id or auth token"));
            }

            $result->free();
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Failed to select user data"));
        }

        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Failed to prepare statement"));
    }

    $db->close();
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Only GET method is allowed"));
}
