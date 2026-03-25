<?php
// رابط القناة الخارجي
$remote_url = "https://liveeu-gcp.alkassdigital.net/shooflive/main.m3u8";

// إعداد خيارات الجلب لمحاكاة متصفح حقيقي
$options = array(
    'http' => array(
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36\r\n" .
                    "Referer: https://alkass.net/\r\n" .
                    "Origin: https://alkass.net\r\n",
        'method' => 'GET'
    )
);

$context = stream_context_create($options);
$data = @file_get_contents($remote_url, false, $context);

if ($data === false) {
    // إذا فشل السيرفر في الجلب، نقوم بالتحويل المباشر
    header("Location: $remote_url");
    exit;
}

// إخبار المتصفح أن هذا بث فيديو
header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-cache");

// تصحيح الروابط الداخلية داخل ملف البث لكي تعمل من سيرفرك
$base_path = "https://liveeu-gcp.alkassdigital.net/shooflive/";
$data = str_replace("index", $base_path . "index", $data);

echo $data;
