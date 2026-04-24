<?php

$base = "http://135.125.109.73:9000/";
$stream = $base . "beinsport7_.m3u8";

header("Access-Control-Allow-Origin: *");

if(isset($_GET['ts'])){

    $ts = basename($_GET['ts']);
    $url = $base . $ts;

    header("Content-Type: video/mp2t");

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");

    $data = curl_exec($ch);
    curl_close($ch);

    echo $data;
    exit;

}

header("Content-Type: application/vnd.apple.mpegurl");

$ch = curl_init($stream);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");

$m3u8 = curl_exec($ch);
curl_close($ch);

$m3u8 = preg_replace('/(.*\.ts)/', 'b7.php?ts=$1', $m3u8);

echo $m3u8;

?>