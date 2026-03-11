<?php
/**
 * دالة جلب بيانات المباريات وتصنيفها
 * تعتمد هذه الدالة على سحب البيانات من مصدر رياضي عام وتصفيتها حسب اسم القناة
 */
function get_matches_for_all_channels() {
    // نستخدم مصدر بيانات رياضي (كمثال نستخدم صفحة جدول مباريات عامة)
    $url = "https://www.beinsports.com/ar-mena/tv-guide"; 
    $options = [
        "http" => [
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36\r\n" .
                        "Accept-Language: ar,en;q=0.9\r\n"
        ]
    ];
    
    $context = stream_context_create($options);
    $html = @file_get_contents($url, false, $context);
    
    $all_channels_data = [];
    
    if ($html) {
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        $xpath = new DOMXPath($dom);
        
        // جلب جميع بطاقات الأحداث الرياضية
        $items = $xpath->query("//div[contains(@class, 'event-card')] | //article[contains(@class, 'event-card')]");

        foreach ($items as $item) {
            $text = $item->nodeValue;
            for ($i = 1; $i <= 9; $i++) {
                // تصفية النتائج: إذا ذكر اسم القناة (beIN SPORTS 1-9)
                if (stripos($text, "beIN SPORTS $i") !== false) {
                    $time = $xpath->query(".//time", $item)->item(0)->nodeValue ?? "--:--";
                    $title = $xpath->query(".//h3 | .//h4", $item)->item(0)->nodeValue ?? "حدث رياضي";
                    
                    $all_channels_data[$i][] = [
                        "time" => trim($time),
                        "title" => trim($title)
                    ];
                }
            }
        }
    }
    return $all_channels_data;
}

$matches = get_matches_for_all_channels();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>البث المباشر و جدول المباريات</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <style>
        :root {
            --primary-color: #e11d48;
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
        }

        body {
            margin: 0;
            font-family: 'Tajawal', sans-serif;
            background: var(--bg-color);
            color: var(--text-main);
            padding-bottom: 50px;
        }

        header {
            background: var(--card-bg);
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-bottom: 3px solid var(--primary-color);
        }

        .top-text {
            padding: 20px;
            font-size: 14px;
            background: #fff;
            margin: 15px auto;
            max-width: 800px;
            border-radius: 12px;
            line-height: 1.8;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            text-align: center;
        }

        .top-text a {
            color: var(--primary-color);
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin-top: 5px;
            font-size: 18px;
        }

        .channels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }

        .card {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            padding: 12px;
            font-size: 18px;
            font-weight: bold;
            background: #f8fafc;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .channel-label {
            background: var(--primary-color);
            color: white;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        video {
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #000;
            display: block;
        }

        .btn-play {
            width: 90%;
            margin: 15px auto;
            display: block;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: var(--primary-color);
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-play:hover {
            background: #be123c;
        }

        /* تنسيق جدول المباريات */
        .matches-section {
            background: #fafafa;
            border-top: 1px solid #eee;
            padding: 15px;
        }

        .matches-title {
            font-size: 13px;
            font-weight: bold;
            color: #64748b;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .match-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .match-item:last-child {
            border-bottom: none;
        }

        .m-time {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 12px;
            background: #fff1f2;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .m-name {
            font-size: 13px;
            color: #334155;
            flex: 1;
            margin-right: 10px;
            text-align: right;
        }

        .no-matches {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            padding: 10px 0;
        }

        @media (max-width: 480px) {
            .channels-grid {
                grid-template-columns: 1fr;
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<header>📺 بوابة الرياضة المباشرة</header>

<div class="top-text">
    جميع القنوات تعمل بجودة عالية على الجوال والشاشات<br>
    للاشتراك في الباقة الكاملة وتفعيل جميع القنوات:<br>
    <a href="https://wa.me/966505571164">📱 واتساب: 0505571164</a>
</div>

<div class="channels-grid">
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card">
        <div class="card-header">
            <span>beIN Sport <?php echo $i; ?></span>
            <span class="channel-label">LIVE</span>
        </div>
        
        <video id="v<?php echo $i; ?>" poster="https://via.placeholder.com/400x225/000000/FFFFFF?text=beIN+Sports+<?php echo $i; ?>" controls></video>
        
        <button class="btn-play" onclick="playStream('v<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ تشغيل البث المباشر</button>
        
        <div class="matches-section">
            <div class="matches-title">📅 جدول مباريات اليوم:</div>
            <div class="matches-list">
                <?php if(isset($matches[$i]) && !empty($matches[$i])): ?>
                    <?php foreach($matches[$i] as $match): ?>
                        <div class="match-item">
                            <span class="m-name"><?php echo htmlspecialchars($match['title']); ?></span>
                            <span class="m-time"><?php echo htmlspecialchars($match['time']); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-matches">لا توجد مباريات مجدولة حالياً</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endfor; ?>
</div>

<script>
function playStream(videoId, streamUrl) {
    var video = document.getElementById(videoId);
    
    if (Hls.isSupported()) {
        var hls = new Hls();
        hls.loadSource(streamUrl);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
            video.play();
        });
    } 
    else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = streamUrl;
        video.addEventListener('loadedmetadata', function() {
            video.play();
        });
    }
}
</script>

</body>
</html>
