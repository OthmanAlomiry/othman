FROM php:8.2-apache

# 1. إنشاء ملف 123.php مباشرة داخل السيرفر لضمان وجوده
RUN echo '<?php \
header("Access-Control-Allow-Origin: *"); \
?> \
<!DOCTYPE html> \
<html> \
<head> \
    <title>Live Stream</title> \
    <link href="https://vjs.zencdn.net/7.20.3/video-js.css" rel="stylesheet" /> \
    <style>body { margin: 0; background: #000; }</style> \
</head> \
<body> \
    <video id="my-video" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" style="width:100vw;height:100vh;"> \
        <source src="http://135.125.109.73:9000/beinsport4_.m3u8" type="application/x-mpegURL"> \
    </video> \
    <script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script> \
</body> \
</html>' > /var/www/html/index.php

# 2. إعطاء الصلاحيات
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

EXPOSE 80
