<?php
$original_url = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8";

if (isset($_GET['proxy'])) {
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: VLC/3.0.18 LibVLC/3.0.18\r\n" .
                        "Referer: https://www.shahid.net/\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $data = file_get_contents($original_url, false, $context);
    
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *");
    
    // تصحيح الروابط الداخلية لتمر عبر البروكسي أيضاً إذا كانت نسبية
    $base_url = dirname($original_url) . "/";
    echo str_replace("index", $base_url . "index", $data);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
</head>
<body style="background:#000; margin:0;">
    <video id="v" controls autoplay playsinline style="width:100%; height:100vh;"></video>
    <script>
        var video = document.getElementById('v');
        if (Hls.isSupported()) {
            var hls = new Hls();
            hls.loadSource('tst.php?proxy=1');
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function() { video.play(); });
        } else {
            video.src = 'tst.php?proxy=1';
        }
    </script>
</body>
</html>
