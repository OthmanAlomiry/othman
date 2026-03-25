<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; overflow: hidden; }
        .container { width: 100%; height: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <video id="player" playsinline controls data-poster="mg/mbc.png"></video>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const video = document.querySelector('#player');
            // الرابط الذي يعمل في VLC
            const source = 'https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8';
            
            const defaultOptions = {};

            if (Hls.isSupported()) {
                const hls = new Hls({
                    // إعدادات لمحاكاة طلب VLC تماماً
                    enableWorker: true,
                    lowLatencyMode: true,
                    xhrSetup: function(xhr, url) {
                        xhr.withCredentials = false;
                    }
                });
                hls.loadSource(source);
                hls.attachMedia(video);
                window.hls = hls;
            } else {
                video.src = source;
            }

            const player = new Plyr(video, defaultOptions);
        });
    </script>
</body>
</html>
