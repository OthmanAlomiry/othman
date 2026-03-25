<?php
// رابط قناة MBC مصر 2 الذي أرسلته
$remote_url = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8";

// إعدادات محاكاة متصفح حقيقي لتجاوز الحماية
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36\r\n" .
                    "Origin: https://shd-gcp-live.edgenextcdn.net\r\n" .
                    "Referer: https://shd-gcp-live.edgenextcdn.net/\r\n"
    ]
];

$context = stream_context_create($opts);
$content = @file_get_contents($remote_url, false, $context);

if ($content === false) {
    // في حال فشل السيرفر في الجلب المباشر، نقوم بالتحويل كحل أخير
    header("Location: $remote_url");
    exit;
}

// إعداد الرؤوس (Headers) لإجبار المتصفح على تشغيل الفيديو
header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *"); // السماح لموقعك بعرض البث
header("Cache-Control: no-cache");

// تصحيح المسارات الداخلية لملفات البث (مهم جداً لعمل البث المستمر)
$base_url = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/";
$content = str_replace("index", $base_url . "index", $content);

echo $content;
