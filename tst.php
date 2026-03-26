<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Live Player</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body { background: #000; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        video { width: 100%; max-width: 800px; height: auto; outline: none; }
    </style>
</head>
<body>

    <video id="video" controls autoplay playsinline></video>

<script>
    var video = document.getElementById('video');
    // استخدمنا هنا "cors-anywhere" أو وسيط لكسر حماية الرابط
    var rawUrl = 'https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8';
    
    // هذا الرابط الوسيط يضيف Headers اللازمة لتشغيل البث على موقعك
    var proxyUrl = 'https://cors-anywhere.herokuapp.com/' + rawUrl;

    if (Hls.isSupported()) {
        var hls = new Hls();
        // جرب أولاً بالرابط المباشر، وإذا لم يعمل جرب proxyUrl
        hls.loadSource(rawUrl); 
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
            video.play();
        });
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = rawUrl;
    }
</script>
</body>
</html>
