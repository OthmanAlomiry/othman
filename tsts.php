<?php
// رابط البث الأصلي
$remoteUrl = "http://apk.arabic-ch.space/live/006900/index.m3u8";

// استخراج الرابط الأساسي (Base URL) لجلب ملفات الـ .ts لاحقاً
$baseUrl = substr($remoteUrl, 0, strrpos($remoteUrl, '/') + 1);

$options = [
    "http" => [
        "header" => "User-Agent: VLC/3.0.18 LibVLC/3.0.18\r\n",
        "follow_location" => 1
    ]
];

$context = stream_context_create($options);
$content = file_get_contents($remoteUrl, false, $context);

if ($content === FALSE) {
    die("تعذر الوصول للسيرفر الأصلي");
}

// إضافة الرابط الأساسي قبل أي ملف .ts لا يحتوي على رابط كامل
// هذا يضمن أن المتصفح يعرف من أين يحمل قطع الفيديو
$content = preg_replace('/^(?!http)(.*\.ts)$/m', $baseUrl . '$1', $content);

// إرسال الترويسات
header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-cache");

echo $content;
