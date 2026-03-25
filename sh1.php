<?php
// رابط القناة الخارجي
$stream_url = "https://liveeu-gcp.alkassdigital.net/shooflive/main.m3u8";

// محاكاة متصفح كامل لتجاوز حماية السيرفر
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $stream_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_REFERER, 'https://alkass.net/');

$content = curl_exec($ch);
curl_close($ch);

if ($content) {
    // إخبار المتصفح بنوع الملف وتجاوز قيود CORS
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *");
    
    // تصحيح الروابط الفرعية لتعمل من خلال السيرفر الأصلي
    $base = "https://liveeu-gcp.alkassdigital.net/shooflive/";
    $content = str_replace("index", $base . "index", $content);
    
    echo $content;
} else {
    // في حال فشل السيرفر في جلب البيانات
    header("Location: " . $stream_url);
}
?>
