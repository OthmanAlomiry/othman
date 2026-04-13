<?php
/**
 * بوابة الرياضة - متجر الخدمة الرقمية
 * تم دمج مفتاح API المستخرج: 49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9
 */

// --- إعدادات API المباريات (RapidAPI) ---
$apiKey = '49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9'; 
$dateToday = date('Y-m-d');
$url = "https://api-football-v1.p.rapidapi.com/v3/fixtures?date=$dateToday";

// خريطة البطولات ومعرفاتها (يدعم آسيا والنخبة)
$leagues_map = [
    850 => ['name' => 'دوري أبطال آسيا للنخبة', 'channel' => 'beIN AFC', 'ch_num' => '11'],
    307 => ['name' => 'دوري روشن السعودي', 'channel' => 'SSC 1', 'ch_num' => '12'],
    39  => ['name' => 'الدوري الإنجليزي', 'channel' => 'beIN Sport 1', 'ch_num' => '1'],
    140 => ['name' => 'الدوري الإسباني', 'channel' => 'beIN Sport 3', 'ch_num' => '3'],
    2   => ['name' => 'دوري أبطال أوروبا', 'channel' => 'beIN Sport 2', 'ch_num' => '2'],
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

// جلب البيانات
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
    <title>بث المباريات - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); --border: rgba(255, 255, 255, 0.1); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); color: #fff; padding-top: 140px; }
        
        /* هيدر المتجر */
        .header { position: fixed; top: 0; width: 100%; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid var(--border); padding: 20px 0; text-align: center; }
        .promo { font-size: 12px; color: #ccc; margin-bottom: 15px; }
        .btns { display: flex; justify-content: center; gap: 10px; }
        .btn-social { padding: 8px 18px; border-radius: 50px; text-decoration: none; font-size: 11px; color: #fff; font-weight: bold; }

        /* قسم المباريات */
        .matches-container { padding: 15px; }
        .match-card { background: var(--glass); border: 1px solid var(--border); border-radius: 15px; padding: 15px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; }
        .team { text-align: center; flex: 1; }
        .team img { width: 40px; height: 40px; object-fit: contain; }
        .team span { display: block; font-size: 11px; margin-top: 5px; }
        .score-info { flex: 1; text-align: center; }
        .score { font-size: 22px; font-weight: 900; }
        .league-name { font-size: 9px; color: #00ff87; margin-bottom: 5px; display: block; }

        /* قنوات البث */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; padding: 15px; }
        .channel-card { background: var(--glass); border-radius: 15px; overflow: hidden; border: 1px solid var(--border); }
        .channel-title { padding: 10px; font-size: 12px; background: rgba(0,0,0,0.4); }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .play-btn { width: 90%; margin: 10px auto; display: block; padding: 12px; background: var(--main); border: none; color: #fff; border-radius: 8px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

<div class="header">
    <div class="promo">متجر الخدمة الرقمية - مشاهدة ممتعة بدون إعلانات</div>
    <div class="btns">
        <a href="https://wa.me/966505571164" class="btn-social" style="background:#25d366;"><i class="fab fa-whatsapp"></i> واتساب</a>
        <a href="https://t.me/d_s_pro" class="btn-social" style="background:#0088cc;"><i class="fab fa-telegram"></i> تليجرام</a>
    </div>
</div>

<div class="matches-container">
    <h3 style="margin-right:10px;"><i class="fas fa-clock"></i> مباريات اليوم</h3>
    <?php 
    if (!empty($match_data['response'])):
        foreach ($match_data['response'] as $m): 
            $lgID = $m['league']['id'];
            if (isset($leagues_map[$lgID])): 
                $status = $m['fixture']['status']['short'];
    ?>
    <div class="match-card">
        <div class="team">
            <img src="<?= $m['teams']['home']['logo'] ?>">
            <span><?= translate_name($m['teams']['home']['name']) ?></span>
        </div>
        <div class="score-info">
            <span class="league-name"><?= $leagues_map[$lgID]['name'] ?></span>
            <div class="score">
                <?php if(in_array($status, ['1H','2H','LIVE'])): ?>
                    <?= ($m['goals']['home'] ?? 0) ?> - <?= ($m['goals']['away'] ?? 0) ?>
                <?php else: ?>
                    <?= date('h:i A', strtotime($m['fixture']['date'])) ?>
                <?php endif; ?>
            </div>
            <div style="font-size:9px; color:<?= $status=='LIVE'?'#ff4d4d':'#aaa' ?>"><?= $status=='LIVE'?'مباشر':$status ?></div>
        </div>
        <div class="team">
            <img src="<?= $m['teams']['away']['logo'] ?>">
            <span><?= translate_name($m['teams']['away']['name']) ?></span>
        </div>
    </div>
    <?php endif; endforeach; else: ?>
        <p style="text-align:center; opacity:0.5;">لا توجد مباريات كبرى مجدولة حالياً</p>
    <?php endif; ?>
</div>

<div class="grid">
    <?php for($i = 1; $i <= 10; $i++): ?>
    <div class="channel-card">
        <div class="channel-title">beIN Sports <?= $i ?></div>
        <video id="vid<?= $i ?>" playsinline controls></video>
        <button class="play-btn" onclick="startStream('vid<?= $i ?>', 'b<?= $i ?>.php')">تشغيل البث</button>
    </div>
    <?php endfor; ?>
    
    <div class="channel-card">
        <div class="channel-title">beIN AFC (آسيا)</div>
        <video id="vid11" playsinline controls></video>
        <button class="play-btn" onclick="startStream('vid11', 'b11.php')">تشغيل البث</button>
    </div>
</div>

<script>
function startStream(id, file) {
    const video = document.getElementById(id);
    if (Hls.isSupported()) {
        const hls = new Hls();
        hls.loadSource(file);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => video.play());
    } else {
        video.src = file;
        video.play();
    }
}
</script>
</body>
</html>
