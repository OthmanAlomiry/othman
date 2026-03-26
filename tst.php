<?php
// يمكنك تغيير الرابط هنا في أي وقت دون البحث داخل كود HTML
$stream_url = "https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8";
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مشغل البث المباشر</title>
    
    <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
    
    <style>
        body { background-color: #1a1a1a; color: white; font-family: sans-serif; }
        .container { max-width: 900px; margin: 40px auto; text-align: center; }
        .video-js { margin: 0 auto; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
    </style>
</head>
<body>

<div class="container">
    <h2>بث مباشر: MBC Masr 2</h2>
    <hr>
    
    <div style="direction: ltr;">
        <video
            id="live-player"
            class="video-js vjs-fluid vjs-default-skin vjs-big-play-button"
            controls
            preload="auto"
            data-setup='{}'>
            <source src="<?php echo $stream_url; ?>" type="application/x-mpegURL">
        </video>
    </div>
</div>

<script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>

</body>
</html>
