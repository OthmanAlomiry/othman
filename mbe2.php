<?php
// رابط البث المباشر
$remote_url = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8";

if (isset($_GET['segment'])) {
    // جلب قطع الفيديو الصغيرة (TS)
    $seg_url = $_GET['segment'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $seg_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $data = curl_exec($ch);
    curl_close($ch);
    header("Content-Type: video/mp2t");
    echo $data;
    exit;
}

// جلب ملف الـ M3U8 الرئيسي وتعديله
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $remote_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$m3u8_content = curl_exec($ch);
curl_close($ch);

if ($m3u8_content) {
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *");
    
    // تصحيح مسارات قطع الفيديو لكي تمر عبر هذا الملف
    $base_path = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/";
    
    // استبدال روابط الملفات برابط يمر عبر هذا السيرفر
    $m3u8_content = preg_replace('/(index.*\.ts)/', "mbe2.php?segment=" . urlencode($base_path) . "$1", $m3u8_content);
    $m3u8_content = preg_replace('/(index.*\.m3u8)/', "mbe2.php?segment=" . urlencode($base_path) . "$1", $m3u8_content);

    // إذا كان الطلب قادماً من iframe
    if (!isset($_GET['raw'])) {
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
            var hls = new Hls();
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
    echo $m3u8_content;
}
?>
