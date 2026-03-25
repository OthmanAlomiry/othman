<?php
// رابط القناة الخارجي الذي لا يعمل مباشرة
$stream_url = "https://liveeu-gcp.alkassdigital.net/shooflive/main.m3u8";

// إعداد الرؤوس لمحاكاة متصفح حقيقي وتجاوز الحماية
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $stream_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36');

$data = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

// إرسال البيانات للمتصفح كملف بث مباشر
header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *"); // السماح لمشغل موقعك بالوصول للبث
header("Cache-Control: no-cache");

// إذا كان الرابط يحتوي على ملفات فرعية (TS segments) بروابط نسبية، نقوم بإصلاحها
$base = "https://liveeu-gcp.alkassdigital.net/shooflive/";
$data = str_replace("index", $base . "index", $data);

echo $data;
