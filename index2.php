<?php
/* require_once 'vendor/autoload.php'; */
include_once __DIR__ . '/vendor/autoload.php';

$client = new Google_Client();
$client->setApplicationName("Client_Library_Examples");
$client->setDeveloperKey("AIzaSyCtFDNvYgfxGLE2ugVILC6LcN2N7paiDmU");

$youtube = new Google_Service_YouTube($client);


/* $searchResponse = $youtube->search->listSearch('id,snippet', array( */
/*        'q' => "Alessandro Santana Oficial", */
/*        'maxResults' => 10, */
/* )); */
/* echo "<pre>"; */
/* var_dump($searchResponse); */

/* $videos = ''; */
/* $channels = ''; */
/* $playlists = ''; */

/* foreach ($searchResponse['items'] as $searchResult) { */
/*     switch ($searchResult['id']['kind']) { */
/*     case 'youtube#video': */
/*         $videos .= sprintf('<li>%s (%s)</li>', */
/*             $searchResult['snippet']['title'], $searchResult['id']['videoId']); */
/*         break; */
/*     case 'youtube#channel': */
/*         $channels .= sprintf('<li>%s (%s)</li>', */
/*             $searchResult['snippet']['title'], $searchResult['id']['channelId']); */
/*         break; */
/*     case 'youtube#playlist': */
/*         $playlists .= sprintf('<li>%s (%s)</li>', */
/*             $searchResult['snippet']['title'], $searchResult['id']['playlistId']); */
/*         break; */
/*     } */
/* } */

/* die; */



$channel = $youtube->channels->listChannels(
    'id,contentDetails',
    array('id' => 'UC7_vgvSBx0Md1wyCGLrgLRA')
    /* array('forUsername' => 'felipeneto') */
);
echo '<pre>';
$id = ($channel[0]['contentDetails']['relatedPlaylists']['uploads']);
/* var_dump($id); */
/* die; */

$pageToken = null;
$v = [];

for($i = 0 ; $i < 1; $i++){

    $videos = $youtube->playlistItems->listPlaylistItems(
        'snippet,contentDetails',
        [
            'maxResults' => 5, 
            'playlistId' => $id, 
            'pageToken' => $pageToken 
        ]
    );

    foreach($videos['items'] as $items){
        $v[] =  $items['contentDetails'];
    }

    $pageToken= $videos['nextPageToken'];
}

var_dump($v);
die;
