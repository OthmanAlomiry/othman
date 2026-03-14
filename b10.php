<?php
// إعدادات الوصول
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/vnd.apple.mpegurl");

// الرابط الذي أكدت أنه يعمل
$stream_url = "http://sportfet.shop/AD1/tracks-v1a1/mono.m3u8";

// إذا طلب المشغل الملف، نعطيه محتوى m3u8 مع روابط كاملة
$content = file_get_contents($stream_url);

if ($content === false) {
    // محاولة أخيرة باستخدام CURL في حال فشل file_get_contents
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $stream_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $content = curl_exec($ch);
    curl_close($ch);
}

// تعديل الروابط الداخلية لتكون روابط كاملة (Absolute) لكي لا يبحث المتصفح في موقعك
$base_path = "http://sportfet.shop/AD1/tracks-v1a1/";
$content = preg_replace('/([a-zA-Z0-9_\-]+\.ts)/', $base_path . '$1', $content);

echo $content;
?>
