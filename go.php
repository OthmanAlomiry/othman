<?php
session_start();
error_reporting(0);

// --- إعدادات جدول المباريات (كود عثمان) ---
date_default_timezone_set('Asia/Riyadh');
$FOOTBALL_API_KEY = '6b9915e3b84f54b3962e5817b9e26e5f'; 

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

$fixtures = getFixturesWithCache($date_get, $FOOTBALL_API_KEY);
$ordered_matches = array();
if (!empty($fixtures)) {
    foreach ($fixtures as $f) {
        $id = (int)$f['league']['id'];
        if (isset($league_settings[$id])) { $ordered_matches[$id][] = $f; }
    }
}

// --- بيانات السحابة ونظام الزوار ---
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
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.03); --glass-border: rgba(255, 255, 255, 0.1); --glass-strong: rgba(255, 255, 255, 0.08); }
        
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 195px; color: #e2e8f0; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; }

        /* الهيدر الثابت */
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.9); backdrop-filter: blur(20px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        
        /* شريط الأخبار المفقود */
        .news-ticker { background: rgba(225, 29, 72, 0.1); height: 32px; overflow: hidden; margin-bottom: 10px; display: flex; align-items: center; position: relative; }
        .ticker-label { background: var(--main); color: #fff; padding: 0 12px; height: 100%; display: flex; align-items: center; font-size: 10px; font-weight: 900; z-index: 10; position: absolute; right: 0; }
        .ticker-move { display: flex; white-space: nowrap; animation: ticker-infinite 60s linear infinite; }
        .ticker-text { color: #fff; font-size: 11px; padding: 0 50px; }
        @keyframes ticker-infinite { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }

        /* التبويبات الزجاجية */
        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; }
        .cat-item { min-width: 65px; background: var(--glass); border: 1px solid var(--glass-border); padding: 10px 5px; border-radius: 15px; text-align: center; cursor: pointer; transition: 0.3s; }
        .cat-item.active { background: rgba(225, 29, 72, 0.15); border-color: var(--main); }
        .cat-item i { font-size: 18px; margin-bottom: 5px; display: block; }
        .cat-item img { width: 24px; height: 24px; object-fit: contain; margin-bottom: 4px; }
        .cat-item span { font-size: 8px; font-weight: 900; }

        /* تصميم الكارت الزجاجي المريح للعين */
        .card { background: var(--glass-strong); border-radius: 25px; overflow: hidden; border: 1px solid var(--glass-border); backdrop-filter: blur(10px); margin-bottom: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .c-head { padding: 12px 15px; display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02); }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000 url('mg/wel.GIF') center/cover; position: relative; }
        
        .play-btn { width: 90%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 8px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--glass-border); padding: 14px; border-radius: 50px; font-weight: 900; cursor: pointer; transition: 0.3s; }
        .play-btn:hover { background: var(--main); border-color: var(--main); }

        /* التشغيل المزدوج */
        .dual-container { padding: 10px; display: flex; flex-direction: column; gap: 15px; }
        .dual-slot { background: var(--glass-strong); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); }
        .dual-screen-v { width: 100%; aspect-ratio: 16/9; background-color: #000; position: relative; background-size: cover; background-position: center; }
        #dual-screen-1 { background-image: url('mg/chn1.png'); }
        #dual-screen-2 { background-image: url('mg/chn2.png'); }
        .dual-btn-select { width: 100%; padding: 12px; background: rgba(0,0,0,0.4); border: none; color: #38bdf8; font-weight: 700; font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; }

        /* القائمة المنبثقة لاختيار القنوات */
        .ch-picker-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(15px); z-index: 7000; display: none; align-items: center; justify-content: center; }
        .ch-picker-window { width: 90%; max-width: 400px; background: #0f172a; border-radius: 25px; border: 1px solid var(--glass-border); max-height: 70vh; overflow-y: auto; }
        .ch-picker-header { padding: 15px; background: var(--main); color: white; font-weight: 900; text-align: center; }
        .ch-pick-item { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); cursor: pointer; text-align: center; }

        iframe { width: 100%; height: 100%; border: none; }
        .visitors-badge-float { position: fixed; bottom: 25px; left: 25px; width: 45px; height: 45px; background: rgba(34, 197, 94, 0.1); backdrop-filter: blur(10px); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 1px solid #22c55e; z-index: 2000; }
        
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #050c14; display: flex; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; }
    </style>
</head>
<body>

<div id="pro-intro"><h2 style="color:white;">الخدمة الرقمية</h2></div>

<div class="ch-picker-overlay" id="ch-picker">
    <div class="ch-picker-window">
        <div class="ch-picker-header">إختر قناة</div>
        <?php foreach($all_channels as $ch): ?>
            <div class="ch-pick-item" onclick="confirmPick('<?= $ch['file'] ?>', '<?= $ch['name'] ?>')"><?= $ch['name'] ?></div>
        <?php endforeach; ?>
        <div class="ch-pick-item" style="color:red;" onclick="closePicker()">إلغاء</div>
    </div>
</div>

<div class="main-container">
    <div class="visitors-badge-float"><span id="realtime-visitors"><?php echo $online_now; ?></span></div>

    <div class="header-fixed">
        <div class="social-links" style="display:flex; gap:5px; padding:0 10px; margin-bottom:10px;">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-sn">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-tw">تويتر</a>
        </div>

        <?php if($news['status'] == 'show'): ?>
        <div class="news-ticker">
            <span class="ticker-label">تنبيه</span>
            <div class="ticker-move"><span class="ticker-text"><?= $news['text'] ?> • <?= $news['text'] ?></span></div>
        </div>
        <?php endif; ?>

        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('matches_table', this)"><i class="fas fa-calendar-alt"></i><span>جدول المباريات</span></div>
            <div class="cat-item" onclick="switchSection('dual_player', this)"><img src="mg/ch2.png"><span>تشغيل مباريتين</span></div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches_table" class="channel-section active">
            <div style="display:flex; justify-content:space-between; margin-bottom:15px; background:var(--glass-strong); padding:10px; border-radius:15px; align-items:center;">
                <a href="?d=<?= $prev_date ?>" style="color:white;"><i class="fas fa-chevron-right"></i></a>
                <span style="font-weight:900; font-size:12px;"><?= $date_get ?></span>
                <a href="?d=<?= $next_date ?>" style="color:white;"><i class="fas fa-chevron-left"></i></a>
            </div>
            <?php foreach($league_settings as $id => $set): if(isset($ordered_matches[$id])): ?>
                <div style="background:var(--main); padding:5px 15px; border-radius:8px; font-size:10px; margin:15px 0 10px;"><?= $set['name'] ?></div>
                <?php foreach($ordered_matches[$id] as $m): ?>
                    <div class="card" style="padding:15px; margin-bottom:10px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; text-align:center;">
                            <div style="flex:1;"><img src="<?= $m['teams']['home']['logo'] ?>" width="30"><br><small><?= $m['teams']['home']['name'] ?></small></div>
                            <div style="flex:1; font-weight:900;"><?= $m['fixture']['status']['short'] == 'NS' ? date("H:i", $m['fixture']['timestamp']) : $m['goals']['home'].'-'.$m['goals']['away'] ?></div>
                            <div style="flex:1;"><img src="<?= $m['teams']['away']['logo'] ?>" width="30"><br><small><?= $m['teams']['away']['name'] ?></small></div>
                        </div>
                    </div>
            <?php endforeach; endif; endforeach; ?>
        </div>

        <div id="section-dual_player" class="channel-section">
            <div class="dual-container">
                <div class="dual-slot">
                    <div class="dual-screen-v" id="dual-screen-1"></div>
                    <button class="dual-btn-select" onclick="openPicker(1)"><i class="fas fa-tv"></i> اختر القناة الأولى</button>
                </div>
                <div class="dual-slot">
                    <div class="dual-screen-v" id="dual-screen-2"></div>
                    <button class="dual-btn-select" onclick="openPicker(2)"><i class="fas fa-tv"></i> اختر القناة الثانية</button>
                </div>
            </div>
        </div>

        <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="c-head"><span style="font-size:12px; font-weight:900;"><?= $ch['name'] ?></span><span style="color:#ff4d4d; font-size:10px;">● مباشر</span></div>
                <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)"><i class="fas fa-play"></i> تشغيل البث المباشر</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
let activeSlot = 0;
function openPicker(slot) { activeSlot = slot; document.getElementById('ch-picker').style.display = 'flex'; }
function closePicker() { document.getElementById('ch-picker').style.display = 'none'; }
function confirmPick(url, name) {
    document.getElementById('dual-screen-' + activeSlot).innerHTML = `<iframe src="${url}?autoplay=1&muted=1" allowfullscreen></iframe>`;
    closePicker();
}
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}
function startStream(boxId, file, btn) {
    document.getElementById(boxId).style.backgroundImage = "none";
    document.getElementById(boxId).innerHTML = `<iframe src="${file}?autoplay=1" allowfullscreen></iframe>`;
    btn.innerHTML = 'متصل الآن...';
}
window.addEventListener('load', () => { setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1000); });
</script>
</body>
</html>
