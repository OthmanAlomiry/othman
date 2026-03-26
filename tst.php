<?php
// 1. رابط البث
$stream_url = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8";

// 2. وظيفة البروكسي المتقدمة
if (isset($_GET['proxy'])) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $stream_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    // إرسال هيدرز وهمية لإقناع السيرفر أننا VLC أو متصفح رسمي
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: VLC/3.0.18 LibVLC/3.0.18',
        'Referer: https://www.shahid.net/',
        'Origin: https://www.shahid.net'
    ]);

    $response = curl_exec($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    header("Content-Type: " . $contentType);
    header("Access-Control-Allow-Origin: *");
    echo $response;
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Streaming</title>
    <script src="https://cdn.jsdelivr.net/npm/@clappr/player@latest/dist/clappr.min.js"></script>
    <style>
        body { background: #000; margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        #player { width: 100%; max-width: 800px; }
    </style>
</head>
<body>

<div id="player"></div>

<script>
    var player = new Clappr.Player({
        source: "tst.php?proxy=1",
        parentId: "#player",
        autoPlay: true,
        width: '100%',
        height: '450px',
        mimeType: "application/x-mpegURL"
    });
</script>

</body>
</html>
