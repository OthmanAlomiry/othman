<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; overflow: hidden; }
        #video { width: 100%; height: 100%; }
    </style>
</head>
<body>
    <video id="video" controls playsinline autoplay></video>
    <script>
        var video = document.getElementById('video');
        // الرابط الأصلي الخاص بك
        var originalSrc = 'https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8';
        
        // استخدام بروكسي وسيط لكسر حماية المتصفح
        var proxySrc = 'https://api.allorigins.win/raw?url=' + encodeURIComponent(originalSrc);

        if (Hls.isSupported()) {
            var hls = new Hls({
                xhrSetup: function(xhr, url) {
                    // إخبار المتصفح بأننا نطلب البيانات بشكل خام لتجاوز الحظر
                    xhr.withCredentials = false;
                }
            });
            hls.loadSource(originalSrc); // محاولة مباشرة
            hls.attachMedia(video);
            
            hls.on(Hls.Events.ERROR, function(event, data) {
                if (data.details === 'manifestLoadError') {
                    // إذا فشلت الطريقة المباشرة (مثلما يحدث معك)، نستخدم البروكسي فوراً
                    hls.loadSource(proxySrc);
                }
            });
            
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                video.play();
            });
        }
    </script>
</body>
</html>
