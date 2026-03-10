<!DOCTYPE html>
<html>
<head>
    <title>Live Stream</title>
    <link href="https://vjs.zencdn.net/7.20.3/video-js.css" rel="stylesheet" />
    <style>
        body { margin: 0; background: #000; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .video-js { width: 100% !important; height: 100% !important; }
    </style>
</head>
<body>
    <video id="my-video" class="video-js vjs-default-skin" controls preload="auto">
        <source src="http://135.125.109.73:9000/beinsport4_.m3u8" type="application/x-mpegURL">
    </video>

    <script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script>
    <script>
        var player = videojs('my-video');
        player.play();
    </script>
</body>
</html>
