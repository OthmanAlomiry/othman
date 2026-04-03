<!DOCTYPE html>
<html>
<head>
    <title>Live Player</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
</head>
<body>
    <video id="video" controls autoplay style="width:100%; max-width:800px;"></video>

    <script>
      var video = document.getElementById('video');
      var videoSrc = 'http://apk.arabic-ch.space/live/006900/index.m3u8';

      if (Hls.isSupported()) {
        var hls = new Hls();
        hls.loadSource(videoSrc);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
          video.play();
        });
      }
      else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = videoSrc;
        video.addEventListener('loadedmetadata', function() {
          video.play();
        });
      }
    </script>
</body>
</html>
