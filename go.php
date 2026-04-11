<?php
session_start();
error_reporting(0);

// --- إعدادات جدول المباريات (كود عثمان) ---
date_default_timezone_set('Asia/Riyadh');
$API_KEY_FOOTBALL = '6b9915e3b84f54b3962e5817b9e26e5f'; 
$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));

$league_settings = array(
    307 => array('name' => 'الدوري السعودي', 'ch_name' => 'SSC'),
    42  => array('name' => 'دوري أبطال أوروبا', 'ch_name' => 'beIN Sports'),
    73  => array('name' => 'الدوري الأوروبي', 'ch_name' => 'beIN Sports'),
    525 => array('name' => 'نخبة آسيا', 'ch_name' => 'beIN AFC'),
    39  => array('name' => 'الدوري الإنجليزي', 'ch_name' => 'beIN Premium'),
    140 => array('name' => 'الدوري الإسباني', 'ch_name' => 'beIN Sports'),
    135 => array('name' => 'الدوري الإيطالي', 'ch_name' => 'AD Sports'),
    78  => array('name' => 'الدوري الألماني', 'ch_name' => 'beIN Sports'),
    61  => array('name' => 'الدوري الفرنسي', 'ch_name' => 'beIN Sports')
);

function getFixturesWithCache($date, $key) {
    $cache_file = "cache_" . $date . ".json";
    $expire_time = 1000; 
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
    $results = (isset($data['response'])) ? $data['response'] : array();
    if (!empty($results)) { file_put_contents($cache_file, json_encode($results)); }
    return $results;
}
$fixtures = getFixturesWithCache($date_get, $API_KEY_FOOTBALL);
$ordered_matches = array();
if (!empty($fixtures)) {
    foreach ($fixtures as $f) {
        $id = (int)$f['league']['id'];
        if (isset($league_settings[$id])) { $ordered_matches[$id][] = $f; }
    }
}

// --- بيانات السحابة (JSONBin) ---
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
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; }
        
        /* شاشة الدخول */
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e293b 0%, #050c14 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.2); }
        .intro-icon-box { width: 100px; height: 100px; background: var(--main); border-radius: 30%; display: flex; align-items: center; justify-content: center; animation: glowPulse 2s infinite ease-in-out; }
        .intro-icon-box i { font-size: 50px; color: white; }
        .intro-title { margin-top: 25px; color: white; font-weight: 900; font-size: 24px; }
        @keyframes glowPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }

        /* الهيدر والتبويبات */
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .visitors-badge-float { position: fixed; bottom: 25px; left: 25px; width: 45px; height: 45px; background: rgba(34, 197, 94, 0.15); backdrop-filter: blur(10px); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 5000; border: 1.5px solid #22c55e; }
        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; text-align: center; border: 1.5px solid #ffffff; }
        .btn-wa { background: #25d366; } .btn-tg { background: #0088cc; } .btn-sn { background: #FFFC00; color: #000 !important; } .btn-tw { background: #000; }

        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item i { font-size: 20px; display: block; margin-bottom: 5px; color: var(--main); }
        .cat-item img { width: 26px; height: 26px; object-fit: contain; margin-bottom: 4px; }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; display: block; }

        /* ستايل جدول المباريات عثمان */
        .match-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; background: var(--glass); padding: 10px; border-radius: 15px; }
        .match-nav a { color: #fff; background: var(--main); width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none; }
        .league-row { background: linear-gradient(90deg, var(--main), transparent); padding: 8px 12px; border-radius: 8px; margin: 20px 0 10px; font-weight: 900; font-size: 12px; border-right: 4px solid #fff; text-align: right; }
        .match-card { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 18px; padding: 12px; margin-bottom: 12px; }
        .m-top { display: flex; align-items: center; justify-content: space-between; }
        .m-team { flex: 1; text-align: center; font-size: 10px; }
        .m-team img { width: 30px; height: 30px; display: block; margin: 0 auto 5px; }
        .m-score { font-size: 20px; font-weight: 900; }
        .m-bottom { border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 8px; margin-top: 8px; display: flex; justify-content: center; }
        .ch-badge { background: rgba(56,189,248,0.1); padding: 3px 12px; border-radius: 50px; font-size: 10px; color: #38bdf8; }

        .grid { padding: 15px; }
        .channel-section { display: none; width: 100%; }
        .channel-section.active { display: block; animation: slideUp 0.5s ease; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); margin-bottom: 20px; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; position: relative; }
        iframe { width: 100%; height: 100%; border: none; }
        .play-btn { width: 90%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 8px; background: var(--glass); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 12px; border-radius: 50px; font-weight: 900; cursor: pointer; }
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
    <div class="visitors-badge-float"><i class="fas fa-users" style="color:#22c55e;"></i> <span id="realtime-visitors"><?php echo $online_now; ?></span></div>

    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-sn">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-tw">تويتر</a>
        </div>

        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('matches_table', this)">
                <i class="fas fa-calendar-alt"></i>
                <span>جدول المباريات</span>
            </div>
            
            <div class="cat-item" onclick="switchSection('dual_player', this)"><img src="mg/ch2.png"><span>تشغيل مباريتين</span></div>
            
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches_table" class="channel-section active">
            <div class="match-nav">
                <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
                <span style="font-weight:900; font-size:14px;"><?= date('Y / m / d', strtotime($date_get)) ?></span>
                <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
            </div>

            <?php if (empty($ordered_matches)): ?>
                <div style="text-align:center; padding:50px; opacity:0.5;">لا توجد مباريات هامة لهذا اليوم</div>
            <?php else: ?>
                <?php foreach($league_settings as $id => $setting): if(isset($ordered_matches[$id])): ?>
                    <div class="league-row"><?= $setting['name'] ?></div>
                    <?php $ch_counter = 1; foreach($ordered_matches[$id] as $m): 
                        $status = $m['fixture']['status']['short'];
                        $mTime = date("H:i", $m['fixture']['timestamp']);
                        $current_ch = $setting['ch_name'] . " " . $ch_counter; $ch_counter++;
                    ?>
                    <div class="match-card">
                        <div class="m-top">
                            <div class="m-team"><img src="<?= $m['teams']['home']['logo'] ?>"><b><?= $m['teams']['home']['name'] ?></b></div>
                            <div style="text-align:center;">
                                <?php if(in_array($status, array('1H','2H','HT','ET','P'))): ?>
                                    <div class="m-score" style="color:var(--main)"><?= $m['goals']['home'] ?>-<?= $m['goals']['away'] ?></div>
                                    <div style="font-size:10px; color:#22c55e;">مباشر</div>
                                <?php elseif($status == 'FT'): ?>
                                    <div class="m-score"><?= $m['goals']['home'] ?>-<?= $m['goals']['away'] ?></div>
                                    <div style="font-size:9px;">انتهت</div>
                                <?php else: ?>
                                    <div style="font-size:16px; font-weight:900;"><?= $mTime ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="m-team"><img src="<?= $m['teams']['away']['logo'] ?>"><b><?= $m['teams']['away']['name'] ?></b></div>
                        </div>
                        <div class="m-bottom"><div class="ch-badge"><i class="fas fa-tv"></i> <?= $current_ch ?></div></div>
                    </div>
                <?php endforeach; endif; endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="section-dual_player" class="channel-section">
            <div style="text-align:center; padding:20px;">استخدم المشغل المزدوج من هنا</div>
        </div>

        <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)"><?= $ch['name'] ?></button>
            </div>
            <?php endforeach; ?>
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
    document.getElementById(boxId).innerHTML = `<iframe src="${file}?autoplay=1" allowfullscreen></iframe>`;
    btn.innerHTML = 'تم الاتصال';
}
window.addEventListener('load', () => { setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1500); });
setInterval(() => { fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(c => { document.getElementById('realtime-visitors').innerText = c; }); }, 5000);
</script>
</body>
</html>
