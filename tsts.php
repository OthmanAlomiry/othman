<?php
// رابط البث الأصلي
$remoteUrl = "http://apk.arabic-ch.space/live/006900/index.m3u8";
$baseUrl = "http://apk.arabic-ch.space/live/006900/";

// إعداد الطلب لمحاكاة متصفح حقيقي تماماً
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36\r\n" .
                    "Accept: */*\r\n" .
                    "Origin: http://apk.arabic-ch.space\r\n" . // إيهام السيرفر أن الطلب داخلي
                    "Referer: http://apk.arabic-ch.space/\r\n"
    ]
];

$context = stream_context_create($opts);
$content = file_get_contents($remoteUrl, false, $context);

if ($content === FALSE) {
    header("HTTP/1.1 403 Forbidden");
    die("السيرفر الأصلي رفض الاتصال. قد يكون محظوراً على سيرفرات الاستضافة.");
}

// تعديل الروابط الداخلية لملفات الـ .ts لتصبح روابط كاملة
$content = preg_replace('/(?<!http:\/\/)(?<!https:\/\/)([\w\.-]+\.ts)/', $baseUrl . '$1', $content);

// إرسال البيانات للمتصفح
header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-cache");

echo $content;
