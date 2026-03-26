<?php
// الرابط الخاص بك
$url = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8";

// الجزء المسؤول عن جلب البيانات (البروكسي)
if (isset($_GET['stream'])) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // محاكاة VLC بشكل كامل
    curl_setopt($ch, CURLOPT_USERAGENT, 'VLC/3.0.18 LibVLC/3.0.18');
    
    $data = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    header("Content-Type: " . $info['content_type']);
    header("Access-Control-Allow-Origin: *");
    
    // تعديل الروابط الداخلية لتجبر السيرفر على إرسال قطع الفيديو
    $base_path = dirname($url) . "/";
    echo str_replace('index', $base_path . 'index', $data);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MBC Masr 2 - Proxy Play</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
</head>
<body style="margin:0; background:#000;">
    <video id="video" controls autoplay playsinline style="width:100%; height:100vh;"></video>
    <script>
        var video = document.getElementById('video');
        var streamUrl = 'tst.php?stream=1'; // الطلب يذهب لسيرفرك أولاً

        if (Hls.isSupported()) {
            var hls = new Hls();
            hls.loadSource(streamUrl);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                video.play();
            });
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = streamUrl;
        }
    </script>
</body>
</html>
