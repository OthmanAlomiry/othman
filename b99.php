<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>beIN Live - Final Solution</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body { margin: 0; background: #000; display: flex; align-items: center; justify-content: center; height: 100vh; }
        video { width: 100%; height: auto; background: #000; }
    </style>
</head>
<body>
    <video id="video" controls autoplay playsinline></video>
    <script>
        var video = document.getElementById('video');
        // رابط الـ Worker الخاص بك بعد التحديث
        var videoSrc = 'https://still-surf-2ef3.othman1405.workers.dev';

        if (Hls.isSupported()) {
            var hls = new Hls();
            hls.loadSource(videoSrc);
            hls.attachMedia(video);
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            // للآيفون (سفاري)
            video.src = videoSrc;
        }
    </script>
</body>
</html>
