<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/vnd.apple.mpegurl");

// الرابط الأساسي للبث
$main_url = "http://sportfet.shop/AD1/tracks-v1a1/mono.m3u8";
// المسار الأساسي لقطع الفيديو
$base_path = "http://sportfet.shop/AD1/tracks-v1a1/";

if (isset($_GET['ts'])) {
    $ts_file = $_GET['ts'];
    // جلب قطعة الفيديو وتمريرها مباشرة
    header("Content-Type: video/mp2t");
    readfile($base_path . $ts_file);
    exit;
}

// جلب ملف الـ m3u8
$content = file_get_contents($main_url);

if ($content === false) {
    die("خطأ: تعذر الوصول إلى سيرفر البث.");
}

// استبدال أسماء قطع الـ .ts لتعمل عبر هذا الملف (Proxy)
$content = preg_replace('/([0-9a-zA-Z_\-]+\.ts)/', 'b10.php?ts=$1', $content);

echo $content;
?>
