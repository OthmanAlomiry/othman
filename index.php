<?php
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// --- بيانات السحابة والـ API عثمان ---
$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';
$FOOTBALL_API_KEY = 'ef02886bbd68ecb3bdfc630f4546eb97';

// دالة جلب المباريات عثمان
function getFixtures($date, $key) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://v3.football.api-sports.io/fixtures?date=$date",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array("x-apisports-key: $key"),
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);
    return $data['response'] ?: array();
}

// دالة الترجمة عثمان
function translateText($text) {
    if(empty($text)) return $text;
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $res = @file_get_contents($url);
    if($res){ $res = json_decode($res, true); return $res[0][0][0] ?: $text; }
    return $text;
}

// دالة فحص الإشعارات (AJAX)
if(isset($_GET['check_notify'])) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $BIN_ID . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Master-Key: " . $API_KEY_BIN, "X-Bin-Meta: false"));
    $res = json_decode(curl_exec($ch), true);
    echo json_encode($res['notification']);
    exit;
}

// نظام عداد المتواجدين
$visitors_file = 'online_visitors.txt';
if (isset($_GET['fetch_visitors'])) {
    $session_id = session_id(); $time = time();
    $data = file_exists($visitors_file) ? unserialize(file_get_contents($visitors_file)) : array();
    $data[$session_id] = $time;
    foreach ($data as $id => $lt) { if ($time - $lt > 120) unset($data[$id]); }
    file_put_contents($visitors_file, serialize($data));
    echo count($data); exit; 
}
$online_now = file_exists($visitors_file) ? count(unserialize(file_get_contents($visitors_file))) : 1;

// جلب بيانات القنوات
function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Master-Key: " . $key, "X-Bin-Meta: false"));
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$cloud = getCloudFullData($BIN_ID, $API_KEY_BIN);
$all_channels = isset($cloud['custom_channels']) ? $cloud['custom_channels'] : array();
$active_sections = array_filter($cloud['sections'] ?: array(), function($s) { return $s['status'] == 'show'; });
$news = isset($cloud['news_ticker']) ? $cloud['news_ticker'] : array('text' => '', 'status' => 'hide');

// جلب مباريات اليوم والدوريات المحددة (نفس tap.php) عثمان
$fixtures = getFixtures(date('Y-m-d'), $FOOTBALL_API_KEY);
$allowed_leagues = array(307, 2, 3, 39, 140, 135, 78, 61);

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

        /* شريط المباريات الصغير عثمان */
        .match-slider { display: flex; gap: 10px; overflow-x: auto; padding: 10px 15px; scrollbar-width: none; background: rgba(255,255,255,0.02); margin-top: 15px; -webkit-overflow-scrolling: touch; }
        .match-slider::-webkit-scrollbar { display: none; }
        .mini-card { min-width: 130px; background: var(--glass); border: 1px solid var(--glass-border); border-radius: 15px; padding: 8px; text-align: center; }
        .mini-teams { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 4px; }
        .mini-teams img { width: 20px; height: 20px; object-fit: contain; }
        .mini-time { font-size: 11px; font-weight: 900; color: #0ea5e9; }
        .mini-league { font-size: 8px; opacity: 0.4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* التصميم الأساسي الخاص بك عثمان */
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e293b 0%, #050c14 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.2); }
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .notify-bell-btn { position: fixed; bottom: 85px; left: 25px; width: 45px; height: 45px; background: var(--main); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; z-index: 5000; cursor: pointer; }
        .visitors-badge-float { position: fixed; bottom: 25px; left: 25px; width: 45px; height: 45px; background: rgba(34, 197, 94, 0.15); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 5000; border: 1.5px solid #22c55e; }
        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; text-align: center; border: 1.5px solid #fff; }
        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); transform: scale(1.03); }
        .cat-item img { width: 26px; height: 26px; margin-bottom: 4px; }
        .grid { padding: 15px; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); margin-bottom: 20px; }
        .c-head { padding: 12px; background: rgba(0,0,0,0.4); display: flex; justify-content: space-between; align-items: center; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 14px; border-radius: 50px; font-weight: 900; cursor: pointer; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; background-image: url('mg/wel.GIF'); background-size: cover; background-position: center; position: relative; }
        iframe { width: 100%; height: 100%; border: none; }
        footer { text-align: center; padding: 40px; font-size: 10px; opacity: 0.5; }
    </style>
</head>
<body>

<div id="pro-intro">
    <div style="width:100px; height:100px; background:var(--main); border-radius:30%; display:flex; align-items:center; justify-content:center;"><i class="fas fa-play-circle" style="font-size:50px; color:white;"></i></div>
    <h2 style="color:white; margin-top:25px;">الخدمة الرقمية</h2>
</div>

<div class="main-container">
    <div class="notify-bell-btn" onclick="toggleNotifyPanel()"><i class="fas fa-bell"></i></div>
    <div class="visitors-badge-float"><i class="fas fa-users"></i><span id="realtime-visitors" style="font-size:11px; font-weight:900; color:#fff;"><?php echo $online_now; ?></span></div>
    <div class="bg-pattern"></div>

    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa" style="background:#25d366;">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg" style="background:#0088cc;">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-sn" style="background:#FFFC00; color:#000;">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-tw" style="background:#000;">تويتر</a>
        </div>
        
        <?php if($news['status'] == 'show'): ?>
        <div class="news-ticker"><span class="ticker-label">تنبيهات</span><div class="ticker-wrap"><div class="ticker-move"><span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span></div></div></div>
        <?php endif; ?>

        <div class="match-slider">
            <?php 
            $found = false;
            foreach($fixtures as $f) {
                if(in_array($f['league']['id'], $allowed_leagues)) {
                    $found = true;
                    echo '<div class="mini-card">
                            <div class="mini-league">'.translateText($f['league']['name']).'</div>
                            <div class="mini-teams">
                                <img src="'.$f['teams']['home']['logo'].'">
                                <span class="mini-time">'.date("H:i", $f['fixture']['timestamp']).'</span>
                                <img src="'.$f['teams']['away']['logo'].'">
                            </div>
                          </div>';
                }
            }
            if(!$found) echo '<p style="font-size:10px; opacity:0.3; width:100%; text-align:center;">لا توجد مباريات هامة اليوم</p>';
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
        <div id="section-<?= $s['key'] ?>" class="channel-section <?= ($count == 0 ? 'active' : '') ?>" style="<?= ($count == 0 ? 'display:block;' : 'display:none;') ?>">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="c-head"><div style="background:var(--blue-grad); color:#000; padding:5px 12px; border-radius:10px; font-size:10px; font-weight:900;"><?= $ch['name'] ?></div><div style="color:#ff4d4d; font-size:10px; font-weight:900;">مباشر</div></div>
                <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)">تشغيل البث المباشر</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php $count++; endforeach; ?>
    </div>
    <footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>
</div>

<script>
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.style.display = 'none');
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).style.display = 'block';
    element.classList.add('active');
}
function startStream(boxId, file, btn) {
    let vBox = document.getElementById(boxId);
    vBox.style.backgroundImage = "none";
    vBox.innerHTML = `<iframe src="${file}?autoplay=1&muted=1" allow="autoplay; encrypted-media" allowfullscreen></iframe>`;
    btn.innerHTML = '<i class="fas fa-check-circle"></i> تم الاتصال بالبث';
}
window.addEventListener('load', () => { setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 2000); });
setInterval(() => { fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(count => { document.getElementById('realtime-visitors').innerText = count; }); }, 5000);
</script>
</body>
</html>
