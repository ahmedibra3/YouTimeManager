<?php
// API Key From Youtube data v3
$apiKey = 'AIzaSyAc1PJeAea52C7vPsYVGfOSspuydCAGkhw';

// To get playlist id fron url
$playlistUrl = readline('Enter the URL of playlist: ');
$playlistId = '';
$totalDurationSeconds = 0;
for($i=0;$i<strlen($playlistUrl);$i++){
    if($playlistUrl[$i]=='='){
        $playlistId = substr($playlistUrl,$i+1);
        break;
    }
    
}

// To Fetch From API And Get The Required Information :
function fetchFromApi($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // To check for a response
    if ($response === false) {
        die('Error: Failed to fetch data from API.');
    }

    $decodedResponse = json_decode($response, true);

    // To check for a decodedResponse
    if ($decodedResponse === null) {
        die('Error: Failed to decode API response.');
    }

    return $decodedResponse;
}

// Function to convert video duration from ISO 8601 format to seconds
function convertIso8601ToSeconds($duration) {
    $interval = new DateInterval($duration);
    return ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
}

$totalDurationSeconds = 0;
$nextPageToken = '';
$totalVideos = 0;

do {
    // Get playlist videos
    $playlistItemsUrl = "https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails&maxResults=50&playlistId=$playlistId&key=$apiKey&pageToken=$nextPageToken";
    
    $playlistItemsResponse = fetchFromApi($playlistItemsUrl);

    // Check if 'items' are present in the response
    if (!isset($playlistItemsResponse['items'])) {
        die('Error: No videos found in playlist.');
    }

    // Collect video IDs
    $videoIds = [];
    foreach ($playlistItemsResponse['items'] as $item) {
        if (isset($item['contentDetails']['videoId'])) {
            $videoIds[] = $item['contentDetails']['videoId'];
        }
    }

    if (empty($videoIds)) {
        die('Error: No video IDs found.');
    }
    $totalVideos = count($videoIds);

    // Get video details based on their IDs
    $videoIdsString = implode(',', $videoIds);
    $videosUrl = "https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=$videoIdsString&key=$apiKey";
    $videosResponse = fetchFromApi($videosUrl);

    if (!isset($videosResponse['items'])) {
        die('Error: No video details found.');
    }

    // Calculate total duration
    foreach ($videosResponse['items'] as $video) {
        if (isset($video['contentDetails']['duration'])) {
            $duration = $video['contentDetails']['duration'];
            $totalDurationSeconds += convertIso8601ToSeconds($duration);
        }
    }

    // Check if another page exists
    $nextPageToken = isset($playlistItemsResponse['nextPageToken']) ? $playlistItemsResponse['nextPageToken'] : '';

} while ($nextPageToken != '');

// Convert total duration from seconds to hours and minutes
$hours = floor($totalDurationSeconds / 3600);
$minutes = floor(($totalDurationSeconds % 3600) / 60);
$seconds = $totalDurationSeconds % 60;

function calculateDailyVideos($dailyHours){
    $videoDurations = [];
    global $playlistId,$apiKey,$nextPageToken,$totalDurationSeconds;
    do {
       // Get playlist videos
        $playlistItemsUrl = "https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails&maxResults=50&playlistId=$playlistId&key=$apiKey&pageToken=$nextPageToken";
        $playlistItemsResponse = fetchFromApi($playlistItemsUrl);

        if (!isset($playlistItemsResponse['items'])) {
            die('Error: No videos found in playlist.');
        }

        $videoIds = [];
        foreach ($playlistItemsResponse['items'] as $item) {
            if (isset($item['contentDetails']['videoId'])) {
                $videoIds[] = $item['contentDetails']['videoId'];
            }
        }

        if (empty($videoIds)) {
            die('Error: No video IDs found.');
        }

        $videoIdsString = implode(',', $videoIds);
        $videosUrl = "https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=$videoIdsString&key=$apiKey";
        $videosResponse = fetchFromApi($videosUrl);

        if (!isset($videosResponse['items'])) {
            die('Error: No video details found.');
        }

        foreach ($videosResponse['items'] as $video) {
            if (isset($video['contentDetails']['duration'])) {
                $duration = $video['contentDetails']['duration'];
                $durationSeconds = convertIso8601ToSeconds($duration);
                $videoDurations[] = $durationSeconds; // Store video duration
                $totalDurationSeconds += $durationSeconds;
            }
        }

        $nextPageToken = isset($playlistItemsResponse['nextPageToken']) ? $playlistItemsResponse['nextPageToken'] : '';

    } while ($nextPageToken != '');

    $dailyVideos = 0;
    $dailyTimeUsed = 0;
    $dailySeconds = $dailyHours * 3600;

    foreach ($videoDurations as $duration) {
        if ($dailyTimeUsed + $duration > $dailySeconds) {
            break; // If the time exceeds the available time in the day, we stop.
        }
        $dailyTimeUsed += $duration;
        $dailyVideos++;
    }

    // Calculate the number of days required to complete the playlist
    $totalDays = ceil(count($videoDurations) / $dailyVideos);

    return array(
        'dailyVideos' => $dailyVideos,
        'totalDays' => $totalDays,    
    ); 
} 

echo"=========================================\n";
echo "Total playlist duration: {$hours}h {$minutes}m {$seconds}s\n";
$houres = (int) readline('Enter the number of hours: ');
$result = calculateDailyVideos($houres);
echo"=========================================\n";

echo "Number of videos per day: {$result['dailyVideos']} videos\n";
echo "This Playlist will be completed in {$result['totalDays']} days\n";
echo"=========================================\n";

// To Know The Date That The Playlist Will Completed...
function playlistFinish(){
    global $result;   
    $dateFinish = new DateTime();
    $dateFinish->modify('+' . $result['totalDays'] . ' days');
    echo "The Playlist Will Finish At {$dateFinish->format('d/m/Y')}\n";
}
playlistFinish();
?>
