<?php
// رابط القناة الأصلي
$stream_url = "http://apk.arabic-ch.space/live/006900/index.m3u8";

// إعداد الرؤوس لمحاكاة جهاز آيفون يتصل بالسيرفر
$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.0 Mobile/15E148 Safari/604.1\r\n"
    ]
];

$context = stream_context_create($options);
$content = @file_get_contents($stream_url, false, $context);

if ($content === FALSE) {
    die("خطأ: سيرفر Render محظور من الوصول لمصدر البث.");
}

// تعديل الروابط الداخلية لتصبح روابط كاملة حتى يفهمها متصفح سفاري
$base_path = "http://apk.arabic-ch.space/live/006900/";
$content = preg_replace('/(?<!http:\/\/)(?<!https:\/\/)([\w\.-]+\.ts)/', $base_path . '$1', $content);

// إرسال الترويسات التي تجعل المتصفح يثق في الملف
header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-cache");

echo $content;
