<?php
/**
 * الجزء الأول: البروكسي لتجاوز حماية MBC
 */
if (isset($_GET['stream'])) {
    $url = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8";
    
    // إعداد الطلب لمحاكاة برنامج VLC تماماً
    $options = [
        "http" => [
            "header" => "User-Agent: VLC/3.0.18 LibVLC/3.0.18\r\n" .
                        "Icy-MetaData: 1\r\n"
        ]
    ];
    
    $context = stream_context_create($options);
    $content = file_get_contents($url, false, $context);
    
    if ($content !== false) {
        header("Content-Type: application/vnd.apple.mpegurl");
        header("Access-Control-Allow-Origin: *");
        echo $content;
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MBC Masr 2 Live</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body { background: #000; margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        video { width: 100%; max-width: 800px; height: auto; box-shadow: 0 0 20px rgba(255,255,255,0.1); }
    </style>
</head>
<body>

    <video id="video" controls autoplay playsinline></video>

    <script>
        var video = document.getElementById('video');
        // هنا نجعل المصدر هو نفس الملف الحالي مع طلب "البروكسي"
        var videoSrc = 'tst.php?stream=1';

        if (Hls.isSupported()) {
            var hls = new Hls({
                debug: false,
                fragLoadingTimeOut: 20000,
            });
            hls.loadSource(videoSrc);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                video.play();
            });
        } 
        else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = videoSrc;
            video.addEventListener('loadedmetadata', function() {
                video.play();
            });
        }
    </script>
</body>
</html>
