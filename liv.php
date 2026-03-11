<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الرياضة - جلب جوجل المباشر</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root { --main: #e11d48; --bg: #f8fafc; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); }
        header { background: #fff; padding: 20px; text-align: center; font-size: 22px; font-weight: bold; border-bottom: 4px solid var(--main); box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 15px; max-width: 1200px; margin: auto; }
        .card { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; }
        .c-head { padding: 12px; background: #fcfcfc; display: flex; justify-content: space-between; font-weight: bold; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; }
        .play-btn { width: 90%; margin: 12px auto; display: block; background: var(--main); color: #fff; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; }
        .sch { padding: 12px; border-top: 1px solid #eee; min-height: 80px; }
        .m-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eee; font-size: 13px; align-items: center; }
        .m-time { color: var(--main); font-weight: bold; font-size: 11px; background: #fff1f2; padding: 2px 5px; border-radius: 4px; }
        .loading { text-align: center; color: #999; font-size: 12px; padding: 15px; }
    </style>
</head>
<body>

<header>📺 بث مباشر - جدول مباريات اليوم</header>

<div class="grid">
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card">
        <div class="c-head">
            <span>beIN Sport <?php echo $i; ?></span>
            <span style="color:#22c55e">● مباشر</span>
        </div>
        <video id="vid<?php echo $i; ?>" controls></video>
        <button class="play-btn" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ تشغيل الآن</button>
        <div class="sch" id="sch-<?php echo $i; ?>">
            <?php
            // نستخدم مصدر RSS الخاص بجوجل نيوز للرياضة لضمان الجلب
            $rss_url = "https://news.google.com/rss/search?q=matches+today+bein+sports&hl=ar&gl=SA&ceid=SA:ar";
            $xml = @simplexml_load_file($rss_url);
            
            if ($xml && isset($xml->channel->item)) {
                $count = 0;
                foreach ($xml->channel->item as $item) {
                    if ($count >= 2) break; // عرض مباراتين لكل قناة
                    // تنظيف النص من جوجل
                    $title = (string)$item->title;
                    $title = explode(" - ", $title)[0]; 
                    
                    echo '<div class="m-row">';
                    echo '<span>' . htmlspecialchars($title) . '</span>';
                    echo '<span class="m-time">بث اليوم</span>';
                    echo '</div>';
                    $count++;
                }
            } else {
                echo '<div class="loading">لا توجد مباريات منقولة حالياً</div>';
            }
            ?>
        </div>
    </div>
    <?php endfor; ?>
</div>

<script>
function play(id, s) {
    var v = document.getElementById(id);
    if (Hls.isSupported()) {
        var hls = new Hls(); hls.loadSource(s); hls.attachMedia(v); v.play();
    } else { v.src = s; v.play(); }
}
</script>
</body>
</html>
