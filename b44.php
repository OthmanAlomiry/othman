<?php
/**
 * beIN Live Stream - Direct Hybrid Mode
 * d-service.pro
 */

$remote_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/67397.m3u8";
$base_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/";

// معالجة الـ TS لضمان عدم التقطيع عبر التمرير المباشر
if (isset($_GET['ts'])) {
    $ts_url = urldecode($_GET['ts']);
    header("Content-Type: video/mp2t");
    header("Access-Control-Allow-Origin: *");
    
    // استخدام طريقة الـ Stream المباشر لتقليل استهلاك الـ RAM في Render
    $opts = ["http" => ["header" => "User-Agent: VLC/3.0.18\r\n", "timeout" => 5]];
    $context = stream_context_create($opts);
    @readfile($ts_url, false, $context);
    exit;
}

// معالجة القائمة m3u8
if (isset($_GET['m3u8'])) {
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *");
    $content = @file_get_contents($remote_url, false, stream_context_create(["http" => ["header" => "User-Agent: VLC/3.0.18\r\n"]]));
    if ($content) {
        foreach (explode("\n", $content) as $line) {
            $line = trim($line);
            if ($line && $line[0] !== '#') {
                $full_ts = (strpos($line, 'http') === 0) ? $line : $base_url . $line;
                echo "b44.php?ts=" . urlencode($full_ts) . "\n";
            } else { echo $line . "\n"; }
        }
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>beIN Live - High Speed</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        #video { width: 100%; height: auto; max-width: 100%; }
        #status { position: absolute; top: 10px; right: 10px; color: #0f0; font-family: sans-serif; font-size: 12px; background: rgba(0,0,0,0.5); padding: 5px; }
    </style>
</head>
<body>
    <div id="status">الوضع: تشغيل سريع...</div>
    <video id="video" controls autoplay playsinline></video>

    <script>
        var video = document.getElementById('video');
        var videoSrc = 'b44.php?m3u8=true';

        if (Hls.isSupported()) {
            var hls = new Hls({
                enableWorker: true,
                lowLatencyMode: true,
                // إعدادات لتقليل التأخير والتقطيع لأقصى درجة
                maxBufferLength: 3, 
                maxMaxBufferLength: 6,
                startLevel: 0,
                nudgeOffset: 0.1,
                nudgeMaxRetry: 10,
                fetchOptions: {
                    mode: 'cors'
                }
            });
            
            hls.loadSource(videoSrc);
            hls.attachMedia(video);
            
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                video.play();
                document.getElementById('status').innerText = "البث مباشر";
            });

            hls.on(Hls.Events.ERROR, function (event, data) {
                if (data.fatal) {
                    hls.startLoad(); // إعادة محاولة التحميل فوراً عند أي خطأ
                }
            });
        } 
        else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = videoSrc;
            video.play();
        }
    </script>
</body>
</html>
