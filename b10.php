<?php
/**
 * محول البث المباشر - STARZPLAY 1
 * هذا الملف يعمل كرابط مباشر ينتهي بـ .m3u8 متوافق مع HTTPS
 */

// 1. إعداد الروابط (المصدر الأصلي)
$remote_url = "http://sportfet.shop/AD1/tracks-v1a1/mono.m3u8";
$base_path  = "http://sportfet.shop/AD1/tracks-v1a1/";

// 2. السماح بمرور البيانات عبر السيرفرات المختلفة (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Expose-Headers: Content-Length, Content-Type");

// 3. معالجة قطع الفيديو (.ts) - يتم استدعاؤها تلقائياً بواسطة المشغل
if (isset($_GET['ts'])) {
    header("Content-Type: video/mp2t");
    // جلب قطعة الفيديو وتمريرها مباشرة للمتصفح
    $ts_content = file_get_contents($base_path . $_GET['ts']);
    echo $ts_content;
    exit;
}

// 4. معالجة ملف البث الأساسي (.m3u8)
header("Content-Type: application/vnd.apple.mpegurl");
header("Content-Disposition: inline; filename='stream.m3u8'");

// جلب محتوى ملف m3u8 من السيرفر الأصلي
$m3u8_content = file_get_contents($remote_url);

if ($m3u8_content === false) {
    // محاولة بديلة باستخدام CURL في حال فشل السيرفر
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    $m3u8_content = curl_exec($ch);
    curl_close($ch);
}

// 5. السحر البرمجي: تحويل الروابط الداخلية لتمر عبر هذا الملف (Proxy)
// هذا يحول قطع الفيديو من HTTP غير آمن إلى HTTPS آمن عبر موقعك
$my_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$m3u8_content = preg_replace('/([a-zA-Z0-9_\-]+\.ts)/', $my_url . '?ts=$1', $m3u8_content);

echo $m3u8_content;
