<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Stream Player</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body { background-color: #000; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; height: 100vh; color: white; }
        #video-container { width: 100%; max-width: 850px; position: relative; }
        video { width: 100%; border: 1px solid #333; box-shadow: 0 0 20px rgba(0,0,0,0.5); }
    </style>
</head>
<body>

<div id="video-container">
    <video id="video" controls autoplay playsinline></video>
</div>

<script>
    var video = document.getElementById('video');
    // الرابط الخاص بك
    var videoSrc = 'https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8';

    if (Hls.isSupported()) {
        var hls = new Hls({
            // إعدادات لتجاوز مشاكل التحميل
            xhrSetup: function(xhr, url) {
                // محاكاة متصفح حقيقي في كل طلب
                xhr.setRequestHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            }
        });
        
        hls.loadSource(videoSrc);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
            video.play();
        });

        // معالجة الأخطاء وإعادة المحاولة تلقائياً
        hls.on(Hls.Events.ERROR, function (event, data) {
            if (data.fatal) {
                switch (data.type) {
                    case Hls.ErrorTypes.NETWORK_ERROR:
                        console.log("خطأ في الشبكة، محاولة إعادة الاتصال...");
                        hls.startLoad();
                        break;
                    case Hls.ErrorTypes.MEDIA_ERROR:
                        console.log("خطأ في الوسائط، محاولة الإصلاح...");
                        hls.recoverMediaError();
                        break;
                    default:
                        hls.destroy();
                        break;
                }
            }
        });
    }
    // دعم خاص لمتصفح Safari على iPhone
    else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = videoSrc;
        video.addEventListener('loadedmetadata', function() {
            video.play();
        });
    }
</script>

</body>
</html>
