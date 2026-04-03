<script>
    var video = document.getElementById('video');
    var videoSrc = 'https://bein4.othman1405.workers.dev';

    if (Hls.isSupported()) {
        var hls = new Hls({
            enableWorker: true,
            lowLatencyMode: true, // تفعيل وضع التأخير المنخفض
            backBufferLength: 30, // تقليل الذاكرة الخلفية لتسريع البث المباشر
            manifestLoadingMaxRetry: 10,
            levelLoadingMaxRetry: 10
        });
        hls.loadSource(videoSrc);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
            video.play();
        });
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = videoSrc;
    }
</script>
