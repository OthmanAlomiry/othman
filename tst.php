<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Live Stream Test</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body { background: #000; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        video { width: 100%; max-width: 800px; border: 2px solid #333; }
    </style>
</head>
<body>

    <video id="video" controls autoplay></video>

<script>
    var video = document.getElementById('video');
    
    // رابط بث مباشر شغال حالياً (قناة الجزيرة - مثال للاختبار)
    var videoSrc = 'https://live-hls-web-aje.getaj.net/AJE/index.m3u8';

    if (Hls.isSupported()) {
        var hls = new Hls();
        hls.loadSource(videoSrc);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
            video.play();
        });
    } 
    // لدعم متصفحات آيفون (Safari) التي تدعم HLS داخلياً
    else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = videoSrc;
        video.addEventListener('loadedmetadata', function() {
            video.play();
        });
    }
</script>
</body>
</html>
