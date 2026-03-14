<?php
// السماح للمتصفح بالوصول للملف من أي مكان
header("Access-Control-Allow-Origin: *");

// الرابط الأساسي الذي يعمل لديك
$remote_url = "http://sportfet.shop/AD1/tracks-v1a1/mono.m3u8";

// جلب المحتوى من الرابط الأساسي
$content = file_get_contents($remote_url);

if ($content === false) {
    // إذا فشل السيرفر في جلب البيانات، سنحاول بطريقة أخرى (CURL)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    $content = curl_exec($ch);
    curl_close($ch);
}

// إخبار المتصفح أن هذا ملف بث مباشر
header("Content-Type: application/vnd.apple.mpegurl");

// أهم خطوة: تحويل الروابط الداخلية لتكون روابط كاملة (Absolute URLs)
// لكي يعرف المشغل أين يجد قطع الفيديو (.ts)
$base_url = "http://sportfet.shop/AD1/tracks-v1a1/";
$content = preg_replace('/([a-zA-Z0-9_\-]+\.ts)/', $base_url . '$1', $content);

echo $content;
?>
