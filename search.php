<?php
include_once __DIR__ . '/vendor/autoload.php';

$_POST = json_decode(file_get_contents('php://input'), true);

$q = $_POST['q'];
/* var_dump($q); */
/* die; */

if($_SERVER["REQUEST_METHOD"] == "OPTIONS")
{
    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT"); //Make sure you remove those you do not want to support

    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    //Just exit with 200 OK with the above headers for OPTIONS method
    exit(0);
}

$client = new Google_Client();
$client->setApplicationName("Client_Library_Examples");
$client->setDeveloperKey("AIzaSyCtFDNvYgfxGLE2ugVILC6LcN2N7paiDmU");

$youtube = new Google_Service_YouTube($client);

// Search Channel
$searchResponse = $youtube->search->listSearch(
    'id,snippet', 
    array(
       'type' => 'channel',
       'q' => $q,
       'maxResults' => 10,
    )
);

/* echo "<pre>"; */
/* var_dump($searchResponse); */
/* die; */

$channels = [];

foreach ($searchResponse['items'] as $searchResult) {
    $channels[] = [
        'name' => $searchResult['snippet']['title'], 
        'id' => $searchResult['id']['channelId']
    ];
}

/* echo "<pre>"; */
/* var_dump($channels); */


header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
echo json_encode($channels);
die;
