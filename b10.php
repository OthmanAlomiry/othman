<?php
// تشفير الرابط بـ Base64 ليبقى بعيداً عن أعين المتصفح العادي
$secret_link = base64_encode("http://sportfet.shop/AD1/tracks-v1a1/mono.m3u8");
?>

<link href="https://vjs.zencdn.net/7.20.3/video-js.css" rel="stylesheet" />
<script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script>

<video id="my-video" class="video-js vjs-default-skin" controls preload="auto" width="640" height="264">
</video>

<script>
    // فك التشفير برمجياً وتشغيله
    var encoded = "<?php echo $secret_link; ?>";
    var decoded = atob(encoded); // تحويل Base64 إلى نص عادي
    
    var player = videojs('my-video');
    player.src({
        src: decoded,
        type: 'application/x-mpegURL'
    });
</script>
