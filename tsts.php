<?php
// رابط البث الأصلي
$remoteUrl = "http://apk.arabic-ch.space/live/006900/index.m3u8";

// إعدادات الطلب لمحاكاة برنامج VLC أو متصفح حقيقي
$options = [
    "http" => [
        "header" => "User-Agent: VLC/3.0.18 LibVLC/3.0.18\r\n", // محاكاة VLC
        "follow_location" => 1,
        "timeout" => 10
    ]
];

$context = stream_context_create($options);
$content = file_get_contents($remoteUrl, false, $context);

if ($content === FALSE) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "خطأ في الاتصال بسيرفر البث.";
    exit;
}

// إرسال الترويسات المناسبة للمتصفح ليقرأ الملف كبث فيديو
header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *"); // لحل مشكلة CORS
header("Cache-Control: no-cache");

echo $content;
