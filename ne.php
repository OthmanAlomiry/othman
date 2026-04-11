<?php
session_start();
error_reporting(0);

// --- إعدادات API المباريات الجديد (football-data.org) ---
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';
date_default_timezone_set('Asia/Riyadh');
$date_get = date('Y-m-d');

// خريطة الدوريات المدعومة في الكود الجديد
$leagues_map = [
    'PL'  => ['name' => 'الدوري الإنجليزي', 'channel' => 'beIN Sport 1'],
    'PD'  => ['name' => 'الدوري الإسباني', 'channel' => 'beIN Sport 3'],
    'SA'  => ['name' => 'الدوري الإيطالي', 'channel' => 'STARZPLAY 1'],
    'BL1' => ['name' => 'الدوري الألماني', 'channel' => 'beIN Sport 5'],
    'CL'  => ['name' => 'دوري أبطال أوروبا', 'channel' => 'beIN Sport 2'],
];

// مصفوفة الترجمة اليدوية (عثمان) - لضمان دقة أسماء الأندية العربية
$translate = [
    'NS' => 'لم تبدأ', 'FT' => 'انتهت', 'FINISHED' => 'انتهت', 'TIMED' => 'لم تبدأ', 'IN_PLAY' => 'مباشر', 'PAUSED' => 'بين الشوطين',
    'Manchester City' => 'مانشستر سيتي', 'Liverpool' => 'ليفربول', 'Arsenal' => 'أرسنال',
    'Real Madrid' => 'ريال مدريد', 'Barcelona' => 'برشلونة', 'Bayern Munich' => 'بايرن ميونخ',
    'Al-Hilal' => 'الهلال', 'Al-Nassr' => 'النصر', 'Al-Ittihad' => 'الاتحاد', 'Al-Ahli' => 'الأهلي'
];

function translate_name($text, $manual_list) {
    if (isset($manual_list[$text])) return $manual_list[$text];
    
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $response = @file_get_contents($url);
    if($response) {
        $result = json_decode($response, true);
        return $result[0][0][0] ?? $text;
    }
    return $text;
}

// جلب بيانات المباريات
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: ' . $apiKey]);
$response = curl_exec($ch);
curl_close($ch);
$match_result = json_decode($response, true);
$matches = $match_result['matches'] ?? [];

// تصنيف المباريات حسب الدوري
$ordered_matches = [];
foreach ($matches as $m) {
    $l_code = $m['competition']['code'];
    if (isset($leagues_map[$l_code])) {
        $ordered_matches[$l_code][] = $m;
    }
}

// --- إعدادات الموقع الأصلية (JSONBin و الزوار) ---
$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
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
        /* ستايل الموقع الأصلي كما هو */
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; }
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; text-align: center; border: 1.5px solid #ffffff; }
        .btn-wa { background: #25d366; } .btn-tg { background: #0088cc; } .btn-sn { background: #FFFC00; color: #000 !important; } .btn-tw { background: #000; }
        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item img { width: 26px; height: 26px; object-fit: contain; }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; display: block; }
        .grid { padding: 15px; }
        .channel-section { display: none; width: 100%; }
        .channel-section.active { display: block; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); margin-bottom: 15px; }
        .play-btn { width: 92%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 10px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--glass-border); padding: 12px; border-radius: 12px; font-weight: 900; cursor: pointer; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; position: relative; background-size: cover; }
        .video-box iframe { position: absolute; top:0; left:0; width: 100%; height: 100%; border: none; }
        .league-sep { background: rgba(255, 255, 255, 0.07); padding: 10px 15px; border-radius: 10px; font-size: 11px; font-weight: 900; margin: 15px 0 10px; border-right: 4px solid var(--main); color: #fff; }
        .m-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 10px; }
        .m-team-col { flex: 1; font-size: 10px; font-weight: 700; color: #fff; text-align: center; }
        .m-team-col img { width: 28px; height: 28px; display: block; margin: 0 auto 6px; }
        .m-time-box { flex: 0.6; background: rgba(225, 29, 72, 0.1); border: 1px solid rgba(225, 29, 72, 0.2); padding: 6px; border-radius: 10px; text-align: center; font-weight: 900; font-size: 12px; color: var(--main); }
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #050c14; display: flex; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; }
        .visitors-badge-float { position: fixed; bottom: 20px; left: 20px; width: 40px; height: 40px; background: rgba(34, 197, 94, 0.1); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 5000; border: 1px solid #22c55e; font-size: 10px; color: #22c55e; }
    </style>
</head>
<body>

<div id="pro-intro">
    <div style="text-align:center;">
        <h2 style="color:white; font-weight:900;">الخدمة الرقمية</h2>
        <p style="color:var(--main);">جاري التحميل...</p>
    </div>
</div>

<div class="main-container">
    <div class="visitors-badge-float"><i class="fas fa-users"></i><span id="realtime-visitors"><?= $online_now ?></span></div>

    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-sn">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-tw">تويتر</a>
        </div>

        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('matches_table', this)"><img src="https://cdn-icons-png.flaticon.com/512/833/833593.png" style="filter: brightness(0) invert(1);"><span>جدول المباريات</span></div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches_table" class="channel-section active">
            <?php if(empty($ordered_matches)): ?>
                <p style="text-align:center; opacity:0.5; padding:20px;">لا توجد مباريات جارية حالياً</p>
            <?php else: foreach($ordered_matches as $code => $matches_list): ?>
                <div class="league-sep"><?= $leagues_map[$code]['name'] ?></div>
                <?php foreach($matches_list as $m): 
                    $h_name = translate_name($m['homeTeam']['shortName'] ?? $m['homeTeam']['name'], $translate);
                    $a_name = translate_name($m['awayTeam']['shortName'] ?? $m['awayTeam']['name'], $translate);
                    $status = $translate[$m['status']] ?? 'جارية';
                    $m_time = date("H:i", strtotime($m['utcDate']));
                ?>
                    <div class="card">
                        <div class="m-row">
                            <div class="m-team-col"><img src="<?= $m['homeTeam']['crest'] ?>"><?= $h_name ?></div>
                            <div class="m-time-box">
                                <?php if($m['status'] == 'TIMED' || $m['status'] == 'SCHEDULED'): ?>
                                    <?= $m_time ?>
                                <?php else: ?>
                                    <?= $m['score']['fullTime']['home'] ?> - <?= $m['score']['fullTime']['away'] ?>
                                <?php endif; ?>
                                <br><small style="font-size:7px;"><?= $status ?></small>
                            </div>
                            <div class="m-team-col"><img src="<?= $m['awayTeam']['crest'] ?>"><?= $a_name ?></div>
                        </div>
                        <div style="text-align:center; padding-bottom:10px;">
                             <small style="font-size:9px; color:var(--main);"><i class="fas fa-broadcast-tower"></i> <?= $leagues_map[$code]['channel'] ?></small>
                        </div>
                    </div>
                <?php endforeach; endforeach; endif; ?>
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
    setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1000); 
});
setInterval(() => { 
    fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(c => { 
        document.getElementById('realtime-visitors').innerText = c; 
    }); 
}, 5000);
</script>
</body>
</html>
