<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; overflow: hidden; }
        video { width: 100%; height: 100%; object-fit: contain; }
    </style>
</head>
<body>
    <video id="video" controls playsinline autoplay></video>
    <script>
        var video = document.getElementById('video');
        // رابط بث بديل ومستقر لـ MBC مصر 2
        var videoSrc = 'https://shls-mbc-masr-2-prod-dub.mshcdn.net/out/v1/754931856515075b0aabf0e583495c68/index.m3u8';

        if (Hls.isSupported()) {
            var hls = new Hls({
                xhrSetup: function(xhr) {
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
            video.play();
        }
    </script>
</body>
</html>
