<?php
session_start();
error_reporting(0);

// --- إعدادات جدول المباريات المحدثة (عثمان) ---
date_default_timezone_set('Asia/Riyadh');
$date_get = date('Y-m-d');

$FOOTBALL_API_KEY_NEW = '273aaeb61360452588653ffea820cc19'; 
$url_new = 'https://api.football-data.org/v4/matches';

// خريطة الدوريات المطلوبة (تم إضافة الدوري الفرنسي FL1)
$leagues_map_new = [
    'PL'  => ['name' => 'الدوري الإنجليزي'],
    'PD'  => ['name' => 'الدوري الإسباني'],
    'SA'  => ['name' => 'الدوري الإيطالي'],
    'BL1' => ['name' => 'الدوري الألماني'],
    'FL1' => ['name' => 'الدوري الفرنسي'],
    'CL'  => ['name' => 'دوري أبطال أوروبا'],
];

$translate = [
    'NS' => 'لم تبدأ', 'FT' => 'انتهت', 'FINISHED' => 'انتهت', 'TIMED' => 'لم تبدأ', 'IN_PLAY' => 'مباشر', 
    '1H' => 'شوط 1', '2H' => 'شوط 2', 'HT' => 'بين الشوطين', 'PAUSED' => 'بين الشوطين', 'LIVE' => 'مباشر'
];

function translate_name_pro($text, $manual_list) {
    if (isset($manual_list[$text])) return $manual_list[$text];
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $response = @file_get_contents($url);
    if($response) {
        $result = json_decode($response, true);
        return $result[0][0][0] ?? $text;
    }
    return $text;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_new);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: ' . $FOOTBALL_API_KEY_NEW]);
$response_new = curl_exec($ch);
curl_close($ch);
$match_data_new = json_decode($response_new, true);

$ordered_matches = [];
if (isset($match_data_new['matches'])) {
    foreach ($match_data_new['matches'] as $m) {
        $l_code = $m['competition']['code'];
        if (isset($leagues_map_new[$l_code])) {
            $ordered_matches[$l_code][] = $m;
        }
    }
}

// --- إعدادات سحابية (عثمان الأصلية) ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69d6f6b636566621a891e6c1';

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

$cloud = getCloudFullData($BIN_ID, $API_KEY);
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
        
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        
        /* تصميم النتيجة المحسن */
        .m-time-box { 
            flex: 0.7; 
            background: rgba(255, 255, 255, 0.08); 
            border: 1px solid var(--glass-border); 
            padding: 8px 5px; 
            border-radius: 12px; 
            text-align: center; 
        }
        .res-num { 
            font-size: 18px; 
            font-weight: 900; 
            color: #ffffff; 
            letter-spacing: 2px;
            display: block;
            margin-bottom: 2px;
        }
        .status-badge {
            background: var(--main);
            color: white;
            font-size: 8px;
            padding: 2px 8px;
            border-radius: 50px;
            font-weight: 700;
        }
        .status-wait {
            background: rgba(255,255,255,0.2);
            color: #ccc;
        }

        /* تنسيقات عثمان الأصلية */
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e293b 0%, #050c14 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.2); }
        .ad-popup-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); z-index: 8000; display: none; align-items: center; justify-content: center; }
        .ad-popup-content { position: relative; width: 85%; max-width: 320px; animation: popZoom 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .ad-popup-image { width: 100%; border-radius: 20px; border: 2px solid var(--glass-border); display: block; }
        .ad-close-btn { position: absolute; top: -15px; right: -15px; width: 35px; height: 35px; background: var(--main); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid white; z-index: 10; }
        .ad-subscribe-btn { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); background: #25d366; color: white; padding: 10px 25px; border-radius: 50px; text-decoration: none; font-weight: 900; font-size: 14px; display: flex; align-items: center; gap: 8px; border: 2px solid white; white-space: nowrap; }
        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; text-align: center; border: 1.5px solid #ffffff; }
        .btn-wa { background: #25d366; } .btn-tg { background: #0088cc; } .btn-sn { background: #FFFC00; color: #000 !important; } .btn-tw { background: #000; }
        .news-ticker { background: rgba(225, 29, 72, 0.15); height: 32px; overflow: hidden; margin-bottom: 10px; display: flex; align-items: center; position: relative; }
        .ticker-label { background: var(--main); color: #fff; padding: 0 12px; height: 100%; display: flex; align-items: center; font-size: 10px; font-weight: 900; z-index: 10; position: absolute; right: 0; }
        .ticker-wrap { flex: 1; overflow: hidden; direction: ltr; display: flex; align-items: center; }
        .ticker-move { display: flex; white-space: nowrap; animation: ticker-infinite 45s linear infinite; width: max-content; }
        .ticker-text { color: #fff; font-size: 12px; font-weight: 700; padding: 0 60px; }
        @keyframes ticker-infinite { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }
        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item img { width: 26px; height: 26px; object-fit: contain; margin-bottom: 4px; }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; display: block; }
        .grid { padding: 15px; }
        .channel-section { display: none; width: 100%; }
        .channel-section.active { display: block; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); backdrop-filter: blur(10px); margin-bottom: 15px; width: 100%; }
        .play-btn { width: 92%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 10px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--glass-border); padding: 12px; border-radius: 12px; font-weight: 900; cursor: pointer; font-size: 13px; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; position: relative; background-size: cover; background-position: center; }
        .video-box iframe { position: absolute; top:0; left:0; width: 100%; height: 100%; border: none; }
        .league-sep { background: rgba(255, 255, 255, 0.07); padding: 10px 15px; border-radius: 10px; font-size: 11px; font-weight: 900; margin: 15px 0 10px; border-right: 4px solid var(--main); color: #fff; }
        .m-row { display: flex; justify-content: space-between; align-items: center; padding: 15px 10px; }
        .m-team-col { flex: 1; font-size: 11px; font-weight: 700; color: #fff; text-align: center; }
        .m-team-col img { width: 32px; height: 32px; display: block; margin: 0 auto 8px; }
        .visitors-badge-float { position: fixed; bottom: 20px; left: 20px; width: 40px; height: 40px; background: rgba(34, 197, 94, 0.1); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 5000; border: 1px solid #22c55e; font-size: 10px; font-weight: 900; color: #22c55e; }
        .bg-pattern { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background: linear-gradient(135deg, #050c14 0%, #0a1f33 100%); }
        footer { text-align: center; padding: 30px; font-size: 9px; opacity: 0.4; }
        @media (orientation: landscape) { .main-container { max-width: 95% !important; } }
    </style>
</head>
<body>

<div id="pro-intro">
    <div class="loader-content">
        <div class="intro-icon-box"><i class="fas fa-play-circle"></i></div>
        <h2 class="intro-title">الخدمة الرقمية</h2>
        <div class="loading-bar"></div>
    </div>
</div>

<div class="ad-popup-overlay" id="adPopup">
    <div class="ad-popup-content">
        <div class="ad-close-btn" onclick="closeAd()"><i class="fas fa-times"></i></div>
        <img src="https://files.catbox.moe/7pik4r.png" class="ad-popup-image" alt="إعلان">
        <a href="https://wa.me/966505571164" class="ad-subscribe-btn">
            <i class="fab fa-whatsapp"></i> اشترك الآن عبر واتساب
        </a>
    </div>
</div>

<div class="main-container">
    <div class="visitors-badge-float"><i class="fas fa-users"></i><span id="realtime-visitors"><?= $online_now ?></span></div>
    <div class="bg-pattern"></div>

    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-sn">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-tw">تويتر</a>
        </div>

        <?php if($news['status'] == 'show'): ?>
        <div class="news-ticker"><span class="ticker-label">تنبيهات</span><div class="ticker-wrap"><div class="ticker-move"><span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span></div></div></div>
        <?php endif; ?>

        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('matches_table', this)"><img src="https://cdn-icons-png.flaticon.com/512/833/833593.png" style="filter: brightness(0) invert(1);"><span>جدول المباريات</span></div>
            <div class="cat-item" onclick="switchSection('dual_player', this)"><img src="mg/ch2.png"><span>شاشتين</span></div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches_table" class="channel-section active">
            <div class="match-nav">
                <span style="font-weight:900; font-size:12px; color:#fff;">مباريات اليوم: <?= $date_get ?></span>
            </div>
            
            <?php if(empty($ordered_matches)): ?>
                <p style="text-align:center; opacity:0.5; padding:20px;">لا توجد مباريات هامة اليوم</p>
            <?php else: foreach($ordered_matches as $code => $matches_list): ?>
                <div class="league-sep"><?= $leagues_map_new[$code]['name'] ?></div>
                <?php foreach($matches_list as $m): 
                    $h_name = translate_name_pro($m['homeTeam']['shortName'] ?? $m['homeTeam']['name'], $translate);
                    $a_name = translate_name_pro($m['awayTeam']['shortName'] ?? $m['awayTeam']['name'], $translate);
                    $status_text = $translate[$m['status']] ?? 'مباشر';
                    $is_live = ($m['status'] == 'IN_PLAY' || $m['status'] == '1H' || $m['status'] == '2H');
                    $is_waiting = ($m['status'] == 'TIMED' || $m['status'] == 'SCHEDULED');
                ?>
                    <div class="card">
                        <div class="m-row">
                            <div class="m-team-col"><img src="<?= $m['homeTeam']['crest'] ?>"><?= $h_name ?></div>
                            
                            <div class="m-time-box">
                                <span class="res-num">
                                    <?php if($is_waiting): ?>
                                        <?= date("H:i", strtotime($m['utcDate'] . ' +3 hours')) ?>
                                    <?php else: ?>
                                        <?= $m['score']['fullTime']['home'] ?> - <?= $m['score']['fullTime']['away'] ?>
                                    <?php endif; ?>
                                </span>
                                <span class="status-badge <?= $is_waiting ? 'status-wait' : '' ?>">
                                    <?= $status_text ?>
                                </span>
                            </div>
                            
                            <div class="m-team-col"><img src="<?= $m['awayTeam']['crest'] ?>"><?= $a_name ?></div>
                        </div>
                    </div>
                <?php endforeach; endforeach; endif; ?>
        </div>

        <div id="section-dual_player" class="channel-section">
             <div class="dual-container">
                <div class="dual-slot">
                    <div class="video-box" id="dual-screen-1" style="background-image:url('mg/chn1.png')"></div>
                    <button class="play-btn" onclick="openPicker(1)"><i class="fas fa-tv"></i> <span id="btn-text-1">القناة الأولى</span></button>
                </div>
                <div class="dual-slot">
                    <div class="video-box" id="dual-screen-2" style="background-image:url('mg/chn2.png')"></div>
                    <button class="play-btn" onclick="openPicker(2)"><i class="fas fa-tv"></i> <span id="btn-text-2">القناة الثانية</span></button>
                </div>
            </div>
        </div>

        <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section">
            <div class="channel-grid">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="video-box" id="vid-<?= $ch['id'] ?>" style="background-image:url('mg/wel.GIF')"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)">
                    <i class="fas fa-play-circle"></i> <span>تشغيل <?= $ch['name'] ?></span>
                </button>
            </div>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>
</div>

<script>
// الوظائف الأصلية بدون أي تغيير لضمان عمل الموقع
function closeAd() { document.getElementById('adPopup').style.display = 'none'; }
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}
function startStream(boxId, file, btn) {
    let vBox = document.getElementById(boxId); vBox.style.backgroundImage = "none";
    vBox.innerHTML = `<iframe src="${file}?autoplay=1&muted=0" allowfullscreen></iframe>`;
    btn.querySelector('span').innerText = 'متصل الآن..'; 
}
window.addEventListener('load', () => { 
    setTimeout(() => { 
        document.getElementById('pro-intro').classList.add('intro-hide');
        setTimeout(() => { document.getElementById('adPopup').style.display = 'flex'; }, 500);
    }, 1500); 
});
setInterval(() => { fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(c => { document.getElementById('realtime-visitors').innerText = c; }); }, 5000);
</script>
</body>
</html>
