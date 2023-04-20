<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $url = "https://restcountries.com/v2/all"; // URL for retrieving all countries
    $response = file_get_contents($url); // Send GET request and retrieve response
    $countries = json_decode($response, true); // Decode JSON response into associative array

    // Filter the data to only include the name and alpha-2 code of each country
    $filteredCountries = array();
    foreach ($countries as $country) {
        $filteredCountries[] = array(
            'name' => $country['name'],
            'code' => $country['alpha2Code']
        );
    }

    // Return the filtered country data as a JSON-encoded string
    header('Content-Type: application/json');
    echo json_encode($filteredCountries);
} else {
    // HTTP method not allowed
    http_response_code(405);
    echo json_encode(array(
        'status' => 'error',
        'message' => 'HTTP method not allowed.'
    ));
}
	
?>