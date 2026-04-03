<?php
// 1. رابط البث الأصلي
$remoteUrl = "http://apk.arabic-ch.space/live/006900/index.m3u8";

// 2. إعداد الرؤوس لمحاكاة جهاز آيفون يتصل بالسيرفر
$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.0 Mobile/15E148 Safari/604.1\r\n" .
                    "Accept: */*\r\n" .
                    "Connection: close\r\n"
    ]
];

$context = stream_context_create($options);
$content = @file_get_contents($remoteUrl, false, $context);

if ($content === FALSE) {
    // إذا فشل السيرفر في جلب البيانات، فهذا يعني أن سيرفر الاستضافة (Render) محظور تماماً من قبل صاحب البث.
    die("خطأ: السيرفر الأصلي يرفض الاتصال بسيرفر الاستضافة الخاص بك.");
}

// 3. تعديل الروابط الداخلية لملفات الـ .ts لتكون روابط كاملة (Absolute URLs)
// هذا السطر ضروري جداً لآيفون لكي يعرف مكان قطع الفيديو
$baseUrl = "http://apk.arabic-ch.space/live/006900/";
$content = preg_replace('/(?<!http:\/\/)(?<!https:\/\/)([\w\.-]+\.ts)/', $baseUrl . '$1', $content);

// 4. إرسال الترويسات الصحيحة لمتصفح سفاري
header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-cache, must-revalidate");

echo $content;
