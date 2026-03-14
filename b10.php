<?php
// إعدادات الرابط الأساسي (تأكد من صحة الرابط)
$remote_url = "http://sportfet.shop/AD1/tracks-v1a1/mono.m3u8";
$base_path = "http://sportfet.shop/AD1/tracks-v1a1/";

header("Access-Control-Allow-Origin: *");

// إذا كان الطلب لقطعة فيديو (.ts)
if (isset($_GET['ts'])) {
    $ts_url = $base_path . $_GET['ts'];
    header("Content-Type: video/mp2t");
    echo file_get_contents($ts_url);
    exit;
}

// إذا كان الطلب لملف البث الأساسي (.m3u8)
header("Content-Type: application/vnd.apple.mpegurl");
$m3u8_content = file_get_contents($remote_url);

// تعديل روابط قطع الفيديو داخل الملف لتعمل عبر هذا الملف نفسه
$m3u8_content = preg_replace('/([a-zA-Z0-9_\-]+\.ts)/', 'b10.php?ts=$1', $m3u8_content);

echo $m3u8_content;
?>
