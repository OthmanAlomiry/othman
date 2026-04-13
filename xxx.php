<?php
// --- إعدادات API الجديدة باستخدام مفتاحك المستخرج ---
$apiKey = '49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9'; // تم استخراج المفتاح من صورتك
$dateToday = date('Y-m-d');
$url = "https://api-football-v1.p.rapidapi.com/v3/fixtures?date=$dateToday";

// خريطة البطولات ومعرفاتها في الـ API
$leagues_map = [
    850 => ['name' => 'دوري أبطال آسيا للنخبة', 'channel' => 'beIN AFC', 'ch_num' => '11'],
    307 => ['name' => 'دوري روشن السعودي', 'channel' => 'SSC 1', 'ch_num' => '12'],
    39  => ['name' => 'الدوري الإنجليزي', 'channel' => 'beIN Sport 1', 'ch_num' => '1'],
    140 => ['name' => 'الدوري الإسباني', 'channel' => 'beIN Sport 3', 'ch_num' => '3'],
    2   => ['name' => 'دوري أبطال أوروبا', 'channel' => 'beIN Sport 2', 'ch_num' => '2'],
    135 => ['name' => 'الدوري الإيطالي', 'channel' => 'STARZPLAY 1', 'ch_num' => '10'],
];

function translate_name($text) {
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $response = @file_get_contents($url);
    if($response) {
        $result = json_decode($response, true);
        return $result[0][0][0] ?? $text;
    }
    return $text;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-RapidAPI-Host: api-football-v1.p.rapidapi.com",
    "X-RapidAPI-Key: $apiKey"
]);
$response = curl_exec($ch);
curl_close($ch);
$match_data = json_decode($response, true);
date_default_timezone_set('Asia/Riyadh');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - متجر الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg-deep); padding-top: 175px; color: #e2e8f0; overflow-x: hidden; }
        .header-fixed-container { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(5, 12, 20, 0.9); backdrop-filter: blur(20px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .match-scroll { display: flex; gap: 12px; overflow-x: auto; padding: 10px 15px; scrollbar-width: none; }
        .match-card { min-width: 270px; background: var(--glass); border-radius: 20px; padding: 15px; border: 1px solid var(--glass-border); transition: 0.3s; }
        .m-league { font-size: 10px; color: #00ff87; font-weight: 800; margin-bottom: 10px; text-align: center; }
        .match-main { display: flex; align-items: center; justify-content: center; gap: 10px; }
        .team img { width: 35px; height: 35px; object-fit: contain; }
        .team-name { font-size: 10px; font-weight: 700; display: block; margin-top: 5px; }
        .m-score { font-size: 1.5rem; font-weight: 900; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; padding: 15px; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); }
        video { width: 100%; aspect-ratio: 16/9; background: #000; }
        .play-btn { width: 90%; margin: 10px auto; display: block; padding: 12px; border-radius: 50px; border: none; background: var(--main); color: #fff; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

<div class="header-fixed-container">
    <div style="font-size:11px; margin-bottom:10px;">هذه الصفحة مقدمة من متجر الخدمة الرقمية مجاناً وبدون إعلانات</div>
    <div style="display:flex; justify-content:center; gap:10px;">
        <a href="https://wa.me/966505571164" style="background:#25d366; color:white; padding:5px 15px; border-radius:20px; text-decoration:none; font-size:10px;">واتساب</a>
        <a href="https://t.me/d_s_pro" style="background:#0088cc; color:white; padding:5px 15px; border-radius:20px; text-decoration:none; font-size:10px;">تليجرام</a>
    </div>
</div>

<div class="matches-section">
    <div style="padding:0 15px; font-weight:900;"><i class="fas fa-trophy" style="color:#f1c40f"></i> مباريات ونتائج اليوم</div>
    <div class="match-scroll">
        <?php 
        if (!empty($match_data['response'])):
            foreach ($match_data['response'] as $m): 
                $leagueID = $m['league']['id'];
                if (isset($leagues_map[$leagueID])): 
                    $status = $m['fixture']['status']['short'];
                    $homeScore = $m['goals']['home'] ?? 0;
                    $awayScore = $m['goals']['away'] ?? 0;
        ?>
                <div class="match-card">
                    <div class="m-league"><?= $leagues_map[$leagueID]['name'] ?></div>
                    <div class="match-main">
                        <div class="team" style="flex:1; text-align:center;">
                            <img src="<?= $m['teams']['home']['logo'] ?>">
                            <span class="team-name"><?= translate_name($m['teams']['home']['name']) ?></span>
                        </div>
                        <div style="flex:0.8; text-align:center;">
                            <?php if(in_array($status, ['1H','2H','HT','ET','P','LIVE'])): ?>
                                <div class="m-score"><?= $homeScore ?> - <?= $awayScore ?></div>
                                <span style="color:red; font-size:9px; font-weight:900;">بث مباشر</span>
                            <?php elseif($status == 'FT'): ?>
                                <div class="m-score"><?= $homeScore ?> - <?= $awayScore ?></div>
                                <span style="font-size:9px;">انتهت</span>
                            <?php else: ?>
                                <div style="font-size:12px; font-weight:bold; color:#f1c40f;"><?= date('h:i A', strtotime($m['fixture']['date'])) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="team" style="flex:1; text-align:center;">
                            <img src="<?= $m['teams']['away']['logo'] ?>">
                            <span class="team-name"><?= translate_name($m['teams']['away']['name']) ?></span>
                        </div>
                    </div>
                </div>
        <?php endif; endforeach; else: ?>
            <p style="padding:20px;">لا توجد مباريات كبرى اليوم</p>
        <?php endif; ?>
    </div>
</div>

<div class="grid">
    <?php for($i = 1; $i <= 10; $i++): ?>
    <div class="card" id="ch-row-<?= $i ?>">
        <div style="padding:10px; background:rgba(0,0,0,0.3); font-size:12px;">beIN Sport <?= $i ?></div>
        <video id="vid<?= $i ?>" playsinline controls></video>
        <button class="play-btn" onclick="robustPlay('vid<?= $i ?>', 'b<?= $i ?>.php')">بدء البث المباشر</button>
    </div>
    <?php endfor; ?>

    <div class="card" id="ch-row-11">
        <div style="padding:10px; background:rgba(0,0,0,0.3); font-size:12px;">beIN Sports AFC</div>
        <video id="vid11" playsinline controls></video>
        <button class="play-btn" onclick="robustPlay('vid11', 'b11.php')">بدء البث المباشر</button>
    </div>
</div>

<footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية</footer>

<script>
function robustPlay(videoId, url) {
    const video = document.getElementById(videoId);
    if (Hls.isSupported()) {
        const hls = new Hls(); hls.loadSource(url); hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => video.play());
    } else { video.src = url; video.play(); }
}
</script>
</body>
</html>
