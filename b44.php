<?php
/**
 * beIN Live Stream Relay - d-service.pro
 * النسخة النهائية: أداء عالٍ + تشغيل سريع
 */

// رابط القناة الأصلي من سيرفر Lynx
$remote_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/67341.m3u8";
$base_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/";

// 1. معالجة قطع الفيديو (TS) - الجزء المسؤول عن جلب الصورة والصوت
if (isset($_GET['ts'])) {
    $ts_url = urldecode($_GET['ts']);
    
    header("Content-Type: video/mp2t");
    header("Access-Control-Allow-Origin: *");
    header("Cache-Control: no-cache, must-revalidate");

    $opts = [
        "http" => [
            "header" => "User-Agent: VLC/3.0.18\r\n",
            "timeout" => 5 // تقليل وقت الانتظار للقطع المتأخرة
        ]
    ];
    $context = stream_context_create($opts);
    
    // استخدام حجم قطعة أكبر (64KB) لتدفق بيانات أسرع للمتصفح
    $handle = @fopen($ts_url, "rb", false, $context);
    if ($handle) {
        while (!feof($handle)) {
            echo fread($handle, 65536); // نقل 64 كيلوبايت في كل دورة
            flush(); // دفع البيانات فوراً للمشغل
        }
        fclose($handle);
    }
    exit;
}

// 2. معالجة قائمة التشغيل (m3u8) عند طلب المشغل لها
if (isset($_GET['m3u8'])) {
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *");
    
    $opts = ["http" => ["header" => "User-Agent: VLC/3.0.18\r\n"]];
    $context = stream_context_create($opts);
    $content = @file_get_contents($remote_url, false, $context);

    if ($content) {
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line && $line[0] !== '#') {
                $full_ts = (strpos($line, 'http') === 0) ? $line : $base_url . $line;
                echo "b44.php?ts=" . urlencode($full_ts) . "\n";
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
    <title>beIN SPORTS Live - D-Service</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body, html { 
            margin: 0; padding: 0; width: 100%; height: 100%; 
            background: #000; display: flex; align-items: center; 
            justify-content: center; overflow: hidden; 
        }
        #video { width: 100%; height: auto; max-width: 100%; background: #000; }
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
                lowLatencyMode: true, // وضع التأخير المنخفض للبدء فوراً
                maxBufferLength: 10,  // تقليل حجم المخزن المؤقت للبدء بسرعة
                maxMaxBufferLength: 20,
                startLevel: -1,       // ترك اختيار أفضل جودة للمشغل تلقائياً
                manifestLoadingMaxRetry: 10,
                levelLoadingMaxRetry: 10
            });
            
            hls.loadSource(videoSrc);
            hls.attachMedia(video);
            
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                video.play();
            });

            // إعادة المحاولة التلقائية في حال حدوث أي خطأ في السيرفر
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
            // دعم متصفح Safari الأصلي في الآيفون
            video.src = videoSrc;
            video.addEventListener('loadedmetadata', function() {
                video.play();
            });
        }
    </script>
</body>
</html>
