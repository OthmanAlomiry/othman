<?php
// إعدادات العرض الأساسية
header("Access-Control-Allow-Origin: *");
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>beIN Live PRO</title>
    
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    
    <style>
        body { 
            margin: 0; 
            padding: 0; 
            background: #000; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            overflow: hidden; 
        }
        #video { 
            width: 100%; 
            max-width: 1000px; 
            height: auto; 
            background: #000;
            box-shadow: 0 0 20px rgba(0,255,0,0.1);
        }
    </style>
</head>
<body>

    <video id="video" controls autoplay playsinline poster="https://via.placeholder.com/800x450/000000/FFFFFF?text=Loading+Stream..."></video>

    <script>
        var video = document.getElementById('video');
        // رابط الـ Worker الخاص بك الذي أنشأناه في Cloudflare
        var videoSrc = 'https://bein4.othman1405.workers.dev';

        if (Hls.isSupported()) {
            var hls = new Hls({
                debug: false,
                enableWorker: true,
                lowLatencyMode: true
            });
            hls.loadSource(videoSrc);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                video.play();
            });
            
            // معالجة أخطاء الشبكة لإعادة الاتصال تلقائياً
            hls.on(Hls.Events.ERROR, function (event, data) {
                if (data.fatal) {
                    switch (data.type) {
                        case Hls.ErrorTypes.NETWORK_ERROR:
                            hls.startLoad();
                            break;
                        case Hls.ErrorTypes.MEDIA_ERROR:
                            hls.recoverMediaError();
                            break;
                        default:
                            hls.destroy();
                            break;
                    }
                }
            });
        } 
        else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            // دعم Safari (آيفون) الأصلي
            video.src = videoSrc;
            video.addEventListener('loadedmetadata', function() {
                video.play();
            });
        }
    </script>
</body>
</html>
