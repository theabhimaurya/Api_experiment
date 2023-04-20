<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $url = "https://cdn-api.co-vin.in/api/v2/admin/location/states"; // URL for retrieving Indian states data
    $response = file_get_contents($url); // Send GET request and retrieve response
    $states = json_decode($response, true); // Decode JSON response into associative array

    // Filter the data to only include the name and code of each state
    $filteredStates = array();
    foreach ($states['states'] as $state) {
        $filteredStates[] = array(
            'name' => $state['state_name'],
            'code' => $state['state_id']
        );
    }

    // Return the filtered state data as a JSON-encoded string
    header('Content-Type: application/json');
    echo json_encode($filteredStates);
} else {
    // HTTP method not allowed
    http_response_code(405);
    echo json_encode(array(
        'status' => 'error',
        'message' => 'HTTP method not allowed.'
    ));
}
	
?>