<?php
/**
 * beIN Live PRO - Zero Bandwidth & High Stability
 * d-service.pro
 */

// الرابط الأصلي للقناة
$remote_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/67397.m3u8";
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>beIN Live PRO - Stable</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        video { width: 100%; height: auto; max-width: 100%; background: #000; }
    </style>
</head>
<body>
    <video id="video" controls autoplay playsinline></video>

    <script>
        var video = document.getElementById('video');
        // الرابط المباشر من السيرفر الأصلي
        var videoSrc = '<?php echo $remote_url; ?>';

        if (Hls.isSupported()) {
            var hls = new Hls({
                enableWorker: true,
                lowLatencyMode: false, 
                // إعدادات البفر التي طلبتها لضمان عدم التقطيع
                maxBufferLength: 30,        
                maxMaxBufferLength: 60,
                maxBufferSize: 60 * 1000 * 1000,
                
                manifestLoadingMaxRetry: 20,
                levelLoadingMaxRetry: 20,
                nudgeOffset: 0.5,
                nudgeMaxRetry: 30,
                enableSoftwareAES: true,
                
                // هذه الإعدادات تجعل المتصفح يطلب الفيديو مباشرة دون وسيط
                xhrSetup: function(xhr, url) {
                    xhr.withCredentials = false;
                }
            });
            
            hls.loadSource(videoSrc);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                video.play().catch(function(e) {
                    console.log("Auto-play blocked, waiting for interaction");
                });
            });

            hls.on(Hls.Events.ERROR, function (event, data) {
                if (data.fatal) {
                    switch (data.type) {
                        case Hls.ErrorTypes.NETWORK_ERROR: hls.startLoad(); break;
                        case Hls.ErrorTypes.MEDIA_ERROR: hls.recoverMediaError(); break;
                        default: hls.destroy(); break;
                    }
                }
            });
        } 
        else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            // دعم الآيفون (سفاري) المباشر
            video.src = videoSrc;
        }
    </script>
</body>
</html>
