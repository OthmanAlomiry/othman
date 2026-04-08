<?php
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// --- إعدادات الـ API والمفاتيح عثمان ---
$FOOTBALL_API_KEY = '1e8894c7e946b851b36fea7f3a3c4d98';
$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';

// --- وظيفة الترجمة التلقائية عثمان ---
function translateText($text) {
    if(empty($text)) return $text;
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $res = @file_get_contents($url);
    if($res){
        $res = json_decode($res, true);
        return $res[0][0][0] ?: $text;
    }
    return $text;
}

// --- نظام الكاش وجلب المباريات عثمان ---
function getFixturesWithCache($key) {
    $cache_file = 'matches_cache.json';
    $cache_time = 3600; // ساعة واحدة بالثواني

    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
        return json_decode(file_get_contents($cache_file), true);
    }

    $date = date('Y-m-d');
    $ch = curl_init("https://v3.football.api-sports.io/fixtures?date=$date");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["x-apisports-key: $key"],
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true)['response'] ?: [];
    file_put_contents($cache_file, json_encode($data));
    return $data;
}

// الدوريات المطلوبة فقط عثمان
$allowed_leagues = [
    307, // الدوري السعودي
    140, // الدوري الإسباني
    39,  // الدوري الإنجليزي
    135, // الدوري الإيطالي
    78,  // الدوري الألماني
    61,  // الدوري الفرنسي
    17,  // دوري أبطال آسيا
    19,  // دوري أبطال آسيا 2 (AFC Cup)
    2,   // دوري أبطال أوروبا
    3    // الدوري الأوروبي
];

$fixtures = getFixturesWithCache($FOOTBALL_API_KEY);

// --- بقية دوال السحابة كما هي عثمان ---
if(isset($_GET['check_notify'])) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $BIN_ID . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER => ["X-Master-Key: " . $API_KEY_BIN, "X-Bin-Meta: false"]);
    $res = json_decode(curl_exec($ch), true);
    $notify = $res['notification'];
    if (isset($notify['time']) && (time() - $notify['time'] > 172800)) { echo json_encode(null); } 
    else { echo json_encode($notify); }
    exit;
}

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
    curl_setopt($ch, CURLOPT_HTTPHEADER => ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$cloud = getCloudFullData($BIN_ID, $API_KEY_BIN);
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
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); --blue-grad: linear-gradient(45deg, #0ea5e9, #fff); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; transition: 0.3s; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; transition: max-width 0.4s; }
        
        /* شاشة الدخول */
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e293b 0%, #050c14 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.2); }
        .intro-icon-box { width: 100px; height: 100px; background: var(--main); border-radius: 30%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 50px rgba(225, 29, 72, 0.5); animation: bounceIn 1s ease-out; }
        .intro-icon-box i { font-size: 50px; color: white; }
        .intro-title { margin-top: 25px; color: white; font-weight: 900; font-size: 24px; }
        @keyframes bounceIn { 0% { opacity: 0; transform: scale(0.3); } 50% { opacity: 1; transform: scale(1.1); } 100% { transform: scale(1); } }

        /* الهيدر عثمان */
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        
        /* شريط المباريات الأفقي عثمان */
        .match-slider { display: flex; gap: 10px; overflow-x: auto; padding: 10px 15px; scrollbar-width: none; background: rgba(255,255,255,0.02); margin-bottom: 10px; }
        .match-slider::-webkit-scrollbar { display: none; }
        .mini-match-card { min-width: 140px; background: var(--glass); border: 1px solid var(--glass-border); border-radius: 15px; padding: 8px; text-align: center; flex-shrink: 0; }
        .m-league { font-size: 7px; opacity: 0.5; margin-bottom: 4px; display: block; white-space: nowrap; overflow: hidden; }
        .m-teams { display: flex; align-items: center; justify-content: space-around; gap: 5px; }
        .m-teams img { width: 22px; height: 22px; object-fit: contain; }
        .m-time { font-size: 10px; font-weight: 900; color: #38bdf8; }

        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item img { width: 26px; height: 26px; margin-bottom: 4px; }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; display: block; }

        /* الأيقونات الجانبية عثمان */
        .notify-bell-btn { position: fixed; bottom: 85px; left: 25px; width: 45px; height: 45px; background: var(--main); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; z-index: 5000; box-shadow: 0 8px 20px rgba(225, 29, 72, 0.4); cursor: pointer; }
        .visitors-badge-float { position: fixed; bottom: 25px; left: 25px; width: 45px; height: 45px; background: rgba(34, 197, 94, 0.15); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 5000; border: 1.5px solid #22c55e; }
        .visitors-badge-float span { font-size: 11px; font-weight: 900; color: #fff; }

        .grid { padding: 15px; }
        .channel-section { display: none; width: 100%; }
        .channel-section.active { display: block; animation: slideUp 0.6s ease-out; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); margin-bottom: 20px; }
        .c-head { padding: 12px; background: rgba(0,0,0,0.4); display: flex; justify-content: space-between; align-items: center; }
        .name-badge { padding: 5px 12px; border-radius: 10px; font-size: 10px; font-weight: 900; color: #000; background: var(--blue-grad); }
        .play-btn { width: 90%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 8px; background: var(--main); color: #fff; border: none; padding: 14px; border-radius: 50px; font-weight: 900; cursor: pointer; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; background-image: url('mg/wel.GIF'); background-size: cover; background-position: center; }
        iframe { width: 100%; height: 100%; border: none; }
        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; text-align: center; border: 1.5px solid #fff; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        footer { text-align: center; padding: 40px; font-size: 10px; opacity: 0.5; }
    </style>
</head>
<body>

<div id="pro-intro">
    <div class="loader-content">
        <div class="intro-icon-box"><i class="fas fa-play-circle"></i></div>
        <h2 class="intro-title">الخدمة الرقمية</h2>
    </div>
</div>

<div class="main-container">
    <div class="notify-bell-btn" onclick="toggleNotifyPanel()"><i class="fas fa-bell"></i><div class="notify-dot" id="n-dot" style="display:none; position:absolute; top:2px; right:2px; width:10px; height:10px; background:#22c55e; border-radius:50%;"></div></div>
    <div class="visitors-badge-float"><i class="fas fa-users" style="color:#22c55e; font-size:12px;"></i><span id="realtime-visitors"><?php echo $online_now; ?></span></div>

    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:#FFFC00; color:#000">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn" style="background:#000">تويتر</a>
        </div>

        <?php if($news['status'] == 'show'): ?>
        <marquee style="color:#fff; font-size:11px; margin-bottom:5px;"><?= $news['text'] ?></marquee>
        <?php endif; ?>

        <div class="match-slider">
            <?php 
            $count_m = 0;
            foreach($fixtures as $f) {
                if(in_array($f['league']['id'], $allowed_leagues)) {
                    $count_m++;
                    echo '<div class="mini-match-card">
                            <span class="m-league">'.translateText($f['league']['name']).'</span>
                            <div class="m-teams">
                                <img src="'.$f['teams']['home']['logo'].'">
                                <span class="m-time">'.date("H:i", $f['fixture']['timestamp']).'</span>
                                <img src="'.$f['teams']['away']['logo'].'">
                            </div>
                          </div>';
                }
            }
            if($count_m == 0) echo '<span style="font-size:10px; opacity:0.5; width:100%;">لا توجد مباريات هامة اليوم</span>';
            ?>
        </div>

        <div class="category-tabs">
            <?php $count = 0; foreach($active_sections as $s): ?>
                <div class="cat-item <?= ($count == 0 ? 'active' : '') ?>" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php $count++; endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <?php $count = 0; foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section <?= ($count == 0 ? 'active' : '') ?>">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="c-head"><div class="name-badge"><?= $ch['name'] ?></div><div style="color:#ff4d4d; font-size:10px; font-weight:900;"><i class="fas fa-circle" style="font-size:7px;"></i> مباشر</div></div>
                <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)"><i class="fas fa-play"></i> تشغيل البث المباشر</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php $count++; endforeach; ?>
    </div>

    <footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>
</div>

<script>
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}

function startStream(boxId, file, btn) {
    document.getElementById(boxId).innerHTML = `<iframe src="${file}?autoplay=1&muted=1" allowfullscreen></iframe>`;
    btn.innerHTML = '<i class="fas fa-check-circle"></i> تم الاتصال بالبث';
}

window.addEventListener('load', () => { 
    setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 2000); 
});

setInterval(() => { 
    fetch(window.location.pathname + '?fetch_visitors=1')
    .then(res => res.text())
    .then(count => { document.getElementById('realtime-visitors').innerText = count; }); 
}, 5000);
</script>
</body>
</html>
