<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MBC Masr 2</title>
    <script src="https://cdn.jsdelivr.net/npm/@clappr/player@latest/dist/clappr.min.js"></script>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; overflow: hidden; }
        #player { width: 100%; height: 100%; }
    </style>
</head>
<body>
    <div id="player"></div>
    <script>
        var player = new Clappr.Player({
            source: 'https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8',
            parentId: '#player',
            preload: 'auto',
            autoPlay: true,
            width: '100%',
            height: '100%',
            mimeType: 'application/vnd.apple.mpegurl',
            hlsjsConfig: {
                xhrSetup: function(xhr, url) {
                    xhr.withCredentials = false;
                }
            }
        });
    </script>
</body>
</html>
