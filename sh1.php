<?php
// رابط قناة shoof الخارجي الذي أرسلته
$remote_url = "https://liveeu-gcp.alkassdigital.net/shooflive/main.m3u8";

// إعداد الرأس (Header) لكسر الحماية الخارجية
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36\r\n" .
                    "Origin: https://liveeu-gcp.alkassdigital.net\r\n" .
                    "Referer: https://liveeu-gcp.alkassdigital.net/\r\n"
    ]
];

$context = stream_context_create($opts);
$content = @file_get_contents($remote_url, false, $context);

if ($content === false) {
    // إذا فشل الجلب، نقوم بالتحويل المباشر
    header("Location: $remote_url");
} else {
    // إرسال المحتوى كملف بث مباشر
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *"); // السماح لموقعك بعرض البث
    echo $content;
}
?>
