<?php
// دالة لجلب المباريات من موقع beIN وتصنيفها
function get_matches_data() {
    $url = "https://www.beinsports.com/ar-mena/tv-guide";
    $options = ["http" => ["header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"]];
    $context = stream_context_create($options);
    $html = @file_get_contents($url, false, $context);
    
    $matches_by_channel = [];
    if ($html) {
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        $xpath = new DOMXPath($dom);
        $items = $xpath->query("//div[contains(@class, 'event-card')]");

        foreach ($items as $item) {
            $nodeText = $item->nodeValue;
            for ($i = 1; $i <= 9; $i++) {
                if (strpos($nodeText, "beIN SPORTS $i") !== false) {
                    $time = $xpath->query(".//time", $item)->item(0)->nodeValue ?? '';
                    $title = $xpath->query(".//h3", $item)->item(0)->nodeValue ?? '';
                    $matches_by_channel[$i][] = [
                        "time" => trim($time),
                        "title" => trim($title)
                    ];
                }
            }
        }
    }
    return $matches_by_channel;
}

$all_matches = get_matches_data();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>البث المباشر</title>
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<style>
    body{ margin:0; font-family:Tajawal, Tahoma; background:#ffffff; text-align:center; }
    header{ background:#ffffff; padding:20px; font-size:26px; font-weight:bold; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
    .top-text{ padding:15px; font-size:15px; background:#f3f4f6; line-height:1.8; }
    .top-text a{ color:#e11d48; font-weight:bold; text-decoration:none; }
    .channels{ display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:25px; padding:30px; max-width:1200px; margin:auto; }
    .card{ background:white; border-radius:14px; overflow:hidden; box-shadow:0 6px 18px rgba(0,0,0,0.12); transition:0.3s; display: flex; flex-direction: column; }
    .card:hover{ transform:translateY(-6px); box-shadow:0 10px 25px rgba(0,0,0,0.18); }
    .title{ padding:12px; font-size:17px; font-weight:bold; background:#f3f4f6; }
    video{ width:100%; background:black; }
    .play{ margin:15px; padding:10px 18px; border:none; border-radius:8px; background:#e11d48; color:white; font-size:15px; cursor:pointer; }
    .play:hover{ background:#be123c; }

    /* تنسيق جدول المباريات */
    .matches-container {
        background: #fdfdfd;
        border-top: 1px solid #eee;
        padding: 10px;
        font-size: 13px;
        text-align: right;
        flex-grow: 1;
    }
    .match-row {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        border-bottom: 1px dashed #eee;
        direction: rtl;
    }
    .m-time { color: #e11d48; font-weight: bold; min-width: 50px; }
    .m-title { color: #333; margin-right: 8px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .no-matches { color: #999; font-style: italic; font-size: 12px; }
</style>
</head>
<body>

<header>
📺 البث المباشر للقنوات الرياضية
</header>

<div class="top-text">
جميع القنوات تعمل على الجوال والشاشات الذكية<br>
للاشتراك في الباقة الكاملة تواصل واتساب<br>
<a href="https://wa.me/966505571164">0505571164</a>
</div>

<div class="channels">
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card">
        <div class="title">beIN Sport <?php echo $i; ?></div>
        <video id="v<?php echo $i; ?>" controls></video>
        <button class="play" onclick="playStream('v<?php echo $i; ?>','b<?php echo $i; ?>.php')">تشغيل القناة</button>
        
        <div class="matches-container">
            <strong>📅 مباريات اليوم:</strong>
            <?php if(isset($all_matches[$i])): ?>
                <?php foreach($all_matches[$i] as $match): ?>
                    <div class="match-row">
                        <span class="m-time"><?php echo $match['time']; ?></span>
                        <span class="m-title"><?php echo $match['title']; ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-matches">لا توجد مباريات مسجلة حالياً</div>
            <?php endif; ?>
        </div>
    </div>
    <?php endfor; ?>
</div>

<script>
function playStream(videoId, stream) {
    var video = document.getElementById(videoId);
    if (Hls.isSupported()) {
        var hls = new Hls();
        hls.loadSource(stream);
        hls.attachMedia(video);
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = stream;
    }
    video.play();
}
</script>

</body>
</html>
