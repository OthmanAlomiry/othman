<?php

$stream = "http://135.125.109.73:9000/beinsport5_.m3u8";

header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $stream);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");

$data = curl_exec($ch);
curl_close($ch);

echo $data;

?>