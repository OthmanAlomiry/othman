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

// جلب بيانات السحابة والقنوات عثمان
function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER => true);
    curl_setopt($ch, CURLOPT_HTTPHEADER => array("X-Master-Key: " . $key, "X-Bin-Meta: false"));
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$cloud = getCloudFullData($BIN_ID, $API_KEY_BIN);
$all_channels = isset($cloud['custom_channels']) ? $cloud['custom_channels'] : array();
$active_sections = array_filter($cloud['sections'] ?: array(), function($s) { return $s['status'] == 'show'; });

// جلب مباريات اليوم عثمان
$fixtures = getFixtures(date('Y-m-d'), $FOOTBALL_API_KEY);
$my_leagues = array(307, 2, 3, 39, 140, 135); // IDs الدوريات المهمة
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
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #fff; overflow-x: hidden; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; }

        /* شريط المباريات الصغير عثمان */
        .match-slider { display: flex; gap: 10px; overflow-x: auto; padding: 15px; scrollbar-width: none; background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border); margin-bottom: 10px; }
        .match-slider::-webkit-scrollbar { display: none; }
        .mini-card { min-width: 130px; background: var(--glass); border: 1px solid var(--glass-border); border-radius: 15px; padding: 8px; text-align: center; display: flex; flex-direction: column; gap: 5px; }
        .mini-teams { display: flex; align-items: center; justify-content: center; gap: 10px; }
        .mini-teams img { width: 20px; height: 20px; object-fit: contain; }
        .mini-time { font-size: 11px; font-weight: 900; color: #0ea5e9; }
        .mini-league { font-size: 8px; opacity: 0.5; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* ستايلاتك الأساسية كما هي دون تغيير عثمان */
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; text-align: center; border: 1.5px solid #fff; }
        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; display: block; }
        .cat-item img { width: 26px; height: 26px; margin-bottom: 4px; }
        .grid { padding: 15px; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); margin-bottom: 20px; }
        .c-head { padding: 12px; background: rgba(0,0,0,0.4); display: flex; justify-content: space-between; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: var(--main); color: #fff; border: none; padding: 14px; border-radius: 50px; font-weight: 900; cursor: pointer; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; }
        iframe { width: 100%; height: 100%; border: none; }
        footer { text-align: center; padding: 40px; font-size: 10px; opacity: 0.5; }
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #050c14; z-index: 9999; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; }
    </style>
</head>
<body>

<div id="pro-intro">
    <div style="width:80px; height:80px; background:var(--main); border-radius:25px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-play" style="font-size:30px; color:#fff;"></i></div>
    <h2 style="color:#fff; margin-top:15px;">الخدمة الرقمية</h2>
</div>

<div class="main-container">
    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366;">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc;">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:#FFFC00; color:#000;">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn" style="background:#000;">تويتر</a>
        </div>
        <div class="category-tabs">
            <?php $count = 0; foreach($active_sections as $s): ?>
                <div class="cat-item <?= ($count == 0 ? 'active' : '') ?>" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php $count++; endforeach; ?>
        </div>
    </div>

    <div class="match-slider">
        <?php 
        $found_match = false;
        foreach($fixtures as $f) {
            if(in_array($f['league']['id'], $my_leagues)) {
                $found_match = true;
                $mTime = date("H:i", $f['fixture']['timestamp']);
                echo '<div class="mini-card">
                        <div class="mini-league">'.translateText($f['league']['name']).'</div>
                        <div class="mini-teams">
                            <img src="'.$f['teams']['home']['logo'].'">
                            <span class="mini-time">'.$mTime.'</span>
                            <img src="'.$f['teams']['away']['logo'].'">
                        </div>
                      </div>';
            }
        }
        if(!$found_match) echo '<p style="font-size:10px; opacity:0.4; width:100%; text-align:center;">لا توجد مباريات كبرى حالياً</p>';
        ?>
    </div>

    <div class="grid">
        <?php $count = 0; foreach($active_sections as $s): $channels = array_filter($all_channels, function($c) use ($s) { return trim(strtolower($c['section'])) == trim(strtolower($s['key'])); }); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section" style="<?= ($count == 0 ? 'display:block;' : 'display:none;') ?>">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="c-head"><div style="background:var(--blue-grad); color:#000; padding:4px 10px; border-radius:8px; font-size:10px; font-weight:900;"><?= $ch['name'] ?></div><div style="color:#ff4d4d; font-size:10px; font-weight:900;">مباشر</div></div>
                <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)">تشغيل البث</button>
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
    document.getElementById(boxId).innerHTML = `<iframe src="${file}?autoplay=1" allowfullscreen></iframe>`;
    btn.style.display = 'none';
}
window.addEventListener('load', () => { setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1500); });
</script>
</body>
</html>
