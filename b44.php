<?php
/**
 * beIN Live Stream Relay - d-service.pro
 * هذا الملف يقوم بجلب البث وتعديله ليعمل على المتصفحات والآيفون
 */

// رابط القناة الأصلي (تأكد من صحة الرابط أو جرب جودة SD إذا استمر التقطيع)
$remote_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/67341.m3u8";
$base_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/";

// أولاً: معالجة قطع الفيديو (TS) عند طلبها من المشغل
if (isset($_GET['ts'])) {
    $ts_url = urldecode($_GET['ts']);
    
    // إرسال هيدرز الفيديو الصحيحة للمتصفح
    header("Content-Type: video/mp2t");
    header("Access-Control-Allow-Origin: *");
    header("Cache-Control: no-cache, must-revalidate");

    $opts = [
        "http" => [
            "header" => "User-Agent: VLC/3.0.18\r\n",
            "timeout" => 10
        ]
    ];
    $context = stream_context_create($opts);
    
    // فتح الرابط ونقل البيانات بقطع صغيرة (Chunks) لضمان ثبات الصورة
    $handle = @fopen($ts_url, "rb", false, $context);
    if ($handle) {
        while (!feof($handle)) {
            echo fread($handle, 16384); // 16KB لكل قطعة
            flush(); // إرسال القطعة فوراً للمتصفح
        }
        fclose($handle);
    }
    exit;
}

// ثانياً: إذا تم فتح الملف مباشرة، نقوم بجلب قائمة التشغيل (m3u8) وعرض المشغل
$opts = ["http" => ["header" => "User-Agent: VLC/3.0.18\r\n"]];
$context = stream_context_create($opts);
$content = @file_get_contents($remote_url, false, $context);

// إذا كان الطلب قادم من مشغل الفيديو (HLS Request)
if (isset($_GET['m3u8'])) {
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *");
    
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>beIN SPORTS Live</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        #video { width: 100%; height: auto; max-width: 100%; box-shadow: 0 0 50px rgba(0,0,0,0.5); }
    </style>
</head>
<body>

    <video id="video" controls autoplay playsinline poster="https://via.placeholder.com/1280x720/000000/FFFFFF?text=D-Service+Loading..."></video>

    <script>
        var video = document.getElementById('video');
        var videoSrc = 'b44.php?m3u8=true'; // طلب قائمة التشغيل المعدلة

        if (Hls.isSupported()) {
            var hls = new Hls({
                enableWorker: true,
                lowLatencyMode: true,
                backBufferLength: 60
            });
            hls.loadSource(videoSrc);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                video.play();
            });
            
            // إعادة المحاولة عند حدوث أخطاء في الشبكة لتقليل التقطيع
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
            // دعم Safari (آيفون) الأصلي
            video.src = videoSrc;
        }
    </script>
</body>
</html>
