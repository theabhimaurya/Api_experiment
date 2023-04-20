<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['state_id'])) {
        // State ID parameter not provided
        http_response_code(400);
        echo json_encode(array(
            'status' => 'error',
            'message' => 'State ID parameter not provided.'
        ));
        exit();
    }
    
    $stateId = $_GET['state_id'];

    $url = "https://cdn-api.co-vin.in/api/v2/admin/location/districts/{$stateId}"; // URL for retrieving city data based on state ID
    $response = file_get_contents($url); // Send GET request and retrieve response
    $cities = json_decode($response, true); // Decode JSON response into associative array

    // Filter the data to only include the name and ID of each city
    $filteredCities = array();
    foreach ($cities['districts'] as $city) {
        $filteredCities[] = array(
            'name' => $city['district_name'],
            'id' => $city['district_id']
        );
    }

    // Return the filtered city data as a JSON-encoded string
    header('Content-Type: application/json');
    echo json_encode($filteredCities);
} else {
    // HTTP method not allowed
    http_response_code(405);
    echo json_encode(array(
        'status' => 'error',
        'message' => 'HTTP method not allowed.'
    ));
}
	
?>