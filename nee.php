<?php
session_start();
error_reporting(0);

// --- إعدادات جدول المباريات (عثمان) ---
date_default_timezone_set('Asia/Riyadh');
$FOOTBALL_API_KEY = '273aaeb61360452588653ffea820cc19'; 
$url_api = 'https://api.football-data.org/v4/matches';
$date_get = date('Y-m-d');

// مصفوفة الترجمة الشاملة (عثمان)
$translate = [
    // الحالات
    'TIMED' => 'لم تبدأ', 'FINISHED' => 'انتهت', 'IN_PLAY' => 'مباشر', 'PAUSED' => 'بين الشوطين',
    'POSTPONED' => 'مؤجلة', 'CANCELLED' => 'ملغاة', 'SUSPENDED' => 'متوقفة',

    // أندية الدوري السعودي (للتوافق)
    'Al Hilal' => 'الهلال', 'Al Nassr' => 'النصر', 'Al Ittihad' => 'الاتحاد', 'Al Ahli' => 'الأهلي',
    
    // أندية عالمية
    'Real Madrid CF' => 'ريال مدريد', 'FC Barcelona' => 'برشلونة', 'Manchester City FC' => 'مانشستر سيتي',
    'Liverpool FC' => 'ليفربول', 'Arsenal FC' => 'أرسنال', 'FC Bayern München' => 'بايرن ميونخ',
    'Paris Saint-Germain FC' => 'باريس سان جيرمان'
];

// ماب الدوريات المتوفرة في الخطة المجانية لـ football-data
$league_settings = array(
    2021 => array('name' => 'الدوري الإنجليزي', 'ch_name' => 'beIN Premium'),
    2014 => array('name' => 'الدوري الإسباني', 'ch_name' => 'beIN Sports'),
    2019 => array('name' => 'الدوري الإيطالي', 'ch_name' => 'AD Sports'),
    2002 => array('name' => 'الدوري الألماني', 'ch_name' => 'beIN Sports'),
    2015 => array('name' => 'الدوري الفرنسي', 'ch_name' => 'beIN Sports'),
    2001 => array('name' => 'دوري أبطال أوروبا', 'ch_name' => 'beIN Sports'),
    2013 => array('name' => 'الدوري البرازيلي', 'ch_name' => 'SSC')
);

function getFixturesWithCache($date, $key, $url) {
    $cache_file = "cache_" . $date . ".json";
    $expire_time = 600; 
    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $expire_time)) {
        return json_decode(file_get_contents($cache_file), true);
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . "?dateFrom=$date&dateTo=$date");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: ' . $key]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER => false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    $results = (isset($data['matches'])) ? $data['matches'] : array();
    if (!empty($results)) { file_put_contents($cache_file, json_encode($results)); }
    return $results;
}

$fixtures = getFixturesWithCache($date_get, $FOOTBALL_API_KEY, $url_api);
$ordered_matches = array();
if (!empty($fixtures)) {
    foreach ($fixtures as $f) {
        $id = (int)$f['competition']['id'];
        if (isset($league_settings[$id])) { $ordered_matches[$id][] = $f; }
    }
}

$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID_BIN = '69d6f6b636566621a891e6c1';

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

function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$cloud = getCloudFullData($BIN_ID_BIN, $API_KEY_BIN);
$all_channels = isset($cloud['custom_channels']) ? $cloud['custom_channels'] : [];
$active_sections = array_filter($cloud['sections'] ?: [], function($s) { return $s['status'] == 'show'; });
$news = isset($cloud['news_ticker']) ? $cloud['news_ticker'] : ['text' => '', 'status' => 'hide'];

function filterSection($channels, $sec) {
    return array_filter($channels, function($c) use ($sec) { return (isset($c['section']) && trim(strtolower($c['section'])) == trim(strtolower($sec))); });
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; transition: 0.3s; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; transition: 0.4s ease-in-out; }
        @media (orientation: landscape) { body { padding-top: 190px; } .main-container { max-width: 95% !important; } .header-fixed { max-width: 95% !important; } .match-grid-container { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; } .league-sep { grid-column: span 2; } }
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e293b 0%, #050c14 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.2); }
        .ad-popup-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); z-index: 8000; display: none; align-items: center; justify-content: center; }
        .ad-popup-content { position: relative; width: 85%; max-width: 320px; animation: popZoom 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .ad-popup-image { width: 100%; border-radius: 20px; border: 2px solid var(--glass-border); box-shadow: 0 10px 40px rgba(0,0,0,0.5); display: block; }
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; text-align: center; border: 1.5px solid #ffffff; }
        .btn-wa { background: #25d366; } .btn-tg { background: #0088cc; }
        .news-ticker { background: rgba(225, 29, 72, 0.15); height: 32px; overflow: hidden; margin-bottom: 10px; display: flex; align-items: center; position: relative; }
        .ticker-label { background: var(--main); color: #fff; padding: 0 12px; height: 100%; display: flex; align-items: center; font-size: 10px; font-weight: 900; z-index: 10; position: absolute; right: 0; }
        .ticker-move { display: flex; white-space: nowrap; animation: ticker-infinite 45s linear infinite; width: max-content; }
        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); backdrop-filter: blur(10px); margin-bottom: 15px; width: 100%; }
        .league-sep { background: rgba(255, 255, 255, 0.07); padding: 10px 15px; border-radius: 10px; font-size: 11px; font-weight: 900; margin: 15px 0 10px; border-right: 4px solid var(--main); color: #fff; }
        .m-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 10px; }
        .m-team-col { flex: 1; font-size: 10px; font-weight: 700; color: #fff; text-align: center; }
        .m-team-col img { width: 28px; height: 28px; display: block; margin: 0 auto 6px; }
        .m-time-box { flex: 0.6; background: rgba(225, 29, 72, 0.1); border: 1px solid rgba(225, 29, 72, 0.2); padding: 6px; border-radius: 10px; text-align: center; font-weight: 900; font-size: 12px; color: var(--main); }
        .play-btn { width: 92%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 10px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--glass-border); padding: 12px; border-radius: 12px; font-weight: 900; cursor: pointer; font-size: 13px; }
        @keyframes ticker-infinite { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }
    </style>
</head>
<body>

<div id="pro-intro">
    <div class="loader-content"><div class="intro-icon-box"><i class="fas fa-play-circle"></i></div><h2 class="intro-title" style="color:#fff;">الخدمة الرقمية</h2></div>
</div>

<div class="ad-popup-overlay" id="adPopup">
    <div class="ad-popup-content">
        <div class="ad-close-btn" onclick="closeAd()" style="position:absolute; top:-10px; right:-10px; background:var(--main); color:#fff; width:30px; height:30px; border-radius:50%; text-align:center; cursor:pointer; line-height:30px;">X</div>
        <img src="https://files.catbox.moe/7pik4r.png" class="ad-popup-image" alt="إعلان">
        <a href="https://wa.me/966505571164" style="display:block; text-align:center; background:#25d366; color:#fff; padding:10px; border-radius:10px; margin-top:10px; text-decoration:none; font-weight:bold;">اشترك الآن عبر واتساب</a>
    </div>
</div>

<div class="main-container">
    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
        </div>

        <?php if($news['status'] == 'show'): ?>
        <div class="news-ticker"><span class="ticker-label">تنبيهات</span><div class="ticker-wrap"><div class="ticker-move"><span style="padding:0 50px;"><?= $news['text'] ?></span><span style="padding:0 50px;"><?= $news['text'] ?></span></div></div></div>
        <?php endif; ?>

        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('matches_table', this)"><img src="https://cdn-icons-png.flaticon.com/512/833/833593.png" style="width:25px; filter: brightness(0) invert(1);"><span>جدول المباريات</span></div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>" style="width:25px;"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches_table" class="channel-section active">
            <div class="match-grid-container" style="padding:15px;">
            <?php if(empty($ordered_matches)): ?>
                <p style="text-align:center; opacity:0.5; padding:20px;">لا توجد مباريات هامة مسجلة اليوم</p>
            <?php else: foreach($league_settings as $id => $set): if(isset($ordered_matches[$id])): ?>
                <div class="league-sep"><?= $set['name'] ?></div>
                <?php foreach($ordered_matches[$id] as $m): 
                    $h_name = $translate[$m['homeTeam']['name']] ?? $m['homeTeam']['shortName'] ?? $m['homeTeam']['name'];
                    $a_name = $translate[$m['awayTeam']['name']] ?? $m['awayTeam']['shortName'] ?? $m['awayTeam']['name'];
                    $status = $translate[$m['status']] ?? $m['status'];
                    $score_h = $m['score']['fullTime']['home'] ?? 0;
                    $score_a = $m['score']['fullTime']['away'] ?? 0;
                ?>
                    <div class="card">
                        <div class="m-row">
                            <div class="m-team-col"><img src="<?= $m['homeTeam']['crest'] ?>"><?= $h_name ?></div>
                            <div class="m-time-box">
                                <?= ($m['status'] == 'TIMED') ? date("H:i", strtotime($m['utcDate'] . ' +3 hours')) : $score_h.'-'.$score_a ?>
                                <br><small style="font-size:7px;"><?= $status ?></small>
                            </div>
                            <div class="m-team-col"><img src="<?= $m['awayTeam']['crest'] ?>"><?= $a_name ?></div>
                        </div>
                    </div>
            <?php endforeach; endif; endforeach; endif; ?>
            </div>
        </div>

        <?php foreach($active_sections as $s): ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section" style="display:none; padding:15px;">
            <?php $channels = filterSection($all_channels, $s['key']); foreach($channels as $ch): ?>
            <div class="card">
                <div class="video-box" id="vid-<?= $ch['id'] ?>" style="background:#000; aspect-ratio:16/9;"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>')">تشغيل <?= $ch['name'] ?></button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <footer style="text-align:center; padding:30px; font-size:9px; opacity:0.4;">جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>
</div>

<script>
function closeAd() { document.getElementById('adPopup').style.display = 'none'; }
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.style.display = 'none');
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).style.display = 'block';
    element.classList.add('active');
}
function startStream(boxId, file) {
    document.getElementById(boxId).innerHTML = `<iframe src="${file}?autoplay=1" style="width:100%; height:100%; border:none;" allowfullscreen></iframe>`;
}
window.addEventListener('load', () => { 
    setTimeout(() => { 
        document.getElementById('pro-intro').classList.add('intro-hide');
        setTimeout(() => { document.getElementById('adPopup').style.display = 'flex'; }, 500);
    }, 1500); 
});
</script>
</body>
</html>
