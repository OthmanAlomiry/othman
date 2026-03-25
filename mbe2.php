<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; overflow: hidden; }
        video { width: 100%; height: 100%; object-fit: contain; }
    </style>
</head>
<body>
    <video id="video" controls playsinline autoplay></video>
    <script>
        // رابط القناة الذي يعمل في VLC
        var videoSrc = 'https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8';
        var video = document.getElementById('video');

        if (Hls.isSupported()) {
            var hls = new Hls({
                xhrSetup: function (xhr, url) {
                    xhr.withCredentials = false;
                }
            });
            hls.loadSource(videoSrc);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                video.play();
            });
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = videoSrc;
            video.addEventListener('loadedmetadata', function() {
                video.play();
            });
        }
    </script>
</body>
</html>
