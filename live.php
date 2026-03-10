<?php

$base = "http://135.125.109.73:9000/";
$source = "beinsport4_.m3u8";

if (isset($_GET['file'])) {
    $url = $base . $_GET['file'];
} else {
    $url = $base . $source;
}

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => [
        "User-Agent: Mozilla/5.0",
        "Referer: $base",
        "Origin: $base"
    ]
]);

$data = curl_exec($ch);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

if (!$data) {
    die("Stream Error");
}

if (strpos($contentType, "mpegurl") !== false) {

    header("Content-Type: application/vnd.apple.mpegurl");

    $data = preg_replace_callback('/(.*?\.ts)/', function($matches) {
        return "live.php?file=" . trim($matches[1]);
    }, $data);

    $data = preg_replace_callback('/(.*?\.m3u8)/', function($matches) {
        return "live.php?file=" . trim($matches[1]);
    }, $data);

    echo $data;

} else {

    header("Content-Type: $contentType");
    echo $data;

}

?>