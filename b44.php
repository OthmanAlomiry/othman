<?php
/**
 * beIN Live - Smooth Stream Edition
 * d-service.pro
 */

$remote_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/67397.m3u8";
$base_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/";

// 1. معالجة قطع الفيديو (TS) مع زيادة سرعة الاستجابة
if (isset($_GET['ts'])) {
    $ts_url = urldecode($_GET['ts']);
    header("Content-Type: video/mp2t");
    header("Access-Control-Allow-Origin: *");
    header("Cache-Control: public, max-age=5"); // تخزين القطعة لـ 5 ثوانٍ لضمان عدم طلبها مجدداً

    $opts = [
        "http" => [
            "header" => "User-Agent: VLC/3.0.18\r\n",
            "timeout" => 3
        ]
    ];
    
    $context = stream_context_create($opts);
    @readfile($ts_url, false, $context);
    exit;
}

// 2. معالجة قائمة التشغيل m3u8
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>beIN Live Smooth</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        #video { width: 100%; height: auto; max-width: 100%; }
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
                // نغلق وضع الـ Low Latency قليلاً لزيادة الثبات
                lowLatencyMode: false, 
                
                // إعدادات "الدرع" ضد التقطيع:
                maxBufferLength: 30,        // سحب 30 ثانية من البث مسبقاً (يمتص تقطعات السيرفر)
                maxMaxBufferLength: 60,     // السماح بمخزن مؤقت يصل لدقيقة كاملة
                startLevel: 0,              // البدء بأقل جودة لضمان عدم الوقوف
                
                // معالجة التعليق التلقائي
                nudgeOffset: 0.5,           // قفزة أكبر قليلاً عند التعليق
                nudgeMaxRetry: 20,
                manifestLoadingMaxRetry: 10,
                levelLoadingMaxRetry: 10,
                
                // تسريع عملية التحميل
                appendErrorMaxRetry: 10,
                backBufferLength: 30
            });
            
            hls.loadSource(videoSrc);
            hls.attachMedia(video);
            
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                // محاولة التشغيل التلقائي
                video.play().catch(() => {
                    console.log("Play blocked by browser - waiting for user.");
                });
            });

            // إصلاح التوقفات الناتجة عن أخطاء الشبكة تلقائياً
            hls.on(Hls.Events.ERROR, function (event, data) {
                if (data.fatal) {
                    if (data.type === Hls.ErrorTypes.NETWORK_ERROR) {
                        hls.startLoad();
                    } else if (data.type === Hls.ErrorTypes.MEDIA_ERROR) {
                        hls.recoverMediaError();
                    }
                }
            });
        } 
        else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            // دعم سفاري (آيفون) الأصلي
            video.src = videoSrc;
        }
    </script>
</body>
</html>
