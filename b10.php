<?php
header("Access-Control-Allow-Origin: *");

$main_url = "http://sportfet.shop/AD1/tracks-v1a1/mono.m3u8";
$base_path = "http://sportfet.shop/AD1/tracks-v1a1/";

// دالة لجلب البيانات باستخدام CURL مع محاكاة متصفح
function get_data($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // محاكاة متصفح كروم على ويندوز لتجنب الحظر
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_REFERER, 'http://sportfet.shop/');
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

// إذا كان الطلب لقطعة فيديو
if (isset($_GET['ts'])) {
    $ts_url = $base_path . $_GET['ts'];
    header("Content-Type: video/mp2t");
    echo get_data($ts_url);
    exit;
}

// جلب ملف البث
$content = get_data($main_url);

if (!$content) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "تعذر الاتصال بمصدر البث. قد يكون الرابط متوقفاً من المصدر.";
    exit;
}

header("Content-Type: application/vnd.apple.mpegurl");
// استبدال روابط القطع لتعمل عبر هذا الملف
$content = preg_replace('/([a-zA-Z0-9_\-]+\.ts)/', 'b10.php?ts=$1', $content);

echo $content;
?>
