<?php
// Call set_include_path() as needed to point to your client library.
if (!file_exists($file = __DIR__ . '/vendor/autoload.php')) {
    throw new \Exception('please run "composer require google/apiclient:~2.0" in "' . __DIR__ .'"');
}
require_once __DIR__ . '/vendor/autoload.php';
session_start();

/*
 * This variable specifies the location of a file where the access and
 * refresh tokens will be written after successful authorization.
 * Please ensure that you have enabled the YouTube Data API for your project.
 */
define('CREDENTIALS_PATH', './php-yt-oauth2.json');

function createClient(){
    $client = new Google_Client();
    // Set to name/location of your client_secrets.json file.
    $client->setAuthConfigFile('cred.json');
    // Set to valid redirect URI for your project.
    $client->setRedirectUri('http://localhost:8080');

    $client->addScope(Google_Service_YouTube::YOUTUBE);
    $client->setAccessType('offline');

    return $client;
}

function getClient() {
    $client = createClient();

    // Load previously authorized credentials from a file.
    $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
    if (file_exists($credentialsPath)) {
        $accessToken = (array)json_decode(file_get_contents($credentialsPath));
    } else {
        // Request authorization from the user.
        $authUrl = $client->createAuthUrl();
        /* printf("Open the following link in your browser:\n%s\n", $authUrl); */
        /* print 'Enter verification code: '; */
        /* $authCode = trim(fgets(STDIN)); */
        $authCode = $_GET['code'];

        // Exchange authorization code for an access token.
        $accessToken = $client->authenticate($authCode);

        // Store the credentials to disk.
        if(!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }

/* var_dump(json_encode($accessToken));die; */

        file_put_contents($credentialsPath, json_encode($accessToken));
        /* var_dump($accessToken);die; */
        /* printf("Credentials saved to %s\n", $credentialsPath); */
    }
    /* var_dump($accessToken);die; */
    $client->setAccessToken($accessToken);

    // Refresh the token if it's expired.
    if ($client->isAccessTokenExpired()) {
        $client->refreshToken($client->getRefreshToken());
        file_put_contents($credentialsPath, $client->getAccessToken());
    }
    return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
    $homeDirectory = getenv('HOME');
    if (empty($homeDirectory)) {
        $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
    }
    return str_replace('~', realpath($homeDirectory), $path);
}

// Call channels.list to retrieve information
function channelsListByUsername($service, $part, $params) {
    $params = array_filter($params);
    $response = $service->channels->listChannels(
        $part,
        $params
    );

    $description = sprintf(
        'This channel\'s ID is %s. Its title is %s, and it has %s views.',
        $response['items'][0]['id'],
        $response['items'][0]['snippet']['title'],
        $response['items'][0]['statistics']['viewCount']);
    print $description . "\n";
}

// Define an object that will be used to make all API requests.
/* $client = getClient(); */
/* $service = new Google_Service_YouTube($client); */

/* if (isset($_GET['code'])) { */
/*     if (strval($_SESSION['state']) !== strval($_GET['state'])) { */
/*         die('The session state did not match.'); */
/*     } */

/*     $client->authenticate($_GET['code']); */
/*     $_SESSION['token'] = $client->getAccessToken(); */
/*     header('Location: ' . $redirect); */
/* } */

/* if (isset($_SESSION['token'])) { */
/*     $client->setAccessToken($_SESSION['token']); */
/* } */

/* if (!$client->getAccessToken()) { */
/*     print("no access token, whaawhaaa"); */
/*     exit; */
/* } */


/* channelsListByUsername($service, 'snippet,contentDetails,statistics', array('forUsername' => 'GoogleDevelopers')); */
echo '<pre>';
if ($_GET['code']) {
    $client = getClient();
    $service = new Google_Service_YouTube($client);
    $channel = $service->channels->listChannels(
        'id,contentDetails',
        array('forUsername' => 'felipeneto')
    );

    /* var_dump($channel[0]['contentDetails']['relatedPlaylists']['uploads']); */
    $id = ($channel[0]['contentDetails']['relatedPlaylists']['uploads']);

    /* $id = $channel[0][id]; */
    /* var_dump($id); */
    /* die; */

    $pageToken = null;
    $v = [];

    for($i = 0 ; $i < 3; $i++){

        $videos = $service->playlistItems->listPlaylistItems(
            'snippet,contentDetails',
            ['maxResults' => 5, 'playlistId' => $id, 'pageToken' => $pageToken ]
        );

        foreach($videos['items'] as $items){
            $v[] =  $items['contentDetails'];
        }

        $pageToken= $videos['nextPageToken'];
    }

    /* var_dump($v);die; */

    /* $videos = $service->playlistItems->listPlaylistItems( */
    /*    'snippet,contentDetails', */
    /*     ['maxResults' => 3, 'playlistId' => $id] */
    /* ); */

    /* foreach($videos['items'] as $items){ */
    /*     $v[] =  $items['contentDetails']; */
    /* } */

    foreach($v as $i){
        $service->videos->rate(
            $i['videoId'],
            'dislike',
            []
        );
    }
    /* var_dump($v); */
    /* var_dump($videos['nextPageToken']); */
    /* var_dump($videos['prevPageToken']); */
    /* var_dump($videos['pageInfo']['totalResults']); */

    die;
} else {
   $clientURL = createClient();
   $authUrl = $clientURL->createAuthUrl();
}
?>

<a href="<?php echo  $authUrl; ?>"> Login no APP </a>
