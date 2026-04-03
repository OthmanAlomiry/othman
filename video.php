<?php
// 1. رابط ملف الـ m3u8 الأصلي
$url = "http://apk.arabic-ch.space/live/006900/index.m3u8";

// 2. محاكاة متصفح Safari بالكامل
$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1\r\n" .
                    "Accept: */*\r\n" .
                    "Referer: http://apk.arabic-ch.space/\r\n"
    ]
];

$context = stream_context_create($options);
$data = @file_get_contents($url, false, $context);

if ($data === FALSE) {
    die("خطأ: سيرفر البث يمنع سيرفر Render من الوصول. جرب استخدام سيرفر VPS بـ IP خاص.");
}

// 3. تعديل الروابط الداخلية لتمر عبر سيرفرك (Proxying segments)
// سنقوم بتحويل كل رابط .ts إلى رابط كامل يشير للسيرفر الأصلي مباشرة
$base = "http://apk.arabic-ch.space/live/006900/";
$data = preg_replace('/(?<!http:\/\/)(?<!https:\/\/)([\w\.-]+\.ts)/', $base . '$1', $data);

// 4. ترويسات خاصة لمتصفح سفاري وآيفون
header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

echo $data;
