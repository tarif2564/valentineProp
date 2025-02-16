<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["response"])) {
    $userIP = $_SERVER["REMOTE_ADDR"];  // Get User's IP Address
    $response = $_POST["response"];
    
    // Get Geolocation Data (Latitude, Longitude)
    $geoData = getGeolocationData($userIP);

    // Get the MAC Address (Note: this is not usually accessible via PHP on most networks)
    // But if you have some way of capturing it on the client-side, you can include it.
    $macAddress = isset($_POST['mac_address']) ? $_POST['mac_address'] : 'N/A';  // Assuming you send MAC from client

    // Construct data string
    $data = "Response: $response | IP: $userIP | Latitude: {$geoData['latitude']} | Longitude: {$geoData['longitude']} | MAC: $macAddress | Date: " . date("Y-m-d H:i:s") . "\n";

    // Save to responses.txt
    file_put_contents("responses.txt", $data, FILE_APPEND | LOCK_EX);

    // Redirect to lastpage.html
    header("Location: lastpage.html");
    exit();
}

// Function to get geolocation data (latitude, longitude) based on IP address
function getGeolocationData($ip) {
    $access_key = '57c7bc55c5e61f'; // Replace with your actual API token

    // Send a request to ipinfo.io API
    $url = "http://ipinfo.io/{$ip}/json?token={$access_key}";

    // Make a cURL request to fetch the data
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the response
    $data = json_decode($response, true);

    // Return latitude and longitude
    $location = isset($data['loc']) ? explode(',', $data['loc']) : [0, 0];
    return [
        'latitude' => $location[0],
        'longitude' => $location[1],
    ];
}
?>
