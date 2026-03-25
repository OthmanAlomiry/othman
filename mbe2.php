<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://vjs.zencdn.net/8.3.0/video-js.css" rel="stylesheet" />
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; overflow: hidden; }
        .video-js { width: 100%; height: 100%; }
        /* إخفاء رسائل الخطأ المزعجة */
        .vjs-error-display { display: none !important; }
    </style>
</head>
<body>

    <video id="mbc-player" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" crossorigin="anonymous" autoplay>
        <source src="https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8" type="application/x-mpegURL">
    </video>

    <script src="https://vjs.zencdn.net/8.3.0/video.min.js"></script>
    <script>
        var player = videojs('mbc-player', {
            html5: {
                vhs: {
                    overrideNative: true,
                    withCredentials: false
                },
                nativeVideoTracks: false,
                nativeAudioTracks: false,
                nativeTextTracks: false
            }
        });

        player.ready(function() {
            var promise = player.play();
            if (promise !== undefined) {
                promise.catch(function(error) {
                    // في حال تطلب تفاعل المستخدم للبدء
                    console.log("Autoplay prevented");
                });
            }
        });

        // محاولة إعادة الاتصال في حال انقطع البث
        player.on('error', function() {
            setTimeout(function() {
                player.src({
                    src: 'https://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-masr-2/754931856515075b0aabf0e583495c68/index.m3u8',
                    type: 'application/x-mpegURL'
                });
                player.play();
            }, 2000);
        });
    </script>
</body>
</html>
