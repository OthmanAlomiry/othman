<?php
/**
 * beIN Live - Cloudflare Integrated Edition
 * d-service.pro
 */

// رابط الـ Worker الخاص بك الذي أنشأته للتو
$cloudflare_worker_url = "https://bein4.othman1405.workers.dev"; 

$remote_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/67397.m3u8";
$base_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/";

// معالجة قائمة التشغيل m3u8 عند طلب المشغل لها
if (isset($_GET['m3u8'])) {
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *");
    
    $opts = ["http" => ["header" => "User-Agent: VLC/3.0.18\r\n"]];
    $content = @file_get_contents($remote_url, false, stream_context_create($opts));
    
    if ($content) {
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line && $line[0] !== '#') {
                $full_ts = (strpos($line, 'http') === 0) ? $line : $base_url . $line;
                // توجيه روابط الفيديو (TS) لتمر عبر Cloudflare Worker الخاص بك
                echo $cloudflare_worker_url . "?ts=" . urlencode($full_ts) . "\n";
            } else {
                echo $line . "\n";
            }
        }
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>beIN Live PRO - Cloud Powered</title>
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
        var videoSrc = 'b44.php?m3u8=true';

        if (Hls.isSupported()) {
            var hls = new Hls({
                enableWorker: true,
                lowLatencyMode: false, // نغلقه لزيادة الثبات عبر الكاش
                maxBufferLength: 30,   // سحب 30 ثانية احتياطياً لامتصاص أي تقطيع
                maxMaxBufferLength: 60,
                manifestLoadingMaxRetry: 20,
                levelLoadingMaxRetry: 20,
                nudgeOffset: 0.5,
                enableSoftwareAES: true
            });
            
            hls.loadSource(videoSrc);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function() { video.play(); });

            hls.on(Hls.Events.ERROR, function (event, data) {
                if (data.fatal) {
                    if (data.type === Hls.ErrorTypes.NETWORK_ERROR) { hls.startLoad(); }
                    else if (data.type === Hls.ErrorTypes.MEDIA_ERROR) { hls.recoverMediaError(); }
                }
            });
        } 
        else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = videoSrc;
        }
    </script>
</body>
</html>
