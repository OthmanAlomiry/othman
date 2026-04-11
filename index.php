<?php
session_start();
error_reporting(0);

// --- إعدادات مباريات اليوم ---
date_default_timezone_set('Asia/Riyadh');
$FOOTBALL_API_KEY = '5dc2fedf076a0c9041ea3515dd83d2fa'; 
$date_get = date('Y-m-d');

// مصفوفة الترجمة
$translate = [
    'NS' => 'لم تبدأ', 'FT' => 'انتهت', '1H' => 'شوط 1', '2H' => 'شوط 2', 
    'HT' => 'بين الشوطين', 'P' => 'ركلات ترجيح', 'PST' => 'مؤجلة', 'CANC' => 'ملغاة',
    'TBD' => 'يحدد لاحقاً', 'ABD' => 'ملغاة', 'LIVE' => 'مباشر',
    'Al-Hilal' => 'الهلال', 'Al-Nassr' => 'النصر', 'Al-Ittihad' => 'الاتحاد', 'Al-Ahli' => 'الأهلي',
    'Manchester City' => 'مانشستر سيتي', 'Liverpool' => 'ليفربول', 'Real Madrid' => 'ريال مدريد', 'Barcelona' => 'برشلونة'
];

$league_settings = array(
    307 => array('name' => 'الدوري السعودي', 'ch_name' => 'SSC'),
    42  => array('name' => 'دوري أبطال أوروبا', 'ch_name' => 'beIN Sports'),
    39  => array('name' => 'الدوري الإنجليزي', 'ch_name' => 'beIN Premium'),
    140 => array('name' => 'الدوري الإسباني', 'ch_name' => 'beIN Sports')
);

function getFixturesWithCache($date, $key) {
    $cache_file = "cache_" . $date . ".json";
    if (file_exists($cache_file) && (time() - filemtime($cache_file) < 600)) {
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
    $results = $data['response'] ?? [];
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
$all_channels = $cloud['custom_channels'] ?? [];
$active_sections = array_filter($cloud['sections'] ?: [], function($s) { return $s['status'] == 'show'; });
$news = $cloud['news_ticker'] ?? ['text' => '', 'status' => 'hide'];

function filterSection($channels, $sec) {
    return array_filter($channels, function($c) use ($sec) { 
        return (isset($c['section']) && trim(strtolower($c['section'])) == trim(strtolower($sec))); 
    });
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
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; }
        
        /* Loader & Popups */
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e293b 0%, #050c14 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.2); }
        .intro-icon-box { width: 100px; height: 100px; background: var(--main); border-radius: 30%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 50px rgba(225, 29, 72, 0.5); animation: glowPulse 2s infinite ease-in-out; }
        .intro-icon-box i { font-size: 50px; color: white; }
        .intro-title { margin-top: 25px; color: white; font-weight: 900; font-size: 24px; }
        .loading-bar { width: 150px; height: 4px; background: rgba(255,255,255,0.1); border-radius: 10px; margin-top: 30px; overflow: hidden; position: relative; }
        .loading-bar::after { content: ""; position: absolute; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, var(--main), transparent); animation: loadingMove 1.5s infinite; }
        @keyframes glowPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
        @keyframes loadingMove { 100% { left: 100%; } }

        /* Header Fixed */
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; text-align: center; border: 1.5px solid #ffffff; }
        .btn-wa { background: #25d366; } .btn-tg { background: #0088cc; } .btn-sn { background: #FFFC00; color: #000 !important; } .btn-tw { background: #000; }

        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; transition: 0.2s; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); transform: translateY(-2px); }
        .cat-item img { width: 26px; height: 26px; object-fit: contain; margin-bottom: 4px; }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; display: block; }

        /* Grid and Sections */
        .grid { padding: 15px; }
        .channel-section { display: none; width: 100%; }
        .channel-section.active { display: block; animation: slideUp 0.6s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); margin-bottom: 15px; }
        .play-btn { width: 92%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 10px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--glass-border); padding: 12px; border-radius: 12px; font-weight: 900; cursor: pointer; font-size: 13px; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; position: relative; background-size: cover; }
        .video-box iframe { position: absolute; top:0; left:0; width: 100%; height: 100%; border: none; }
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

<div class="main-container">
    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-sn">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-tw">منصة X</a>
        </div>

        <div class="category-tabs">
            <div class="cat-item" id="tab-matches_table" onclick="switchSection('matches_table', this)"><img src="https://cdn-icons-png.flaticon.com/512/833/833593.png" style="filter: brightness(0) invert(1);"><span>مباريات اليوم</span></div>
            <div class="cat-item" id="tab-dual_player" onclick="switchSection('dual_player', this)"><img src="mg/ch2.png"><span>شاشتين</span></div>
            
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item <?= ($s['key'] == 'beIN') ? 'active' : '' ?>" id="tab-<?= $s['key'] ?>" onclick="switchSection('<?= $s['key'] ?>', this)">
                    <img src="<?= $s['img'] ?>">
                    <span><?= $s['name'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches_table" class="channel-section">
             <div class="match-nav" style="text-align:center; padding:10px; color:#fff;">جدول المباريات اليومية</div>
             </div>

        <div id="section-dual_player" class="channel-section">
             <div style="text-align:center; color:#38bdf8; font-size:12px;">ميزة البث المزدوج</div>
        </div>

        <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section <?= ($s['key'] == 'beIN') ? 'active' : '' ?>">
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
</div>

<script>
function switchSection(id, element) {
    // إخفاء كل الأقسام
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    // إزالة النشاط من كل التبويبات
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    
    // إظهار القسم المطلوب
    const targetSection = document.getElementById('section-' + id);
    if(targetSection) targetSection.classList.add('active');
    
    // تفعيل التبويب المطلوب
    element.classList.add('active');
}

function startStream(boxId, file, btn) {
    let vBox = document.getElementById(boxId); 
    vBox.style.backgroundImage = "none";
    vBox.innerHTML = `<iframe src="${file}?autoplay=1&muted=0" allowfullscreen></iframe>`;
    btn.querySelector('span').innerText = 'متصل الآن..'; 
}

window.addEventListener('load', () => { 
    setTimeout(() => { 
        document.getElementById('pro-intro').classList.add('intro-hide');
    }, 1500); 
});
</script>
</body>
</html>
