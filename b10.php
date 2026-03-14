<?php
// إعدادات البروكسي في b10.php
$remote_url = "http://sportfet.shop/AD1/tracks-v1a1/mono.m3u8";
$base_path = "http://sportfet.shop/AD1/tracks-v1a1/";

if (isset($_GET['ts'])) {
    header("Content-Type: video/mp2t");
    echo file_get_contents($base_path . $_GET['ts']);
    exit;
}

if (isset($_GET['get_stream'])) {
    header("Content-Type: application/vnd.apple.mpegurl");
    $m3u8 = file_get_contents($remote_url);
    // تحويل الروابط لتعمل عبر هذا الملف
    echo preg_replace('/([a-zA-Z0-9_\-]+\.ts)/', 'b10.php?ts=$1', $m3u8);
    exit;
}
?>
