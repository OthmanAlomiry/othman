<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* إزالة أي هوامش أو فراغات لتناسب الـ Iframe */
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; background: #000; }
        video { width: 100%; height: 100%; object-fit: contain; }
    </style>
</head>
<body>

<video id="live-player" controls autoplay playsinline></video>

<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
    (function() {
        const videoElement = document.getElementById('live-player');
        // الرابط الذي أكدت أنه يعمل
        const streamUrl = 'https://ssserver.site/Kass-1/tracks-v1a1/mono.m3u8';

        if (Hls.isSupported()) {
            const hls = new Hls({
                enableWorker: true,
                lowLatencyMode: true
            });
            hls.loadSource(streamUrl);
            hls.attachMedia(videoElement);
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                videoElement.play().catch(() => {
                    console.log("التشغيل التلقائي يحتاج تفاعل مستخدم");
                });
            });
        } 
        else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
            videoElement.src = streamUrl;
            videoElement.addEventListener('loadedmetadata', function() {
                videoElement.play();
            });
        }
    })();
</script>

</body>
</html>
