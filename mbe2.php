<?php
// رابط القناة الأصلي
$original_url = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8";

// إذا كان الطلب قادم لجلب قطعة فيديو (TS)
if (isset($_GET['ts'])) {
    $ts_url = base64_decode($_GET['ts']);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ts_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $data = curl_exec($ch);
    curl_close($ch);
    header("Content-Type: video/mp2t");
    echo $data;
    exit;
}

// جلب ملف الـ M3U8 الرئيسي
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $original_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$content = curl_exec($ch);
curl_close($ch);

if ($content && !isset($_GET['raw'])) {
    // عرض صفحة المشغل
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>body,html{margin:0;padding:0;width:100%;height:100%;background:#000;overflow:hidden;}video{width:100%;height:100%;}</style>
</head>
<body>
    <video id="video" controls playsinline autoplay></video>
    <script>
        var video = document.getElementById('video');
        var source = window.location.href + (window.location.href.indexOf('?') > -1 ? '&raw=1' : '?raw=1');
        if (Hls.isSupported()) {
            var hls = new Hls({xhrSetup: function(xhr){ xhr.withCredentials = false; }});
            hls.loadSource(source);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function() { video.play(); });
        }
    </script>
</body>
</html>
<?php
    exit;
}

// إذا كان الطلب لجلب ملف الـ M3U8 (البيانات الخام)
if ($content) {
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *");
    
    $base_path = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/";
    
    // استبدال أسماء الملفات بروابط تمر عبر سيرفرك ومشفرة بـ base64
    $content = preg_replace_callback('/(index.*\.ts|index.*\.m3u8)/', function($m) use ($base_path) {
        return "mbe2.php?ts=" . base64_encode($base_path . $m[1]);
    }, $content);

    echo $content;
}
?>
