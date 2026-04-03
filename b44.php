<?php
/**
 * beIN Live - Low Bandwidth Edition
 * d-service.pro
 */

$remote_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/67397.m3u8";
$base_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/";

// 1. معالجة قطع الفيديو مع تقليل ضغط الطلبات
if (isset($_GET['ts'])) {
    $ts_url = urldecode($_GET['ts']);
    header("Content-Type: video/mp2t");
    header("Access-Control-Allow-Origin: *");
    // زيادة مدة الكاش لتقليل إعادة تحميل نفس القطع
    header("Cache-Control: public, max-age=30"); 

    $opts = [
        "http" => [
            "header" => "User-Agent: VLC/3.0.18\r\n",
            "method" => "GET",
            "timeout" => 5
        ]
    ];
    
    $context = stream_context_create($opts);
    
    $fp = @fopen($ts_url, 'rb', false, $context);
    if ($fp) {
        // تقليل حجم الـ Chunk لتقليل الضغط اللحظي على الباندويث
        while (!feof($fp)) {
            echo fread($fp, 32768); // 32KB بدلاً من 64KB
            flush();
        }
        fclose($fp);
    }
    exit;
}

// 2. معالجة قائمة التشغيل
if (isset($_GET['m3u8'])) {
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *");
    
    $content = @file_get_contents($remote_url, false, stream_context_create(["http" => ["header" => "User-Agent: VLC/3.0.18\r\n"]]));
    if ($content) {
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>beIN Live PRO - Balanced</title>
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
                lowLatencyMode: false, 
                // تقليل البفر قليلاً من 30 إلى 15 لتوفير استهلاك البيانات الضائعة
                maxBufferLength: 15,        
                maxMaxBufferLength: 30,     
                maxBufferSize: 30 * 1000 * 1000, 
                
                manifestLoadingMaxRetry: 10,
                levelLoadingMaxRetry: 10,
                nudgeOffset: 0.5,
                enableSoftwareAES: true
            });
            
            hls.loadSource(videoSrc);
            hls.attachMedia(video);
            
            // ميزة إضافية: إيقاف البث إذا خرج المستخدم من الصفحة لتوفير الباندويث
            document.addEventListener("visibilitychange", function() {
                if (document.hidden) {
                    video.pause();
                }
            });

            hls.on(Hls.Events.MANIFEST_PARSED, function() { video.play(); });
        } 
        else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = videoSrc;
        }
    </script>
</body>
</html>
