<?php

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
};

// ---- YOUTUBE SECTION ---- //

function get_youtube_video_details($video_id, $api_key) {
    if (!empty($api_key)) {
        $url = "https://www.googleapis.com/youtube/v3/videos?id=$video_id&key=$api_key&part=snippet";
        $responsed = wp_remote_get($url);
        if (is_wp_error($responsed)) {
            return false;
        }
        $body = wp_remote_retrieve_body($responsed);
        $data = json_decode($body, true);

        if (isset($data['items'][0])) {
            // Extract and return relevant data
            return $data['items'][0];
        }

        return false;
    } 
    
    return false;
}

function get_dailymotion_video_details($url) {
  // Placeholder for Dailymotion API request and response handling.
  return null;
}

function get_vimeo_video_details($url, $apikey = null) {
  // Placeholder for Vimeo API request and response handling.
  return null;
}


/* EXAMPLE RES

{
  "items": [
    {
      "kind": "youtube#video",
      "etag": "slH6Ae79d0XeftmCsKTpFCSghI4",
      "id": "Ks-_Mh1QhMc",
      "snippet": {
        "publishedAt": "2012-10-01T15:27:35Z", //UPLOADED AT :)
        "channelId": "UCAuUUnT6oDeKwE6v1NGQxug",
        "title": "Your body language may shape who you are | Amy Cuddy | TED",
        "description": "Body language affects how others see us, but it may also change how we see ourselves. Social psychologist Amy Cuddy argues that \"power posing\" -- standing in a posture of confidence, even when we don't feel confident -- can boost feelings of confidence, and might have an impact on our chances for success. (Note: Some of the findings presented in this talk have been referenced in an ongoing debate among social scientists about robustness and reproducibility. Read Amy Cuddy's response here: http://ideas.ted.com/inside-the-debate-about-power-posing-a-q-a-with-amy-cuddy/)\n\nGet TED Talks recommended just for you! Learn more at https://www.ted.com/signup.\n\nThe TED Talks channel features the best talks and performances from the TED Conference, where the world's leading thinkers and doers give the talk of their lives in 18 minutes (or less). Look for talks on Technology, Entertainment and Design -- plus science, business, global issues, the arts and more.\n\nFollow TED on Twitter: http://www.twitter.com/TEDTalks\nLike TED on Facebook: https://www.facebook.com/TED\n\nSubscribe to our channel: https://www.youtube.com/TED",
        "thumbnails": {
          "standard": {
            "url": "https://i.ytimg.com/vi/Ks-_Mh1QhMc/sddefault.jpg",
            "width": 640,
            "height": 480
          },
        }
    }
  ],
  "pageInfo": {
    "totalResults": 1,
    "resultsPerPage": 1
  }
}

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
  throw new Exception(sprintf('Please run "composer require google/apiclient:~2.0" in "%s"', __DIR__));
}
require_once __DIR__ . '/vendor/autoload.php';

$client = new Google_Client();
$client->setApplicationName('API code samples');
$client->setScopes([
    'https://www.googleapis.com/auth/youtube.readonly',
]);

// TODO: For this request to work, you must replace
//       "YOUR_CLIENT_SECRET_FILE.json" with a pointer to your
//       client_secret.json file. For more information, see
//       https://cloud.google.com/iam/docs/creating-managing-service-account-keys
$client->setAuthConfig('YOUR_CLIENT_SECRET_FILE.json');
$client->setAccessType('offline');

// Request authorization from the user.
$authUrl = $client->createAuthUrl();
printf("Open this link in your browser:\n%s\n", $authUrl);
print('Enter verification code: ');
$authCode = trim(fgets(STDIN));

// Exchange authorization code for an access token.
$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
$client->setAccessToken($accessToken);

// Define service object for making API requests.
$service = new Google_Service_YouTube($client);

$queryParams = [
    'id' => 'Ks-_Mh1QhMc'
];

$response = $service->videos->listVideos('snippet', $queryParams);
print_r($response);

*/

// ---- END OF YOUTUBE SECTION ---- //




// ---- VIMEO SECTION ---- // 

/* function get_vimeo_video_details($video_id, $access_token) {

    if (!empty($access_token)) {
        $url = "https://api.vimeo.com/videos/$video_id";

        $response = wp_remote_get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token
            ]
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    } 
    
    return false;
} */

/* EXAMPLE RES  

{
  "name": "Haha…",
  "description": null or "str"
  "release_time": "2021-12-24T03:32:06+00:00",
  "link": "https://vimeo.com/659834209",
  ""pictures": {
    "sizes": [ // we gonna need only the 3rd element query field is pictures.sizes
      {
        "width": 100,
        "height": 75,
        "link": "https://i.vimeocdn.com/video/1332830944-0a69e2184853b9adb07d02a592a56a1d31d19a7a0d6581ecbc39235a244f9780-d_100x75?r=pad",
        ...
      }
    ]
}

*/

// ---- END OF VIMEO SECTION ---- //




// ---- DAILYMOTION SECTION ---- // 

/* function get_dailymotion_video_details($video_id) {
    $fields_strs = '?fields=thumbnail_120_url,description,title,uploaded_time,embeded_url,';
    $url = "https://api.dailymotion.com/video/$video_id" . $fields_strs;

    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
} */

/* EXAMPLE RES  

{
  "thumbnail_120_url": "https://s1.dmcdn.net/v/VSAJ71bPcixHleF0M/x120",
  "description": "Say hello to the internet's big sis Tefi, who joins Cosmo to keep the good vibes going as she talks casual dating, green flags, and spooning. As a social media guru herself, Tefi knows a thing or two about a social deep dive, so her dating profile dissections are profound as she swipes right... and left.",
  "title": "Tefi Has Lots To Say About These REAL Dating Profiles and They're Accurate AF | Cosmopolitan",
  "uploaded_time": 1701201856,
  "embed_url": "https://www.dailymotion.com/embed/video/x8q1n0n"
}

*/

// ---- END OF DAILYMOTION SECTION ---- //
