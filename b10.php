<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/vnd.apple.mpegurl");

// الرابط الجديد الذي قدمته لـ STARZPLAY 1
$remote_url = "http://sportfet.shop/AD2/tracks-v1a1/mono.m3u8";
$base_path  = "http://sportfet.shop/AD2/tracks-v1a1/";

// معالجة قطع الفيديو
if (isset($_GET['ts'])) {
    header("Content-Type: video/mp2t");
    echo file_get_contents($base_path . $_GET['ts']);
    exit;
}

// جلب وتعديل ملف m3u8
$m3u8_content = file_get_contents($remote_url);
$my_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$m3u8_content = preg_replace('/([a-zA-Z0-9_\-]+\.ts)/', $my_url . '?ts=$1', $m3u8_content);

echo $m3u8_content;
