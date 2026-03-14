<?php
// 1. الجزء البرمجي (الخادم الوسيط Proxy)
if (isset($_GET['proxy_stream'])) {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/vnd.apple.mpegurl");
    
    $remote_url = "http://sportfet.shop/AD1/tracks-v1a1/mono.m3u8";
    $base_path  = "http://sportfet.shop/AD1/tracks-v1a1/";
    
    $content = file_get_contents($remote_url);
    if ($content === false) {
        // محاولة بديلة بـ CURL
        $ch = curl_init($remote_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
        $content = curl_exec($ch);
        curl_close($ch);
    }
    
    // تحويل الروابط الداخلية لتعمل بـ HTTP كامل ليتجاوز السيرفر مشكلة المسارات
    $content = preg_replace('/([a-zA-Z0-9_\-]+\.ts)/', $base_path . '$1', $content);
    echo $content;
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>STARZPLAY 1 - البث المباشر</title>
    <link href="https://vjs.zencdn.net/7.20.3/video-js.css" rel="stylesheet" />
    <style>
        body, html { margin: 0; padding: 0; height: 100%; background: #000; display: flex; justify-content: center; align-items: center; }
        .vjs-matrix.video-js { color: #e11d48; }
        .video-js .vjs-big-play-button { background-color: rgba(225, 29, 72, 0.7); border-radius: 50%; width: 80px; height: 80px; line-height: 80px; margin-top: -40px; margin-left: -40px; }
    </style>
</head>
<body>

<video id="my-video" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" data-setup='{}' style="width: 100%; height: 100vh;">
</video>

<script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script>
<script>
    // طلب البث من نفس الملف عبر الوسيط لتجاوز حظر HTTPS
    var streamUrl = window.location.pathname + "?proxy_stream=1";
    
    var player = videojs('my-video');
    player.src({
        src: streamUrl,
        type: 'application/x-mpegURL'
    });

    // محاولة التشغيل التلقائي
    player.ready(function() {
        var promise = player.play();
        if (promise !== undefined) {
            promise.catch(function(error) {
                console.log("التشغيل التلقائي يحتاج ضغطة مستخدم");
            });
        }
    });
</script>
</body>
</html>
