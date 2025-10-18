<?php
// Call Express REST API from PHP

// GET Request
function getFromExpressAPI($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// POST Request
function postToExpressAPI($url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// Example usage
$expressApiUrl = 'http://localhost:5000/students'; // Replace with your Express API URL

// GET Request
echo "<h2>GET Request to Express API</h2>";
$getResult = getFromExpressAPI($expressApiUrl);
echo "<pre>";
print_r($getResult);
echo "</pre>";

// POST Request
echo "<h2>POST Request to Express API</h2>";
$postData = [
    'name' => 'Muhammad Shakil Patel',
    'email' => 'shakil@example.com',
    'age' => 25,
    'course' => 'Computer Science'
];
$postResult = postToExpressAPI($expressApiUrl, $postData);
echo "<pre>";
print_r($postResult);
echo "</pre>";
?>
