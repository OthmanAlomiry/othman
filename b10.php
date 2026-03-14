<?php
// play.php
$remote_url = "http://sportfet.shop/AD1/tracks-v1a1/mono.m3u8";

// إرسال الترويسات المناسبة لملفات m3u8
header('Content-Type: application/vnd.apple.mpegurl');

// جلب وعرض المحتوى
echo file_get_contents($remote_url);
?>
