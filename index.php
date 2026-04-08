<?php
session_start();
error_reporting(0);

// --- بيانات السحابة والـ API عثمان ---
$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';
$FOOTBALL_API_KEY = '1e8894c7e946b851b36fea7f3a3c4d98'; // مفتاحك الجديد الشغال

// --- إعدادات الكاش للمباريات عثمان ---
$cache_file = 'matches_cache.json';
$cache_time = 3600; // ساعة كاملة للحفاظ على الطلبات

function getMatchesData($key, $file, $time) {
    if (file_exists($file) && (time() - filemtime($file) < $time)) {
        $data = json_decode(file_get_contents($file), true);
        if (!empty($data)) return $data;
    }
    $today = date('Y-m-d');
    $ch = curl_init("https://v3.football.api-sports.io/fixtures?date=$today&timezone=Asia/Riyadh");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["x-apisports-key: $key"]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $result = json_decode($response, true);
    
    if (isset($result['response']) && !empty($result['response'])) {
        file_put_contents($file, json_encode($result['response']));
        return $result['response'];
    }
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

// دالة فحص الإشعارات
if(isset($_GET['check_notify'])) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $BIN_ID . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $API_KEY_BIN, "X-Bin-Meta: false"]);
    $res = json_decode(curl_exec($ch), true);
    echo json_encode($res['notification']);
    exit;
}

// نظام المتواجدين
$visitors_file = 'online_visitors.txt';
if (isset($_GET['fetch_visitors'])) {
    $session_id = session_id(); $time = time();
    $data = file_exists($visitors_file) ? unserialize(file_get_contents($visitors_file)) : [];
    $data[$session_id] = $time;
    foreach ($data as $id => $last_time) { if ($time - $last_time > 120) unset($data[$id]); }
    file_put_contents($visitors_file, serialize($data));
    echo count($data); exit; 
}
$online_now = file_exists($visitors_file) ? count(unserialize(file_get_contents($visitors_file))) : 1;

// جلب بيانات السحابة والقنوات
function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    $res = curl_exec($ch);
    return json_decode($res, true);
}

$cloud = getCloudFullData($BIN_ID, $API_KEY_BIN);
$all_channels = $cloud['custom_channels'] ?: [];
$active_sections = array_filter($cloud['sections'] ?: [], function($s) { return $s['status'] == 'show'; });
$news = $cloud['news_ticker'] ?: ['text' => '', 'status' => 'hide'];

// جلب وفلترة المباريات المطلوبة عثمان
$all_matches = getMatchesData($FOOTBALL_API_KEY, $cache_file, $cache_time);
$target_leagues = [307, 140, 39, 135, 78, 61, 281, 17, 2, 3]; // معرفات الدوريات المطلوبة
$my_matches = array_filter($all_matches, function($m) use ($target_leagues) {
    return in_array($m['league']['id'], $target_leagues);
});

function filterSection($channels, $sec) {
    return array_filter($channels, function($c) use ($sec) { return (isset($c['section']) && trim(strtolower($c['section'])) == trim(strtolower($sec))); });
}
date_default_timezone_set('Asia/Riyadh');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({pageLanguage: 'en', includedLanguages: 'ar', layout: google.translate.TranslateElement.InlineLayout.SIMPLE, autoDisplay: false}, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <style>
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); --sky: #0ea5e9; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; }

        /* شاشة الدخول */
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #050c14; display: flex; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; }

        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        
        /* كرت المباراة عثمان */
        .match-card { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 20px; padding: 15px 10px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; }
        .m-league { position: absolute; top: -10px; right: 15px; background: var(--sky); font-size: 8px; padding: 3px 10px; border-radius: 50px; font-weight: 900; }
        .m-team { flex: 1.2; text-align: center; font-size: 11px; font-weight: 900; }
        .m-team img { width: 32px; height: 32px; display: block; margin: 0 auto 8px; }
        .m-info { flex: 0.8; text-align: center; border-left: 1px solid rgba(255,255,255,0.05); border-right: 1px solid rgba(255,255,255,0.05); }
        .m-score { font-size: 20px; font-weight: 900; letter-spacing: 2px; }
        .m-time { font-size: 12px; color: var(--sky); font-weight: 900; }

        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item img { width: 26px; height: 26px; display: block; margin: 0 auto 4px; }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; }

        .grid { padding: 15px; }
        .channel-section { display: none; width: 100%; }
        .channel-section.active { display: block; animation: slideUp 0.6s ease-out; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); margin-bottom: 20px; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; }
        .play-btn { width: 90%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 8px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 14px; border-radius: 50px; font-weight: 900; cursor: pointer; }
        
        .visitors-badge-float { position: fixed; bottom: 25px; left: 25px; width: 45px; height: 45px; background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 5000; }
        .notify-bell-btn { position: fixed; bottom: 85px; left: 25px; width: 45px; height: 45px; background: var(--main); border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 5000; cursor: pointer; }
        
        /* إخفاء شريط ترجمة قوقل العلوي عثمان */
        .goog-te-banner-frame.skiptranslate { display: none !important; }
        body { top: 0px !important; }
        #google_translate_element { display: none; }
    </style>
</head>
<body>

<div id="google_translate_element"></div>

<div id="pro-intro">
    <h2 style="color:white; font-weight:900;">الخدمة الرقمية</h2>
</div>

<div class="main-container">
    <div class="notify-bell-btn"><i class="fas fa-bell"></i></div>
    <div class="visitors-badge-float">
        <i class="fas fa-users" style="font-size:12px; color:#22c55e;"></i>
        <span id="realtime-visitors" style="font-size:10px; font-weight:900;"><?= $online_now ?></span>
    </div>

    <div class="header-fixed">
        <div style="display:flex; justify-content:space-around; margin-bottom:15px; padding:0 10px;">
            <a href="https://wa.me/966505571164" style="background:#25d366; color:white; padding:6px 15px; border-radius:20px; font-size:10px; text-decoration:none; font-weight:bold;">واتساب</a>
            <a href="https://t.me/d_s_pro" style="background:#0088cc; color:white; padding:6px 15px; border-radius:20px; font-size:10px; text-decoration:none; font-weight:bold;">تليجرام</a>
        </div>

        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('matches', this)"><img src="https://cdn-icons-png.flaticon.com/512/33/33736.png"><span>المباريات</span></div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches" class="channel-section active">
            <div style="margin-bottom: 20px; font-weight: 900; color: var(--sky); border-right: 4px solid var(--sky); padding-right: 12px;">أهم مباريات اليوم</div>
            <?php if(empty($my_matches)): ?>
                <div style="text-align:center; padding:50px; opacity:0.3;">لا توجد مباريات حالياً.</div>
            <?php else: foreach($my_matches as $m): 
                $status = $m['fixture']['status']['short'];
                $isLive = in_array($status, ['1H', '2H', 'HT', 'ET', 'P']);
            ?>
                <div class="match-card">
                    <div class="m-league"><?= $m['league']['name'] ?></div>
                    <div class="m-team"><img src="<?= $m['teams']['home']['logo'] ?>"><span><?= $m['teams']['home']['name'] ?></span></div>
                    <div class="m-info">
                        <div class="m-score" style="<?= $isLive ? 'color:var(--main)' : '' ?>"><?= $m['goals']['home'] ?? 0 ?> - <?= $m['goals']['away'] ?? 0 ?></div>
                        <div class="m-time"><?= $isLive ? 'مباشر' : date("H:i", $m['fixture']['timestamp']) ?></div>
                    </div>
                    <div class="m-team"><img src="<?= $m['teams']['away']['logo'] ?>"><span><?= $m['teams']['away']['name'] ?></span></div>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)">تشغيل البث المباشر</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <footer>جميع الحقوق محفوظة © 2026</footer>
</div>

<script>
window.addEventListener('load', () => { setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1500); });

function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}

function startStream(boxId, file, btn) {
    let vBox = document.getElementById(boxId);
    vBox.innerHTML = `<iframe src="${file}?autoplay=1" allow="autoplay; encrypted-media" allowfullscreen></iframe>`;
}

setInterval(() => { 
    fetch(window.location.pathname + '?fetch_visitors=1')
    .then(res => res.text())
    .then(count => { document.getElementById('realtime-visitors').innerText = count; }); 
}, 5000);
</script>
</body>
</html>
