<?php
// 1. رابط البث الأصلي
$original_url = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8";

// 2. إذا طلب المتصفح الرابط من خلال البروكسي
if (isset($_GET['proxy'])) {
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *"); // السماح بتشغيل البث
    echo file_get_contents($original_url);
    exit;
}

// 3. رابط ملفنا الحالي مع خاصية البروكسي
$proxy_url = "tst.php?proxy=1";
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بث مباشر</title>
    <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
    <style>
        body { background-color: #000; color: #fff; text-align: center; font-family: sans-serif; }
        .vjs-fluid { max-width: 800px; margin: 20px auto; }
    </style>
</head>
<body>

    <h3>بث مباشر: MBC Masr 2</h3>

    <div style="direction: ltr;">
        <video id="player" class="video-js vjs-default-skin vjs-fluid vjs-big-play-button" controls preload="auto">
            <source src="<?php echo $proxy_url; ?>" type="application/x-mpegURL">
        </video>
    </div>

    <script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>
    
    <script>
        // محاولة تشغيل تلقائي وتجاوز بعض قيود CORS
        var player = videojs('player');
        player.ready(function() {
            player.play().catch(function(error) {
                console.log("التشغيل التلقائي يحتاج تفاعل من المستخدم");
            });
        });
    </script>
</body>
</html>
