

<?php
/**
 * beIN Live - Anti-Lag Edition
 * d-service.pro
 */

$remote_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/67397.m3u8";
$base_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/";

// 1. معالجة قطع الفيديو مع تحسين سرعة القراءة
if (isset($_GET['ts'])) {
    $ts_url = urldecode($_GET['ts']);
    header("Content-Type: video/mp2t");
    header("Access-Control-Allow-Origin: *");
    header("Cache-Control: public, max-age=10"); // زيادة الكاش لـ 10 ثواني للقطع لضمان السلاسة

    $opts = [
        "http" => [
            "header" => "User-Agent: VLC/3.0.18\r\n",
            "method" => "GET",
            "timeout" => 5
        ]
    ];
    
    $context = stream_context_create($opts);
    
    // استخدام fopen بدلاً من readfile للتحكم في تدفق البيانات بقطع كبيرة 64KB
    $fp = @fopen($ts_url, 'rb', false, $context);
    if ($fp) {
        while (!feof($fp)) {
            echo fread($fp, 65536);
            flush(); // إرسال البيانات للمتصفح فوراً دون انتظار امتلاء بفر PHP
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
        var videoSrc = 'b44.php?m3u8=true';

        if (Hls.isSupported()) {
            var hls = new Hls({
                enableWorker: true,
                // نغلق الـ Low Latency لزيادة الثبات (لأن الثبات أهم من السرعة الآن)
                lowLatencyMode: false, 
                
                // --- إعدادات الدرع الواقي من التقطيع ---
                maxBufferLength: 30,        // سحب 30 ثانية من البث مسبقاً (احتياطي ضخم)
                maxMaxBufferLength: 60,     // السماح بالوصول لدقيقة كاملة من التخزين
                maxBufferSize: 60 * 1000 * 1000, // 60 ميجابايت من الذاكرة
                
                // معالجة الأخطاء والقفز التلقائي
                manifestLoadingMaxRetry: 20,
                levelLoadingMaxRetry: 20,
                nudgeOffset: 0.5,           // قفزة أكبر (نصف ثانية) لتجاوز "الفريمات" التالفة
                nudgeMaxRetry: 30,
                
                // تحسين سرعة استعادة البث
                appendErrorMaxRetry: 10,
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