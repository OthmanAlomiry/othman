<?php
// الرابط غير الرسمي الذي زودتني به
$remote_url = "http://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8";

if (isset($_GET['stream'])) {
    // محاكاة دقيقة لـ VLC لتجاوز الحظر
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: VLC/3.0.18 LibVLC/3.0.18\r\n" .
                        "Accept: */*\r\n" .
                        "Range: bytes=0-\r\n" .
                        "Connection: close\r\n"
        ]
    ];

    $context = stream_context_create($opts);
    $data = @file_get_contents($remote_url, false, $context);

    if ($data === FALSE) {
        header("HTTP/1.1 403 Forbidden");
        die("السيرفر الأصلي حظر الوصول من سيرفر الاستضافة.");
    }

    // تعديل الروابط الداخلية لملفات الـ .ts لتصبح روابط كاملة
    $base = "http://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/";
    $data = preg_replace('/([\w\.-]+\.ts)/', $base . '$1', $data);

    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *");
    echo $data;
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>D-Service Player</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body { background: #000; margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        video { width: 100%; max-width: 800px; background: #222; }
    </style>
</head>
<body>

<video id="video" controls autoplay playsinline></video>

<script>
  var video = document.getElementById('video');
  // الرابط يشير لنفس الملف مع بارامتر السيرفر
  var videoSrc = window.location.href.split('?')[0] + '?stream=1';

  if (Hls.isSupported()) {
    var hls = new Hls({
        xhrSetup: function (xhr, url) {
            xhr.withCredentials = false; // لتجنب مشاكل CORS
        }
    });
    hls.loadSource(videoSrc);
    hls.attachMedia(video);
  } 
  else if (video.canPlayType('application/vnd.apple.mpegurl')) {
    video.src = videoSrc;
  }
</script>
</body>
</html>
