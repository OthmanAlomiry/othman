<?php
// ملف تشغيل قناة SHOOF - الخدمة الرقمية
$url = "https://liveeu-gcp.alkassdigital.net/shooflive/main.m3u8";

// إعداد الطلب ليبدو كأنه من متصفح رسمي لتجاوز الحماية
$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36\r\n" .
                    "Referer: https://www.alkass.net/\r\n" .
                    "Origin: https://www.alkass.net\r\n"
    ]
];

$context = stream_context_create($options);
$content = @file_get_contents($url, false, $context);

if ($content === false) {
    // إذا فشل السيرفر في الجلب المباشر، نقوم بعمل تحويل مباشر كحل أخير
    header("Location: $url");
} else {
    // جلب البث وتمريره للمشغل في موقعك
    header("Content-Type: application/vnd.apple.mpegurl");
    echo $content;
}
?>
