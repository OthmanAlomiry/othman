<?php
session_start();
error_reporting(0);

// --- إعدادات جدول المباريات الأصلية (عثمان) ---
date_default_timezone_set('Asia/Riyadh');
$FOOTBALL_API_KEY = '6b9915e3b84f54b3962e5817b9e26e5f'; 
$date_get = date('Y-m-d');

// --- إعدادات API المباريات الجديدة المطلوبة ---
$apiKey_new = '273aaeb61360452588653ffea820cc19';
$url_new = 'https://api.football-data.org/v4/matches';

$leagues_map_new = [
    'PL'  => ['name' => 'الدوري الإنجليزي', 'channel' => 'beIN Sport 1', 'ch_num' => '1'],
    'PD'  => ['name' => 'الدوري الإسباني', 'channel' => 'beIN Sport 3', 'ch_num' => '3'],
    'SA'  => ['name' => 'الدوري الإيطالي', 'channel' => 'STARZPLAY 1', 'ch_num' => '10'],
    'BL1' => ['name' => 'الدوري الألماني', 'channel' => 'beIN Sport 5', 'ch_num' => '5'],
    'CL'  => ['name' => 'دوري أبطال أوروبا', 'channel' => 'beIN Sport 2', 'ch_num' => '2'],
    'FL1' => ['name' => 'الدوري الفرنسي', 'channel' => 'beIN Sport 4', 'ch_num' => '4'],
];

// دالة الترجمة عبر قوقل (المطلوبة)
function translate_name($text) {
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $response = @file_get_contents($url);
    if($response) {
        $result = json_decode($response, true);
        return $result[0][0][0] ?? $text;
    }
    return $text;
}

// مصفوفة الترجمة الشاملة اليدوية
$translate = [
    'NS' => 'لم تبدأ', 'FT' => 'انتهت', '1H' => 'شوط 1', '2H' => 'شوط 2', 
    'HT' => 'بين الشوطين', 'P' => 'ركلات ترجيح', 'PST' => 'مؤجلة', 'CANC' => 'ملغاة',
    'TBD' => 'يحدد لاحقاً', 'ABD' => 'ملغاة', 'LIVE' => 'مباشر',
    'Al-Hilal' => 'الهلال', 'Al-Nassr' => 'النصر', 'Al-Ittihad' => 'الاتحاد', 'Al-Ahli' => 'الأهلي',
    'Paris Saint Germain' => 'باريس سان جيرمان', 'Marseille' => 'مارسيليا', 'Lyon' => 'ليون',
];

$league_settings = array(
    307 => array('name' => 'الدوري السعودي', 'ch_name' => 'SSC'),
    42  => array('name' => 'دوري أبطال أوروبا', 'ch_name' => 'beIN Sports'),
    39  => array('name' => 'الدوري الإنجليزي', 'ch_name' => 'beIN Premium'),
    140 => array('name' => 'الدوري الإسباني', 'ch_name' => 'beIN Sports'),
    135 => array('name' => 'الدوري الإيطالي', 'ch_name' => 'AD Sports'),
    61  => array('name' => 'الدوري الفرنسي', 'ch_name' => 'beIN Sports')
);

// جلب بيانات API الأصلية
function getFixturesWithCache($date, $key) {
    $cache_file = "cache_" . $date . ".json";
    $expire_time = 600; 
    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $expire_time)) {
        return json_decode(file_get_contents($cache_file), true);
    }
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://v3.football.api-sports.io/fixtures?date=$date&timezone=Asia/Riyadh",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array("x-apisports-key: $key"),
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);
    $results = $data['response'] ?? array();
    if (!empty($results)) { file_put_contents($cache_file, json_encode($results)); }
    return $results;
}

$fixtures = getFixturesWithCache($date_get, $FOOTBALL_API_KEY);
$ordered_matches = array();
if (!empty($fixtures)) {
    foreach ($fixtures as $f) {
        $id = (int)$f['league']['id'];
        if (isset($league_settings[$id])) { $ordered_matches[$id][] = $f; }
    }
}

// إعدادات المتجر والسحابة (لا تلمس)
$API_KEY_CLOUD = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID_CLOUD = '69d6f6b636566621a891e6c1';

function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$cloud = getCloudFullData($BIN_ID_CLOUD, $API_KEY_CLOUD);
$all_channels = $cloud['custom_channels'] ?? [];
$active_sections = array_filter($cloud['sections'] ?: [], function($s) { return $s['status'] == 'show'; });
$news = $cloud['news_ticker'] ?? ['text' => '', 'status' => 'hide'];

$visitors_file = 'online_visitors.txt';
$online_now = file_exists($visitors_file) ? count(unserialize(file_get_contents($visitors_file))) : 1;
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
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; }
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; text-align: center; border: 1.5px solid #ffffff; }
        .btn-wa { background: #25d366; } .btn-tg { background: #0088cc; }
        .news-ticker { background: rgba(225, 29, 72, 0.15); height: 32px; overflow: hidden; margin-bottom: 10px; display: flex; align-items: center; position: relative; }
        .ticker-label { background: var(--main); color: #fff; padding: 0 12px; height: 100%; display: flex; align-items: center; font-size: 10px; font-weight: 900; z-index: 10; position: absolute; right: 0; }
        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item img { width: 26px; height: 26px; margin-bottom: 4px; }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; display: block; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); backdrop-filter: blur(10px); margin-bottom: 15px; width: 100%; }
        .league-sep { background: rgba(255, 255, 255, 0.07); padding: 10px 15px; border-radius: 10px; font-size: 11px; font-weight: 900; margin: 15px 0 10px; border-right: 4px solid var(--main); color: #fff; }
        .m-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 10px; }
        .m-team-col { flex: 1; font-size: 10px; font-weight: 700; color: #fff; text-align: center; }
        .m-team-col img { width: 28px; height: 28px; display: block; margin: 0 auto 6px; }
        .m-time-box { flex: 0.6; background: rgba(225, 29, 72, 0.1); border: 1px solid rgba(225, 29, 72, 0.2); padding: 6px; border-radius: 10px; text-align: center; font-weight: 900; font-size: 12px; color: var(--main); }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; position: relative; }
        .play-btn { width: 92%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 10px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--glass-border); padding: 12px; border-radius: 12px; font-weight: 900; cursor: pointer; }
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #050c14; display: flex; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; }
    </style>
</head>
<body>

<div id="pro-intro">
    <div style="text-align:center;">
        <div style="width:80px; height:80px; background:var(--main); border-radius:20px; display:flex; align-items:center; justify-content:center; margin:0 auto; box-shadow:0 0 30px var(--main);"><i class="fas fa-play" style="color:#fff; font-size:30px;"></i></div>
        <h2 style="color:#fff; margin-top:20px;">الخدمة الرقمية</h2>
    </div>
</div>

<div class="main-container">
    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
        </div>
        
        <?php if($news['status'] == 'show'): ?>
        <div class="news-ticker"><span class="ticker-label">تنبيه</span><marquee style="color:#fff; font-size:12px;"><?= $news['text'] ?></marquee></div>
        <?php endif; ?>

        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('matches_table', this)"><img src="https://cdn-icons-png.flaticon.com/512/833/833593.png" style="filter: brightness(0) invert(1);"><span>جدول المباريات</span></div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid" style="padding:15px;">
        <div id="section-matches_table" class="channel-section" style="display:block;">
            <div class="match-grid-container">
            <?php if(empty($ordered_matches)): ?>
                <p style="text-align:center; opacity:0.5; padding:20px;">لا توجد مباريات هامة حالياً</p>
            <?php else: foreach($league_settings as $id => $set): if(isset($ordered_matches[$id])): ?>
                <div class="league-sep"><?= $set['name'] ?> - <small><?= $set['ch_name'] ?></small></div>
                <?php foreach($ordered_matches[$id] as $m): 
                    $h_name = $translate[$m['teams']['home']['name']] ?? translate_name($m['teams']['home']['name']);
                    $a_name = $translate[$m['teams']['away']['name']] ?? translate_name($m['teams']['away']['name']);
                    $status = $translate[$m['fixture']['status']['short']] ?? $m['fixture']['status']['short'];
                ?>
                    <div class="card">
                        <div class="m-row">
                            <div class="m-team-col"><img src="<?= $m['teams']['home']['logo'] ?>"><?= $h_name ?></div>
                            <div class="m-time-box">
                                <?= ($m['fixture']['status']['short'] == 'NS') ? date("H:i", $m['fixture']['timestamp']) : $m['goals']['home'].'-'.$m['goals']['away'] ?>
                                <br><span style="font-size:8px;"><?= $status ?></span>
                            </div>
                            <div class="m-team-col"><img src="<?= $m['teams']['away']['logo'] ?>"><?= $a_name ?></div>
                        </div>
                    </div>
            <?php endforeach; endif; endforeach; endif; ?>
            </div>
        </div>

        <?php foreach($active_sections as $s): ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section" style="display:none;">
            <?php 
            $channels = array_filter($all_channels, function($c) use ($s) { return trim(strtolower($c['section'])) == trim(strtolower($s['key'])); });
            foreach($channels as $ch): ?>
            <div class="card">
                <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>')">تشغيل <?= $ch['name'] ?></button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <footer style="text-align:center; padding:20px; font-size:10px; opacity:0.5;">جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>
</div>

<script>
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.style.display = 'none');
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).style.display = 'block';
    element.classList.add('active');
}
function startStream(boxId, file) {
    document.getElementById(boxId).innerHTML = `<iframe src="${file}?autoplay=1" style="width:100%; height:100%; border:none;" allowfullscreen></iframe>`;
}
window.onload = () => { setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1500); };
</script>
</body>
</html>
