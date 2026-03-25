<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بث مباشر - IPTV Player</title>
    <link href="https://vjs.zencdn.net/7.20.3/video-js.css" rel="stylesheet" />
    <style>
        body { background-color: #121212; color: white; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        h1 { text-align: center; color: #00ff88; }
        
        /* مشغل الفيديو */
        .video-section { position: sticky; top: 10px; z-index: 100; background: #000; padding: 10px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.5); margin-bottom: 30px; }
        .video-js { margin: auto; width: 100%; max-height: 500px; }

        /* شبكة القنوات */
        .channels-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; }
        .channel-card { background: #1e1e1e; border: 1px solid #333; padding: 15px; border-radius: 8px; text-align: center; cursor: pointer; transition: 0.3s; }
        .channel-card:hover { background: #00ff88; color: #000; transform: translateY(-5px); }
        .channel-card img { width: 50px; height: 50px; object-fit: contain; margin-bottom: 10px; display: block; margin-left: auto; margin-right: auto; }
        .channel-name { font-size: 0.9rem; font-weight: bold; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    </style>
</head>
<body>

<div class="container">
    <h1>📺 منصة البث المباشر</h1>

    <div class="video-section">
        <video id="my-video" class="video-js vjs-big-play-centered" controls preload="auto" width="640" height="360" data-setup='{}'>
            <p class="vjs-no-js">للمشاهدة، يرجى تفعيل جافا سكريبت.</p>
        </video>
    </div>

    <div class="channels-grid">
        <?php
        $url = "https://iptv-org.github.io/iptv/index.m3u";
        $content = file_get_contents($url);

        // تقسيم الملف إلى أسطر
        $lines = explode("\n", $content);
        $channels = [];

        for ($i = 0; $i < count($lines); $i++) {
            if (strpos($lines[$i], '#EXTINF') !== false) {
                // استخراج اسم القناة
                $name = explode(',', $lines[$i])[1] ?? 'قناة غير معروفة';
                // استخراج رابط الصورة (Logo) إذا وجد
                preg_match('/tvg-logo="([^"]+)"/', $lines[$i], $logo_match);
                $logo = $logo_match[1] ?? 'https://via.placeholder.com/50?text=TV';
                // السطر التالي هو رابط القناة
                $link = trim($lines[$i+1]);
                
                if (filter_var($link, FILTER_VALIDATE_URL)) {
                    echo '<div class="channel-card" onclick="playChannel(\''.$link.'\')">';
                    echo '<img src="'.$logo.'" alt="logo">';
                    echo '<div class="channel-name">'.$name.'</div>';
                    echo '</div>';
                }
            }
        }
        ?>
    </div>
</div>

<script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script>
<script>
    var player = videojs('my-video');

    function playChannel(url) {
        player.src({
            src: url,
            type: 'application/x-mpegURL'
        });
        player.play();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>

</body>
</html>
