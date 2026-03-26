<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Live Stream Player</title>
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <style>
        body { background: #000; margin: 0; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .container { width: 100%; max-width: 800px; }
    </style>
</head>
<body>

<div class="container">
    <video id="player" playsinline controls data-poster="https://bitdash-a.akamaihd.net/content/sintel/poster.png">
        <source src="https://bitdash-a.akamaihd.net/content/MI201109210084_1/m3u8s/f08e80da-bf1d-4e3d-8899-f0f6155f6efa.m3u8" type="application/x-mpegURL">
    </video>
</div>

<script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const video = document.querySelector('video');
        const source = video.getElementsByTagName('source')[0].src;
        
        const defaultOptions = {};

        if (Hls.isSupported()) {
            const hls = new Hls();
            hls.loadSource(source);
            hls.attachMedia(video);
            window.hls = hls;
        }

        const player = new Plyr(video, defaultOptions);
    });
</script>
</body>
</html>
