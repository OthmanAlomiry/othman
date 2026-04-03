<?php
// --- الجزء الأول: معالجة البث (البروكسي) ---
if (isset($_GET['get_stream'])) {
    $url = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8";
    
    $options = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)\r\n" .
                        "Referer: https://shahid.mbc.net/\r\n" .
                        "Origin: https://shahid.mbc.net\r\n"
        ]
    ];

    $context = stream_context_create($options);
    $content = @file_get_contents($url, false, $context);

    if ($content !== FALSE) {
        // تحويل الروابط النسبية إلى روابط كاملة لضمان عملها في المتصفح
        $base = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/";
        $content = preg_replace('/([\w\.-]+\.ts)/', $base . '$1', $content);
        
        header("Content-Type: application/vnd.apple.mpegurl");
        header("Access-Control-Allow-Origin: *");
        echo $content;
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>D-Service | بث مباشر MBC Masr 2</title>
    
    <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
    <style>
        body { background-color: #1a1a1a; color: white; font-family: sans-serif; text-align: center; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; }
        .video-js { width: 100% !important; height: 450px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
        h1 { color: #f39c12; margin-bottom: 20px; }
        .status { margin-top: 10px; font-size: 14px; color: #aaa; }
    </style>
</head>
<body>

<div class="container">
    <h1>D-Service Live</h1>
    
    <video id="my-video" class="video-js vjs-big-play-centered" controls preload="auto" poster="https://d-service.pro/logo.png" data-setup='{}'>
        <source src="?get_stream=1" type="application/vnd.apple.mpegurl">
        <p class="vjs-no-js">متصفحك لا يدعم تشغيل الفيديو، يرجى تحديث المتصفح.</p>
    </video>

    <div class="status">البث المباشر: MBC Masr 2 (HD)</div>
</div>

<script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>
</body>
</html>
