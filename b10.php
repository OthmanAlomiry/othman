<?php
// السماح بالوصول من أي مكان
header("Access-Control-Allow-Origin: *");

// الرابط الذي يعمل لديك لـ STARZPLAY 1
$remote_url = "http://sportfet.shop/AD1/tracks-v1a1/mono.m3u8";
$base_path  = "http://sportfet.shop/AD1/tracks-v1a1/";

// إذا طلب المتصفح قطعة فيديو (ts)
if (isset($_GET['ts'])) {
    header("Content-Type: video/mp2t");
    // جلب قطعة الفيديو وتمريرها مباشرة
    echo file_get_contents($base_path . $_GET['ts']);
    exit;
}

// جلب ملف البث m3u8 الأساسي
header("Content-Type: application/vnd.apple.mpegurl");
$content = file_get_contents($remote_url);

if ($content === false) {
    die("خطأ: تعذر جلب البث من المصدر");
}

// تحويل روابط الـ ts لتمر عبر هذا الملف لتعمل بـ HTTPS
$my_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$content = preg_replace('/([a-zA-Z0-9_\-]+\.ts)/', $my_url . '?ts=$1', $content);

echo $content;
?>
