<?php
// رابط MBC مصر 2
$video_url = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8";

// إذا كان الطلب قادم من المشغل (HLS)
if (isset($_GET['stream'])) {
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36\r\n" .
                        "Referer: https://shd-gcp-live.edgenextcdn.net/\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $content = @file_get_contents($video_url, false, $context);
    
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *");
    
    // إصلاح الروابط الداخلية لتعمل عبر السيرفر الأصلي
    $base_url = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/";
    echo str_replace("index", $base_url . "index", $content);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; overflow: hidden; }
        video { width: 100%; height: 100%; object-fit: contain; }
    </style>
</head>
<body>
    <video id="video" controls playsinline autoplay></video>
    <script>
        var video = document.getElementById('video');
        // نطلب الرابط من نفس ملف الـ PHP عبر سطر الباراميتر stream
        var source = window.location.href + (window.location.href.indexOf('?') > -1 ? '&stream=1' : '?stream=1');

        if (Hls.isSupported()) {
            var hls = new Hls({ xhrSetup: function (xhr) { xhr.withCredentials = false; } });
            hls.loadSource(source);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function() { video.play(); });
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = source;
            video.play();
        }
    </script>
</body>
</html>
